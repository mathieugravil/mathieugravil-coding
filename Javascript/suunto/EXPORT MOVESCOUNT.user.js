// ==UserScript==
// @name         EXPORT MOVESCOUNT
// @namespace    http://your.homepage/
// @version      0.1
// @description  enter something useful
// @author       You
// @require http://code.jquery.com/jquery-2.1.4.min.js
// @match       http*://*.movescount.com/moves/move*
// @grant        none
// ==/UserScript==


$('body').append('<input type="button" value="Sync" id="CP">');
$("#CP").css("position", "fixed").css("top", 0).css("left", 0);


function convert_activityID(id){
switch (id)
{
   case 4: //velo
        return 1;
   case 16: //escalade
        return 2;
   case 6: //natation
        return 3;
   case 3: //footing
        return 4;
   case 12: //rando
        return 5;
   case 78: //ski rando
        return 6;
    case 22://skating
        return 7;
    case 20: //ski alpin
        return 8;
    case 71: //musculation
        return 10;
   default: 
       alert('Default case');
}
}



function getTitleContent() { 
   var metas = document.getElementsByTagName('meta'); 

   for (i=0; i<metas.length; i++) { 
      if (metas[i].getAttribute("name") == "description") { 
         return metas[i].getAttribute("content"); 
      } 
   } 

    return "";
} 
//alert('test');
//console.log('test console');

//var zone1 = document.getElementsByTagName('timeInZone1');
console.log(suunto.move.moveData);
//console.log(suunto.move.moveData.timeInZone1);

$('#CP').click(function(){ 
var action = 'insert';


var Title = getTitleContent();
//console.log(Title);
var ddate = Title.split("-")[0];
var day = ddate.split(".")[0];
var month= ddate.split(".")[1];
var year = ddate.split(".")[2].trim();
ddate = year+"-"+month+"-"+day;
var duration =  suunto.move.moveData.duration;
    //Title.split("-")[1].split("h")[0];
var hdur = Math.floor(duration/3600000);
var mindur = Math.floor((duration - 3600000 * hdur)/60000);
var secdur =  Math.round((duration - 60000*mindur - 3600000 * hdur)/1000);
var dur=hdur+':'+mindur+':'+secdur;
var dist= suunto.move.moveData.distance;
var above= suunto.move.moveData.timeInZone3+suunto.move.moveData.timeInZone4+suunto.move.moveData.timeInZone5;
if (typeof suunto.move.moveData.timeInZone3 == 'undefined'){
above=0;
}
var habove = Math.floor(above/3600);
var minabove = Math.floor((above - 3600 * habove)/60) ;
var secabove = above - 60*minabove - 3600 * habove;
var abo=habove+':'+minabove+':'+secabove;

var below = suunto.move.moveData.timeInZone1;
if (typeof below == 'undefined'){
below=0;
}
var hbelow = Math.floor(below/3600);
var minbelow = Math.floor((below - 3600 * hbelow)/60) ;
var secbelow = below - 60*minbelow - 3600 * hbelow;
var bel=hbelow+':'+minbelow+':'+secbelow;

//var in_zone = suunto.move.moveData.timeInZone2;
var in_zone = Math.round(duration/1000 - above - below);
var hzone = Math.floor(in_zone/3600);
var minzone = Math.floor((in_zone - 3600 * hzone)/60) ;
var seczone = in_zone - 60*minzone - 3600 * hzone;
var inz=hzone+':'+minzone+':'+seczone;

if ( typeof suunto.move.moveData.hrAvg == 'undefined'){
    hrAvg=150;
}else{
    hrAvg=suunto.move.moveData.hrAvg;
}


if ( typeof suunto.move.moveData.hrPeak == 'undefined'){
    hrPeak=150;
}else{
    hrPeak=suunto.move.moveData.hrPeak;
}

if ( typeof suunto.move.moveData.speedAvg == 'undefined'){
    speedAvg=0;
}else{
    speedAvg=suunto.move.moveData.speedAvg;
}

if ( typeof suunto.move.moveData.speedMax == 'undefined'){
    speedMax=speedAvg;
}else{
    speedMax=suunto.move.moveData.speedMax;
}

if ( typeof suunto.move.moveData.ascentAltitude == 'undefined'){
    ascentAltitude=0;
}else{
    ascentAltitude=suunto.move.moveData.ascentAltitude;
}

if ( typeof suunto.move.moveData.calories == 'undefined'){
    calories=0;
}else{
    calories=suunto.move.moveData.calories;
}
if (speedAvg === 0){
    speedAvg = prompt("Please enter speedAvg(km/h)", "3")/3.6;
}
    if (speedMax === 0){
    speedMax = prompt("Please enter speedMax(km/h)", "3")/3.6;
}

var sportid=convert_activityID(suunto.move.moveData.activityID);
if (( dist === 0 ) && ( sportid === 3 )){
    speedMax = prompt("Please enter distance(m)", "4000");
}
var url = '<a href="http://www.movescount.com/moves/move'+suunto.move.moveData.moveID+'">Movescount</a>';

var url_called = 'http://sports-mathieugravil.rhcloud.com/sports/sync.php?seance_name='+Title+'&sport_id='+sportid+'&date='+
    ddate+'&cal='+calories+'&dist='+dist+'&duration='+dur+'&above='+abo+'&below='+bel+'&in_zone='+inz+'&lower='+
    suunto.move.hrZones[0]+'&upper='+suunto.move.hrZones[1]+'&fmoy='+hrAvg+'&fmax='+hrPeak+
    '&vmoy='+3.6*speedAvg+'&vmax='+3.6*speedMax+'&altitude='+ascentAltitude+'&url="'+url+'"&action=insert';
if (confirm('Date:'+ddate+'\n duration:'+dur+'\n urlcalled'+url_called)){
    //$.get("http://mathieugravil:fsdfqsdfgaez@192.168.0.20/cgi-bin/ConfigManApp.com?Id=34&Command=1&Number=0123456789")
var wnd = window.open(url_called);
    
   // wnd.close();
    } else {
    console.log('Do nothing!');
}
var url_called2 = 'http://mathieug1.byethost9.com/sports/sync.php?seance_name='+Title+'&sport_id='+sportid+'&date='+
    ddate+'&cal='+calories+'&dist='+dist+'&duration='+dur+'&above='+abo+'&below='+bel+'&in_zone='+inz+'&lower='+
    suunto.move.hrZones[0]+'&upper='+suunto.move.hrZones[1]+'&fmoy='+hrAvg+'&fmax='+hrPeak+
    '&vmoy='+3.6*speedAvg+'&vmax='+3.6*speedMax+'&altitude='+ascentAltitude+'&url="'+url+'"&action=insert';
if (confirm('Date:'+ddate+'\n duration:'+dur+'\n urlcalled'+url_called2)){
    //$.get("http://mathieugravil:fsdfqsdfgaez@192.168.0.20/cgi-bin/ConfigManApp.com?Id=34&Command=1&Number=0123456789")
var wnd2 = window.open(url_called2);
    
   // wnd.close();
    } else {
    console.log('Do nothing!');
}
});
