####################################################
function Get-ESX($myvsphere, $myuser, $mypasswd, $mydbfile)
####################################################
{
Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd
$list_esx= Get-VMHost 
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
$sql += "'"+ $($ESX.Parent)+"' ,";
$sql += "'"+ $($ESX.Name)+"'," ;
$sql += "'"+ $($ESX.Model)+"', " ;
$sql += "'"+ $($ESX.NumCpu)+"', ";
$sql += "'"+ $($ESX.MemoryTotalGB)+"', ";
$sql += "'"+ $($ESX.Version)+"' ) ;";
 $sw.WriteLine($sql);

}
$sw.Dispose();
$fs.Dispose();
Disconnect-VIServer -Server *  -Force
}

####################################################
function Get-DS($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd
$list_ds= Get-Datastore 
$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)
Foreach ( $ds in $list_ds)
{
$sql = 
@"
INSERT INTO DS (
vsphere_server,
cluster,
id,
name	,
filesystemversion	,
state,
free_gb,
size_gb
) 
VALUES 
(
"@

$sql += "'"+$myvsphere+"', ";
$sql += "'"+$($ds.Datacenter)+"', ";
$sql += "'"+$($ds.Id)+"', ";
$sql += "'"+$($ds.Name)+"', ";
$sql += "'"+$($ds.FileSystemVersion)+"', ";
$sql += "'"+$($ds.State)+"', ";
$sql += "'"+$($ds.FreeSpaceGB)+"', ";
$sql += "'"+$($ds.CapacityGB)+"'  );";
$sw.WriteLine($sql); 
}
$sw.Dispose()
$fs.Dispose()
Disconnect-VIServer -Server *  -Force
}
####################################################
function Get-Ressources($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd
$mycllist = Get-Cluster

$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)


foreach ($mycl in $mycllist)
{
    Write-Host "$mycl"

    $myesxlist=$mycl | Get-VMHost
    Write-Host "ESX-name ; nbvm ; nbcpuallocated ; nbcpu ;ConsoCpu ; MemoryAllocateGb ; ConsoMem ; Memallocation; MemoryTotalGb  "
    foreach ($myesx in $myesxlist)
    {
    $mymemallocated = [long]"0"
    $mynbcpuallocated = [int]"0"
    $mynbvm = [int]"0"
    $mynbvmup = [int]"0"
    $sql = 
@"
INSERT INTO 
RESSOURCES_STATE (
vsphere_server,
cluster	,
esx_name ,
timestamp,
nb_vm_up,
nb_vm,
nbcpuallocated,
nbcpu, 
consocpu,
memoryallocategb ,
consomem ,
memallocation ,
memorytotalgb 
) 
VALUES 
(
"@

    $myvmlist = $myesx| Get-VM 
    #| WHERE {$_.PowerState -eq "PoweredOn"}
    foreach ($myvm in $myvmlist)
    {
    $mynbcpuallocated +=[int]$($myvm.NumCpu)
    $mymemallocated += [long]$($myvm.MemoryGB)
    $mynbvm += 1 
    if ($($myvm.PowerState) -eq "PoweredOn" )
    {
    $mynbvmup += 1
    }
    }
    $consocpu = [Math]::Round(100*$($myesx.CpuUsageMhz)/$($myesx.CpuTotalMhz))
    $consomem = [Math]::Round(100*$($myesx.MemoryUsageGB)/$($myesx.MemoryTotalGB))
    $mymemallocated = [Math]::Round($mymemallocated)
    $mymemtotal = [Math]::Round($($myesx.MemoryTotalGB))
    $memallocation = [Math]::Round(100*$mymemallocated/$($myesx.MemoryTotalGB))
    
    $sql += "'"+$myvsphere+"', ";
    $sql += "'"+$mycl+"', ";
    $sql += "'"+$myesx+"', ";
    $sql += "'"+$mytimestamp+"', ";
    $sql += "'"+$mynbvmup+"', ";
    $sql += "'"+$mynbvm+"', ";
    $sql += "'"+$mynbcpuallocated+"', ";
    $sql += "'"+$($myesx.NumCpu)+"', ";
    $sql += "'"+$consocpu+"', ";
    $sql += "'"+$mymemallocated+"', ";
    $sql += "'"+$consomem+"', ";
    $sql += "'"+$memallocation+"', ";
    $sql += "'"+$mymemtotal+"'); ";

    $sw.WriteLine($sql);


    Write-Host "$($myesx.Name) ; $mynbvm ; $mynbcpuallocated ;$($myesx.NumCpu);$consocpu  ;  $mymemallocated ;$consomem ;  $memallocation ;$mymemtotal"
    }
}
$sw.Dispose()
$fs.Dispose()
Disconnect-VIServer -Server *  -Force
#Disconnect-VIServer -Server *  -Force
}
####################################################
function Get-License($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
# Set to multiple VC Mode 
#if(((Get-PowerCLIConfiguration).DefaultVIServerMode) -ne "Multiple") { 
#    Set-PowerCLIConfiguration -DefaultVIServerMode Multiple | Out-Null 
#}

# Make sure you connect to your VCs here
Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd

# Get the license info from each VC in turn 
$vSphereLicInfo = @() 
$ServiceInstance = Get-View ServiceInstance 
Foreach ($LicenseMan in Get-View ($ServiceInstance | Select -First 1).Content.LicenseManager) { 
    Foreach ($License in ($LicenseMan | Select -ExpandProperty Licenses)) { 
        $Details = "" |Select VC, Name, Key, Total, Used, ExpirationDate , Information 
        $Details.VC = ([Uri]$LicenseMan.Client.ServiceUrl).Host 
        $Details.Name= $License.Name 
        $Details.Key= $License.LicenseKey 
        $Details.Total= $License.Total 
        $Details.Used= $License.Used 
        $Details.Information= $License.Labels | Select -expand Value 
        $Details.ExpirationDate = $License.Properties | Where { $_.key -eq "expirationDate" } | Select -ExpandProperty Value 
        $vSphereLicInfo += $Details 
    } 
} 
$vSphereLicInfo | Format-Table -AutoSize
}

add-pssnapin VMware.VimAutomation.Core

$myuser="MAIN\AJ0242224"
$mypasswd="En@el&666"
$mydbfile = "C:\Local\DB\MS_res.sql"


#Get-ESX -myvsphere "frmsstd-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#Get-ESX -myvsphere "frmscli-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#Get-DS -myvsphere "frmsstd-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#Get-DS -myvsphere "frmscli-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
Get-Ressources -myvsphere "frmscli-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
Get-Ressources -myvsphere "frmsstd-vc01" -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile