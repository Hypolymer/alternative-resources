   <?php
   
   require_once('vendor/autoload.php');

   $configFile = "appConfig.ini";
   $ini_array = parse_ini_file ($configFile);

   use OCLC\Auth\WSKey;
   use OCLC\Auth\AccessToken;
   use WorldCat\Discovery\Bib;

   $key = $ini_array["key"];
   $secret = $ini_array["secret"];
   $institution_id = $ini_array["institution_id"];
   $options = array('services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
   $wskey = new WSKey($key, $secret, $options);
   $accessToken = $wskey->getAccessTokenWithClientCredentials($institution_id, $institution_id);

   $bib = Bib::Find(586757123, $accessToken);

    
   if (is_a($bib, 'WorldCat\Discovery\Error')) {
       echo $bib->getErrorCode();
       echo $bib->getErrorMessage();
   #} else {
   #   $array = file_get_contents("http://xisbn.worldcat.org/webservices/xid/isbn/181516677?method=getEditions&format=xml&fl=*");
   #   echo $array;
   } else {
		// echo "json list of xID OCLC Num";
		// echo "</br><br/>";
		// $array = file_get_contents("http://xisbn.worldcat.org/webservices/xid/oclcnum/586757123?method=getVariants&format=json&fl=oclcnum");
		// echo $array;
		// echo "<br/><br/>";
		// echo "Now XML for ISXN";
		// echo "<br/></br>";

    $response_xml_data = file_get_contents("http://xisbn.worldcat.org/webservices/xid/isbn/9780439023528?method=getEditions&format=xml&fl=*");
    if($response_xml_data)
    {
      echo "read";
      $data = simplexml_load_string($response_xml_data);
      //var_dump($data);
      print_r($data->isbn);
      $listIsbn = $data->isbn;
      foreach($listIsbn as $item)
      {
        echo "Value: $item<br />\n";
      }
    }
		if ($xISBN_array == "") {
			echo "no results";
			}
		else {
        foreach ($xISBN_array as $item)
        {
          echo "Value: $item<br />\n";
        }
        echo "This ran </br>";
}
      }
//	  require 'getHoldings.php';
	 // holdingsFinder->getHoldings(9780439023528);
	  
?>
