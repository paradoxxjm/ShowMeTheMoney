<?php

require_once 'country_codes.inc.php';

const BORROWER_COUNTRY_INDEX = 11;
const BORROWER_COUNTRY_CODE_INDEX = 12;
const SUPPLIER_COUNTRY_INDEX = 24;
const TOTAL_CONTRACT_AMOUNT_INDEX = 25;


$jsonurl = "https://finances.worldbank.org/api/views/kdui-wcs3/rows.json?search=".urlencode($country_codes[$country_code]);


# using Curl instead of file-get.
#$json = file_get_contents($jsonurl,0,null,null);


$ch = curl_init();
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
curl_setopt($ch, CURLOPT_FAILONERROR,0 );
curl_setopt($ch, CURLOPT_URL, $jsonurl);
$json = curl_exec($ch);
curl_close ($ch);


$json_output = json_decode($json);

$num_results =  count($json_output->data);

$in_country_total = 0;
$in_country_count = 0;
$out_of_country_total = 0;
$out_of_country_count = 0;
$supplier_countries = array();


for($i=0; $i<$num_results; $i++){
  $row = $json_output->data[$i];

  if($row[BORROWER_COUNTRY_CODE_INDEX] != $country_code){
    continue;
  }

  $amount = $row[TOTAL_CONTRACT_AMOUNT_INDEX];

  if($row[BORROWER_COUNTRY_INDEX] == $row[SUPPLIER_COUNTRY_INDEX]){
    $in_country_total += $amount;
    $in_country_count++;
  }else {
    $supplier_countries[] = $row[SUPPLIER_COUNTRY_INDEX];
    $out_of_country_total += $amount;
    $out_of_country_count++;
  }

}


  $supplier_countries = array_unique($supplier_countries);
  $supplier_coordinates = array();
  $borrower_geo_json =  json_decode(file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($country_codes[$country_code])."&sensor=false"));
  $borrower_cordinates = array(
        "lat"=>$borrower_geo_json->results[0]->geometry->location->lat,
        "lng"=>$borrower_geo_json->results[0]->geometry->location->lng);
  
  $flipped = array_flip($country_codes);

  foreach($supplier_countries as $country){
    $geo_json_url = file_get_contents("https://maps.googleapis.com/maps/api/geocode/json?address=".urlencode($country)."&sensor=false");
    $geo_json = json_decode($geo_json_url);
    $supplier_coordinates[] = array(
        $geo_json->results[0]->geometry->location->lng,
        $geo_json->results[0]->geometry->location->lat
       );
  }



?>