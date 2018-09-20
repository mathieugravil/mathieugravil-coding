$myfile = "C:\Local\OFFSHORES.txt"
ForEach( $line in Get-Content $myfile) {
$ptr_state = ""
$name = ""
$name2 = ""
$toto = nslookup $line
$name=($toto[3]).split(':')[1].ToLower().replace(' ','').ToString()
$ip=($toto[4]).split(':')[1].replace(' ','').ToString()
$tutu = nslookup $ip
$name2=($tutu[3]).split(':')[1].ToLower().replace(' ','').ToString()
$ip2=($tutu[4]).split(':')[1].replace(' ','').ToString()
if ($name2 -Contains($name) ){ 
$ptr_sate = "OK"
 }
else
{
$ptr_state = $name2
}
Write-Output $name";"$ip";"$ptr_state
}