add-pssnapin VMware.VimAutomation.Core
Connect-VIServer -Server  (read-host "Vsphere hostname") -Protocol https -User  (read-host "Set username") -Password  (read-host "Set user password" -AsSecureString)

Get-Datastore
Get-VirtualSwitch
Get-VMHost
Get-VM 

Disconnect-VIServer -Server * -Force