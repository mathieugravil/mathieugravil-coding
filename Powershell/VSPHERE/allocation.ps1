add-pssnapin VMware.VimAutomation.Core

$myuser="MAIN\AJ0242224"
$mypasswd="En@el&666"
$mydbfile = "C:\Local\DB\MS.sql"
$myvsphere = "frmsstd-vc01"


Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd
$mydclist = Get-Datacenter
foreach ($mydc in $mydclist)
{
    Write-Host "$mydc"

    $myesxlist=$mydc | Get-VMHost
    Write-Host "ESX-name ; nbvm ; nbcpuallocated ; nbcpu ;ConsoCpu ; MemoryAllocateGb ; ConsoMem ; Memallocation; MemoryTotalGb  "
    foreach ($myesx in $myesxlist)
    {
    $mymemallocated = [long]"0"
    $mynbcpuallocated = [int]"0"
    $mynbvm = [int]"0"
    $myvmlist = $myesx| Get-VM | WHERE {$_.PowerState -eq "PoweredOn"}
    foreach ($myvm in $myvmlist)
    {
    $mynbcpuallocated +=[int]$($myvm.NumCpu)
    $mymemallocated += [long]$($myvm.MemoryGB)
    $mynbvm += 1 
    }
    $consocpu = [Math]::Round(100*$($myesx.CpuUsageMhz)/$($myesx.CpuTotalMhz))
    $consomem = [Math]::Round(100*$($myesx.MemoryUsageGB)/$($myesx.MemoryTotalGB))
    $mymemallocated = [Math]::Round($mymemallocated)
    $mymemtotal = [Math]::Round($($myesx.MemoryTotalGB))
    $memallocation = [Math]::Round(100*$mymemallocated/$($myesx.MemoryTotalGB))
    
    Write-Host "$($myesx.Name) ; $mynbvm ; $mynbcpuallocated ;$($myesx.NumCpu);$consocpu  ;  $mymemallocated ;$consomem ;  $memallocation ;$mymemtotal"
    }
}
#Disconnect-VIServer -Server *  -Force