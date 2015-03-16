
function Mydashboard(dashboard_title,label1,nb1,day1,title1,label2,nb2,day2,title2,label3,nb3,day3,title5,label4,nb4,day4,title4,list_cat_proj
,cat_proj_day,label6,day6,title6,KPI_Nb,KPI_Day,list_dep,list_requester,requester,requester_day,Title_Requesters)
{
	
var color=["#00FFFF","#7FFFD4","#000000","#0000FF","#8A2BE2","#A52A2A","#DEB887","#5F9EA0","#7FFF00","#D2691E","#FF7F50","#6495ED","#DC143C","#00FFFF","#00008B","#008B8B","#B8860B","#A9A9A9","#006400","#BDB76B","#8B008B","#556B2F","#FF8C00","#9932CC","#8B0000","#E9967A","#8FBC8F","#483D8B","#2F4F4F","#00CED1","#9400D3","#FF1493","#00BFFF","#696969","#1E90FF","#B22222","#228B22","#FF00FF","#FFD700","#DAA520","#808080","#008000","#ADFF2F","#FF69B4","#CD5C5C","#4B0082","#F0E68C","#7CFC00","#ADD8E6","#F08080","#D3D3D3","#90EE90","#FFB6C1","#FFA07A","#20B2AA","#87CEFA","#778899","#B0C4DE","#00FF00","#32CD32","#FF00FF","#800000","#66CDAA","#0000CD","#BA55D3","#9370DB","#3CB371","#7B68EE","#00FA9A","#48D1CC","#C71585","#191970","#F5FFFA","#FFE4E1","#FFE4B5","#FFDEAD","#000080","#FDF5E6","#808000","#6B8E23","#FFA500","#FF4500","#DA70D6","#EEE8AA","#98FB98","#AFEEEE","#DB7093","#FFEFD5","#FFDAB9","#CD853F","#FFC0CB","#DDA0DD","#B0E0E6","#800080","#663399","#FF0000","#BC8F8F","#4169E1","#8B4513","#FA8072","#F4A460","#2E8B57","#FFF5EE","#A0522D","#C0C0C0","#87CEEB","#6A5ACD","#708090","#00FF7F","#4682B4","#D2B48C","#008080","#D8BFD8","#FF6347","#40E0D0","#EE82EE","#F5DEB3","#9ACD32","#FFFF00"]
randNumMin=0
randNumMax=color.length

//StandaloneDashboard(function(tdb){
	var tdb = new TabbedDashboard();
	
	tdb.setDashboardTitle (dashboard_title);

	// Dashboard 1 
	var db1 = new Dashboard();
    db1.setDashboardTitle(dashboard_title+' NB of tickets');
	
	// Add a chart to the dashboard. This is a simple chart with no customization.
	var chart = new ChartComponent();
	var chart2 = new ChartComponent();

	chart.setCaption(title1);
	chart.setDimensions (6, 6);	
	chart.setLabels (label1);
	chart.setPieValues(nb1);
	db1.addComponent (chart);
	chart2.setCaption(title2);
	chart2.setDimensions (6, 6);	

	chart2.setLabels (label2);
	chart2.setPieValues(nb2);
	db1.addComponent (chart2);


	var chart6 = new ChartComponent();
	chart6.setDimensions (12, 6);	
	chart6.setCaption(title5);
	chart6.setLabels(label3);
	var serie=[]
  
	for (var i = 0 ; i < label1.length; i++ )
	{
		serie[i]=[]
		temp=label3.slice(0)
		while(temp.length >0)
		{
			var x = temp.shift()
			if (nb3[i][x]!= null)
			{
			serie[i].push(nb3[i][x])
			}
			else
			{
				serie[i].push(0)
			}
		}
	
       j=(Math.floor(Math.random() * (randNumMax - randNumMin + 1)) + randNumMin);

       chart6.addSeries (label1[i],label1[i],serie[i],{
       seriesStacked: true,
	   seriesColor: color[j],
        seriesDisplayType: "column"
         });
	}

	db1.addComponent (chart6);




	
 // Dashboard 2
    var db2 = new Dashboard();
    db2.setDashboardTitle(dashboard_title+' Manday');
	// Add a chart to the dashboard. This is a simple chart with no customization.
	var chart3 = new ChartComponent();
	var chart4 = new ChartComponent();


	chart3.setCaption(title1);
	chart3.setDimensions (6, 6);	

	chart3.setLabels (label1);
	//chart.addSeries (values);
	chart3.setPieValues(day1);
	db2.addComponent (chart3);
	
	chart4.setCaption(title2);
	chart4.setDimensions (6, 6);	
	chart4.setLabels (label2);
	//chart2.addSeries (values2);
	chart4.setPieValues(day2);
	db2.addComponent (chart4);
	
	
	var chart5 = new ChartComponent();
	chart5.setDimensions (12, 6);	
	chart5.setCaption(title5);
	//labelt=[ 201409,201410,201411,201412,201501,201502,201503,201504,201505,201506]
	chart5.setLabels(label3);
	var serie=[]
  
	for (var i = 0 ; i < label1.length; i++ )
	{
		serie[i]=[]
		temp=label3.slice(0)
		while(temp.length >0)
		{
			var x = temp.shift()
			if (day3[i][x]!= null)
			{
			serie[i].push(day3[i][x])
			}
			else
			{
				serie[i].push(0)
			}
		}
       j=(Math.floor(Math.random() * (randNumMax - randNumMin + 1)) + randNumMin);
       chart5.addSeries (label1[i],label1[i],serie[i],{
       seriesStacked: true,
	   seriesColor: color[j],
        seriesDisplayType: "column"
         });
	}
	db2.addComponent (chart5);

// Dashboard 3	
var db3 = new Dashboard();
    db3.setDashboardTitle('Resources in days');
	var chart7 = new ChartComponent();
	chart7.setDimensions (12, 6);	
	chart7.setCaption(title4);
	chart7.setLabels(label3);
	var serie=[]
	for (var i = 0 ; i < label4.length; i++ )
	{
		serie[i]=[]
		temp=label3.slice(0)
		while(temp.length >0)
		{
			var x = temp.shift()
			if (day4[i][x]!= null)
			{
			serie[i].push(day4[i][x])
			}
			else
			{
				serie[i].push(0)
			}
		}
	
       j=(Math.floor(Math.random() * (randNumMax - randNumMin + 1)) + randNumMin);
       chart7.addSeries (label4[i].replace("/",""),label4[i],serie[i],{
       seriesStacked: true,
	   seriesColor: color[j],
        seriesDisplayType: "column"
         });
	}	
	db3.addComponent (chart7);

// Dashboard 4
var db4 = new Dashboard();
    db4.setDashboardTitle('Resources in tickets');
	var chart8 = new ChartComponent();
	chart8.setDimensions (12, 6);	
	chart8.setCaption(title4);
	chart8.setLabels(label3);
	var serie=[]
	for (var i = 0 ; i < label4.length; i++ )
	{
		serie[i]=[]
		temp=label3.slice(0)
		while(temp.length >0)
		{
			var x = temp.shift()
			if (nb4[i][x]!= null)
			{
			serie[i].push(nb4[i][x])
			}
			else
			{
				serie[i].push(0)
			}
		}
       j=(Math.floor(Math.random() * (randNumMax - randNumMin + 1)) + randNumMin);
       chart8.addSeries (label4[i].replace("/",""),label4[i],serie[i],{
       seriesStacked: true,
	   seriesColor: color[j],
        seriesDisplayType: "column"
         });
	}
	db4.addComponent (chart8);

	// Dashboard 5
var db5 = new Dashboard();
    db5.setDashboardTitle(title6);	
	var chart9 = new ChartComponent();

	chart9.setCaption(title6);
	chart9.setDimensions (6, 6);	

	//chart9.setPieValues(day6);
	chart9.setLabels (list_cat_proj);
//	chart9.setPieValues(cat_proj_day);
chart9.addSeries("CAT_PROJ","CAT_PROJ",cat_proj_day);
	 chart9.addDrillStep(function(done,obj) {
        console.log(obj);
		var m=0
	var labelp=[]
	var datap=[]
	for (var z=0;z<label6.length;z++)
	{
		if(label6[z].split(" ")[0]==obj.label)
		{
			labelp.push(label6[z])
			datap.push(day6[z])
		}
	}
	chart9.setLabels (labelp)
    chart9.addSeries('code','code',datap)
//chart9.setPieValues(datap)
    done (); // This is required
	
    });
	db5.addComponent (chart9);
	
		// Dashboard 6
var db6 = new Dashboard();
    db6.setDashboardTitle('Non closed tickets');	
	var gaugenb = new GaugeComponent ();
	gaugenb.setLimits(0, KPI_Nb+10);
	gaugenb.setCaption('Number of non closed tickets');
	gaugenb.setValue(KPI_Nb);
db6.addComponent(gaugenb);

var gaugeday = new GaugeComponent ();
gaugeday.setLimits(0, KPI_Day+10);
gaugeday.setCaption('Mandays of non closed tickets');
	gaugeday.setValue(KPI_Day);
db6.addComponent(gaugeday);

		// Dashboard 7
	var db7 = new Dashboard();
    db7.setDashboardTitle(Title_Requesters);	
//	list_dep,list_requester,requester,requester_day,
var dep=[];
var dep_day=[];
var list_uniq_dep=[]
for (var i =0 ; i < list_dep.length; i ++)
{
	if (dep[list_uniq_dep.indexOf(list_dep[i])] == null)
	{
		list_uniq_dep.push(list_dep[i])
		dep[list_uniq_dep.indexOf(list_dep[i])]=requester[i]
		dep_day[list_uniq_dep.indexOf(list_dep[i])]=requester_day[i]
	}
else
{
		dep[list_uniq_dep.indexOf(list_dep[i])]=dep[list_uniq_dep.indexOf(list_dep[i])]+requester[i]
		dep_day[list_uniq_dep.indexOf(list_dep[i])]=dep_day[list_uniq_dep.indexOf(list_dep[i])]+requester_day[i]
}	

}

var depchart_day = new ChartComponent ();
depchart_day.setCaption(Title_Requesters+" day");
depchart_day.setLabels(list_uniq_dep)
depchart_day.addSeries("dep","Department",dep_day)
db7.addComponent(depchart_day)
depchart_day.addDrillStep(function(done,params){
	var m=0
	var label=[]
	var data=[]
	while(list_dep.indexOf(params.label,m) != -1)
	{
		label.push(list_requester[list_dep.indexOf(params.label,m)])
		data.push(requester_day[list_dep.indexOf(params.label,m)])
		m=list_dep.indexOf(params.label,m)+1
	}
	depchart_day.setLabels (label)
    depchart_day.addSeries ("requester", "Requester", data)

    done (); // This is required
	
})
var depchart = new ChartComponent ();
depchart.setCaption(Title_Requesters+" nb");
depchart.setLabels(list_uniq_dep)
depchart.addSeries("dep","Department",dep)
db7.addComponent(depchart)
depchart.addDrillStep(function(done,params){
	var m=0
	var label=[]
	var data=[]
	while(list_dep.indexOf(params.label,m) != -1)
	{
		label.push(list_requester[list_dep.indexOf(params.label,m)])
		data.push(requester[list_dep.indexOf(params.label,m)])
		m=list_dep.indexOf(params.label,m)+1
	}
	depchart.setLabels (label)
    depchart.addSeries ("requester", "Requester", data)

    done (); // This is required
	
})


	tdb.addDashboardTab(db1);
  tdb.addDashboardTab(db2,{active: true    });
	tdb.addDashboardTab(db3);
 tdb.addDashboardTab(db4);
  tdb.addDashboardTab(db5);
   tdb.addDashboardTab(db6);
   tdb.addDashboardTab(db7);
tdb.embedTo('dashboard_target');
   //}, {tabbed: true});

}