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
	$options = array('format' => 'json', 'services' => array('WorldCatDiscoveryAPI', 'refresh_token'));
	$wskey = new WSKey($key, $secret, $options);
	$accessToken = $wskey->getAccessTokenWithClientCredentials($institution_id, $institution_id);
	
///////////////////////////////////////////////////////////
/////XML to JSON Function:  http://outlandish.com/blog/xml-to-json/
///////////////////////////////////////////////////////////
	
	function xmlToArray($xml, $options = array()) {
    $defaults = array(
        'namespaceSeparator' => ':',//you may want this to be something other than a colon
        'attributePrefix' => '@',   //to distinguish between attributes and nodes with the same name
        'alwaysArray' => array(),   //array of xml tag names which should always become arrays
        'autoArray' => true,        //only create arrays for tags which appear more than once
        'textContent' => '$',       //key used for the text content of elements
        'autoText' => true,         //skip textContent key if node has no attributes or child nodes
        'keySearch' => false,       //optional search and replace on tag and attribute names
        'keyReplace' => false       //replace values for above search values (as passed to str_replace())
    );
    $options = array_merge($defaults, $options);
    $namespaces = $xml->getDocNamespaces();
    $namespaces[''] = null; //add base (empty) namespace
 
    //get attributes from all namespaces
    $attributesArray = array();
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->attributes($namespace) as $attributeName => $attribute) {
            //replace characters in attribute name
            if ($options['keySearch']) $attributeName =
                    str_replace($options['keySearch'], $options['keyReplace'], $attributeName);
            $attributeKey = $options['attributePrefix']
                    . ($prefix ? $prefix . $options['namespaceSeparator'] : '')
                    . $attributeName;
            $attributesArray[$attributeKey] = (string)$attribute;
        }
    }
 
    //get child nodes from all namespaces
    $tagsArray = array();
    foreach ($namespaces as $prefix => $namespace) {
        foreach ($xml->children($namespace) as $childXml) {
            //recurse into child nodes
            $childArray = xmlToArray($childXml, $options);
            list($childTagName, $childProperties) = each($childArray);
 
            //replace characters in tag name
            if ($options['keySearch']) $childTagName =
                    str_replace($options['keySearch'], $options['keyReplace'], $childTagName);
            //add namespace prefix, if any
            if ($prefix) $childTagName = $prefix . $options['namespaceSeparator'] . $childTagName;
 
            if (!isset($tagsArray[$childTagName])) {
                //only entry with this key
                //test if tags of this type should always be arrays, no matter the element count
                $tagsArray[$childTagName] =
                        in_array($childTagName, $options['alwaysArray']) || !$options['autoArray']
                        ? array($childProperties) : $childProperties;
            } elseif (
                is_array($tagsArray[$childTagName]) && array_keys($tagsArray[$childTagName])
                === range(0, count($tagsArray[$childTagName]) - 1)
            ) {
                //key already exists and is integer indexed array
                $tagsArray[$childTagName][] = $childProperties;
            } else {
                //key exists so convert to integer indexed array with previous value in position 0
                $tagsArray[$childTagName] = array($tagsArray[$childTagName], $childProperties);
            }
        }
    }
 
    //get text content of node
    $textContentArray = array();
    $plainText = trim((string)$xml);
    if ($plainText !== '') $textContentArray[$options['textContent']] = $plainText;
 
    //stick it all together
    $propertiesArray = !$options['autoText'] || $attributesArray || $tagsArray || ($plainText === '')
            ? array_merge($attributesArray, $tagsArray, $textContentArray) : $plainText;
 
    //return node as array
    return array(
        $xml->getName() => $propertiesArray
    );
}
	$query = $_GET["num"];

	//$query = 287628;
	//$query = http_build_query($passedQuery, '', "&");
	$response_xml_data = file_get_contents("http://experimental.worldcat.org/recommender/MLT/" . $query . "?inst=YGM&count=10");
    if($response_xml_data)
    {
      // echo "read";
	 $data = simplexml_load_string($response_xml_data);
	 $arrayData = xmlToArray($data);
	 echo $newJSON_array = json_encode($arrayData);
	 
	 
	  // echo var_dump($response_xml_data);
	  
	  //print_r($data->likeItems->likeItem->ocn);
      //$listIsbn = $data->likeItems->likeItem['ocn'];
      // $datecleaner = array("[", "]", "Â", "©", ".");
	  // $titlecleaner = "/";
	  // foreach($data->likeItems->children() as $items){
	    // echo "<div class=\"rec_floater\">";
        // echo "<b>Title: </b>" . str_replace($titlecleaner, "", $items['title'] . "<br/>\n");
		// echo "<b>Author: </b>" . $items['author'] . "<br/>\n";
		// echo "<b>Pub. Year: </b>" . str_replace($datecleaner, "", $items['pubDate'] . "<br />\n");
		// echo "<b>OCLC #: </b>" . $items['ocn'] . "<br />\n";
		// echo "</div>";
      // }
 }
?>