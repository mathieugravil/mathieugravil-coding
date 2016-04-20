// ==UserScript==
// @name         CLOSE_ARCH_TICKET_FROM_SELENE
// @namespace    http:///
// @version      0.1
// @description  CLOSE SHAREPOINT WHEN OMEGA
// @author       Mathieu GRAVIL
// @match        http*://ipprdcs.rm.corp.local/cloud/DDS_SELENE_V1/Lists/Liste_Demande_de_service/EditForm.aspx?ID*
// @require http://code.jquery.com/jquery-2.1.4.min.js
// @grant        none
// ==/UserScript==


var ticket_nb =  window.location.search.substring(1).split("&")[0].split("=")[1];

//alert(ticket_nb);

$('body').append('<input type="button" value="Close ARCH Ticket" id="CP">')
$("#CP").css("position", "fixed").css("top", 0).css("left", 0);

$('#CP').click(function(){ 
//alert('on est dans la fonction '+ticket_nb );
var url_called='http://portal1.rm.corp.local/dsi/community/urbanistes/Expert/omega_scripts/change.html?ticket_nb='+ticket_nb
var wnd = window.open(url_called);
    // wnd.close();   
});