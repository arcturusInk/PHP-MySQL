<?php

// Connect to the database, configuration information is stored in
// config.php file
function connectDb() {
  try {
    $dbconn = 'mysql:host=' . DBHOST . ';dbname=' . DBDATA ; 
    $db = new PDO($dbconn , DBUSER , DBPASS , array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
    // set the error mode to exceptions
    $db->setAttribute(PDO::ATTR_ERRMODE , PDO::ERRMODE_EXCEPTION);
     return $db; 
  }// try
  catch(PDOException $e) {
    error_log('message:' . $e->getMessage());
  }// catch
}// connectDb

// Put needed getPOI request parameters and their values in an associative array
//
// Arguments:
//  array ; An array of needed parameters passed in getPOI request
//
// Returns:
//  array ; An associative array which contains the request parameters and
//  their values.
function getRequestParams($keys) {

  $paramsArray = array();
  try {
    // Retrieve parameter values using $_GET and put them in $value array with
    // parameter name as key. 
    foreach( $keys as $key ) {
      if (isset($_GET[$key]))
        $paramsArray[$key] = $_GET[$key]; 
      else 
        throw new Exception($key .' parameter is not passed in GetPOI request.');
    }
    return $paramsArray;
  }
  catch(Exception $e) {
    echo 'Message: ' .$e->getMessage();
  }
}//getRequestParams

// Change a string value to float
//
// Arguments:
//   string ; A string value.
// 
// Returns:
//   float ; If the string is empty, return NULL.
function changetoFloat($string) {
  if (strlen(trim($string)) != 0) 
    return (float)$string;
  return NULL;
}//changetoFloat

// Change a string value to integer. 
//
// Arguments:
//   string ; A string value.
// 
// Returns:
//   Int ; If the string is empty, return NULL.
function changetoInt($string) {
  if (strlen(trim($string)) != 0) 
    return (int)$string;
  return NULL;
}//changetoInt

// Convert a TinyInt value to a boolean value TRUE or FALSE
//
// Arguments: 
//  int  value_Tinyint ; The Tinyint value (0 or 1) of a key in the database. 
//
// Returns:
//   boolean ; The boolean value, return 'TRUE' when Tinyint is 1. Return
//     'FALSE' when Tinyint is 0.
//
function changetoBool($value_Tinyint) {
  if (strlen(trim($value_Tinyint)) != 0) {
    if ($value_Tinyint == 0)
      return FALSE;
    else 
      return TRUE;
   }
  return NULL;
}//changetoBool

// Convert a string into an array.
//
// Arguments:
//  string ; The input string
//  separater, string ; The boundary string used to separate the input string
//
// Returns:
//  array ; An array of strings. Otherwise, return an empty array. 
function changetoArray($string, $separator){
  $newArray = array();
  if($string) {
    if (substr_count($string,$separator)) {
      $newArray= array_map('trim' , explode($separator, $string));
        }//if
    else 
      $newArray[0] = trim($string);
  }
  return $newArray;
}//changetoArray

function getPoiActions($db , $poi) {
  // Define an empty $actionArray array. 
  $actionArray = array();

  // A new table called 'POIAction' is created to store actions, each action
  // has a field called 'poiID' which shows the POI id that this action belongs
  // to. 
  // The SQL statement returns actions which have the same poiID as the id of
  // the POI($poiID).
  $sql_actions = $db->prepare(' 
      SELECT label, 
             uri, 
             contentType,
             activityType
      FROM POIAction
      WHERE poiID = :id '); 

  // Binds the named parameter marker ':id' to the specified parameter value
  // '$poiID.                 
  $sql_actions->bindParam(':id', $poi['id'], PDO::PARAM_STR);
  // Use PDO::execute() to execute the prepared statement $sql_actions. 
  $sql_actions->execute();
  // Iterator for the $actionArray array.
  $count = 0; 
  // Fetch all the poi actions. 
  $actions = $sql_actions->fetchAll(PDO::FETCH_ASSOC);

  // Process the $actions result 
  // if $actions array is not empty. 
  if ($actions) {
    // Put each action information into $actionArray array.
    foreach ($actions as $action) { 
      // Change 'activityType' to Integer.
      $action['activityType'] = changetoInt($action['activityType']);
      $action['autoTriggerRange'] = changetoInt($action['autoTriggerRange']);
      $action['autoTriggerOnly'] = changetoBool($action['autoTriggerOnly']);
      $action['params'] = changetoArray($action['params'] , ',');
      // Assign each action to $actionArray array. 
      $actionArray[$count] = $action;
      $count++; 
    }// foreach
  }//if
  return $actionArray;
}//getPoiActions

// Put received POIs into an associative array. The returned values are
// assigned to $reponse['hotspots'].
//
// Arguments:
//   db ; The handler of the database.
//   value , array ; An array which contains all the needed parameters
//   retrieved from GetPOI request. 
//
// Returns:
//   array ; An array of received POIs.
//
function getHotspots( $db, $value ) {
	// Define an empty $hotspots array.
	$hotspots = array();
/* Create the SQL query to retrieve POIs whose distance between POIs and the 
	 user is within the "radius" returned from GetPOI request.
     Returned POIs are sorted by distance and the first 50 POIs are selected.
     The distance is caculated based on the Haversine formula.
     Note: this way of calculation is not scalable for querying large database.
*/
    
  // Use PDO::prepare() to prepare SQL statement.
  // This statement is used due to security reasons and will help prevent general SQL injection attacks.
  // ":lat1", ":lat2", ":long" and ":radius" are named parameter markers for which real values
  // will be substituted when the statement is executed.
  // $sql is returned as a PDO statement object.
  $sql = $db->prepare(' 
              SELECT id,
               imageURL,
               title,
               description,
               footnote,
               lat,
               lon,
               (((acos(sin((:lat1 * pi() / 180)) * sin((lat * pi() / 180)) +
                  	  cos((:lat2 * pi() / 180)) * cos((lat * pi() / 180)) * 
                      cos((:long  - lon) * pi() / 180))
                      ) * 180 / pi()
               )* 60 * 1.1515 * 1.609344 * 1000
               ) as distance
  			  FROM POI
        HAVING distance < :radius
      ORDER BY distance ASC
         LIMIT 0, 83 ');
			  
  // PDOStatement::bindParam() binds the named parameter markers to the
  // specified parameter values. 
  $sql->bindParam( ':lat1', $value['lat'], PDO::PARAM_STR );
  $sql->bindParam( ':lat2', $value['lat'], PDO::PARAM_STR );
  $sql->bindParam( ':long', $value['lon'], PDO::PARAM_STR );
  $sql->bindParam( ':radius', $value['radius'], PDO::PARAM_INT );  
  // Use PDO::execute() to execute the prepared statement $sql. 
  $sql->execute();
  // Iterator for the response array.
  $i = 0; 
  // Use fetchAll to return an array containing all of the remaining rows in
  // the result set.
  // Use PDO::FETCH_ASSOC to fetch $sql query results and return each row as an
  // array indexed by column name.
  $rawPois = $sql->fetchAll(PDO::FETCH_ASSOC);
 
  /* Process the $rawPois result */
  // if $rawPois array is not  empty
  if ($rawPois) {
    // Put each POI information into $hotspots array.
    foreach ( $rawPois as $rawPoi ) {
      $poi = array(); 
      $poi['id'] = $rawPoi['id'];
      $poi['imageURL'] = $rawPoi['imageURL'];
      // get anchor object information, note that changetoFloat is a custom function used to covert a string variable to float.
      $poi['anchor']['geolocation']['lat'] = changetoFloat($rawPoi['lat']);
      $poi['anchor']['geolocation']['lon'] = changetoFloat($rawPoi['lon']);
      // get text object information
      $poi['text']['title'] = $rawPoi['title'];
      $poi['text']['description'] = $rawPoi['description'];
      $poi['text']['footnote'] = $rawPoi['footnote'];
      //User function getPOiActions() to return an array of actions associated
      //with the current POI
      $poi['actions'] = getPoiActions($db, $rawPoi);
	 // Put the poi into the $hotspots array.
     $hotspots[$i] = $poi;
     $i++;
    }//foreach
  }//if
  return $hotspots;
}//getHotspots

?>