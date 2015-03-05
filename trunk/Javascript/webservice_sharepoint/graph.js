// Welcome to the RazorFlow Dashbord Quickstart. Simply copy this "dashboard_quickstart"
// to somewhere in your computer/web-server to have a dashboard ready to use.
// This is a great way to get started with RazorFlow with minimal time in setup.
// However, once you're ready to go into deployment consult our documentation on tips for how to 
// maintain the most stable and secure 



function Mydashboard(dashboard_title,label1,nb1,day1,title1,label2,nb2,day2,title2,label3,day3,title5)
{
/*	labels5 = new Array()
	values5 = new Array()
	for(key in data5) 
	{
		labels5.push(key)
		values5.push(data5[key])
	}
*/	
var color=["#00FFFF","#7FFFD4","#000000","#0000FF","#8A2BE2","#A52A2A","#DEB887","#5F9EA0","#7FFF00","#D2691E","#FF7F50","#6495ED","#DC143C","#00FFFF","#00008B","#008B8B","#B8860B","#A9A9A9","#006400","#BDB76B","#8B008B","#556B2F","#FF8C00","#9932CC","#8B0000","#E9967A","#8FBC8F","#483D8B","#2F4F4F","#00CED1","#9400D3","#FF1493","#00BFFF","#696969","#1E90FF","#B22222","#228B22","#FF00FF","#FFD700","#DAA520","#808080","#008000","#ADFF2F","#FF69B4","#CD5C5C","#4B0082","#F0E68C","#7CFC00","#ADD8E6","#F08080","#D3D3D3","#90EE90","#FFB6C1","#FFA07A","#20B2AA","#87CEFA","#778899","#B0C4DE","#00FF00","#32CD32","#FF00FF","#800000","#66CDAA","#0000CD","#BA55D3","#9370DB","#3CB371","#7B68EE","#00FA9A","#48D1CC","#C71585","#191970","#F5FFFA","#FFE4E1","#FFE4B5","#FFDEAD","#000080","#FDF5E6","#808000","#6B8E23","#FFA500","#FF4500","#DA70D6","#EEE8AA","#98FB98","#AFEEEE","#DB7093","#FFEFD5","#FFDAB9","#CD853F","#FFC0CB","#DDA0DD","#B0E0E6","#800080","#663399","#FF0000","#BC8F8F","#4169E1","#8B4513","#FA8072","#F4A460","#2E8B57","#FFF5EE","#A0522D","#C0C0C0","#87CEEB","#6A5ACD","#708090","#00FF7F","#4682B4","#D2B48C","#008080","#D8BFD8","#FF6347","#40E0D0","#EE82EE","#F5DEB3","#9ACD32","#FFFF00"]
randNumMin=0
randNumMax=color.length

StandaloneDashboard(function(tdb){
	tdb.setDashboardTitle (dashboard_title);

	// Dashboard 1 
	var db1 = new Dashboard();
    db1.setDashboardTitle('NB of tickets');
	
	// Add a chart to the dashboard. This is a simple chart with no customization.
	var chart = new ChartComponent();
	var chart2 = new ChartComponent();

	chart.setCaption(title1);
	chart.setDimensions (6, 6);	
	chart.setLabels (label1);
	//chart.addSeries (values);
	chart.setPieValues(nb1);
	db1.addComponent (chart);
	chart2.setCaption(title2);
	chart2.setDimensions (6, 6);	

	chart2.setLabels (label2);
	//chart2.addSeries (values2);
	chart2.setPieValues(nb2);
	db1.addComponent (chart2);
	
 // Dashboard 2
    var db2 = new Dashboard();
    db2.setDashboardTitle('Man day');
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
	chart.setYAxis("", {
        numberPrefix: "$"
    });
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
/*	
	serie0=[0,18,36,21.5,31,94,13,0,20,0]

	serie1=[0,21.5,3,5.5,18,11,5.5,21.5,0,0]
    
chart5.addSeries (label1[0],label1[0],serie0,{
       seriesStacked: true,
        seriesDisplayType: "column"
         });
    chart5.addSeries (label1[1],label1[1],serie1,{
       seriesStacked: true,
        seriesDisplayType: "column"
         });	
		 */
		 
	db2.addComponent (chart5);
	

	tdb.addDashboardTab(db1, {
        active: true
    });
  tdb.addDashboardTab(db2, {
    });

}, {tabbed: true});

}