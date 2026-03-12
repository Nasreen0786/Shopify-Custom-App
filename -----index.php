<?php

// Get our helper functions
require_once("inc/functions.php");

$requests = $_GET;
$hmac = $_GET['hmac'];
$serializeArray = serialize($requests);
$requests = array_diff_key($requests, array('hmac' => ''));
krsort($requests);

echo "<pre>"; print_r($requests); die('bintu');
$token = "";
$shop = "simple-interface";

// Product title and image
$image = '';
$title = '';

//admin/api/2021-04/users.json
$userList = shopify_call($token, $shop, "/admin/api/2021-04/users.json", array(), 'GET');
echo "<pre>"; print_r($userList); echo "Bawas";
$userList = json_decode($userList['response'], JSON_PRETTY_PRINT);




$users_id = $userList = ['users'][0]['id'];
echo $users_id;
$user = shopify_call($token, $shop, "/admin/api/2021-04/users.json", array("users_id"=>$users_id), 'GET');
$user = json_decode($user['response'], JSON_PRETTY_PRINT);

foreach($user as $users){
   foreach($users as $key => $values ){
       print_r($values);
       echo "end here";
   }
}



$collectionlist = shopify_call($token, $shop, "/admin/api/2021-04/custom_collections.json", array(), 'GET');  // products.json  show all collection 
$collectionlist = json_decode($collectionlist['response'], JSON_PRETTY_PRINT);
$collection_id = $collectionlist['custom_collection'][0]['id'];
echo $collection_id;
$collects = shopify_call($token, $shop, "/admin/api/2021-04/collects.json", array("collection_id"=>$collection_id), 'GET');
$collects = json_decode($collects['response'], JSON_PRETTY_PRINT);
    // echo "<pre>";
     // print_r($collects);
     // echo "sachin here";
foreach ($collects as $collect) {

	foreach ($collect as $key => $value) {
	    
		$products = shopify_call($token, $shop, "/admin/api/2021-04/products/" . $value['product_id'] . ".json", array(), 'GET');
		$products = json_decode($products['response'], JSON_PRETTY_PRINT);

		$images = shopify_call($token, $shop, "/admin/api/2021-04/products/" . $value['product_id'] . "/images.json", array(), 'GET');
		$images = json_decode($images['response'], JSON_PRETTY_PRINT);

		$image = $images['images'][0]['src'];
		$title = $products['product']['title'];
        //echo "<pre>";
          //  print_r($products); // get prducts 
          //  echo "sachin here";
	//	echo "Product Title : " ; echo $products['product']['title'] . '<br />';
		//echo "Collection Title : " ;  echo $value['title'] . '<br />';
	}
} 

$theme = shopify_call($token, $shop, "/admin/api/2021-04/themes.json", array(), 'GET');
//$theme = shopify_call($token, $shop, "/admin/api/2021-04/themes.json", array(), 'GET');
//echo "<pre>";
//print_r($theme);
$theme = json_decode($theme['response'], JSON_PRETTY_PRINT);

foreach ($theme as $cur_theme) {
	foreach ($cur_theme as $keys => $values) {
	
	    
    //print_r($values); 
	    	    
		if ($values['role'] === 'main') {
			echo "Theme ID : ". $values['id'] . "<br />";
			echo "Theme Name : ". $values['name'] . "<br />";

			$array = array(
				'asset'=>array(
					'key' => 'templates/index.liquid',
					'value' => '<h1> Hello Here Is Simple Interface </h1>'
				)
			);

			$assets = shopify_call($token, $shop, "/admin/api/2021-04/themes/".$values['id']. "/assets.json", $array, 'PUT');
			$assets = json_decode($assets['response'], JSON_PRETTY_PRINT);
		//	print_r($assets);echo 123;
		}
	}
}

$script_array = array(
					'script_tag' => array(
						'event' => 'onload',
						'src' => 'https://developer.dbuglab.com/shopify-app/simple-interface/scripts/script.js'
					)
				);
$scriptTag = shopify_call($token, $shop, "/admin/api/2021-04/script_tags.json", $script_array, 'POST');
$scriptTag = json_decode($scriptTag['response'], JSON_PRETTY_PRINT);
//print_r($scriptTag);
//echo "sachin here";


$lib_array = array(
                    'script_tag' => array(
                        'event' => 'onload',
                        'src' => 'https://code.jquery.com/jquery-1.7.1.min.js'
                    )
            );
$scriptLib = shopify_call($token, $shop, "/admin/api/2021-04/script_tags.json", $lib_array, 'POST');
$scriptLib = json_decode($scriptLib['response'], JSON_PRETTY_PRINT);


$liquid_array = array(
                    'script_tag' => array(
                        'event' => 'onload',
                        'src' => 'https://developer.dbuglab.com/shopify-app/simple-interface/user_product.js.liquid'
                    )
            );
$liquidFile = shopify_call($token, $shop, "/admin/api/2021-04/script_tags.json", $liquid_array, 'POST');
$liquidFile = json_decode($liquidFile['response'], JSON_PRETTY_PRINT);


print_r($liquidFile);
echo "bk sachin";


?>

<!DOCTYPE html>
<html>
<head>
	<title> Shopify App </title>
</head>
<body>
	<h1>Shopify App for Products</h1>
	<img src="<?php echo $image; ?>" alt="Product Image" style="width: 250px;">
	<p></p><?php echo $title; ?></p>

</body>
</html>