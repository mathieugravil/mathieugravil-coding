####################################################
function Get-ESX($myvsphere, $myuser, $mypasswd, $mydbfile)
####################################################
{
Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd
$list_esx= Get-VMHost 
$con = New-Object -TypeName System.Data.SQLite.SQLiteConnection
$con.ConnectionString = "Data Source=$($mydbfile)"
$con.Open()

$sql = $con.CreateCommand()
$sql.CommandText = 
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
@vsphere_server,
@cluster,
@name,
@model,
@numcpu,
@memory_gb,
@version
)
"@

Foreach ( $ESX in $list_esx)
{
$sql.Parameters.AddWithValue("@vsphere_server", $myvsphere);
$sql.Parameters.AddWithValue("@cluster", $ESX.Parent);
$sql.Parameters.AddWithValue("@name", $ESX.Name);
$sql.Parameters.AddWithValue("@model", $ESX.Model);
$sql.Parameters.AddWithValue("@numcpu", $ESX.NumCpu);
$sql.Parameters.AddWithValue("@memory_gb", $ESX.MemoryTotalGB );
$sql.Parameters.AddWithValue("@version", $ESX.Version );
$sql.ExecuteNonQuery()
}
$sql.Dispose()
$con.Close()
}

####################################################
function Get-DS($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd
$list_ds= Get-Datastore 
$con = New-Object -TypeName System.Data.SQLite.SQLiteConnection
$con.ConnectionString = "Data Source=$($mydbfile)"
$con.Open()

$sql = $con.CreateCommand()
$sql.CommandText = 
@"
INSERT INTO DS (
vsphere_server,
name	,
filesystemversion	,
state,
free_gb,
size_gb
) 
VALUES 
(
@vsphere_server,
@name	,
@filesystemversion	,
@state,
@free_gb,
@size_gb
)
"@
Foreach ( $ds in $list_ds)
{
$sql.Parameters.AddWithValue("@vsphere_server", $myvsphere);
$sql.Parameters.AddWithValue("@name", $ds.Name);
$sql.Parameters.AddWithValue("@filesystemversion", $ds.FileSystemVersion);
$sql.Parameters.AddWithValue("@state", $ds.State);
$sql.Parameters.AddWithValue("@free_gb", $ds.FreeSpaceGB );
$sql.Parameters.AddWithValue("@size_gb", $ds.CapacityGB );
$sql.ExecuteNonQuery()
}
$sql.Dispose()
$con.Close()

}



add-pssnapin VMware.VimAutomation.Core

$myuser="MAIN\AJ0242224"
$mypasswd="En@el&666"
$mydbfile = "C:\Local\DB\VMWARE.db"


#Get-ESX -myvsphere "frmsstd-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#Get-ESX -myvsphere "frmscli-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
Get-DS -myvsphere "frmsstd-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
Get-DS -myvsphere "frmscli-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
