/* From Quirks Mode */

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	return window.rfAccessToken;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

$(document).ready(function(){
	$("#webProperties").attr('disabled', 'true');
	$("#profiles").attr('disabled', 'true');

	$('#accounts').change(function() {
		accountID = $(this).find(":selected").val();
		var webPropURL = 'https://www.googleapis.com/analytics/v3/management/accounts/' + accountID + '/webproperties?access_token=' + readCookie('access_token');
		$("#webProperties").attr('disabled', 'true');
		$("#profiles").attr('disabled', 'true');
	  $.ajax({
	  	type: 'GET',
	  	url: webPropURL

	  }).done(function(data){
	  	var webProps = data['items'];
	  	$('#webProperties').append($("<option></option>").text("Select a web property"));
	  	$.each(webProps, function(key, val) {   
	  	     $('#webProperties')
	  	         .append($("<option></option>")
	  	         .attr("value",val.id)
	  	         .text(val.name)); 
	  	});
		$("#webProperties").attr('disabled', 'false');
	  });
	});//accounts change

	$('#webProperties').change(function() {
		propertyID = $(this).find(":selected").val();
		var profilesURL = 'https://www.googleapis.com/analytics/v3/management/accounts/' + accountID + '/webproperties/' + propertyID + '/profiles?access_token=' + readCookie('access_token');
		$("#profiles").attr('disabled', 'true');
	  $.ajax({
	  	type: 'GET',
	  	url: profilesURL

	  }).done(function(data){
		$("#profiles").attr('disabled', 'false');
	  	var profiles = data['items'];
	  	$('#profiles').append($("<option></option>").text("Select a profile"));
	  	$.each(profiles, function(key, val) {   
	  	     $('#profiles')
	  	         .append($("<option></option>")
	  	         .attr("value",val.id)
	  	         .text(val.name)); 
	  	});
	  });
	});//webprops change

	$('#profiles').change(function(){
		profileID = $(this).find(":selected").val();
		$tBody = $('#info tbody');
		$tBody.append("<tr><td> Account ID </td><td>" + accountID + "</td></tr>");
		$tBody.append("<tr><td> Web Property ID </td><td>" + propertyID + "</td></tr>");
		$tBody.append("<tr><td> Profile ID </td><td>" + profileID + "</td></tr>");
	});

});