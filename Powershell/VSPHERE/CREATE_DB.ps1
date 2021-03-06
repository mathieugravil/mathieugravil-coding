$mydbfile = "C:\Local\DB\VMWARE.db"

Add-Type -Path "C:\Program Files\System.Data.SQLite\2005\bin\System.Data.SQLite.dll"
# Connection
$con = New-Object -TypeName System.Data.SQLite.SQLiteConnection
$con.ConnectionString = "Data Source=$($mydbfile)"
$con.Open()

 
$sql = $con.CreateCommand()
### E3SX TABLE ####
$sql.CommandText = @"
CREATE TABLE ESX (
vsphere_server	TEXT,
cluster	TEXT,
name	TEXT,
model	TEXT,
numcpu	INTEGER,
memory_gb	NUMERIC,
version	TEXT,
PRIMARY KEY(name)
);
"@
Try
{
$sql.ExecuteNonQuery()
}
Catch
{
Write-Error "$($sql.CommandText) Failed !!"
}

### DATASTORES TABLE ####
$sql.CommandText = @"
CREATE TABLE DS (
vsphere_server	TEXT,
cluster	TEXT,
id TEXT,
name	TEXT,
filesystemversion	TEXT,
state	TEXT,
free_gb	NUMERIC,
size_gb	NUMERIC,
PRIMARY KEY(vsphere_server,id)
);
"@
Try
{
$sql.ExecuteNonQuery()
}
Catch
{
Write-Error "$($sql.CommandText) Failed !!"

}

### RESSOURCES_STATE TABLE ####
$sql.CommandText = @"
CREATE TABLE RESSOURCES_STATE (
vsphere_server	TEXT,
cluster	TEXT,
esx_name TEXT,
timestamp INT,
nb_vm_up	INT,
nb_vm	INT,
nbcpuallocated	INT,
nbcpu INT, 
consocpu INT,
memoryallocategb INT,
consomem INT,
memallocation INT,
memorytotalgb INT,
PRIMARY KEY(esx_name,timestamp)
);
"@
Try
{
$sql.ExecuteNonQuery()
}
Catch
{
Write-Error "$($sql.CommandText) Failed !!"

}


### LICENSES TABLE ####
$sql.CommandText = @"
CREATE TABLE LICENSES (
vsphere_server	TEXT,
name	TEXT,
key TEXT,
total	INT,
used	INT,
expirationdate	INT,
PRIMARY KEY(vsphere_server, key)
);
"@
Try
{
$sql.ExecuteNonQuery()
}
Catch
{
Write-Error "$($sql.CommandText) Failed !!"

}


$sql.Dispose()


# CLOSE CONNECTION 
 $con.Close()