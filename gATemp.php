<?php
    
/*Allow the users to see multiple items that could be represented by their query and select the best one*/

require_once ('vendor/autoload.php');

//include(returnError.php);

$configFile = "appConfig.ini";
$ini_array = parse_ini_file($configFile);
useOCLCAuthWSKey;
useOCLCAuthAccessToken;
useWorldCatDiscoveryBib;
$key = $ini_array["key"];
$secret = $ini_array["secret"];
$institution_id = $ini_array["institution_id"];

$options = array('services' => array('WorldCatDiscoveryAPI','refresh_token'));

$wskey = new WSKey($key, $secret, $options);
$accessToken = $wskey->getAccessTokenWithClientCredentials($institution_id, $institution_id);

/*First step is to get the workID to get the non-FRBRized list of OCLC numbers*/

$bib = Bib::Find(586757123, $accessToken);

if (is_a($bib, 'WorldCat\Discovery\Error'))
{
	$ec = $bib->getErrorCode();
	$em = $bib->getErrorMessage();
    //returnError($ec,$em);
}
else
{
	$response_xml_data = file_get_contents("http://xisbn.worldcat.org/webservices/xid/isbn/9780439023528?method=getEditions&format=xml&fl=*");
	if ($response_xml_data)
	{
		echo "read";
		$data = simplexml_load_string($response_xml_data);

		// var_dump($data);

		print_r($data->isbn);
		$listIsbn = $data->isbn;
		foreach($listIsbn as $item)
		{
			echo "Value: $item<br />\n";
		}
	}

	if ($xISBN_array == "")
	{
		echo "no results";
	}
	else
	{
		foreach($xISBN_array as $item)
		{
			echo "Value: $item<br />\n";
		}

		echo "This ran </br>";
	}
}

?>
