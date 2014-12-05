<?php
   
/*Encourage selection of items that are more widely available than the one presently selected*/

include ('returnError.php');
require_once ('vendor/autoload.php');

$oclcnumber = 7977212;

use OCLC\Auth\WSKey;
use OCLC\Auth\AccessToken;
use WorldCat\Discovery\Offer;
	
$configFile = "appConfig.ini";
$ini_array = parse_ini_file ($configFile);
   
$holdingsList = "holdingsList.ini";
$ini_holdings = parse_ini_file($holdingsList);
 
$key = $ini_array["key"];
$secret = $ini_array["secret"];
$institution_id = $ini_array["institution_id"];
$options = array('services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
$wskey = new WSKey($key, $secret, $options);
$accessToken = $wskey->getAccessTokenWithClientCredentials($institution_id, $institution_id);

$options = array('heldBy' => array($ini_array["institution_symbol"]));

/*First check to see if the item is available locally. If it is then don't bother suggesting alternate editions*/

$response = Offer::findByOclcNumber($oclcnumber, $accessToken, $options);

if (is_a($response, 'WorldCat\Discovery\Error'))
{
	$et = $response->getErrorCode();
	$em = $response->getErrorMessage();
    returnError($et,$em);
}
else
{
    $offers = $response->getOffers();
    logMessage(count($offers));
    if (count($offers) > 0)
    {
        logMessage("We own item with oclc number $oclcnumber - terminating" . __FUNCTION__ . " in " . __FILE__ . " at line " . __LINE__);
        echo ("We own item with oclc number $oclcnumber <br />");
        //exit(0);
    }
    else
    {
        logMessage("We do not own item with oclc number $oclcnumber so moving on. Logged by " . __FUNCTION__ . " in " . __FILE__ . " at line " . __LINE__);
        echo "Not held locally, moving on </br>";
    }
}

/*If not, then fetch the workID to use in retrieving related OCLC numbers*/

$response2 = Offer::findByOclcNumber($oclcnumber, $accessToken);
if (is_a($response2, 'WorldCat\Discovery\Error'))
{
	$et = $response2->getErrorCode();
	$em = $response2->getErrorMessage();
    logMessage("Error $et with message $em. Logged by " . __FUNCTION__ . " in " . __FILE__ . " at line " . __LINE__);
    returnError($et,$em);
}
else
{
    $offers = $response2->getOffers();
    $creativeWork = $response2->getCreativeWorks();
    $creativeWork = $creativeWork[0];
    $workID = $creativeWork->getWork()->getURI();
/*  foreach($offers as $offer)
    {
	    echo $offer->getSeller()->getName();
        echo "</br>";
    }*/
}

//echo $workID;

$workDetails = json_decode(str_replace("@","",file_get_contents($workID . ".jsonld")));
//var_dump($workDetails);
//echo count($workDetails->graph);
$commandIndex = (count($workDetails->graph) - 1);
echo $commandIndex;
$oclcnAlternatives = preg_replace('/^.*\/\s*/', '', $workDetails->graph[$commandIndex]->workExample);
foreach($oclcnAlternatives as $oclcnAlternative)
{
    echo $oclcnAlternative;
    echo "</br>";
}
echo $accessToken->getValue();
/*
$guzzleOptions = static::getGuzzleOptions(array('accessToken' => $accessToken, 'logger' => $logger));
        
        $bibURI = Bib::$serviceUrl . '/offer/oclc/' . $id . '?' . static::buildParameters(null, $requestOptions);
        
        try {
            $response = \Guzzle::get($bibURI, $guzzleOptions);
            $graph = new EasyRdf_Graph();
            $graph->parse($response->getBody(true));
            $results = $graph->allOfType('discovery:SearchResults');
            return $results[0];
        } catch (\Guzzle\Http\Exception\BadResponseException $error) {
            return Error::parseError($error);
        }
*/
?>

