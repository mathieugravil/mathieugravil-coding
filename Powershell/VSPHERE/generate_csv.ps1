####################################################
function Get-myESX($myvsphere, $myuser, $mypasswd, $mydbfile)
####################################################
{
Connect-VIServer -ErrorAction Stop -Server $myvsphere  -User $myuser  -Password $mypasswd
$list_esx= Get-VMHost 
$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)
write-Host "VCENTER;Cluster;name;model;numcpu;memory_gb;version"
$sw.WriteLine("VCENTER;Cluster;name;model;numcpu;memory_gb;version")
Foreach ( $ESX in $list_esx)
{
write-Host "$myvsphere;$($ESX.Parent);$($ESX.Name);$($ESX.Model);$($ESX.NumCpu);$($ESX.MemoryTotalGB);$($ESX.Version)"
$sw.WriteLine("$myvsphere;$($ESX.Parent);$($ESX.Name);$($ESX.Model);$($ESX.NumCpu);$($ESX.MemoryTotalGB);$($ESX.Version)")
}
$sw.Dispose();
$fs.Dispose();
Disconnect-VIServer -Server *  -Force -confirm:$false
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
write-Host " VCENTER ; Cluster ; ID ;NAME;LUNID;Policy;filesystemversion;state;free_gb;size_gb "
$sw.WriteLine(" VCENTER;Cluster;ID;NAME;LUNID;Policy;filesystemversion;state;free_gb;size_gb ")
Foreach ( $ds in $list_ds)
{
$mylun= $(Get-ScsiLun -Datastore $ds.Name| select -uniq)
write-Host "$myvsphere;$($ds.Datacenter);$($ds.Id);$($ds.Name);$($mylun.CanonicalName);$($mylun.MultipathPolicy);$($ds.FileSystemVersion);$($ds.State);$($ds.FreeSpaceGB);$($ds.CapacityGB)"
$sw.WriteLine("$myvsphere;$($ds.Datacenter);$($ds.Id);$($ds.Name);$($mylun.CanonicalName);$($mylun.MultipathPolicy);$($ds.FileSystemVersion);$($ds.State);$($ds.FreeSpaceGB);$($ds.CapacityGB)")
}
$sw.Dispose()
$fs.Dispose()
#Disconnect-VIServer -Server *  -Force
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


$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)


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
         $sql = 
@"
INSERT INTO 
LICENSES (
vsphere_server,
name	,
key ,
total,
used,
expirationdate
) 
VALUES 
(
"@
         $sql += "'"+$myvsphere+"', ";
         $sql += "'"+$($License.Name)+"', ";
         $sql += "'"+$($License.LicenseKey)+"', ";
         $sql += "'"+$($License.Total)+"', ";
         $sql += "'"+$($License.Used)+"', ";
         $sql += "'"+$($Details.ExpirationDate)+"'); ";

         $sw.WriteLine($sql);
    } 
} 
$vSphereLicInfo | Format-Table -AutoSize
$sw.Dispose()
$fs.Dispose()
Disconnect-VIServer -Server *  -Force
}

####################################################
function Get-MGRVM($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
Connect-VIServer -ErrorAction Stop -Server $myvsphere   -User $myuser  -Password $mypasswd


$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)

# write-Host " VCENTER ; Cluster ; HOST ;VM;DS;lunid;policy;DS_cap_GB;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles "
#$sw.WriteLine(" VCENTER ; Cluster ; HOST ;VM;DS;lunid;policy;DS_cap_GB;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles ")
 #write-Host " VCENTER ; Cluster ; HOST ;VM;DS;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles;os;net "
$sw.WriteLine(" VCENTER ; Cluster ; HOST ;VM;DS;STATE;VCPU;DynMem;MemAssigned;MemoryMaximum;SizeOfSystemFiles;os;net ")
$mycllist = Get-Cluster
foreach ($mycl in $mycllist)
{
$myesxlist=$mycl | Get-VMHost
foreach ($myesx in $myesxlist)
    {
    $myvmlist = $myesx| Get-VM 
      foreach ($myvm in $myvmlist)
      { 
      $toto = 0 
      $myds=$($myvm| Get-Datastore).Name
      #$mylun= $(Get-ScsiLun -Datastore $myds| select -uniq)
       foreach ($mydtest in $($myvm | Get-HardDisk)) { $toto=$toto+$mydtest.CapacityGB }
     #  write-Host "$myvsphere;$mycl;$($myvm.VMHost);$($myvm.Name);$myds;$($mylun.CanonicalName| select -uniq);$($mylun.MultipathPolicy| select -uniq);$($mylun.MultipathPolicy| select -uniq);$($mylun.CapacityGB| select -uniq);$($myvm.PowerState);$($myvm.NumCpu);NA;$($myvm.MemoryGB);$($myvm.MemoryGB);$toto "
   #$sw.WriteLine( "$myvsphere;$mycl;$($myvm.VMHost);$($myvm.Name);$myds;$($mylun.CanonicalName| select -uniq);$($mylun.MultipathPolicy| select -uniq);$($mylun.MultipathPolicy| select -uniq);$($mylun.CapacityGB| select -uniq);$($myvm.PowerState);$($myvm.NumCpu);NA;$($myvm.MemoryGB);$($myvm.MemoryGB);$toto ")
           $ofs='|'
           $myvm_net = $([String] $(Get-NetworkAdapter -vm $($myvm.Name) | select -ExpandProperty NetworkName))
          
          
        #  write-Host "$myvsphere;$mycl;$($myvm.VMHost);$($myvm.Name);$myds;$($myvm.PowerState);$($myvm.NumCpu);NA;$($myvm.MemoryGB);$($myvm.MemoryGB);$toto;$($myvm.ExtensionData.Guest.GuestFullName);$myvm_net "
   $sw.WriteLine( "$myvsphere;$mycl;$($myvm.VMHost);$($myvm.Name);$myds;$($myvm.PowerState);$($myvm.NumCpu);NA;$($myvm.MemoryGB);$($myvm.MemoryGB);$toto;$($myvm.ExtensionData.Guest.GuestFullName);$myvm_net ")

     }
    }


}
     
$sw.WriteLine($sql);
   

$sw.Dispose()
$fs.Dispose()
Disconnect-VIServer -Server *  -Force -confirm:$false
}

####################################################
function Get-SAN($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
Connect-VIServer -Server $myvsphere  -User $myuser  -Password $mypasswd


$path = [System.IO.Path]::Combine($Env:TEMP,$mydbfile)
$mode = [System.IO.FileMode]::Append
$access = [System.IO.FileAccess]::Write
$sharing = [IO.FileShare]::Read

$fs = New-Object IO.FileStream($path, $mode, $access, $sharing)
$sw = New-Object System.IO.StreamWriter($fs)

 write-Host " VCENTER ; Cluster ; HOST ; ID ; policy ; size_GB "
 $sw.WriteLine(" VCENTER ; Cluster ; HOST ; ID ; policy ; size_GB ")
$mycllist = Get-Cluster
foreach ($mycl in $mycllist)
{
$myesxlist=$mycl | Get-VMHost
foreach ($myesx in $myesxlist)
    {
    $mylunlist =  Get-VMHost $myesx| Get-ScsiLun
      foreach ($mylun in $mylunlist)
      { 
       write-Host "$myvsphere;$mycl;$($myesx.Name);$($mylun.CanonicalName);$($mylun.MultipathPolicy);$($mylun.CapacityGB) "
   $sw.WriteLine( "$myvsphere;$mycl;$($myesx.Name);$($mylun.CanonicalName);$($mylun.MultipathPolicy);$($mylun.CapacityGB)")

     }
    }


}
    
   

$sw.Dispose()
$fs.Dispose()
#Disconnect-VIServer -Server *  -Force
}


####################################################
function Get-LAN($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
Connect-VIServer -ErrorAction Stop -Server $myvsphere   -User $myuser  -Password $mypasswd

Get-VDPortgroup | select @{Name='vcenter';Expression={$myvsphere}},  Name , VlanConfiguration  ,@{Name='Type';Expression={"VDS"}} | Export-Csv -Path $mydbfile -NoTypeInformation 
Get-VirtualPortGroup | Select  @{Name='vcenter';Expression={$myvsphere}}, Name , VLanId, @{Name='Type';Expression={"STANDARD"}}   | Export-Csv -Path $mydbfile -Append -Force -NoTypeInformation
Disconnect-VIServer -Server *  -Force -confirm:$false
}


####################################################
function Get-PowerOffDate($myvsphere, $myuser ,$mypasswd,$mydbfile)
####################################################
{
$ExportFilePath = $mydbfile
$VC = Connect-VIServer -ErrorAction Stop -Server $myvsphere   -User $myuser  -Password $mypasswd

$Report = @()
$VMs = get-vm |Where-object {$_.powerstate -eq "poweredoff"}
$Datastores = Get-Datastore | select Name, Id
$VMHosts = Get-VMHost | select Name, Parent
### Get powered off event time:
Get-VIEvent -Entity $VMs -MaxSamples ([int]::MaxValue) | 
where {$_ -is [VMware.Vim.VmPoweredOffEvent]} |
Group-Object -Property {$_.Vm.Name} | %{
  $lastPO = $_.Group | Sort-Object -Property CreatedTime -Descending | Select -First 1
  $vm = Get-VIObjectByVIView -MORef $lastPO.VM.VM
  $report += New-Object PSObject -Property @{
    VMName = $vm.Name
    Powerstate = $vm.Powerstate
    OS = $vm.Guest.OSFullName
    IPAddress = $vm.Guest.IPAddress[0]
    ToolsStatus = $VMView.Guest.ToolsStatus
    Host = $vm.host.name
    Cluster = $vm.host.Parent.Name
    Datastore = ($Datastores | where {$_.ID -match (($vmview.Datastore | Select -First 1) | Select Value).Value} | Select Name).Name
    NumCPU = $vm.NumCPU
    MemMb = [Math]::Round(($vm.MemoryMB),2)
    DiskGb = [Math]::Round((($vm.HardDisks | Measure-Object -Property CapacityKB -Sum).Sum * 1KB / 1GB),2)
    PowerOFF = $lastPO.CreatedTime
    Note = $vm.Notes  }
}

$Report = $Report | Sort-Object VMName

if ($Report) {
  $report | Export-Csv $ExportFilePath -NoTypeInformation}
else{
  "No PoweredOff events found"
}

$VC = Disconnect-VIServer -Server *  -Force -confirm:$false
}



#add-pssnapin VMware.VimAutomation.Core




#$myuser="SPFRDEFVMON"
#$mypasswd='$PFRd3fVm0n'
#$myvcenterlist=@('10.15.194.46')
#$myvcenterlist=@('10.15.194.46')
#$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
#foreach ( $myvcenter in $myvcenterlist )
# {
#$mydbfile = "C:\Local\DB\ESX_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-ESX -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\VM_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-MGRVM  -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\DS_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-DS -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\SAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-SAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#}

#$myuser="MAIN\SPFRDEFCAPACITYTOOL"
#$mypasswd='$PFRd3fc@p@c!TY00L'
#$myvcenterlist=@('FRMSCLI-VC01','FRMSSTD-VC01','DERMBER-VC01')
#$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
#foreach ( $myvcenter in $myvcenterlist )
# {
#$mydbfile = "C:\Local\DB\ESX_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-myESX -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\VM_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-MGRVM  -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\DS_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-DS -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\SAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-SAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\LAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-LAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile

#$mydbfile = "C:\Local\DB\PowerOff_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-PowerOffDate -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile

#}

#$myuser="SPFREPCAPACITYTOOL"
#$mypasswd='Sjx;bv,F>u(;4LwS#\d!'
#$myvcenterlist=@('FREPDEF-APVCH01','FREPPAU-APVCH01')#,'FREPPAU-APVCP01')
#$myvcenterlist=@('FREPPAU-APVCP01')
#$myvcenterlist=@('10.15.194.46')
#$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
#foreach ( $myvcenter in $myvcenterlist )
# {
# $mydbfile = "C:\Local\DB\ESX_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-myESX -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\VM_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-MGRVM  -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\DS_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-DS -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\SAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-SAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\LAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-LAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\PowerOff_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-PowerOffDate -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#}

#$myuser="SPFRGPCAPACITYTOOL"
#$mypasswd='-z*sa4e+RUxaB'
#$myvcenterlist=@('FRGPDEF-VI2')
#$myvcenterlist=@('10.15.194.46')
#$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
#foreach ( $myvcenter in $myvcenterlist )
 #{
 #$mydbfile = "C:\Local\DB\ESX_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-myESX -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\VM_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-MGRVM  -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\DS_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-DS -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\SAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-SAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile#
#$mydbfile = "C:\Local\DB\LAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-LAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\PowerOff_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-PowerOffDate -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#}


$myuser="SPBETPCAPACITYTOOL"
$mypasswd='3j&HkE99G!V-]v<Lrpa+'


$myvcenterlist=@('BERCBRU-VI91','BERCBRU-VI92','BERCBRU-VI94')
#$myvcenterlist=@('10.15.194.46')
$mytimestamp=$(Get-Date  -f yyyyMMddHHmmss )
foreach ( $myvcenter in $myvcenterlist )
{
 #$mydbfile = "C:\Local\DB\ESX_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-myESX -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
$mydbfile = "C:\Local\DB\VM_"+$myvcenter+"_"+$mytimestamp+".csv"
Get-MGRVM  -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\DS_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-DS -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\SAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-SAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\LAN_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-LAN -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
#$mydbfile = "C:\Local\DB\PowerOff_"+$myvcenter+"_"+$mytimestamp+".csv"
#Get-PowerOffDate -myvsphere $myvcenter -myuser $myuser -mypasswd $mypasswd -mydbfile $mydbfile
}