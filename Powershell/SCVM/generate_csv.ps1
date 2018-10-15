
####################################################
function Get-ESX($myscvmm, $mydbfile )
####################################################
{


$list_esx=Get-SCVMHost -VMMServer $myscvmm

$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)
write-Host "VCENTER;PATH;Cluster;name;model;numcpu;memory_gb;version"
$sw.WriteLine("VCENTER;PATH;Cluster;name;model;numcpu;memory_gb;version")

Foreach ( $ESX in $list_esx)
{
write-Host "$myscvmm;$($ESX.VMHostGroup);$($ESX.HostCluster);$($ESX.Name);unknow;$($ESX.LogicalProcessorCount);$([Math]::Round($($ESX.TotalMemory)/1024/1024/1024));$($ESX.HyperVVersion )"
$sw.WriteLine("$myscvmm;$($ESX.VMHostGroup);$($ESX.HostCluster);$($ESX.Name);unknow;$($ESX.LogicalProcessorCount);$([Math]::Round($($ESX.TotalMemory)/1024/1024/1024));$($ESX.HyperVVersion )")

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
function Get-SCVM($myscvmm, $mydbfile)
####################################################
{

  $sw.WriteLine(" SCVMM ; Cluster ; HOST ;VM;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles;os ")

   Get-SCVirtualMachine | Select $myscvmm, HostGroupPath, SourceObjectType,CreationTime, CPUCount , MemoryAssignedMB, TotalSize, OperatingSystem  | Export-Csv -Path $mydbfile

}

####################################################
function Get-HOTfixes($mydbfile )
####################################################
{
$hosts_list=@("OPGSFR-WPBS1301.ostc.cloud.corp.local","opgsfr-wpbs1101.ostc.cloud.corp.local","OPGSFR-WPBS1201.ostc.cloud.corp.local","OPGSFR-WPBS1202.ostc.cloud.corp.local","opgsfr-wpbs1102.ostc.cloud.corp.local","OPGSFR-WPBS1302.ostc.cloud.corp.local","OPGSFR-WPBS1303.ostc.cloud.corp.local","OPGSFR-WPBS1203.ostc.cloud.corp.local","OPGSFR-WPBS1103.ostc.cloud.corp.local","OPGSFR-WPGC2105.ostc.cloud.corp.local","OPGSFR-WPGS3101.ostc.cloud.corp.local","OPGSFR-WPGS3104.ostc.cloud.corp.local","OPGSFR-WPGC2106.ostc.cloud.corp.local","OPGSFR-WPGC2101.ostc.cloud.corp.local","OPGSFR-WPGC2102.ostc.cloud.corp.local","OPGSFR-WPGS3106.ostc.cloud.corp.local","OPGSFR-WPGC2104.ostc.cloud.corp.local","OPGSFR-WPGS3105.ostc.cloud.corp.local","OPGSFR-WPGC2103.ostc.cloud.corp.local","OPGSFR-WPGS3103.ostc.cloud.corp.local","OPGSFR-WPGS3102.ostc.cloud.corp.local","OPGSFR-WPBS1110.ostc.cloud.corp.local","OPGSFR-WPBS1310.ostc.cloud.corp.local","OPGSFR-WPBS1210.ostc.cloud.corp.local","OPGSFR-WPBC1102.ostc.cloud.corp.local","OPGSFR-WPBC1101.ostc.cloud.corp.local","opgsfr-wpbc1202.ostc.cloud.corp.local","OPGSFR-WPBC1301.ostc.cloud.corp.local","OPGSFR-WPBC1203.ostc.cloud.corp.local","OPGSFR-WPBC1201.ostc.cloud.corp.local","OPGSFR-WPBC1103.ostc.cloud.corp.local","OPGSFR-WPBC1303.ostc.cloud.corp.local","OPGSFR-WPBC1302.ostc.cloud.corp.local","OPGSFR-WPBC1110.ostc.cloud.corp.local","OPGSFR-WPBC1210.ostc.cloud.corp.local","OPGSFR-WPBC1310.ostc.cloud.corp.local","opgsbe-wphvbr01.ostc.cloud.corp.local","OPGSBE-WPHVBR02.ostc.cloud.corp.local","OPGSFR-WPBS1109.ostc.cloud.corp.local","OPGSFR-WPBS1209.ostc.cloud.corp.local","OPGSFR-WPBS1309.ostc.cloud.corp.local","OPGSFR-WPBS1206.ostc.cloud.corp.local","opgsfr-wpbs1306.ostc.cloud.corp.local","OPGSFR-WPBS1106.ostc.cloud.corp.local","OPGSFR-WPBS1105.ostc.cloud.corp.local","OPGSFR-WPBS1205.ostc.cloud.corp.local","opgsfr-wpbs1305.ostc.cloud.corp.local","OPGSFR-WPBC1209.ostc.cloud.corp.local","OPGSFR-WPBC1309.ostc.cloud.corp.local","opgsfr-wpbc1109.ostc.cloud.corp.local","opgsbe-wphvfe01.ostc.cloud.corp.local","opgsbe-wphvfe02.ostc.cloud.corp.local")

$myuser="OSTC\AJ0242224"
$secpasswd = ConvertTo-SecureString “He@rtbleed09” -AsPlainText -Force
$mycreds = New-Object System.Management.Automation.PSCredential ($myuser, $secpasswd)

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

$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
 $myscvmmlist=@('opgsfr-wpavmm01.ostc.cloud.corp.local', 'opgsfr-wpavmm02.ostc.cloud.corp.local')

#$myuser="OSTC\AJ0242224"
#$secpasswd = ConvertTo-SecureString “He@rtbleed09” -AsPlainText -Force
#$mycreds = New-Object System.Management.Automation.PSCredential ($myuser, $secpasswd)

 #foreach ( $myscvmm in $myscvmmlist )
# {
 #Get-SCVMMServer -Credential $mycreds -ComputerName $myscvmm
#$mydbfile = "D:\Local\DB\HYPER_"+$myscvmm+"_$mytimestamp.csv"
#Get-ESX -myscvmm $myscvmm -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "D:\Local\DB\VM_"+$myscvmm+"_$mytimestamp.csv"
#Get-MGRVM  -myscvmm $myscvmm  -mydbfile $mydbfile
# Get-SCVirtualMachine | Select @{N="SCVMM";E={$myscvmm}},@{N ="TYPE"  ;E= { $_.HostGroupPath.split('\')[1]}},@{N ="SLA"  ;E= { $_.HostGroupPath.split('\')[2]}}, @{N ="SITE"  ;E= { $_.HostGroupPath.split('\')[3]}},VMHost,Tag, ObjectType,Name,CreationTime, CPUCount , MemoryAssignedMB, @{Name="Used_GB"; E={$_.TotalSize/1024/1024/1024} } , OperatingSystem  | Export-Csv -Path $mydbfile


#}
$mydbfile = "D:\Local\DB\HOST_PAtch_"+"_$mytimestamp.csv"
Get-HOTfixes( $mydbfile )
