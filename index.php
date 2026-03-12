<?php
 include 'inc/functions.php';
include 'db/db.php';



$requests = $_GET;
$hmac = $_GET['hmac'];
$serializeArray = serialize($requests);
$requests = array_diff_key($requests, array('hmac' => ''));
krsort($requests);

$token = "";
$shop = "tester-assiste";
$image = '';
$title = '';

$userList = shopify_call($token, $shop, "/admin/api/2023-07/users.json", array(), 'GET');
// echo "<pre>";
// print_r($userList);
//  die();
$userList = json_decode($userList['response'], JSON_PRETTY_PRINT);
$users_id = $userList = ['users'][0]['id'];


$user = shopify_call($token, $shop, "/admin/api/2023-07/users.json", array("users_id"=>$users_id), 'GET');
$user = json_decode($user['response'], JSON_PRETTY_PRINT);
foreach($user as $users){
   foreach($users as $key => $values ){
       print_r($values);
       echo "end here";
   }
}


$collectionlist = shopify_call($token, $shop, "/admin/api/2023-07/custom_collections.json", array(), 'GET');  // products.json  show all collection 
$collectionlist = json_decode($collectionlist['response'], JSON_PRETTY_PRINT);
$collection_id = $collectionlist['custom_collection'][0]['id'];
echo $collection_id;
$collects = shopify_call($token, $shop, "/admin/api/2023-07/collects.json", array("collection_id"=>$collection_id), 'GET');
$collects = json_decode($collects['response'], JSON_PRETTY_PRINT);
foreach ($collects as $collect) {
	foreach ($collect as $key => $value) {
	    $products = shopify_call($token, $shop, "/admin/api/2023-07/products/" . $value['product_id'] . ".json", array(), 'GET');
		$products = json_decode($products['response'], JSON_PRETTY_PRINT);
		$images = shopify_call($token, $shop, "/admin/api/2023-07/products/" . $value['product_id'] . "/images.json", array(), 'GET');
		$images = json_decode($images['response'], JSON_PRETTY_PRINT);
		$image = $images['images'][0]['src'];
		$title = $products['product']['title'];
	}
} 
$theme = shopify_call($token, $shop, "/admin/api/2023-07/themes.json", array(), 'GET');
$theme = json_decode($theme['response'], JSON_PRETTY_PRINT);
foreach ($theme as $cur_theme) {
	foreach ($cur_theme as $keys => $values) {
		if ($values['role'] === 'main') {
			$array = array(
				'asset'=>array(
					'key' => 'templates/index.liquid',
					'value' => '<h1> Hello Here Is Simple Interface </h1>'
				)
			);
			$assets = shopify_call($token, $shop, "/admin/api/2023-07/themes/".$values['id']. "/assets.json", $array, 'PUT');
			$assets = json_decode($assets['response'], JSON_PRETTY_PRINT);
		}
	}
}
$script_array = array(
					'script_tag' => array(
						'event' => 'onload',
						'src' => 'https://brandclever.in/diy-assist-app/simple-interface/scripts/script.js'
					)
				);
$scriptTag = shopify_call($token, $shop, "/admin/api/2023-07/script_tags.json", $script_array, 'POST');
$scriptTag = json_decode($scriptTag['response'], JSON_PRETTY_PRINT);
$lib_array = array(
                    'script_tag' => array(
                        'event' => 'onload',
                        'src' => 'https://code.jquery.com/jquery-1.7.1.min.js'
                    )
            );
$scriptLib = shopify_call($token, $shop, "/admin/api/2023-07/script_tags.json", $lib_array, 'POST');
$scriptLib = json_decode($scriptLib['response'], JSON_PRETTY_PRINT);
$liquid_array = array(
                    'script_tag' => array(
                        'event' => 'onload',
                        'src' => 'https://brandclever.in/diy-assist-app/user_product.js.liquid'
                    )
            );
$liquidFile = shopify_call($token, $shop, "/admin/api/2023-07/script_tags.json", $liquid_array, 'POST');
$liquidFile = json_decode($liquidFile['response'], JSON_PRETTY_PRINT);
$servername = "localhost";
$username = "shopper_app";
$password = "";
$db = "shopper_app";
$conn = mysqli_connect($servername, $username, $password, $db);
$query = "SELECT * FROM shopify_diy_assist WHERE id = 1";
$connection = $conn->query($query);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $diy_assist = $_POST['diy-assist-checkbox'];
    $diy_publishkey = $_POST['diy-assist-publishkey']; 
    $diy_securitytoken = $_POST['diy-assist-securitytoken'];
    $diy_discount_type = $_POST['discount_type'];
    $diy_discount_price = $_POST['discount_price'];
    $diy_apply_dis_all = $_POST['apply_dis_all'];
    $diy_discount_apply_specific_pro = $_POST['discount_apply_specific_pro'];
    $query = "SELECT * FROM shopify_diy_assist WHERE id = 1";
	$connection = $conn->query($query);
	if($connection->num_rows>0){
		$update_data = "UPDATE shopify_diy_assist SET diy_assist_checkbox='$diy_assist', diy_assist_publishkey='$diy_publishkey', diy_assist_securitytoken='$diy_securitytoken', discount_type='$diy_discount_type', discount_price='$diy_discount_price', apply_dis_all='$diy_apply_dis_all', discount_apply_specific_pro='$diy_discount_apply_specific_pro' WHERE id = 1";
		if ($conn->query($update_data)){
			 // header("Location: javascript:history.go(-1)");
			 header("Location: https://brandclever.in/diy-assist-app/create_section.php");
		}else{
			echo "not updated";
		}
	}else{
		$data = "INSERT INTO shopify_diy_assist (diy_assist_checkbox,diy_assist_publishkey,diy_assist_securitytoken,discount_type,	discount_price, apply_dis_all, discount_apply_specific_pro) VALUES ('$diy_assist','$diy_publishkey','$diy_securitytoken','$diy_discount_type','$diy_discount_price','$diy_apply_dis_all','$diy_discount_apply_specific_pro')";
	    if ($conn->query($data)) {
	    	// header("Location: javascript:history.go(-1)");
	    	header("Location: https://brandclever.in/diy-assist-app/create_section.php");
	    } else {
	        echo "Error: " . $data . "<br>" . $conn->error;
	    }
	}
}
if($connection->num_rows>0){
	while($row = $connection->fetch_assoc()){
		$diy_assist_value = $row['diy_assist_checkbox'];
	    $diy_publishkey_value = $row['diy_assist_publishkey'];
	    $diy_securitytoken_value = $row['diy_assist_securitytoken'];
	    $diy_discount_type_value = $row['discount_type'];
	    $diy_discount_price_value = $row['discount_price'];
	    $row_id = $row['id'];
	}
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>DIY Assist Form</title>
    <link rel="stylesheet" href="style.css">
    <style type="text/css">
    	.inpt_assit input{
		    padding: 10px 0px 11px 9px;
		    border-radius: 5px;
		    border: 1px solid #dddada;
		    width: 100%;
		}
			.select_products_discount h3 {
				    margin: 10px 0px 10px;
				    text-align: center;
				    font-size: 24px;
				}
		#discount-type {
		    padding: 10px 0px 11px 9px;
		    border-radius: 5px;
		    border: 1px solid #dddada;
		    width: 100%;
		}

		.create_form label {
			    font-size: 15px;
			    width: 100%;
			    max-width: 150px;
			}
			.create_form label {
			    font-size: 15px;
			    width: 100%;
			    max-width: 150px;
			}



.inpt_assit input[type="checkbox"] {
    width: 50px;
}
.create_form {
    width: 80%;
    margin: auto;
}

#select_product {
    padding: 10px 0px 10px 9px;
    border: 1px solid #dddada;
    border-radius: 5px;
    font-size: 11px;
    width: 100%;
}
.submit_btn input {
    background: #313131;
    color: #fff;
    width: 100% !important;
    border-radius: 5px;
    padding: 13px;
    border: 1px solid;
}
.create_form  label {
    font-size: 15px;
}
.inpt_assit {
    width: 100%;
    padding: 12px 0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
#selectedProduct {
    min-height: 110px;
}
.select_assist {
    width: 100%;
    padding: 12px 0;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.aplly_all_divv {
    text-align: center;
    font-size: 18px;
    margin: 5px 0px;
}

#apply_selected {
    margin-left: 20px;
}
.hidden {
      display: none;
    }
    </style>
</head>
<body>
	<div class="create_form">
	    <form method="POST">
	    	<div class="inpt_diy">
		        <label for="diy-assist-checkbox">DIY Assist Checkbox:</label>
		        <input type="checkbox" name="diy-assist-checkbox" value="1" <?php if ($diy_assist_value == 1) echo "checked"; ?>>
	       </div>
	        <div class="inpt_assit">
	          <label for="diy-assist-publishkey">DIY Assist Publish Key:</label>
	          <input type="text" name="diy-assist-publishkey" value="<?php echo $diy_publishkey_value; ?>">
	         </div>
	         <div class="inpt_assit">
	          <label for="diy-assist-securitytoken">DIY Assist Security Token:</label>
	          <input type="text" name="diy-assist-securitytoken" value="<?php echo $diy_securitytoken_value; ?>">
	        </div>
	        <div class="select_assist">
	         <label for="diy-assist-publishkey">Diyassist type:</label>
				<select name="discount_type" id="discount-type" onchange="validateInput()">
				  
				  <?php if($diy_discount_type_value != ''){?>
				  <option value="<?php echo $diy_discount_type_value; ?>"><?php echo $diy_discount_type_value; ?></option>
				<?php }else{?>
					<option value="">Select any one</option>
					<?php
				}?>
				  <option value="Decimal">Decimal</option>
				  <option value="Percentage">Percentage</option>
				</select>
			</div>
			<div class="inpt_assit">
			 <label for="diy-assist-publishkey">DIY Assist credit ($):</label>
				<input type="text" id="discount-price" name="discount_price" value="<?php echo $diy_discount_price_value; ?>"></div>
			<div class="select_products_discount">
				<h3>Select Products for add discount</h3>
				<div class="aplly_all_divv">
				<input type ="checkbox" name="check_all" id="apply_all" value="1">Apply for all
				<input type ="checkbox" name="check_all" id="apply_selected" value="1">Select Products
			</div>
			<!-- <div style="display:flex;"> -->
				<div class="inpt_assit hidden" id="apply_for_all">
				 <label for="diy-assist-publishkey">Apply for all Products</label>
					<input type="checkbox" name="apply_dis_all" value="1">
				</div>
				<div class="inpt_assit hidden" id="apply_for_specific">
				 <label for="diy-assist-publishkey">Select Specific Product</label><br>
				 <select id="select_product">
	    			<option value="">Select Product</option>
				 		<?php
					  $get_query = "SELECT product_name FROM product_details";

					    // Execute the query
					    $get_connection = $conn->query($get_query);

					    if ($get_connection->num_rows > 0) {
					        while ($row = $get_connection->fetch_assoc()) {
					            $product_name = $row['product_name'];
					            ?>
						  <option value="<?php echo $product_name; ?>"><?php echo $product_name; ?></option>
			            <?php
					        }
					    }

					    ?>
					</select>
					<textarea id="selectedProduct" name="discount_apply_specific_pro" cols="100" readonly></textarea>
				</div>
			<!-- </div> -->
			</div>
				<div class="submit_btn">
	        <input type="submit" name="submit" value="Submit">
	    </div>
	    </form>
	</div>
</body>
<script>
function validateInput() {
    var selectedOption = document.getElementById("discount-type").value;
    var inputField = document.getElementById("discount-price");
    if (selectedOption === "Decimal") {
        inputField.setAttribute("pattern", "^\\d+(\\.\\d{1,2})?$");
        inputField.setAttribute("title", "Enter a valid decimal number with up to two decimal places");
    } else if (selectedOption === "Percentage") {
        // Allow valid percentage values
        inputField.setAttribute("pattern", "^[0-9]+%?$");
        inputField.setAttribute("title", "Enter a valid percentage (e.g., 10% or 10)");
    }
    inputField.value = "";
}

// Selected products show in field

var selectElement = document.getElementById("select_product");
  var inputElement = document.getElementById("selectedProduct");
  var selectedOptions = [];
  selectElement.addEventListener("change", function() {
    var selectedOption = selectElement.options[selectElement.selectedIndex];
    if (selectedOption && selectedOption.value !== "") {
      if (!selectedOptions.includes(selectedOption.text)) {
        selectedOptions.push(selectedOption.text);
        inputElement.value = selectedOptions.join(", ");
      }
    }
  });



  // Function to hide an element by ID
  function hideElement(id) {
    var element = document.getElementById(id);
    if (element) {
      element.classList.add('hidden');
    }
  }

  // Function to show an element by ID
  function showElement(id) {
    var element = document.getElementById(id);
    if (element) {
      element.classList.remove('hidden');
    }
  }

  // Hide both elements initially on page load
  hideElement('apply_for_all');
  hideElement('apply_for_specific');

  // Add event listeners to the checkboxes
  var applyAllCheckbox = document.getElementById('apply_all');
  var applySelectedCheckbox = document.getElementById('apply_selected');

  applyAllCheckbox.addEventListener('change', function() {
    if (applyAllCheckbox.checked) {
      // If "Apply for all" is checked, show its content and hide "Select Products"
    	applySelectedCheckbox.checked = false;
      showElement('apply_for_all');
      hideElement('apply_for_specific');
    } else {
      // If "Apply for all" is unchecked, hide its content
      hideElement('apply_for_all');
    }
  });

  applySelectedCheckbox.addEventListener('change', function() {
    if (applySelectedCheckbox.checked) {
      // If "Select Products" is checked, show its content and hide "Apply for all"
     applyAllCheckbox.checked = false;
      showElement('apply_for_specific');
      hideElement('apply_for_all');
    } else {
      // If "Select Products" is unchecked, hide its content
      hideElement('apply_for_specific');
    }
  });
</script>
</html>