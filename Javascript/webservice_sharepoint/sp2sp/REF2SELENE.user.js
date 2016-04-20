// ==UserScript==
// @name         REF2SELENE
// @namespace    http:///
// @version      0.1
// @description  CLOSE SHAREPOINT WHEN OMEGA
// @author       Mathieu GRAVIL
// @match        http://portal1.rm.corp.local/dsi/community/urbanistes/Lists/Portfoli_expertise/EditForm.aspx*
// @require http://code.jquery.com/jquery-2.1.4.min.js
// @grant        none
// ==/UserScript==

//form=document.getElementsByClassName('ms-dtinput');
//input= document.getElementsByTagName("input");
//form=document.getElementsByClassName('ms-formbody');
//console.log(input);
//sele =  document.getElementsByTagName("option");
//console.log(sele);<input name="ctl00$m$g_47e0bc13_eb23_4c7d_8a02_f8bfce832106$ctl00$ctl04$ctl01$ctl00$ctl00$ctl04$ctl00$ctl00$TextField" type="text" 

function UO_2_workload(Affectation)
{
    if (Affectation == 'ATOS')
    {
        var WORKLOAD = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl13_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value.split("=")[1];
        var NB_UO = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl14_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value ;
        var workload = Number(NB_UO) * Number(WORKLOAD.replace(",",".")) ;
        document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl10_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value = (workload.toString()).replace(".",",")  ; 
    }
}

function get_button(Affectation)
{
    if (Affectation == 'ATOS')
    {
        document.getElementById('CP').style.visibility = 'visible';
    }
    else
    {
        document.getElementById('CP').style.visibility = 'hidden';
    }
}

$('body').append('<input type="button" value="CREATE SELENE REQUEST" id="CP">');
$("#CP").css("position", "fixed").css("top", 0).css("left", 0);
$('#CP').click(function(){ 
        var Title=document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl01_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value.replace(/</g, '&' + 'amp;' + 'lt;');
        var Creation_date= document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl00_ctl00_ctl00_ctl04_ctl00_ctl00_DateTimeField_ctl00").value ;
        var Requester = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl02_ctl00_ctl00_ctl04_ctl00_ctl00_HiddenUserFieldValue").value.split("#")[1] ;
        var Delivery_date = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl04_ctl00_ctl00_ctl04_ctl00_ctl00_DateTimeField_DateTimeFieldDate").value;
        var Area= document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl05_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value;
        var Description= document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl07_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value.replace(/</g, '&' + 'amp;' + 'lt;') ;
        var OMEGA_ticket = document.getElementById ("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl09_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value ;
        var UO = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl13_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value.split("=")[0];
        var ID = window.location.search.substring(1).split("&")[0].split("=")[1];
        var NB_UO = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl14_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value ;
        var url_called='http://ipprdcs.rm.corp.local/cloud/DDS_SELENE_V1/Documents%20partages/MG_scripts/create.html?title='+Title+'&Delivery_date='+Delivery_date+'&Requester='+Requester+'&area='+Area+'&UO='+UO+'&NB_UO='+NB_UO+'&ID='+ID+'&desc='+Description ;
        var wnd = window.open(url_called);
   document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl03_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value="WORK_IN_PROGRESS"; 
        });
document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl13_ctl00_ctl00_ctl04_ctl00_DropDownChoice").addEventListener("change",function() {UO_2_workload(document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl08_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value );
} );
document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl08_ctl00_ctl00_ctl04_ctl00_DropDownChoice") .addEventListener("change",function() {get_button(document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl08_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value);});                                                                                                                                               
document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl14_ctl00_ctl00_ctl04_ctl00_ctl00_TextField") .addEventListener("change",function() {UO_2_workload(document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl08_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value);;});                                                                                                                                               

var Affectation = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl08_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value ;
var UO = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl13_ctl00_ctl00_ctl04_ctl00_DropDownChoice").value.split("=")[0];
var WORKLOAD = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl10_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value ;
var NB_UO = document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl14_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value ;
if (NB_UO == '' )
{
  document.getElementById("ctl00_m_g_47e0bc13_eb23_4c7d_8a02_f8bfce832106_ctl00_ctl04_ctl14_ctl00_ctl00_ctl04_ctl00_ctl00_TextField").value = 1 ;
    NB_UO = 1 ;
}


get_button(Affectation);
if (( WORKLOAD == 0 ) &&  ( UO != 'NONE') )
{
UO_2_workload(Affectation);
}
/*
console.log(Title);
console.log(Description);
console.log(Delivery_date);
console.log(Area);
console.log(Requester);
console.log(Affectation);
console.log(OMEGA_ticket);
console.log(UO);
console.log(ID);
console.log(WORKLOAD);

*/


//alert(ticket_nb);