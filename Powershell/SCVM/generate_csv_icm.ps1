

$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
#$sUserName = "MAIN\SPFREPCAPACITYTOOL"
 #   $sPassword = ConvertTo-SecureString 'Sjx;bv,F>u(;4LwS#\d!' -AsPlainText -Force
    $sUserName = "MAIN\AJ0242224"
        $sPassword = ConvertTo-SecureString 'He@rtbleed0814' -AsPlainText -Force
    $sServerWAPVMM = 'FREPPAU-HPVMMI1'
    $sServerRebond = 'FREPPAU-HPVMMI1'
   #     $sServerWAPVMM = 'FREPPAU-HQVMMI1'
    #$sServerRebond = 'FREPPAU-HQVMMI1'
    Enable-WSManCredSSP -Role Client -DelegateComputer  $sServerRebond -Force


    $oCred = New-Object -TypeName System.Management.Automation.PSCredential -ArgumentList $sUserName, $sPassword #Get-Credential -Message "Entrez votre mot de passe" -UserName "$env:USERDOMAIN\$env:USERNAME"
    
    $oSessionWAPVMM = New-PSSession `
                        -ComputerName $sServerRebond `
                        -Authentication Credssp `
                        -Credential $oCred

# $mydbfile = "C:\Local\DB\HYPER_"+$sServerWAPVMM+"_$mytimestamp.csv"
  

#    $job1= icm -Session $oSessionWAPVMM -ErrorAction Stop -ArgumentList $sServerWAPVMM -ScriptBlock {
 #       param($sServer)

#        Import-Module virtualmachinemanager
 #       Get-SCVMMServer -ComputerName $sServer  | Out-Null
       
  #      $list_esx=Get-SCVMHost -VMMServer $sServer
      


#write-Output "VCENTER;Cluster;name;model;numcpu;memory_gb;version"


#Foreach ( $ESX in $list_esx)
#{

#Write-Output "$sServer;$($ESX.VMHostGroup)+$($ESX.HostCluster);$($ESX.Name);unknow;$($ESX.LogicalProcessorCount);$([Math]::Round($($ESX.TotalMemory)/1024/1024/1024));$($ESX.HyperVVersion )"
#}
#} -AsJob
#Wait-Job $job1
#$data = Receive-Job -Job $job1 
# $data > $mydbfile


$mydbfile = "C:\Local\DB\VM_"+$sServerWAPVMM+"_$mytimestamp.csv"

$job2 =     icm -Session $oSessionWAPVMM -ErrorAction Stop -ArgumentList $sServerWAPVMM -ScriptBlock {
        param($sServer)

        Import-Module virtualmachinemanager
        Get-SCVMMServer -ComputerName $sServer | Out-Null
remove-item alias:get-vm
#Set-Alias -Name Get-VM -Value Get-SCVirtualMachine
#Import-Alias -Path "C:\Local\temp\alias\export_getvm"
$mycluster_list = Get-SCVMHostCluster -VMMServer $sServer
write-Output " SCVMM ; Cluster ; HOST ;VM;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles;os;vlan "
foreach ($myclus in  $mycluster_list)
{
$myvmlist=Get-ClusterResource -Cluster $myclus  | Where-Object {$_.ResourceType –eq 'Virtual Machine'} | Get-VM 
  foreach($myvm in $myvmlist)
  {
  $mymem_gb=$([Math]::Round($myvm.MemoryAssigned/1024/1024/1024))
  $mymemmax_gb=$([Math]::Round($myvm.MemoryMaximum/1024/1024/1024))
  $myvmdisk_gb= 0 
 # foreach ($md in $($myvm| Select-Object VMId |  Get-VHD -ComputerName $myvm.ComputerName))
  #{
  #$myvmdisk_gb= $myvmdisk_gb + [Math]::Round($md.Size /1024 /1024 /1024)
  #}
  $myos = $(Get-SCVirtualMachine -VMMServer $sServer -Name $($myvm.Name) | select -expandproperty OperatingSystem)
  $myvlan = $([String] $(Get-SCVirtualMachine -VMMServer $sServer   -Name  $($myvm.Name) | Select @{label="VlanID";expression={[string]::join(“;”,($_.VirtualNetworkAdapters).VlanID)}}| select -ExpandProperty VlanID))
    write-Output " $sServer;$myclus;$($myvm.ComputerName);$($myvm.Name);$($myvm.State);$($myvm.ProcessorCount);$($myvm.DynamicMemoryEnabled);$mymem_gb;$mymemmax_gb;$myvmdisk_gb;$myos;$myvlan "

  }
  }
} -AsJob
Wait-Job $job2
$r2 = Receive-Job -Job $job2                                                                                                          
$r2 > $mydbfile   
