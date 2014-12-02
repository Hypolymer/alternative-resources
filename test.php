   <?php
   
   require_once('vendor/autoload.php');

   $filename = "appConfig.ini";
   $ini_array = parse_ini_file ($filename);
   
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
		echo "json list of xID OCLC Num";
		echo "</br><br/>";
		$array = file_get_contents("http://xisbn.worldcat.org/webservices/xid/oclcnum/586757123?method=getVariants&format=json&fl=oclcnum");
		echo $array;
		echo "<br/><br/>";
		echo "json list of xISBN";
		echo "<br/></br>";
		$xISBN_array = array(file_get_contents("http://xisbn.worldcat.org/webservices/xid/isbn/9780439023528?method=getEditions&format=xml&fl=*"));
		if ($xISBN_array == "") {
			echo "no results";
			}
		else {
        while (list(, $value) = each($xISBN_array)) {
			echo $value;
}
      }
   }
?>
