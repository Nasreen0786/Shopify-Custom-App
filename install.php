<?php
// hit this file to install app eg. https://<url>/install.php?shop=<shop-name>
// Set variables for our request
$shop = $_GET['shop'];
$api_key = "";
$scopes = "read_orders,write_products,read_themes,write_themes,read_script_tags,write_script_tags,read_customers";
$redirect_uri = "https://brandclever.in/diy-assist-app/generate_token.php";

// Build install/approval URL to redirect to
$install_url = "https://" . $shop . ".myshopify.com/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);

echo $install_url;
// Redirect
header("Location: " . $install_url);
die();