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
	
	$recommend = file_get_contents("http://experimental.worldcat.org/recommender/YGM/978-0316769532");
	echo $recommend;
	
	$response_xml_data = file_get_contents("http://experimental.worldcat.org/recommender/YGM/978-0316769532");
    if($response_xml_data)
    {
      echo "read";
      $data = simplexml_load_string($response_xml_data);
      var_dump($data);
      print_r($data->isbn);
      $listIsbn = $data->isbn;
      foreach($listIsbn as $item)
      {
        echo "Value: $item<br />\n";
      }
 
?>