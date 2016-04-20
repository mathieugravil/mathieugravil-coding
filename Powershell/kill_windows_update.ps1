[decimal]$seuil = 65
while (1)
{
$mem_use=gwmi -Class win32_operatingsystem -computername localhost |Select-Object @{Name = "MemoryUsage"; Expression = { “{0:N2}” -f ((($_.TotalVisibleMemorySize - $_.FreePhysicalMemory)*100)/ $_.TotalVisibleMemorySize) }}
$now = Get-Date -Format yyyyMMdd-HH:mm:ss
$mem_str=$mem_use.MemoryUsage.Replace(',','.')
$mem_int=[decimal]$mem_str
if ($mem_int -gt $seuil )
{

$serv= Get-Service | Where-Object {$_.DisplayName -like "Windows Update"}
if ($serv.Status -eq "Stopped")
{
Write-Host " $($now)  -   $($mem_use.MemoryUsage) % Used - rien à faire win update already killed"
}
else
{
Stop-Service $serv
Set-Service -StartupType Disabled -Name $serv.Name
Write-Host " $($now)  -   $($mem_use.MemoryUsage) % Used - on tu win update"
}
}
else
{
$serv= Get-Service | Where-Object {$_.DisplayName -like "Windows Update"}
if ($serv.Status -eq "Stopped")
{
Set-Service -StartupType Automatic -Name $serv.Name
Start-Service $serv
Write-Host " $($now)  -   $($mem_use.MemoryUsage) % Used - On a restarté "
}
else
{
Write-Host " $($now)  -   $($mem_use.MemoryUsage) % Used - On fait rien."
}
}
sleep 60
}