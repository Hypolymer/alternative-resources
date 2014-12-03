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
	

	
	$response_xml_data = file_get_contents("http://experimental.worldcat.org/recommender/MLT/978-0316769532?inst=YGM&count=10");
    if($response_xml_data)
    {
      // echo "read";
	 $data = simplexml_load_string($response_xml_data);
	  // echo var_dump($response_xml_data);
	  
	  //print_r($data->likeItems->likeItem->ocn);
      //$listIsbn = $data->likeItems->likeItem['ocn'];
      $datecleaner = array("[", "]", "Â", "©", ".");
	  $titlecleaner = "/";
	  foreach($data->likeItems->children() as $items){
	    echo "<div class=\"rec_floater\">";
        echo "<b>Title: </b>" . str_replace($titlecleaner, "", $items['title'] . "<br/>\n");
		echo "<b>Author: </b>" . $items['author'] . "<br/>\n";
		echo "<b>Pub. Year: </b>" . str_replace($datecleaner, "", $items['pubDate'] . "<br />\n");
		echo "<b>OCLC #: </b>" . $items['ocn'] . "<br />\n";
		echo "</div>";
      }
 }
?>