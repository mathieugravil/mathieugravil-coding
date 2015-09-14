New-NetIPAddress -IPAddress 10.0.0.2 -InterfaceAlias "Ethernet"  -DefaultGateway 10.0.0.1 -AddressFamily IPv4 -PrefixLength 24

Install-WindowsFeature AD-Domain-Services -IncludeManagementTools
#Faut le réseau pour creer le domain...
# Demande un password Pa$$w0rd...
Install-ADDSForest -DomainName fr.rh.is.gi.local
Add-DnsServerPrimaryZone 0.0.10.in-addr-arpa -ZoneFile 0.0.10.in-addr.arpa.dns
Add-DnsServerPrimaryZone 0.0.10.in-addr-arpa -ZoneFile 0.0.10.in-addr.arpa.dns


Add-Computer -DomainName fr.rh.is.gi.local
New-ADUser -SamAccountName mathieugravil -AccountPassword (read-host "Set user password" -AsSecureString) -Name "Mathieu GRAVIL" -Enabled $true -PasswordNeverExpires $true -ChangePasswordAtLogon $false
 Get-ADUser -Filter{Name -eq "Mathieu GRAVIL"}
Add-ADPrincipalGroupMembership -Identity "CN=Mathieu GRAVIL,CN=Users,DC=fr,DC=rh,DC=is,DC=gi,DC=local" -MemberOf "CN=Enterprise Admins,CN=Users,DC=fr,DC=rh,DC=is,DC=gi,DC=local","CN=Domain Admins,CN=Users,DC=fr,DC=rh,DC=is,DC=gi,DC=local"
