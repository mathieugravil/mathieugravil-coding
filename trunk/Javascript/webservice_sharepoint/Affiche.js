function GetArea(list){
	var area=[]
	$().SPServices({
    operation: "GetList",
    async: false,
    listName: list,
	completefunc: function (xData, Status) {
	//	console.log(xData.responseText)
      $(xData.responseXML).find("Field[DisplayName='Area'] CHOICE").each(function() {
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
var cal_area_day=[];

var list_area=[];

list_area=GetArea("CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9");
console.log(list_area.indexOf("SA"))


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
			 if (area_day[$(this).attr("ows_Domaine")] != null)
		  {
			area_day[$(this).attr("ows_Domaine")] = area_day[$(this).attr("ows_Domaine")] +  parseFloat($(this).attr("ows_CHARGE"))
			//console.log(area_day[$(this).attr("ows_Domaine")])
		  }
		  else
		  {
			area_day[$(this).attr("ows_Domaine")] =  parseFloat($(this).attr("ows_CHARGE"))  
			//console.log(area_day[$(this).attr("ows_Domaine")])
		  }  
		  if (area[$(this).attr("ows_Domaine")] != null)
		  {
			area[$(this).attr("ows_Domaine")] = area[$(this).attr("ows_Domaine")] + 1  
			//console.log(area[$(this).attr("ows_Domaine")])
		  }
		  else
		  {
			 area[$(this).attr("ows_Domaine")] = 1 
		//	 console.log(area[$(this).attr("ows_Domaine")])
		  }
		  }
		  //=================== END AREA =========================//
		  //=================== STATUS =========================//
		  if (status[$(this).attr("ows_STATUS")] != null)
		  {
			status[$(this).attr("ows_STATUS")] = status[$(this).attr("ows_STATUS")] + 1  
	//		console.log(status[ $(this).attr("ows_STATUS")])
		  }
		  else
		  {
			 status[$(this).attr("ows_STATUS")] = 1 
//			 console.log(status[ $(this).attr("ows_STATUS")])
		  }
		  if (status_day[$(this).attr("ows_STATUS")] != null)
		  {
			status_day[$(this).attr("ows_STATUS")] = status_day[$(this).attr("ows_STATUS")] +  parseFloat($(this).attr("ows_CHARGE"))
		  }
		  else
		  {
			status_day[$(this).attr("ows_STATUS")] =  parseFloat($(this).attr("ows_CHARGE"))  
		  }
		 //=================== END STATUS =========================//
	  //=================== CAL =========================//
	  if($(this).attr("ows_STATUS")!="REJECTED")
	  {
	  // console.log(parseInt($(this).attr("ows_Deliver_MONTH").split("#")[1].split(".")[0]))
	   var period=parseInt($(this).attr("ows_Deliver_MONTH").split("#")[1].split(".")[0])
	   /*
	   //        <z:row ows_OMEGA_TICKET="NONE" ows_Deliver_MONTH="float;#201410.000000000" ows_MetaInfo="5;#" ows__ModerationStatus="0" ows__Le
	   */
	   
	   if(cal_area_day[period] != null)
	   {
		     cal_area_day[period] =   cal_area_day[period]+ parseFloat($(this).attr("ows_CHARGE"))
	   }
	   else
	   {
		  cal_area_day[period] =  parseFloat($(this).attr("ows_CHARGE")) 
	   }
	  }
	   
	  //=================== END CAL =========================//
      });
    }
	  });
	  Mydashboard('Statistics on tickets', area,'Distribution by area for non closed tickets',status,'Distribution by STATUS',area_day,'Distribution by area for non closed tickets',status_day,'Distribution by STATUS',cal_area_day,"Planning");
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



