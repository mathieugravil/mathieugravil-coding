<?php
global $keepHead;
global $rfdemopath;
if(!isset($rfdemopath))
{
  
  $rfdemopath = "";
}

if(!isset($keepHead))
{
  $keepHead = true;
}
if(isset($_GET['item']))
{
    $slug = $_GET['item'];
    preg_match('/[a-z0-9_]*/', $slug, $matches);
    $filename = $matches[0];

    if(isset($filename))
    {
        $path = $filename.'.php';
        if(file_exists($path))
        {
            header('Content-Type: text/plain');
            echo htmlentities(file_get_contents($filename.'.php'));
            exit();
        }
        else {
            die("Unknown file $filename");
        }
    }
    else {
        die("Error. invalid request");
    }
}
if($keepHead):
?><!DOCTYPE html>

<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>RazorFlow Demo Browser</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <!-- Le styles -->
    <link href="../static/installer/css/bootstrap.css" rel="stylesheet">
    <link href="../static/installer/css/demobrowser.css" rel="stylesheet">
    <style>
      body {
      }
    </style>
    <link href="../static/installer/css/bootstrap-responsive.css" rel="stylesheet">

    <!-- HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>

    <![endif]-->
  </head>

  <body>
<?php endif; ?>
    <div class="container">
      <div class="row">
        <div class="span12">
          <h2>Business Demos</h2>
          <p>
            These demos simulate real-life business usage of RazorFlow PHP. 
            We have built industry and function-specific demos like
            Sales Dashboard, Retail Inventory Dashboard, and an Executive Overview Dashboard
            that depict real-life scenarios. View these examples to get inspired and learn RazorFlow PHP best practices.
          </p>
          <div class="row" style="margin-left: 0px;">
            <ul class="thumbnails">
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>sales_dashboard.png" alt="">
                  <div class="caption">
                    <h3>Sales Dashboard</h3>
                    <p>A simple but powerful dashboard for sales reps. This Dashboard is for a company selling scale models of cars.</p>
                    <p>This dashboard is designed to provide a high level overview of sales trends but also have access to all sales records which can be filtered quickly and easily.</p>
                    <a href="<?php echo $rfdemopath;?>sales_dashboard.php" class='dbLink btn btn-primary' data-slug='sales_dashboard'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>executive_dashboard.png" alt="">
                  <div class="caption">
                    <h3>Executive Dashboard</h3>
                    <p>A high level overview designed for the busy executive who always needs up-to-date information on the most metrics of his business.</p>
                    <p>Simple, yet effective KPIs and Gauges give status at a glance, and charts provide important summarized data.</p>
                    <a href="<?php echo $rfdemopath;?>executive_dashboard.php" class='dbLink btn btn-primary' data-slug='executive_dashboard'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>inventory_dashboard.png" alt="">
                  <div class="caption">
                    <h3>Inventory Dashboard</h3>
                    <p>An extremely effective Dashboard for inventory and supply-chain managers.</p>
                    <p>Make critical purchasing decisions on the go with summarized high level overviews, and also filter and find the exact status of a single product in the warehouse, even from a mobile device.</p>
                    <a href="<?php echo $rfdemopath;?>inventory_dashboard.php" class='dbLink btn btn-primary' data-slug='inventory_dashboard'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
        <div class="span12">
          <h2>Feature Demos</h2>
          <p>
            These Demo Dashboards explore specific features of RazorFlow PHP.
          </p>
          <div class="row" style="margin-left: 0px;">
            <ul class="thumbnails">
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>chart_types.png" alt="">
                  <div class="caption">
                    <h3>Chart Types</h3>
                    <p>RazorFlow has support for several types of charts with single and multiple series.</p>
                    <a href="<?php echo $rfdemopath;?>chart_types.php" class='dbLink btn btn-primary' data-slug='chart_types'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>kpi_types.png" alt="">
                  <div class="caption">
                    <h3>KPI Types</h3>
                    <p>A KPI (Key Performance Indicator) is a simple but powerful component supported by RazorFlow.</p>
                    <a href="<?php echo $rfdemopath;?>kpi_types.php" class='dbLink btn btn-primary' data-slug='kpi_types'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>table_types.png" alt="">
                  <div class="caption">
                    <h3>Tables</h3>
                    <p>RazorFlow PHP has great support for tables, including auto sorting, pagination and filter.</p>
                    <a href="<?php echo $rfdemopath;?>table_types.php" class='dbLink btn btn-primary' data-slug='table_types'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>filter_types.png" alt="">
                  <div class="caption">
                    <h3>Filters</h3>
                    <p>Filters are a flexible and extremely easy way to help your users find exactly the data they need.</p>
                    <a href="<?php echo $rfdemopath;?>filter_types.php" class='dbLink btn btn-primary' data-slug='filter_types'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>chart_drilldown1.png" alt="">
                  <div class="caption">
                    <h3>Drill down into same chart</h3>
                    <p>Drill downs let your users see several dimensions of data, from a high level summary to more detail.</p>
                    <a href="<?php echo $rfdemopath;?>chart_drilldown1.php" class='dbLink btn btn-primary' data-slug='chart_drilldown1'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>chart_drilldown2.png" alt="">
                  <div class="caption">
                    <h3>Drill down into other chart</h3>
                    <p>You can also use one chart to trigger drill downs on other charts, letting your users explore data.</p>
                    <a href="<?php echo $rfdemopath;?>chart_drilldown2.php" class='dbLink btn btn-primary' data-slug='chart_drilldown2'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>socialauth.png" alt="">
                  <div class="caption">
                    <h3>SocialAuth</h3>
                    <p> You can use RazorFlow SocialAuth to enable authentication to your dashboards with Google, Twitter and LinkedIn as providers. </p>
                    <a href="<?php echo $rfdemopath;?>social_auth.php" class='btn btn-primary'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
              <li class="span4">
                <div class="thumbnail">
                  <img src="<?php echo $rfdemopath;?>embed.png" alt="">
                  <div class="caption">
                    <h3>Embedding a Dashboard</h3>
                    <p>Embed your RazorFlow dashboard into your custom web application using the <a href="http://razorflow.com/docs/manual/php/concepts/embed.php">RazorFlow jQuery Plugin</a>.</p>
                    <a href="<?php echo $rfdemopath;?>embed.html" class='btn btn-primary'>Launch Dashboard</a>
                  </div>
                </div>
              </li>
            </ul>
          </div>
        </div>
   </div>
 </div>
</div> <!-- /container -->

<div class="modal hide" id="rfDemoModal">
  <div class="modal-header" style="padding-top: 2px; padding-bottom: 2px;">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
    <h3 style="display:inline; float: left; margin-right: 30px" id="rfDemoName">Demo Name</h3>
    <ul class="nav nav-pills" id="myTab" style='margin-bottom: 0px; display: inline'>
      <li class="active"><a href="#desktop" data-toggle="tab">Desktop View</a></li>
      <li><a href="#mobile" data-toggle="tab" id='mobileViewButton'>Mobile View</a></li>
      <li><a href="#source" id='sourceButton' data-toggle="tab">Source Code</a></li>
      <li><a href="" id='openButton'>View Full Size</i></a></li>
    </ul>
  </div>
  <div class="modal-body rfModalBody">
        <div class="tab-content">
          <div class="tab-pane active" id="desktop">
            <iframe class="rfDesktopDemo rfDemoFrame" src=""></iframe>
          </div>
          <div class="tab-pane" id="mobile">
           <div class='iosChrome'>
            <iframe class="iosFrame rfDemoFrame" src=""></iframe>
          </div>
        </div>
        <div class="tab-pane" id="source">
         <pre class="prettyprint" id='sourceContent'>
         </pre>
       </div>
 </div>
</div>
</div>
<?php if ($keepHead): ?>
    <script src="../static/installer/js/jquery.min.js"></script>
    <script src="../static/installer/js/bootstrap.min.js"></script>
    <script src="../static/installer/js/demobrowser.js"></script>

  </body>
</html>
<?php endif; ?>
