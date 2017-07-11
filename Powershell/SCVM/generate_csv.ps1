
####################################################
function Get-ESX($myscvmm, $mydbfile)
####################################################
{


$list_esx=Get-SCVMHost -VMMServer $myscvmm

$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)
write-Host "VCENTER;Cluster;name;model;numcpu;memory_gb;version"
$sw.WriteLine("VCENTER;Cluster;name;model;numcpu;memory_gb;version")

Foreach ( $ESX in $list_esx)
{
write-Host "$myscvmm;$($ESX.VMHostGroup)+$($ESX.HostCluster);$($ESX.Name);unknow;$($ESX.LogicalProcessorCount);$([Math]::Round($($ESX.TotalMemory)/1024/1024/1024));$($ESX.HyperVVersion )"
$sw.WriteLine("$myscvmm;$($ESX.VMHostGroup)+$($ESX.HostCluster);$($ESX.Name);unknow;$($ESX.LogicalProcessorCount);$([Math]::Round($($ESX.TotalMemory)/1024/1024/1024));$($ESX.HyperVVersion )")
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
remove-item alias:get-vm
Import-Alias -Path "C:\Local\temp\alias\export_getvm"
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
$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
 $myscvmmlist=@('freppau-hqvmmi1', 'freppau-hpvmmi1')

$myuser="MAIN\Typhoon-hydration"
$secpasswd = ConvertTo-SecureString “@Typh00n” -AsPlainText -Force
$mycreds = New-Object System.Management.Automation.PSCredential ($myuser, $secpasswd)

 foreach ( $myscvmm in $myscvmmlist )
 {
 Get-SCVMMServer -Credential $mycreds -ComputerName $myscvmm
 $mydbfile = "C:\Local\DB\HYPER_"+$myscvmm+"_$mytimestamp.csv"
Get-ESX -myscvmm $myscvmm -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
$mydbfile = "C:\Local\DB\VM_"+$myscvmm+"_$mytimestamp.csv"
Get-MGRVM  -myscvmm $myscvmm  -mydbfile $mydbfile
}
$mydbfile = "C:\Users\AJ0242224\Documents\hyperv.sql"