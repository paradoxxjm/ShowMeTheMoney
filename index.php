<?php
$country_code = "";
$is_dynamic = false;
$is_juxtaposed = false;

require_once 'country_codes.inc.php';


if (isset($_GET["country_code"])) {
  $country_code = $_GET["country_code"];
  $is_dynamic = true;

  $is_juxtaposed = $country_code == "juxtaposed";

  if (!$is_juxtaposed) {
    require_once 'header.inc.php';
  }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />

    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
         Remove this if you use the .htaccess -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>Where does the money really go ?</title>
    <meta name="description" content="" />
    <link type="text/css" rel="stylesheet" media="all" href="https://finances.worldbank.org/styles/merged/base.css?9c036218b2bed9731820a76a21514981aaecf6e6.233"/>
    <link type="text/css" rel="stylesheet" media="all" href="https://finances.worldbank.org/styles/current_site.css"/>
    <link rel="stylesheet" type="text/css" href="index.css" />


    <link rel="stylesheet" href="./Leaflet/leaflet.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="./Leaflet/leaflet.ie.css" /><![endif]-->
    <script src="./Leaflet/leaflet.js"></script>
    <script src="./arc.js"></script>

    <meta name="viewport" content="width=device-width; initial-scale=1.0" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="index.js"></script>
    <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />
<?php if ($is_dynamic && !$is_juxtaposed): ?>
      <script type="text/javascript" src="https://www.google.com/jsapi"></script>
      <script type="text/javascript">
        google.load("visualization", "1", {packages:["corechart"]});
        google.setOnLoadCallback(drawChart);
        function drawChart() {
          var data_total = google.visualization.arrayToDataTable([
            ['Spent Where?', 'How Much?'],
            ['Stayed In Country',     <?php echo $in_country_total ?>],
          [' Flowed Out of Country', <?php echo $out_of_country_total ?>]
        ]);


        var data_count = google.visualization.arrayToDataTable([
          ['Spent Where?', 'How Many?'],
          ['Stayed In Country',     <?php echo $in_country_count ?>],
          ['Flowed Out of Country', <?php echo $out_of_country_count ?>]
        ]);

        var options_total = {
          'is3D':true,
          title: 'Cash Flow of Contract'
        };


        var options_count = {
          'is3D':true,
          title: 'Number Contracts Awarded'
        };

        var formatter = new google.visualization.NumberFormat({prefix: '$'});
        formatter.format(data_total, 1);

        var chart = new google.visualization.PieChart(document.getElementById('chart_div'));
        chart.draw(data_total, options_total);

        var chart2 = new google.visualization.PieChart(document.getElementById('chart_div2'));
        chart2.draw(data_count, options_count);
      }
    </script>

<?php require_once 'leaflet.inc.php'; ?>

    <?php endif; ?>

    <?php if ($is_juxtaposed): ?>
      <?php
        $countries_json = json_decode(file_get_contents("country_summaries.json"), true);

        $js_string = "";
       foreach($countries_json as $ckey => $cval){
         $stayed_in_percent = 100*($cval["TotalInAmt"]/($cval["TotalOutAmt"] + $cval["TotalInAmt"]));
         $js_string.= "[\" ".$cval["CountryName"]."\", ".$stayed_in_percent."],";
       }


      ?>
      <script type='text/javascript' src='https://www.google.com/jsapi'></script>
      <script type='text/javascript'>
        google.load('visualization', '1', {'packages': ['geochart']});
        google.setOnLoadCallback(drawMarkersMap);

        function drawMarkersMap() {
          var data = google.visualization.arrayToDataTable([
            ["Country",  'Percentage Stayed In'],<?php echo $js_string; ?>
            
          ]);

          var options = {
            region: 'world',
            displayMode: 'markers',
            colorAxis: {colors: ['red', 'blue']}
          };

          var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
          chart.draw(data, options);
        };
      </script>
<?php endif; ?>

    </head>

    <body>
<?php require_once 'nav.inc.php'; ?>
      <div id="content">
        <form action="index.php" method="get">
          <select id="country_code" name="country_code">
            <option value="">Select Country</option>
<?php foreach ($country_codes as $cid => $cval): ?>
              <option value="<?php echo $cid ?>"><?php echo $cval ?></option>
          <?php endforeach; ?>
        </select> <input type="submit" value="submit"/> OR <a href="index.php?country_code=juxtaposed" id="juxtaposed">View Juxtaposed Global View</a>

      </form>
<?php if ($is_dynamic && !$is_juxtaposed): ?>
        <h1>Cash Flow for <?php echo $row[BORROWER_COUNTRY_INDEX] ?> (Major Contract Awards 2012)</h1>

        <div id="pie-charts" class="grouping">
          <h2>Pie Charts</h2>
          <div id="chart_div" style="width: 650px; height: 500px; float:left;"></div>

          <div id="chart_div2" style="width: 650px; height: 500px;float:left;"></div>
          <div style="clear:both;"></div>
        </div>

        <div id="flow-map" class="grouping">
          <h2>Flow Map</h2>
          <div id="map" style="width: 1200px; height: 600px; margin: 10px auto;"></div>
        </div>
<?php elseif (!$is_juxtaposed) : ?>
              <div class="grouping">

                <b>RHOK Featured Problem: Show me the Money.</b>
                <h2>Where Does the Money Really go?</h2>
                <p>
                  <b>Problem:</b> Billions of dollars in aid and/or grant money flow into various nations of the world. How much of that money stays "in country?" Presumably, this has implications on longer-term impact on development funding's impact through local job creation. However, that money sometimes ends up flowing outside the beneficiary country to a foreign supplier. If the money is utilized in the country of origin (to original target to which the aid/grant was initially given to), that could go a long way in promoting sustainable development- but first we need to know where the money really goes.
                  <br>
                  <br>
                  To begin exploring this question and more, the World Bank released Major Contract Awards data from January-April 2012 just for RHoK.
                  <br>
                  <br>
                  <b>The Solution:</b>
                  Looking at the trends by mapping the inflow and outflow of the money in aid/grant recepient countries.
                  <br>
                  <br>
                  <b>Concept:</b> Using the World Bank Contracts data and "following the money" to find the net amount that stayed in the countries receiving the aid/grant. Using simple data visualization tools and processes to help users trace, compare, and contrast the money flow between various countries.
                  <br>
                  <br>
                  <b>Beyond this App:</b>
                  <a href="https://www.nodexlgraphgallery.org/Pages/Graph.aspx?graphID=661">WBContracts data visualization</a> on "single-sourced contracts" data that Lucas Cioffi created which was a source of inspiration for the "Show Me the Money- Where does the money really go?" App.
                  <Br>
                  <br>
                  The outflow of contract money is not currently reflected as an "input" to supplier countries- but this is of course a natural next step.
                  <Br>
                  <br>
                  As data becomes more interesting in a local context, it would be interesting to explore hyperlocal links and flows- especially when viewed with corresponding development indicators.
                  <br>
                  <br>
                  Additionally, a citizen engagement/feedback model getting feedback from local stakeholders on the actual suppliers would be an important next step.
                  <br>
                  <br>
                  <b>The Team:</b>
                  <br>
                  <br>
                  Matt Glover, mpglover@gmail.com
                  <br>Serign Jobe, serignjobe@gmail.com
                  <br>Vijay Rao, mail.vjrao@gmail.com
                  <br>Nehaumma Reddy, nehaummareddy@gmail.com
                  <br>Chelsey Towns, towns.chelsey@gmail.com
                  <br>Lucas Cioffi, lucas.cioffi@gmail.com
                  <br>Matthew McNaughton mamcnaughton@gmail.com
                  <br>Julia Bezgacheva, julia.bezgacheva@gmail.com
                  <br>Sam Lee, samyslee@gmail.com
                  <br>Gaurav Tiwari, gtiwari@syr.edu

                  <br>
                  <br>
                  <b>Get the data:</b>
                  <br>
                  <br>
                  <a href="bit.ly/WBContracts">Major WB Contracts Award data</a>
                  <br>
                  <br>
                  This set of procurement contract awards includes data on commitments against contracts that were reviewed and agreed to by Bank staff before they were awarded (prior-review Bank-funded contracts) awarded under IDA/IBRD investment projects and related Trust Funds.This dataset does not list all contracts awarded by the Bank, and should be viewed only as a guide to determine the distribution of major contracts among the Bank's member countries. The Procurement Policy and Services Group does not guarantee the data included in this publication and accepts no responsibility whatsoever for any consequences of its use.
                  <br>
                </p>
              </div>
<?php endif; ?>

      <?php if ($is_juxtaposed): ?>
                <div id="flow-map" class="grouping">
                  <h2>Country Comparison [Percentage that stayed in country)</h2>
                  <div id="chart_div" style="width: 1300px; height: 600px;"></div>
                </div>
<?php endif; ?>
      <footer>
        <p>&copy; RHoK 2012</p>
      </footer>
    </div>
  </body>
</html>
