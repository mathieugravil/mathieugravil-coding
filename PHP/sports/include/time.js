function calcul_duration_total(myForm) {
var form_in_zone = Number(hms2sec(String(myForm.in_zone.value)));
var form_below = Number(hms2sec(String(myForm.below.value)));
var form_above = Number(hms2sec(String(myForm.above.value)));

//alert(myForm.below.value + " "+myForm.in_zone.value + " "+myForm.above.value + " " )
//add the variables and update the total field value with the result.
//alert(Number(form_below + form_in_zone + form_above )  );
var total_sec = Number(form_below + form_in_zone + form_above ) ; 
myForm.duration.value = sec2hms(total_sec )  ;

}
function hms2sec (hms) {
	var dur = hms.split(":");
	var seconds = Number(dur[2]);
	seconds += Number(dur[0] * 3600);
	seconds += Number(dur[1] * 60);
//	alert("secondes" + seconds);
	return seconds;
}

//97211

function sec2hms(sec) {
//alert("secondes" + sec );
var hours = parseInt( sec / 3600 ) ;
//alert("H:" +  hours);
//27
var minutes = parseInt( (sec - 3600 * hours )  / 60 ) ;
//alert("M:" +  minutes);
//0
var seconds = parseInt( (sec - 3600 * hours - 60 * minutes)   ) ;
//
//alert ("s:" + seconds ) ;
//alert(hours + ":" + minutes + ":" + seconds )
return hours + ":" + minutes + ":" + seconds ;
}

function calcul_vmoy(myForm){
var form_duration = Number(hms2sec(String(myForm.duration.value)));
var form_distance = Number(myForm.dist.value);
if (form_duration != 0){
myForm.vmoy.value = Number( 3600*form_distance /(1000*form_duration));
}
}
