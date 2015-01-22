<?php

/*Encourage selection of items that are more widely available than the one presently selected*/

include ('returnError.php');
include ('classRequestable.php');
require_once ('vendor/autoload.php');

/*Load the OCLC number from the querystring*/
$oclcnumber = $_GET["num"];
//$oclcnumber = 7977212;

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
//echo $accessToken->getValue() . "<br />";
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
//        echo ("We own item with oclc number $oclcnumber <br />");
//        exit(0);
    }
    else
    {
        logMessage("We do not own item with oclc number $oclcnumber so moving on. Logged by " . __FUNCTION__ . " in " . __FILE__ . " at line " . __LINE__);
//        echo "Not held locally, moving on </br>";
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
$commandIndex = (count($workDetails->graph) - 1); /*Section with peers in work seems to always be in last cluster but this seems like a really ugly way to get there*/
//echo $commandIndex;
$oclcnAlternatives = preg_replace('/^.*\/\s*/', '', $workDetails->graph[$commandIndex]->workExample);

/*You now have a list of OCLC numbers that each need to become a brief summary and holdings count since this can be overwhelming, I'm just getting up to ten*/
$i = 0;
$t = (count($oclcnAlternatives) > 10 ? 10 : count($oclcnAlternatives));
logMessage("There are " . count($oclcnAlternatives) . " alternative records. $t are presented. Logged by " . __FUNCTION__ . " in " . __FILE__ . " at line " . __LINE__);
//echo "<br />" . $t . "<br />";

/*Now to create and fill an array of requestables*/
//$requestables[] = new requestable;
while($i < $t)
{
    $requestables[$i] = new requestable;
//    echo $oclcnAlternatives[$i];
//    echo "</br>";
    $options = array('useFRBRGrouping' => 'False');
	$response3 = Offer::findByOclcNumber($oclcnAlternatives[$i], $accessToken, $options);
	if (is_a($response3, 'WorldCat\Discovery\Error'))
    {
	    $et = $response3->getErrorCode();
	    $em = $response3->getErrorMessage();
        returnError($et,$em);
    }
    else
    {
		$offers = $response3->getOffers();
		$creativeWork = $response3->getCreativeWorks();
		$creativeWork = $creativeWork[0];
        $title = $creativeWork->getName()->getValue(); /*ToDo: Use the entire object rather than jumping to name value*/
        $author = $creativeWork->getAuthor()->getName()->getValue();
        $format = str_replace("schema:", "", $creativeWork->type());
        $oclcn = $creativeWork->getOCLCNumber()->getValue();
        $language = $creativeWork->getLanguage()->getValue();
        $edition = is_null($creativeWork->getBookEdition()) ? "Not Specified" : ($creativeWork->getBookEdition()->getValue()); //ToDo: Test serial handling
        $holdingsCount = count($offers);
        $requestables[$i]->title = $title;
        $requestables[$i]->author = $author;
        $requestables[$i]->format = $format;
        $requestables[$i]->oclcn = $oclcn;
        $requestables[$i]->language = $language;
        $requestables[$i]->edition = $edition;
        $requestables[$i]->timeToFill = $requestables[$i]->getTimeToFill();
/*		echo "<b>Title:</b> $title<br/>";
		echo "<b>Author:</b> $author<br/>";
		echo "<b>Catalog Entry:</b> <a href='http://www.worldcat.org/oclc/$oclcn'>$oclcn</a><br/>";
		echo "<b>Format:</b> $format<br/>";
        echo "<b>Language:</b> $language<br/>";
        echo "<b>Edition:</b> $edition<br/>";
        echo "<b>Holding Libraries:</b> $holdingsCount<br/>";*/
        $si = 0;
		foreach ($offers as $offer)
            {
//			echo $offer->getItemOffered()->getCollection()->getOCLCSymbol() . "<br/>";
            $requestables[$i]->lenders[$si] = $offer->getItemOffered()->getCollection()->getOCLCSymbol()->getValue();
            $si++;
            }
    }
    $i++;
}
// var_dump($requestables);
echo json_encode($requestables);
// echo $accessToken->getValue();
?>
