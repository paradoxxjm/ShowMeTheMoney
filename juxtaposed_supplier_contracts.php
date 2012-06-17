<?php
$country_code = "";
$is_dynamic = false;
$is_juxtaposed = true;

require_once 'supplier_country_codes.inc.php';


if (isset($_GET["country_code"])) {
  $country_code = $_GET["country_code"];
  $is_dynamic = true;

  $is_juxtaposed = $country_code == "juxtaposed";
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />

    <!-- Always force latest IE rendering engine (even in intranet) & Chrome Frame
         Remove this if you use the .htaccess -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

    <title>Show Me the Money!</title>
    <meta name="description" content="" />
    <link type="text/css" rel="stylesheet" media="all" href="https://finances.worldbank.org/styles/merged/base.css?9c036218b2bed9731820a76a21514981aaecf6e6.233"/>
    <link type="text/css" rel="stylesheet" media="all" href="https://finances.worldbank.org/styles/current_site.css"/>
    <link rel="stylesheet" type="text/css" href="index.css" />


    <link rel="stylesheet" href="./Leaflet/leaflet.css" />
    <!--[if lte IE 8]><link rel="stylesheet" href="./Leaflet/leaflet.ie.css" /><![endif]-->
    <script src="./Leaflet/leaflet.js"></script>
    <script src="./arc.js"></script>
    <script src="./arc_map.js"></script>

    <meta name="viewport" content="width=device-width; initial-scale=1.0" />
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    <script src="index.js"></script>
    <!-- Replace favicon.ico & apple-touch-icon.png in the root of your domain and delete these references -->
    <link rel="shortcut icon" href="/favicon.ico" />
    <link rel="apple-touch-icon" href="/apple-touch-icon.png" />

    <?php
    $countries_json = json_decode(file_get_contents("supplier_country_summary.json"), true);
    $js_string = "";
    foreach ($countries_json as $ckey => $cval) {
      $js_string.= "[\" " . $cval["SupplierCountry"] . "\", " . $cval["TotalAmount"] . "],";
    }
    ?>
    <script type='text/javascript' src='https://www.google.com/jsapi'></script>
    <script type='text/javascript'>
      google.load('visualization', '1', {'packages': ['geochart']});
      google.setOnLoadCallback(drawMarkersMap);

      function drawMarkersMap() {
        var data = google.visualization.arrayToDataTable([
          ["Country",  'Total Amount'],<?php echo $js_string; ?>
            
        ]);

        var options = {
          region: 'world',
          colorAxis: {colors: ['blue', 'green', 'yellow', 'red']},
          legend: {numberFormat: "$#,##0.00" }
        };

        var formatter = new google.visualization.NumberFormat({prefix: '$'});
        formatter.format(data, 1);

        var chart = new google.visualization.GeoChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      };
    </script>

  </head>

  <body>
<?php require_once 'supplier_nav.inc.php'; ?>
    <div id="content">
      <form action="index.php" method="get">
        <select id="country_code" name="country_code">
          <option value="">Select Country</option>
<?php foreach ($supplier_country_codes as $cid => $cval): ?>
            <option value="<?php echo $cid ?>"><?php echo $cval ?></option>
          <?php endforeach; ?>
        </select> <input type="submit" value="submit"/> OR <a href="index.php?country_code=juxtaposed" id="juxtaposed">View Juxtaposed Global View</a>

      </form>
      <div id="flow-map" class="grouping">
        <h2>Distributions By Supplier Country</h2>
        <div id="chart_div" style="width: 1300px; height: 600px;"></div>
      </div>
      <footer>
        <p>&copy; RHoK 2012</p>
      </footer>
    </div>
  </body>
</html>
