<?php
$country_code = "";

require_once 'supplier_country_codes.inc.php';

if (isset($_GET["country_code"])) {
  $country_code = $_GET["country_code"];
  $is_dynamic = true;
}

function get_url_content($url){
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_HEADER, 0);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
  curl_setopt($ch, CURLOPT_FAILONERROR,0 );
  curl_setopt($ch, CURLOPT_URL, $url);
  $contents = curl_exec($ch);
  curl_close ($ch);

  return $contents;
}

function get_country_by_code($countries_json, $country_code){
  for($i=0; $i< count($countries_json); $i++){
    if($countries_json[$i]->iso2Code == $country_code){
      return $countries_json[$i];
    }
  }
}

$supplier_countries_json = json_decode(file_get_contents("supplier_country_summary.json"), true);
$countries_geo_json = json_decode(get_url_content("http://api.worldbank.org/countries?format=json&per_page=400"));
$supplier_coordinates = get_country_by_code($countries_geo_json[1], $country_code);
$borrower_coordinates = array();

  foreach($supplier_countries_json[$country_code]["CountryFrom"] as $c_code => $amount){
       $country = get_country_by_code($countries_geo_json[1], $c_code);
       if(isset($country) && $country->longitude != ""){
    $borrower_coordinates[] = array(
        $country->longitude,
        $country->latitude
       );}
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


<?php require_once 'supplier_leaflet.inc.php'; ?>
  </head>

  <body>
    <pre><?php //print_r($supplier_countries_json[$country_code]["CountryFrom"]);    print_r($borrower_coordinates); ?></pre>
<?php require_once 'supplier_nav.inc.php'; ?>
    <div id="content">
      <form action="supplier_contracts.php" method="get">
        <select id="country_code" name="country_code">
          <option value="">Select Supplier Country</option>
<?php foreach ($supplier_country_codes as $cid => $cval): ?>
          <option value="<?php echo $cid ?>"><?php echo $cval ?></option>
<?php endforeach; ?>
        </select> <input type="submit" value="submit"/> OR <a href="juxtaposed_supplier_contracts.php" id="juxtaposed">Global View</a>

      </form>
      <h1>Cash Flow for <?php echo $supplier_countries_json[$country_code]["SupplierCountry"] ?></h1>

      <div id="flow-map" class="grouping">
        <h2>Flow Map</h2>
        <div id="map" style="width: 1200px; height: 600px; margin: 10px auto;"></div>
      </div>

      <footer>
        <p>&copy; RHoK 2012</p>
      </footer>
    </div>
  </body>
</html>
