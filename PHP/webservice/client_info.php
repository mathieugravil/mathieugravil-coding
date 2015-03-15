<?php
if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
    $ip = $_SERVER['HTTP_CLIENT_IP'];
} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
} else {
    $ip = $_SERVER['REMOTE_ADDR'];
}

$token="pk.eyJ1IjoibWF0aGlldWdyYXZpbCIsImEiOiJobnZZZG5BIn0.WtRMrGCzpuD-d3JusE0zqQ";
//echo $ip ;echo"<br>";
//echo $_SERVER['HTTP_CLIENT_IP'];echo"<br>";
//echo $_SERVER['REMOTE_ADDR'];echo"<br>";
//echo $_SERVER['HTTP_X_FORWARDED_FOR'];echo"<br>";
$url_location_of_ip="http://api.hostip.info/get_html.php?ip=".$ip."&position=true";
//echo $url;echo"<br>";
$location = file_get_contents($url_location_of_ip);
echo $location;echo"<br>";
echo"<A HREF=\"http://www.hostip.info\"> <IMG SRC=\"http://api.hostip.info/flag.php?ip=".$ip." ALT=\"IP Address Lookup\"></A>";echo"<br>";

//Latitude: 45.1833 Longitude: 5.7833 IP
$temp=explode(":",$location);
$temp2=explode("L",$temp[3]);
$lat=trim($temp2[0]," \x00..\x1F");
echo "X".$temp2[0]."X";echo"<br>";
$temp2=explode("I", $temp[4]);
$lon=trim($temp2[0]," \x00..\x1F");
$url_precise_location="http://api.tiles.mapbox.com/v4/geocode/mapbox.places/".$lon.",".$lat.".json?access_token=".$token ;
echo $url_precise_location;echo"<br>";
//es/ 5.7833, 45.1833.json?access_
$precise_location=file_get_contents($url_precise_location);
echo htmlspecialchars($precise_location);echo"<br>";

$url_map="http://api.tiles.mapbox.com/v4/mapbox.comic/".$lon.",".$lat.",14/500x300.png?access_token=".$token ;
 
echo"<img src=".$url_map." alt=map />";


$wsdl = "http://www.webservicex.net/length.asmx?WSDL";


$client = new SoapClient($wsdl);
 
$LengthValue=1;
$fromLengthUnit='Miles';
$toLengthUnit='Kilometers';
 
$res = $client->ChangeLengthUnit(array('LengthValue' => $LengthValue , 'fromLengthUnit' => $fromLengthUnit,'toLengthUnit' => $toLengthUnit ));
//print $res->ChangeLengthUnitResult;
 
$wsdl_weather="http://www.webservicex.net/globalweather.asmx?WSDL";
$client_weather = new SoapClient($wsdl_weather, array('trace' => 1));

$country_code='France';
$city_name='Grenoble';

//$res_city=$client_weather->GetCitiesByCountry(array('CountryName'=>$country_code));
//echo"<br>";
// echo '<br />Reponse SOAP : '.$client_weather->__getLastResponse().'<br />';   
//print $res_city->GetCitiesByCountryResult;
$res_weather = $client_weather->GetWeather(array('CityName' => $city_name , 'CountryName'=>$country_code));
echo"<br>";
//print $res_weather->GetWeatherResult;
 // Affichage des requêtes et réponses SOAP (pour debug)
// echo '<br />Requete SOAP : '.htmlspecialchars($client_weather->__getLastRequest()).'<br />';
$xml_answer=$client_weather->__getLastResponse();
echo '<br />Reponse SOAP : '.$xml_answer.'<br />';
$xml=simplexml_load_string($xml_answer) or die("Error: Cannot create object");
print_r($xml);
    
//phpinfo()
print_r($res_weather)



?>


<!DOCTYPE html>
<html>
<body>

<p>Click the button to get your coordinates.</p>

<button onclick="getLocation()">Try It</button>

<p id="demo"></p>
<div id="mapholder"></div>

<script>
var x = document.getElementById("demo");

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(showPosition);
    } else { 
        x.innerHTML = "Geolocation is not supported by this browser.";
    }
}

function showPosition(position) {
    x.innerHTML = "Latitude: " + position.coords.latitude + 
    "<br>Longitude: " + position.coords.longitude;

var latlon = position.coords.latitude + "," + position.coords.longitude;
    var img_url = "http://maps.googleapis.com/maps/api/staticmap?center="
    +latlon+"&zoom=14&size=400x300&sensor=false";
    document.getElementById("mapholder").innerHTML = "<img src='"+img_url+"'>";	
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            x.innerHTML = "User denied the request for Geolocation."
            break;
        case error.POSITION_UNAVAILABLE:
            x.innerHTML = "Location information is unavailable."
            break;
        case error.TIMEOUT:
            x.innerHTML = "The request to get user location timed out."
            break;
        case error.UNKNOWN_ERROR:
            x.innerHTML = "An unknown error occurred."
            break;
    }
}
</script>

</body>
</html>
