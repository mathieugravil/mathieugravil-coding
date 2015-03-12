<?php
require "../razorflow.php";

// Get the sample DataSource
$dataSource = RFUtil::getSampleDataSource();
$dataSource->setSQLSource("Invoice");

$filter = new ConditionFilterComponent();
$filter->setCaption("Filter Sales");
$filter->setDataSource($dataSource);
$filter->addTextCondition("City", "Invoice.BillingCity={{value}}");
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