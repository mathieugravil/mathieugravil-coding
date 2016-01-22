



/*
$(document).ready(function() {	

var d = new Date();
var yyyy = d.getFullYear();
var mm = d.getMonth()+1 - 3
var yyyymm;
if (mm <=0)
{
	yyyy=yyyy-1
	mm=12+mm
	if(mm<10)
	{
	yyyymm=String(yyyy)+"0"+String(mm)
	}
	else
	{
	yyyymm=String(yyyy)+String(mm)		
	}
}
else
{
	yyyymm=String(yyyy)+"0"+String(mm)
}

GetResult(yyyymm)	

	});
	*/




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



function GetFromPeople(name,field){
	var dep;
	$().SPServices({
  operation: "SearchPrincipals",
    async: false,
  searchText: name,
  maxResults: 1,
  SPPrincipalType: "SPPrincipalType.User",
  completefunc: function (xData, Status) {
	//  console.log(xData.responseText)
	   $(xData.responseXML).SPFilterNode(field).each(function() {
		dep=$(this).text()		
	});
  }
});
		if (dep == null)
		{
			dep="UNDEFINED"
		}
	return dep ; 
}
	
	

function GetResult(from){
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
var list_projects=[];
var list_cat_proj=[];
var projects_day=[];
var cat_proj_day=[];
var list_requester=[];
var list_requester_name=[];
var list_dep=[];
var requester=[];
var requester_day=[];
var KPI_nb=0;
var KPI_day=0;



list_area=GetChoice("CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9","Area");
list_affetation=GetChoice("CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9","Affectation");
list_status = GetChoice("CC1BFE7F-07A5-458E-8163-22A8AEDD6CC9","STATUS");



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
    completefunc: function (xData, Status) {
	//	console.log(xData.responseText)
      $(xData.responseXML).SPFilterNode("z:row").each(function() {
		  //=================== AREA =========================//
		   var period=$(this).attr("ows_Deliver_MONTH").split("#")[1].split(".")[0]
if(period>=from){		
	if ($(this).attr("ows_STATUS")!="CLOSED" && $(this).attr("ows_STATUS")!="REJECTED")
//		if ( $(this).attr("ows_STATUS")!="REJECTED")
		{
			  KPI_day=KPI_day+parseFloat($(this).attr("ows_CHARGE"))
		      KPI_nb=KPI_nb+1
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
	   	  //=================== END CAL =========================//
		 //=================== PROJECTS =========================//
		 	 var cat=$(this).attr("ows_Code_x0020_projet").split(" ")[0]
			 if (list_cat_proj.indexOf(cat)== -1 )
			 {
				list_cat_proj.push(cat)
				cat_proj_day[list_cat_proj.indexOf(cat)]=parseFloat($(this).attr("ows_CHARGE"))
			 }
			 else
			 {
				cat_proj_day[list_cat_proj.indexOf(cat)]=cat_proj_day[list_cat_proj.indexOf(cat)]+parseFloat($(this).attr("ows_CHARGE"))
			 }
//	 list_cat_proj.push(cat)
// cat_proj_day=[];
 if (list_projects.indexOf($(this).attr("ows_Code_x0020_projet"))== -1 )
 {

	 list_projects.push($(this).attr("ows_Code_x0020_projet"))
	  projects_day[list_projects.indexOf($(this).attr("ows_Code_x0020_projet"))]=parseFloat($(this).attr("ows_CHARGE"))
 }
 else
 {
	 projects_day[list_projects.indexOf($(this).attr("ows_Code_x0020_projet"))]=projects_day[list_projects.indexOf($(this).attr("ows_Code_x0020_projet"))]+parseFloat($(this).attr("ows_CHARGE")) 	 
 }
  //=================== END PROJECTS =========================//
    //=================== REQUESTER=========================//
	var requester_name=$(this).attr("ows_Requester").split("#")[1]
	if(list_requester.indexOf(requester_name)== -1 )
	{
		list_requester.push(requester_name)
		requester[list_requester.indexOf(requester_name)]=1
		requester_day[list_requester.indexOf(requester_name)]= parseFloat($(this).attr("ows_CHARGE"))
	}
	else
	{
		requester[list_requester.indexOf(requester_name)]=requester[list_requester.indexOf(requester_name)]+1
		requester_day[list_requester.indexOf(requester_name)]= requester_day[list_requester.indexOf(requester_name)]+parseFloat($(this).attr("ows_CHARGE"))
	}
	//=================== END REQUESTER =========================//
	  }  
}
      });	  
    }
	  });
	  list_period.sort()
	  //console.log(cal_area_day[0].sort())
	 for (var k = 0 ; k <list_requester.length ; k++)
	 {
		 list_dep.push(GetFromPeople(list_requester[k],'Department'))
		 list_requester_name.push(GetFromPeople(list_requester[k],'DisplayName'))
	 }		 	  
 Mydashboard('Statistics on tickets', list_area,area,area_day,'Distribution by area for non closed tickets',list_status,status,status_day,'Distribution by STATUS',list_period,cal_area,cal_area_day,"Planning by area",list_affetation,cal_aff,cal_aff_day,"Planning per resource",list_cat_proj
,cat_proj_day, list_projects,projects_day,'Distribution projects in manday',KPI_nb,KPI_day,list_dep,list_requester_name,requester,requester_day,'Requesters');


}



