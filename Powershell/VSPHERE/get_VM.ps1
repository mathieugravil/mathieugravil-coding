add-pssnapin VMware.VimAutomation.Core
Connect-VIServer -Server  (read-host "Vsphere hostname") -Protocol https -User  (read-host "Set username") -Password  (read-host "Set user password" -AsSecureString)
Get-Cluster
Get-Datastore
Get-VirtualSwitch
Get-VMHost
Get-VM 
Get-Cluster "Cluster Name" | Get-VM | Export-CSV C:\ListVMsInCluster.csv
 Get-Cluster "CL-CLI-ORA-01" | Get-VMHost