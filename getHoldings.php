<?php
  
	require_once('vendor/autoload.php');

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


    $oclcnumber = 63400277;

	$options = array('heldBy' => array('VJA', 'YDM', 'YAH', 'VVB', 'BNG', 'XBM', 'VDB', 'YSY', 'YBM', 'ZZY', 'ZXC', 'VYT', 'WKM', 'VXF', 'VXP', 'VJN', 'VSI', 'VDH', 'ZDG', 'YCM', 'VWB', 'ZGM', 'ZHC', 'VVD', 'YEM', 'YJL', 'YFM', 'UVV', 'XFM', 'YJM', 'YJA', 'YGM', 'ZEM', 'VXV', 'ZHM', 'XIM', 'VND', 'VVJ', 'CTX', 'VYE', 'ZMM', 'ZVM', 'VYA', 'VQT', 'XMM', 'VVX', 'XNC', 'ZLM', 'VVO', 'NYP', 'NYG', 'ZOW', 'ZBM', 'VGA', 'YOM', 'YPM', 'ZQM', 'ZPM', 'XQM', 'RVE', 'XJM', 'VZJ', 'YSM', 'VZB', 'VGK', 'XDM', 'YTM', 'SYB', 'ZRS', 'VXT', 'BUF', 'VYQ', 'VVV'));
	//$options = array('heldBy' => $ini_holdings["Consortia"]);
	$response = Offer::findByOclcNumber($oclcnumber, $accessToken, $options);
	if (is_a($response, 'WorldCat\Discovery\Error')) {
		echo $response->getErrorCode();
		echo $response->getErrorMessage();
} 	else {
		$offers = $response->getOffers();
		$creativeWork = $response->getCreativeWorks();
		$creativeWork = $creativeWork[0];
		echo "<b>Title:</b> " . $creativeWork->getName() . "<br/>";
		//can put if statement
		echo "<b>Author:</b> " . $creativeWork->getAuthor()->getName() . "<br/>";
		echo "<b>OCLC #:</b> ". $creativeWork->getOCLCNumber() . "<br/>";
		
		$manipulate_schema = str_replace("schema:", "", $creativeWork->type()); 
		echo "<b>Format:</b> " . $manipulate_schema . "<br/>";

		foreach ($offers as $offer) {

			//$instURI = $offer->getItemOffered()->getCollection()->getURI();

			//echo preg_replace('/^.*\/\s*/', '', $instURI) . "<br/>";
			
			echo $offer->getItemOffered()->getCollection()->getOCLCSymbol() . "<br/>";
   }
}

?>