function GetChoice(list,field){
	var area=[]
	$().SPServices({
    operation: "GetList",
    async: false,
    listName: list,
	completefunc: function (xData, Status) {
	//	console.log(xData.responseText)
      $(xData.responseXML).find("Field[DisplayName='"+field+"'] CHOICE").each(function() {
		area.push($(this).text())  ;
	});
    }
	  });    
	  return area ;
}





$(document).ready(function() {

var status=[];
var area=[];
var status_day=[];
var area_day=[];
var cal_area_day=[[]];
var cal_area=[[]]
var cal_aff=[[]]
var cal_aff_day=[[]]


var list_area=[];
var list_affetation=[];
var list_period=[];

list_area=GetChoice("CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9","Area");
list_affetation=GetChoice("CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9","Affectation");
list_status = GetChoice("CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9","STATUS");
//console.log(list_area.indexOf("SA"))
for (var i = 0 ; i<list_area.length; i++){
	area[i]= 0
	area_day[i] = 0
	cal_area_day[i]=[];
	cal_area[i]=[]
}
for (var i = 0 ; i<list_status.length; i++){
	status[i]= 0
	status_day[i] = 0
}

for (var i = 0 ; i<list_affetation.length; i++){
cal_aff[i]=[]
cal_aff_day[i]=[]		
}





$().SPServices({
    operation: "GetListItems",
    async: false,
    listName: "CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9",
	viewName: "A014C29D-C4F5-411E-B9E7-A854A8426BC7",
    //CAMLViewFields: "<ViewFields><FieldRef Name='Domaine'/><FieldRef Name='STATUS'/></ViewFields>",
    completefunc: function (xData, Status) {
      $(xData.responseXML).SPFilterNode("z:row").each(function() {
		  //=================== AREA =========================//
		  if ($(this).attr("ows_STATUS")!="CLOSED" && $(this).attr("ows_STATUS")!="REJECTED")
		  {
			  area_day[list_area.indexOf($(this).attr("ows_Domaine"))]=area_day[list_area.indexOf($(this).attr("ows_Domaine"))]+ parseFloat($(this).attr("ows_CHARGE"))
			  area[list_area.indexOf($(this).attr("ows_Domaine"))]=area[list_area.indexOf($(this).attr("ows_Domaine"))]+ 1
		  }
		  //=================== END AREA =========================//
		  //=================== STATUS =========================//
		  status[list_status.indexOf($(this).attr("ows_STATUS"))]=status[list_status.indexOf($(this).attr("ows_STATUS"))]+1
		  status_day[list_status.indexOf($(this).attr("ows_STATUS"))]=status_day[list_status.indexOf($(this).attr("ows_STATUS"))]+parseFloat($(this).attr("ows_CHARGE"))
		 //=================== END STATUS =========================//
	  //=================== CAL =========================//
	  if($(this).attr("ows_STATUS")!="REJECTED")
	  {
	  // console.log(parseInt($(this).attr("ows_Deliver_MONTH").split("#")[1].split(".")[0]))
	   var period=$(this).attr("ows_Deliver_MONTH").split("#")[1].split(".")[0]
	   /*
	   //        <z:row ows_OMEGA_TICKET="NONE" ows_Deliver_MONTH="float;#201410.000000000" ows_MetaInfo="5;#" ows__ModerationStatus="0" ows__Le
	   */
	   if(list_period.indexOf(period) == -1)
	   {
		   list_period.push(period)
	   }
	   if(cal_area_day[list_area.indexOf($(this).attr("ows_Domaine"))][period] != null)
	   {
		     cal_area_day[list_area.indexOf($(this).attr("ows_Domaine"))][period] =   cal_area_day[list_area.indexOf($(this).attr("ows_Domaine"))][period]+ parseFloat($(this).attr("ows_CHARGE"))
			 cal_area[list_area.indexOf($(this).attr("ows_Domaine"))][period] =   cal_area[list_area.indexOf($(this).attr("ows_Domaine"))][period]+ 1
	   }
	   else
	   {
		  cal_area_day[list_area.indexOf($(this).attr("ows_Domaine"))][period] =  parseFloat($(this).attr("ows_CHARGE")) 
		  cal_area[list_area.indexOf($(this).attr("ows_Domaine"))][period] =  1 
	   }
	    if(cal_aff_day[list_affetation.indexOf($(this).attr("ows_Affetation"))][period] != null)
	   {
		     cal_aff_day[list_affetation.indexOf($(this).attr("ows_Affetation"))][period] =   cal_aff_day[list_affetation.indexOf($(this).attr("ows_Affetation"))][period]+ parseFloat($(this).attr("ows_CHARGE"))
			 cal_aff[list_affetation.indexOf($(this).attr("ows_Affetation"))][period] =   cal_aff[list_affetation.indexOf($(this).attr("ows_Affetation"))][period]+ 1
	   }
	   else
	   {
		  cal_aff_day[list_affetation.indexOf($(this).attr("ows_Affetation"))][period] =  parseFloat($(this).attr("ows_CHARGE")) 
		  cal_aff[list_affetation.indexOf($(this).attr("ows_Affetation"))][period] =  1 
	   }
	   
	   
	   
	  }
	   
	  //=================== END CAL =========================//
      });
    }
	  });
	  list_period.sort()
	  //console.log(cal_area_day[0].sort())
	  
	  Mydashboard('Statistics on tickets', list_area,area,area_day,'Distribution by area for non closed tickets',list_status,status,status_day,'Distribution by STATUS',list_period,cal_area,cal_area_day,"Planning by area",list_affetation,cal_aff,cal_aff_day,"Planning per resource");
	/*  var tableHtml = "<table>"
	  $("#tasksUL").append(tableHtml);
	for(key in area) { 
	var tableHtml = "<tr><td>"+key+"</td><td>"+ area[key]+"</td></tr>"
	$("#tasksUL").append(tableHtml);
	var tableHtml = "</tr></table>"
	$("#tasksUL").append(tableHtml);
	//console.log("key " + key + " has value " + area[key]); 
	} 
	var tableHtml = "<br><br><table>"
$("#tasksUL").append(tableHtml);
	for(key in status) { 
    var tableHtml = "<tr><td>"+key+"</td><td>"+ status[key]+"</td></tr>"
	$("#tasksUL").append(tableHtml);
	var tableHtml = "</tr></table>"
	$("#tasksUL").append(tableHtml);
	//console.log("key " + key + " has value " + status[key]); 	
	} 
	*/
});



