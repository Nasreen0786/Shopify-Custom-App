<?php

include 'db/db.php';
 $query = "SELECT * FROM shopify_diy_assist WHERE id = 1";
    $connection = $conn->query($query);
    if($connection->num_rows>0){
        while($row = $connection->fetch_assoc()){
            $diy_id = $row['diy_assist_checkbox'];
        }
    }
if($diy_id == '1'){
    //echo "run";
$ch = curl_init();
$shopUrl = 'tester-assiste.myshopify.com';
$accessToken = '';
// Set the cURL options
curl_setopt($ch, CURLOPT_URL, "https://$shopUrl/admin/api/2021-07/products.json"); // Replace with the correct API version
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Content-Type: application/json",
    "X-Shopify-Access-Token: $accessToken"
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Execute cURL session and fetch data
$response = curl_exec($ch);
if ($response) {
    $products = json_decode($response, true)["products"];
    foreach ($products as $product) {
        // echo "<pre>";
        // print_r($product);
        // die;
        $product_id = $product['variants'][0]['product_id'];
        $product_name = $product['title'];
        $product_price = $product['variants'][0]['price'];
        $pro_id = $product['id'];
        $var_id = $product['variants'][0]['id'];

        // Check if the product_id already exists in the table
        $check_query = "SELECT * FROM product_details WHERE product_id = '$product_id'";
        $check_result = $conn->query($check_query);

       if ($check_result->num_rows > 0) {
        $fixed_price = 0; // Initialize $fixed_price before the loop
        $products_id=0;
        while ($row = $check_result->fetch_assoc()) {
            $product_name = $row['product_name'];
            $products_id = $row['product_id'];
            $fixed_price = $row['product_price']; // Update $fixed_price within the loop
        }
        $update_data = "UPDATE product_details SET product_name = '$product_name' WHERE product_id = '$product_id'";

        if ($conn->query($update_data)) {
            // echo "Data updated successfully\n";

            // Discount code
            $shopify_api_key = '';
            $shopify_api_secret = '';
            $shopify_store_url = 'https://tester-assiste.myshopify.com';
            $shopify_access_token = '';
            $query = "SELECT * FROM shopify_diy_assist WHERE id = 1";
            $connection = $conn->query($query);
            $total_amount = 500; // Assuming $total_amount is initially 500
            $discounted_total = $fixed_price; // Initialize the discounted total

            if ($connection->num_rows > 0) {
                while ($row = $connection->fetch_assoc()) {
                    $diy_discount_type_value = $row['discount_type'];
                    $discount_price = $row['discount_price'];

                    if ($diy_discount_type_value == 'Decimal') {
                        // If the discount type is Decimal, subtract the discount directly
                        $discounted_total -= $discount_price;
                    } else {
                        // If the discount type is Percentage, calculate the discount and subtract it
                        $discount_percentage = floatval($discount_price) / 100;
                        $discounted_amount = $fixed_price * $discount_percentage;
                        $discounted_total -= $discounted_amount;
                    }

                    $row_id = $row['id'];
                }
            }

            $discountedPrice = number_format($discounted_total, 2);
            $headers = array(
                "Content-Type: application/json",
                "X-Shopify-Access-Token: $shopify_access_token",
            );

            $api_url = "$shopify_store_url/admin/api/2021-07/products.json";

            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if ($response === false) {
                die('Error fetching data from Shopify API: ' . curl_error($ch));
            } else {
                $products = json_decode($response, true);
                    $product['variants'][0]['price'] = $discountedPrice;
                    $product['variants'][0]['compare_at_price'] = $fixed_price; // Set compare_at_price

                    $update_api_url = "$shopify_store_url/admin/api/2021-07/products/{$product['id']}.json";

                    $data = array(
                        'product' => array(
                            'id' => $pro_id,
                            'variants' => array(
                                array(
                                    'id' => $var_id,
                                    'price' => $discountedPrice,
                                    'compare_at_price' => $fixed_price, // Set compare_at_price
                                ),
                            ),
                        ),
                    );
                    $ch = curl_init($update_api_url);
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $update_response = curl_exec($ch);
                    curl_close($ch);

                    if ($update_response === false) {
                        echo 'Error updating product: ' . curl_error($ch) . "\n";
                    } else {
                        // echo '<br>Product updated: ' . $product['title'] . "\n";
                        // header("location:javascript://history.go(-1)");
                    }
                // }
            }
        } else {
            echo "Error updating data: " . $conn->error . "\n";
        }
    
}else {
        $insert_data = "INSERT INTO product_details (product_id, product_name, product_price) VALUES ('$product_id', '$product_name', '$product_price')";
        if ($conn->query($insert_data)) {
            //Discount code   
            $shopify_api_secret = '';
            $shopify_store_url = 'https://tester-assiste.myshopify.com';
            $shopify_access_token = '';
            $query = "SELECT * FROM shopify_diy_assist WHERE id = 1";
            $connection = $conn->query($query);
            $total_amount = 500; // Assuming $total_amount is initially 500
            $discounted_total = $fixed_price; // Initialize the discounted total

            if ($connection->num_rows > 0) {
                while ($row = $connection->fetch_assoc()) {
                    $diy_discount_type_value = $row['discount_type'];
                    $discount_price = $row['discount_price'];

                    if ($diy_discount_type_value == 'Decimal') {
                        // If the discount type is Decimal, subtract the discount directly
                        $discounted_total -= $discount_price;
                    } else {
                        // If the discount type is Percentage, calculate the discount and subtract it
                        $discount_percentage = floatval($discount_price) / 100;
                        $discounted_amount = $fixed_price * $discount_percentage;
                        $discounted_total -= $discounted_amount;
                    }

                    $row_id = $row['id'];
                }
            }

            $discountedPrice = number_format($discounted_total, 2);
            $headers = array(
                "Content-Type: application/json",
                "X-Shopify-Access-Token: $shopify_access_token",
            );

            $api_url = "$shopify_store_url/admin/api/2021-07/products.json";

            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $response = curl_exec($ch);

            if ($response === false) {
                die('Error fetching data from Shopify API: ' . curl_error($ch));
            } else {
                $products = json_decode($response, true);
                $product['variants'][0]['price'] = $discountedPrice;
                $product['variants'][0]['compare_at_price'] = $fixed_price; // Set compare_at_price

                $update_api_url = "$shopify_store_url/admin/api/2021-07/products/{$product['id']}.json";

                $data = array(
                    'product' => array(
                        'id' => $pro_id,
                        'variants' => array(
                            array(
                                'id' => $var_id,
                                'price' => $discountedPrice,
                                'compare_at_price' => $fixed_price, // Set compare_at_price
                            ),
                        ),
                    ),
                );
                $ch = curl_init($update_api_url);
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $update_response = curl_exec($ch);
                curl_close($ch);

                if ($update_response === false) {
                    echo 'Error updating product: ' . curl_error($ch) . "\n";
                } else {
                    // echo '<br>Product updated: ' . $product['title'] . "\n";
                    // header("location:javascript://history.go(-1)");
                }
            }
            //end discount code
            } else {
                echo "Error inserting data: " . $conn->error;
            }
        }

    }
} else {
    // Handle the case where there is no response or an error occurred
    echo 'Error fetching data from Shopify.';
}

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    die();
}

// Close cURL session
curl_close($ch);
}else{
    //echo "not run";
}