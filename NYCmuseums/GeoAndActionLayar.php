<?php
include('config.php'); 
include ('CommonFuncDef.php');

// Connect to predefined MySQl database.  
$db = connectDb(); 

/* Put parameters from GetPOI request into an associative array named $requestParams */
// Put needed parameter names from GetPOI request in an array called $keys. 
$keys = array( 'layerName', 'lat', 'lon', 'radius' );

// Initialize an empty associative array.
$requestParams = array(); 
// Call funtion getRequestParams()  
$requestParams = getRequestParams($keys);
	
/* Construct the response into an associative array.*/
	
// Create an empty array named response.
$response = array();
	
// Assign cooresponding values to mandatory JSON response keys.
$response['layer'] = $requestParams['layerName'];
	
// Use Gethotspots() function to retrieve POIs with in the search range.  
$response['hotspots'] = getHotspots($db, $requestParams);

// if there is no POI found, return a custom error message.
if (!$response['hotspots'] ) {
	$response['errorCode'] = 20;
 	$response['errorString'] = 'No POI found. Please adjust the range.';
}//if
else {
  $response['errorCode'] = 0;
  $response['errorString'] = 'ok';
}//else
   
/* All data is in $response, print it into JSON format.*/

// Put the JSON representation of $response into $jsonresponse.
$jsonresponse = json_encode( $response );

// Declare the correct content type in HTTP response header.
header( 'Content-type: application/json; charset=utf-8' );

// Print out Json response.
echo $jsonresponse;

?>
