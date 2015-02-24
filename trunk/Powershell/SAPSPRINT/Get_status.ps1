param([Int32]$days=1, [String]$SID="", [Int32]$SPNB="")

$file_name="C:\Users\AJ0242224\SAPSPRINT_STATUS.html"

Remove-Item ($file_name)
$stream = [System.IO.StreamWriter] $file_name
$servername=hostname
$current_time = get-date 
$from_time = (get-date).AddDays( - $days)
$i= 0




function mystatus ($thisstatus) {
    switch ($thisstatus) {
         "" { $mytext = "OK" }
         "1" { $mytext = "Other" }
         "2" { $mytext = "No Error" }
         "3" { $mytext = "Low Paper" }
         "4" { $mytext = "No Paper" }
         "5" { $mytext = "Low Toner" }
         "6" { $mytext = "No Toner" }
         "7" { $mytext = "Door Open" }
         "8" { $mytext = "Jammed" }
         "9" { $mytext = "Service Requested" }
         "10" { $mytext = "Output Bin Full" }
         "11" { $mytext = "Paper Problem" }
         "12" { $mytext = "Cannot Print Page" }
         "13" { $mytext = "User Intervention Required" }
         "14" { $mytext = "Out of Memory" }
         "15" { $mytext = "Server Unknown" }
         default {$mytext = "KO" }
    }
return ($mytext)
}

function printer_KO_state($hostname)
{
$prnstats = get-wmiobject -class "Win32_Printer" -namespace "root\CIMV2" -computername $hostname | select Name,Status,   DetectedErrorState | Where-Object { $_.DetectedErrorState -ne 0 }
return $prnstats
}

function nb_jobs($printer_name)
{
$printer_nbjob=Get-WMIObject Win32_PerfFormattedData_Spooler_PrintQueue | Select Name, jobs | Where-Object Name -eq $printer_name
 return $printer_nbjob
  }


$stream.WriteLine("<html><head><title>SAPSPRINT STATUS"+ $hostname +" at "+$current_time.ToString("dd/MM/yyyy HH:mm:ss")+" </title> 
<style>
H1
{
text-align : center
}
p.KO
{
background-color : red
}
p.Error
{
background-color : red
}
p.Degraded
{
background-color : Orange
}
p.OK
{
background-color : green
}



</style>

</head><body>")

$stream.WriteLine("<H1>SAPSPRINT STATUS </H1> <br> <H1> "+$servername +" from "+ $from_time.ToString("dd/MM/yyyy HH:mm:ss") +" to "+ $current_time.ToString("dd/MM/yyyy HH:mm:ss") +"</H1>")

$stream.WriteLine("<table cellpadding=2 cellspacing=0 border=3>")
$stream.WriteLine("<tr><td><b>Printer</b></td><td><b>Printer status</b></td><td><b>Information Error</b></td><td><b>NB jobs</b></td></tr>")
   

$printer_KO = printer_KO_state($servername)| select Name, Status, @{Name="DetectedErrorState";Expression={mystatus $_.DetectedErrorState }}
$all_jobs_win = get-wmiobject "Win32_PrintJob" 

foreach ($p in $printer_KO )
 {
 $nb_jobs=nb_jobs($p.name)
  $stream.WriteLine("<tr><td><p class="+$p.status+">"+$p.Name+"</p></td><td>"+$p.Status+"</td><td>"+$p.DetectedErrorState+"</td><td>"+$nb_jobs.jobs+"</td></tr>")
  $stream.Flush()
 }  

 $stream.WriteLine("</table><br>")


$stream.WriteLine("<br>SID : "+$SID+"<br> Spool Number:"+$SPNB)

$stream.WriteLine("<table cellpadding=2 cellspacing=0 border=3>")
$stream.WriteLine("<tr><td><b>SID</b></td><td><b>Spool</b></td><td><b>Printer</b></td><td><b>Printer status</b></td> <td><b>Timestamp SAPSprint</b></td><td><b>SAPSPRINT</b></td><td><b>WINDOWS</b></td></tr></b>")
   

Get-ChildItem -Path  E:\SAP\SAPSprint\Logs  sapwin*$SID*$SPNB* | Where-Object { $_.LastWriteTime -gt $from_time } | ForEach-Object -process {
$i=$i+1
$sp=$_.name.Split("_")[1].substring(3); 

$sid =  $_.name.Split("_")[1].replace($sp,"");

$printer_line=Get-Content  $_.Fullname | Select-String  "printer"| Select-String  $sp | Select-Object -Last 1  ; 

$printer_temp= $printer_line -match "printer (.*?) with"
$printer_temp2=$Matches[1]
$printer = $printer_temp2.Trim()
$printer_criteria="*"+$printer+"*"


$rc_printer = $printer_KO | Where-Object { $_.Name -like $printer_criteria } |Select  DetectedErrorState 
$printer_state = mystatus($rc_printer)
if ( $printer_state -eq "OK") { $printer_class = "OK"} else { $printer_class = "KO" }

$rc_line=Get-Content  $_.Fullname | Select-String "return code" | Select-Object -Last 1;  
$rc_time_temp=$rc_line -match "\((.*?)\)"
$rc_time=$Matches[1]
$rc_temp=$rc_line -match "return code (.*?)\."
$rc=$Matches[1]

$link = $_.Fullname

if ( $rc -eq 0 ) {
$rc="OK"
$criteria="*"+$sid+"*"+$sp+"*"
$job_win = $all_jobs_win | where { $_.Document -like  $criteria }
$rc_win=$job_win.status
if ( -not $rc_win ){$rc_win ="OK"}
else{$rc_win ="KO"}
}
else{
$rc="KO"
$rc_win = "KO"
}


$stream.WriteLine("<tr><td>"+$sid+"</td><td><a href="+$link+">"+$sp+"</a></td><td>"+$printer+"</td><td><p class="+$printer_class+">"+$printer_state+"</p></td><td>"+$rc_time+"</td><td><p class="+$rc+">"+$rc+"</p></td><td><p class="+$rc_win+">"+$rc_win+"</p<</td></tr>")
$stream.Flush()
}
$stream.WriteLine("</table></body>")
$End_time = get-date
$duration=NEW-TIMESPAN –Start $current_time –End $End_Time
 $stream.WriteLine("<br>Duration of script execution :"+$duration+"<br>NB spool:"+$i )
 $stream.WriteLine("</body></html>")
$stream.Flush()
$stream.close()
