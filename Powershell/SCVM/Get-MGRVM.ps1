 
 $mydbfile ="c:\Users\Public\res.sql3"
$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)

# $myscvmm= 'freppau-hqvmmi1' 
 $myscvmmlist=@('freppau-hqvmmi1', 'freppau-hpvmmi1')
 foreach ( $myscvmm in $myscvmmlist )
 {
 remove-item alias:get-vm
 $mycluster_list = Get-SCVMHostCluster -VMMServer $myscvmm
 write-Host " SCVMM ; Cluster ; HOST ;VM;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles "
  $sw.WriteLine(" SCVMM ; Cluster ; HOST ;VM;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles ")
foreach ($myclus in  $mycluster_list)
{
 $myvmlist=Get-ClusterResource -Cluster $myclus  | Where-Object {$_.ResourceType –eq 'Virtual Machine'} | Get-VM 
  foreach($myvm in $myvmlist)
  {
  $mymem_gb=[Math]::Round($myvm.MemoryAssigned/1024/1024/1024)
  $mymemmax_gb=[Math]::Round($myvm.MemoryMaximum/1024/1024/1024)
  $myvmdisk_gb= 0 
  foreach ($md in $($myvm| Select-Object VMId |  Get-VHD -ComputerName $myvm.ComputerName))
  {
  $myvmdisk_gb= $myvmdisk_gb + [Math]::Round($md.Size /1024 /1024 /1024)
  }
  write-Host " $myscvmm;$myclus;$($myvm.ComputerName);$($myvm.Name);$($myvm.State);$($myvm.ProcessorCount);$($myvm.DynamicMemoryEnabled);$mymem_gb;$mymax_gb;$myvmdisk_gb "
   $sw.WriteLine( " $myscvmm;$myclus;$($myvm.ComputerName);$($myvm.Name);$($myvm.State);$($myvm.ProcessorCount);$($myvm.DynamicMemoryEnabled);$mymem_gb;$mymax_gb;$myvmdisk_gb ")
  }
  }
  }
  $sw.Dispose();
$fs.Dispose();