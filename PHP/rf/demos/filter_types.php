<?php
require "../razorflow.php";
Dashboard::setTitle("Filters in RazorFlow PHP");

// Get the sample DataSource
$dataSource = new SQLiteDataSource('databases/chinook.sqlite');
$dataSource->setSQLSource("Invoice");

$filter = new AutoFilterComponent();
$filter->setCaption("Filter Sales");
$filter->setDataSource($dataSource);
$filter->addTextFilter("City", "Invoice.BillingCity");
$filter->addMultiSelectFilter("Select Country", "Invoice.BillingCountry");
$filter->addTimeRangeFilter("Sale Date", "Invoice.InvoiceDate");
$filter->addNumericRangeFilter("Sale Amount", "Invoice.Total");
Dashboard::addcomponent($filter);

$sales = new TableComponent();
$sales->setCaption("Sales Table");
$sales->setDataSource($dataSource);
$sales->addColumn("Billing Country", "Invoice.BillingCountry");
$sales->addColumn("Billing City", "Invoice.BillingCity");
$sales->addColumn("Date", "Invoice.InvoiceDate");
$sales->addColumn("Amount", "Invoice.Total");
Dashboard::addComponent($sales);

$filter->addFilterTo($sales);

Dashboard::Render();