// Welcome to the RazorFlow Dashbord Quickstart. Simply copy this "dashboard_quickstart"
// to somewhere in your computer/web-server to have a dashboard ready to use.
// This is a great way to get started with RazorFlow with minimal time in setup.
// However, once you're ready to go into deployment consult our documentation on tips for how to 
// maintain the most stable and secure 



function Mydashboard(dashboard_title,data,title,data2,title2,data3,title3,data4,title4,data5,title5)
{
	labels = new Array()
	values = new Array()
	for(key in data) 
	{
		labels.push(key)
		values.push(data[key])
	}
	labels2 = new Array()
	values2 = new Array()
	for(key in data2) 
	{
		labels2.push(key)
		values2.push(data2[key])
	}
	labels3 = new Array()
	values3 = new Array()
	for(key in data3) 
	{
		labels3.push(key)
		values3.push(data3[key])
	}
	labels4 = new Array()
	values4 = new Array()
	for(key in data4) 
	{
		labels4.push(key)
		values4.push(data4[key])
	}	
	
	labels5 = new Array()
	values5 = new Array()
	for(key in data5) 
	{
		labels5.push(key)
		values5.push(data5[key])
	}		
StandaloneDashboard(function(tdb){
	tdb.setDashboardTitle (dashboard_title);

	// Dashboard 1 
	var db1 = new Dashboard();
    db1.setDashboardTitle('NB of tickets');
	
	// Add a chart to the dashboard. This is a simple chart with no customization.
	var chart = new ChartComponent();
	var chart2 = new ChartComponent();

	chart.setCaption(title);
	chart.setDimensions (6, 6);	
	chart.setLabels (labels);
	//chart.addSeries (values);
	chart.setPieValues(values);
	db1.addComponent (chart);
	chart2.setCaption(title2);
	chart2.setDimensions (6, 6);	

	chart2.setLabels (labels2);
	//chart2.addSeries (values2);
	chart2.setPieValues(values2);
	db1.addComponent (chart2);
	
 // Dashboard 2
    var db2 = new Dashboard();
    db2.setDashboardTitle('Man day');
	// Add a chart to the dashboard. This is a simple chart with no customization.
	var chart3 = new ChartComponent();
	var chart4 = new ChartComponent();
    var chart5 = new ChartComponent();

	chart3.setCaption(title3);
	chart3.setDimensions (6, 6);	

	chart3.setLabels (labels3);
	//chart.addSeries (values);
	chart3.setPieValues(values3);
	db2.addComponent (chart3);
	
	chart4.setCaption(title4);
	chart4.setDimensions (6, 6);	
	chart4.setLabels (labels4);
	//chart2.addSeries (values2);
	chart4.setPieValues(values4);
	db2.addComponent (chart4);
	
	chart5.setCaption(title5);
	chart5.setDimensions (12, 6);	
	chart5.setLabels (labels5);
	chart5.addSeries (values5);
	db2.addComponent (chart5);
	
	

	tdb.addDashboardTab(db1, {
        active: true
    });
  tdb.addDashboardTab(db2, {
    });

}, {tabbed: true});

}