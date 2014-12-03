<?php	
	require_once('vendor/autoload.php');

	use OCLC\Auth\WSKey;
	use OCLC\Auth\AccessToken;
	use WorldCat\Discovery\Bib;

	$filename = "appConfig.ini";
	$ini_array = parse_ini_file ($filename);
	
	$key = $ini_array["key"];
	$secret = $ini_array["secret"];
	$institution_id = $ini_array["institution_id"];
	$options = array('services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
	$wskey = new WSKey($key, $secret, $options);
	$accessToken = $wskey->getAccessTokenWithClientCredentials($institution_id, $institution_id);
	
	$bib = "";
	$query = 'name:hunger';

	$results = Bib::Search($query, $accessToken);
	if (is_a($bib, 'WorldCat\Discovery\Error')) {
		echo $results->getErrorCode();
   		echo $results->getErrorMessage();
	} else {
   		foreach ($results->getSearchResults() as $bib){
      			echo $bib->getName()->getValue();
      			echo ($bib->getDatePublished() ?  ' ' . $bib->getDatePublished()->getValue()  : '');
	  		echo "<br/><br/>";
   		}
   	}
?>
