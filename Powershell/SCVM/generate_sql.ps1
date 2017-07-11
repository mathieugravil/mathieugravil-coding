
####################################################
function Get-ESX($myvsphere, $mydbfile)
####################################################
{
$list_esx=Get-SCVMHost -VMMServer $myvsphere

$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)

Foreach ( $ESX in $list_esx)
{
$sql = 
@"
INSERT INTO ESX (
vsphere_server,
cluster,
name,
model,
numcpu,
memory_gb,
version	
) 
VALUES 
(
"@


$sql += "'"+$myvsphere+"', "
$sql += "'"+ $($ESX.VMHostGroup)+$($ESX.HostCluster)+"' ,";
$sql += "'"+ $($ESX.Name)+"'," ;
$sql += "'unknow', " ;
$sql += "'"+ $($ESX.LogicalProcessorCount)+"', ";
$sql += "'"+ [Math]::Round($($ESX.TotalMemory)/1024/1024/1024)+"', ";
$sql += "'"+ $($ESX.HyperVVersion )+"' ) ;";
 $sw.WriteLine($sql);

}
$sw.Dispose();
$fs.Dispose();
}

$mydbfile = "C:\Users\AJ0242224\Documents\hyperv.sql"
Get-ESX -myvsphere "freppau-hqvmmi1" -mydbfile $mydbfile
Get-ESX -myvsphere "freppau-hpvmmi1" -mydbfile $mydbfile