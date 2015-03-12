<?php
require "../razorflow.php";
Dashboard::setTitle("Table in RazorFlow PHP");

// Get the sample DataSource
$dataSource = new SQLiteDataSource("databases/chinook.sqlite");
$dataSource->setSQLSource("Invoice");

$sales = new TableComponent();
$sales->setWidth(4);
$sales->setCaption("Sales Table");
$sales->setDataSource($dataSource);
$sales->addColumn("Billing Country", "Invoice.BillingCountry");
$sales->addColumn("Billing City", "Invoice.BillingCity");
$sales->addColumn("Date", "Invoice.InvoiceDate");
$sales->addColumn("Amount", "Invoice.Total");
Dashboard::addComponent($sales);

Dashboard::Render();