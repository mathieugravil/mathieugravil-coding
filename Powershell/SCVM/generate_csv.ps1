####################################################
function Get-ESX($myscvmm, $mydbfile, $mycreds )
####################################################
{
$list_esx=Get-SCVMHost -VMMServer $myscvmm  | where-object  {$_.DomainName -eq "ostc.cloud.corp.local" }

$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)
write-Host "SCVMM;PATH;Cluster;name;numcpu;memory_gb;version;KBs;KBdetails"
$sw.WriteLine("SCVMM;PATH;Cluster;name;numcpu;memory_gb;version;KBs;KBdetails")

Foreach ( $ESX in $list_esx)
{
 $result = Get-HotFix -ComputerName $ESX  -Credential $mycreds| select HotFixID, @{N="myDate";E={  $(Get-Date $_.InstalledOn -Format 'dd/MM/yyyy')}}
 $mykbs=""
 $mydetails=""
 Foreach ( $kb in $result)
 {
 $mykbs+=$kb.HotFixID+" "
 $mydetails+=$kb.HotFixID+"_"+$kb.myDate+"#"
 }
 # $KB = [system.String]::Join("#", $result)
write-Host "$myscvmm;$($ESX.VMHostGroup);$($ESX.HostCluster);$($ESX.Name);$($ESX.LogicalProcessorCount);$([Math]::Round($($ESX.TotalMemory)/1024/1024/1024));$($ESX.HyperVVersion );$mykbs;$mydetails"
$sw.WriteLine("$myscvmm;$($ESX.VMHostGroup);$($ESX.HostCluster);$($ESX.Name);$($ESX.LogicalProcessorCount);$([Math]::Round($($ESX.TotalMemory)/1024/1024/1024));$($ESX.HyperVVersion );$mykbs;$mydetails")

}
$sw.Dispose();
$fs.Dispose();
}
####################################################
function Get-MGRVM($myscvmm, $mydbfile)
####################################################
{
$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)

 $mycluster_list = Get-SCVMHostCluster -VMMServer $myscvmm
 write-Host " SCVMM ; Cluster ; HOST ;VM;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles;os "
  $sw.WriteLine(" SCVMM ; Cluster ; HOST ;VM;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles;os ")
foreach ($myclus in  $mycluster_list)
{
Write-Host $myclus 
 $myvmlist=Get-ClusterResource -Cluster $myclus  | Where-Object {$_.ResourceType –eq 'Virtual Machine'} | Get-VM 
  foreach($myvm in $myvmlist)
  {
  $mymem_gb=$([Math]::Round($myvm.MemoryAssigned/1024/1024/1024))
  $mymemmax_gb=$([Math]::Round($myvm.MemoryMaximum/1024/1024/1024))
  $myvmdisk_gb= 0 
  foreach ($md in $($myvm| Select-Object VMId |  Get-VHD -ComputerName $myvm.ComputerName))
  {
  $myvmdisk_gb= $myvmdisk_gb + [Math]::Round($md.Size /1024 /1024 /1024)
  }
  $myos = $(Get-SCVirtualMachine -VMMServer $myscvmm -Name $($myvm.Name) | select -expandproperty OperatingSystem)
    write-Host " $myscvmm;$myclus;$($myvm.ComputerName);$($myvm.Name);$($myvm.State);$($myvm.ProcessorCount);$($myvm.DynamicMemoryEnabled);$mymem_gb;$mymemmax_gb;$myvmdisk_gb;$myos "
   $sw.WriteLine( " $myscvmm;$myclus;$($myvm.ComputerName);$($myvm.Name);$($myvm.State);$($myvm.ProcessorCount);$($myvm.DynamicMemoryEnabled);$mymem_gb;$mymemmax_gb;$myvmdisk_gb;$myos ")
  }
  }
  $sw.Dispose();
$fs.Dispose();
}
####################################################
function Get-HOTfixes($myscvmm,$mydbfile,$mycreds )
####################################################
{
$hosts_list= Get-SCVMHost -VMMServer $myscvmm | where-object  {$_.DomainName -eq "ostc.cloud.corp.local" }| select -ExpandProperty  Name
$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read
$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)
write-Host "PSComputerName;HotFixID;InstalledOn"
$sw.WriteLine("PSComputerName;HotFixID;InstalledOn")
 foreach($ESX in $hosts_list)
  {
 $result = Get-HotFix -ComputerName $ESX  -Credential $mycreds 
 foreach($kb in $result)
 {
  $mydate = Get-Date $kb.InstalledOn -Format 'dd/MM/yyyy'
  $sw.WriteLine("$($kb.PSComputerName);$($kb.HotFixID);$mydate")
  write-Host "$($kb.PSComputerName);$($kb.HotFixID);$mydate"
 }
 }
  $sw.Dispose();
$fs.Dispose();
}
Add-PSSnapin VeeamPSSnapin
$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
 $myscvmmlist=@('opgsfd.corp.local', 'opgsfd.corp.local')
 $veeam_em="opgsfd.corp.local"
$myuser="toto"
$secpasswd = ConvertTo-SecureString “titi” -AsPlainText -Force
$mycreds = New-Object System.Management.Automation.PSCredential ($myuser, $secpasswd)


Connect-VBRServer -Credential $mycreds -Server $veeam_em 
foreach ( $myscvmm in $myscvmmlist )
 {
 $myconn=Get-SCVMMServer -Credential $mycreds -ComputerName $myscvmm
#$mydbfile = "D:\Local\DB\HYPER_"+$myscvmm+"_$mytimestamp.csv"
#Get-ESX -myscvmm $myscvmm -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile -mycreds $mycreds


$i = 0
$report = @()
$mydbfile = "D:\Local\DB\BACVM_"+$myscvmm+"_$mytimestamp.csv"
write $mytimestamp
$mylist_vms= Get-SCVirtualMachine -VMMServer $myconn | Where-Object { $_.VirtualizationPlatform -eq "HyperV"}
$report=foreach($vm in $mylist_vms){
    $backup=Get-VBRRestorePoint -Name $vm.Name |
            Sort-Object –Property CreationTime –Descending |
            Select -First 1 | 
            select Type, CreationTimeUtc, @{N="Size_GB";E={[Math]::Round($_.ApproxSize/1024/1024/1024)}}
    #New-Object PSObject -Property  
    [PSCustomObject]@{
    SCVMM = $myscvmm
    HostGroupPath = $vm.HostGroupPath
    TYPE =  $vm.HostGroupPath.split('\')[1]
    SLA =  $vm.HostGroupPath.split('\')[2]
    SITE = $vm.HostGroupPath.split('\')[3]
    Cluster = Get-SCVMHost  -ComputerName $vm.VMHost  | select -ExpandProperty HostCluster
    VMHost = $vm.VMHost
    Tag = $vm.Tag
    Name = $vm.Name
    CreationTime = $vm.CreationTime
    CPUCount = $vm.CPUCount
    MemoryAssignedMB = $vm.MemoryAssignedMB
    Sto_GB = [Math]::Round($vm.TotalSize/1024/1024/1024)
    OperatingSystem = $vm.OperatingSystem
    status =  $vm.status
    Backup_type = $backup.Type
    Backup_date =  $backup.CreationTimeUtc
    Backup_size_GB = $backup.Size_GB
    }
}
$report | Export-Csv -Path $mydbfile 

#$mylist_vms | Select @{N="SCVMM";E={$myscvmm}},HostGroupPath, 
#@{N ="TYPE"  ;E= { $_.HostGroupPath.split('\')[1]}},
#@{N ="SLA"  ;E= { $_.HostGroupPath.split('\')[2]}},
# @{N ="SITE"  ;E= { $_.HostGroupPath.split('\')[3]}},
# @{N="Cluster";E={Get-VMHost  -ComputerName $_.VMHost  | select -ExpandProperty HostCluster}},
# VMHost,Tag,Name,CreationTime, CPUCount , MemoryAssignedMB,
#  @{Name="Used_GB"; E={$_.TotalSize/1024/1024/1024} } , OperatingSystem, status  | Export-Csv -Path $mydbfile

#$mydbfile = "D:\Local\DB\HOST_PATCHS_"+$myscvmm+"_$mytimestamp.csv"
#Get-HOTfixes -myscvmm $myscvmm -mydbfile $mydbfile -mycreds $mycreds
}

Disconnect-VBRServer 

