<#
.Synopsis
   Calculates Memory Commitment on a Hyper-V Cluster within SCVMM for potential overcommitment
.DESCRIPTION
   The script uses the calculation method described in the following article:
   http://blogs.technet.com/b/scvmm/archive/2012/03/27/system-center-2012-vmm-cluster-reserve-calculations.aspx
   to check if the cluster can still serve all the running memory when a node
   in the cluster fails. The calculation currently supports a node reserve value of 1.
   Scenarios with a higher reserve values must be calculated manually. However the reported
   Memory usage Values can help for the calculation.
.PARAMETER
  Clustername
  The name of the clusterobject within VMM to be checked for memory overcommitment
.EXAMPLE
  Get-ClusterMemoryStatus.ps1 -Clustername acluster.contoso.com
.NOTES
    NAME: GetClusterMemoryStatus.ps1
    VERSION: 1.0
    AUTHOR: Michael Rueefli
    LASTEDIT: 13.09.2013
#>

[CMDLETBinding(SupportsShouldProcess = $False, ConfirmImpact = "None", DefaultParameterSetName = "")]
Param(
    [Parameter(Mandatory=$True,
    ValueFromPipeline=$True)]
    $ClusterName
)

#Set strict mode
Set-StrictMode -Version 3

Function Get-LargestVMOnHost
{
    param(
    [STRING]$nodename
    )
    $vms = Get-SCVirtualMachine -VMHost $nodename
    [ARRAY]$a = @()
    Foreach ($vm in $vms)
    {

        If ($vm.DynamicMemoryMaximumMB)
        {
            $vmmaxmem = [decimal]::round(($vm.DynamicMemoryMaximumMB)/1024)
            $vmdemandmem = "{0:N1}" -f (($vm.DynamicMemoryDemandMB)/1024)
        }
        Else
        {
            $vmmaxmem =  [decimal]::round(($vm.Memory)/1024)
            $vmdemandmem = $vmmaxmem
        }
        $vmmemstatus = New-Object -TypeName PSObject -Property @{
        VMname=($vm.Name)
        VMMemDemand=$vmdemandmem
        VMMaxMem=$vmmaxmem
        }
        $a += $vmmemstatus
    }
    Return ($a | Sort-Object VMMaxMem -Descending)[0]
}

Function Get-RemainingVMTotalMemory
{
    param(
    [STRING]$nodename,
    [STRING]$largestVMName
    )
    [INT]$TotalRemainingMemMax=0
    [INT]$TotalRemainingMemDemand=0
    $remainingVMs = Get-SCVirtualMachine -VMHost $nodename | ? {$_.Name -ne $largestVMname -and $_.IsHighlyAvailable -eq $true}
    Foreach ($rvm in $remainingvms)
    {
        If ($rvm.DynamicMemoryMaximumMB)
        {
            $vmmaxmem = [decimal]::round(($rvm.DynamicMemoryMaximumMB)/1024)
            $vmdemandmem = "{0:N1}" -f (($rvm.DynamicMemoryDemandMB)/1024)
        }
        Else
        {
            $vmmaxmem =  [decimal]::round(($rvm.Memory)/1024)
            $vmdemandmem = $vmmaxmem
        }
        $TotalRemainingMemMax += $vmmaxmem
        $TotalRemainingMemDemand += $vmdemandmem
    }

    $result = New-Object -TypeName PSObject -Property @{OtherVMMax=$TotalRemainingMemMax;OtherVMDemand=$TotalRemainingMemDemand}
    return $result
}

## Main Routine ###
If (!(Get-Module VirtualMachineManager))
{
    Import-Module VirtualMachineManager
}

$cluster = Get-SCVMHostCluster $clustername
$Clusterreserve = $cluster.ClusterReserve
$ClusterNodes = $cluster | Get-SCVMHost
$NodeCount = $Clusternodes.count
$statusreport = @()

Foreach ($node in $ClusterNodes)
{
    #Get the largest VM on Host
    $largestVMonHost = Get-LargestVMOnHost -nodename $node.Name

    #Get the Total Memory of all remaining VMs except the largest
    $remaingVMMem = Get-RemainingVMTotalMemory -nodename $node.name -largestVMName $largestVMonHost.VMName
    [INT]$otherVMMax = $remaingVMMem.OtherVMMax
    [INT]$otherVMDemand = $remaingVMMem.OtherVMDemand

    #Create new Host Object
    $hoststatus = new-object -TypeName PSObject -Property @{
    NodeName=($node.Name)
    TotalGB=([decimal]::round((Get-SCVMHost $node.name).TotalMemory/1024/1024/1024))
    ReserveGB=([decimal]::round(($node.MemoryReserveMB)/1024))
    AvailableGB=([decimal]::round($node.AvailableMemory/1024))
    LargestVMName=$largestVMonHost.VMName
    LargestVMDemandGB=$largestVMonHost.VMMemDemand
    LargestVMMaxGB=$largestVMonHost.VMMaxMem
    OtherVMTotalMaxGB=$otherVMMax
    OtherVMTotalDemandGB=$otherVMDemand
    TotalVMUsedGB=$otherVMDemand + $largestVMonHost.VMMemDemand
    }
    $statusreport += $hoststatus 

 }

  #Report Host Memory Summary
 "=============================================================================="
 "Clustername: $clustername"
 "Node Count: $nodecount"
 "Cluster Reserve Count: $clusterreserve"
 "=============================================================================="
 $statusreport | Sort-Object NodeName | Format-Table NodeName,TotalGB,AvailableGB,ReserveGB,TotalVMUsedGB,LargestVMName,LargestVMMaxGB,LargestVMDemandGB,OtherVMTotalMaxGB,OtherVMTotalDemandGB -AutoSize

 Foreach ($obj in $statusreport)
 {
    "Calculating with node failure: $($obj.NodeName)"
    $nodeotherVMTotalDemand = ($obj.OtherVMTotalDemandGB)
    $othernodes = $statusreport | Sort-Object NodeName | ? {$_.NodeName -ne $obj.NodeName}
    [INT]$totalextracapacity=0

    Foreach ($element in $othernodes)
    {

        $nodeextracapacity = (($element.TotalGB) - ($element.ReserveGB) - ($element.TotalVMUsedGB) - ($obj.LargestVMDemandGB))

        If ($nodeextracapacity -gt 0)
        {
            $totalextracapacity += $nodeextracapacity
        }

        #Report
        write-verbose "    Balance for Node: $($element.NodeName) on Failure of Node: $($obj.NodeName)"
        If ($nodeextracapacity -gt 0)
        {
            write-verbose "        Extra Capacity Balance (GB) looks ok: $nodeextracapacity"
        }
        Else
        {
            write-verbose "        Negative Extra Capacity Balance (GB): $nodeextracapacity"
        }
        #

    }
    If ($nodeotherVMTotalDemand -gt $totalextracapacity)
    {
        write-host "WARNING! Cluster will be overcommitted upon a failure of node: $($obj.NodeName)" -ForegroundColor red
    }
    Else
    {
        write-host "Cluster can serve all current Memory Resources upon a failure of node: $($obj.NodeName)" -ForegroundColor Green
    }
 }