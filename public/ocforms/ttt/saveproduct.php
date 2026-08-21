<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('config.php');
// DB connection (adjust if needed)
$oc_conn = new \mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

if ($oc_conn->connect_error) {
    die("DB connection failed: " . $oc_conn->connect_error);
}
// 2. Product Data
$language_id = 1;
$store_id = 0;
$name = $_POST["name"];
$description = $_POST["description"];
$model = $_POST["model"];
$price = $_POST["price"];
$quantity = $_POST["quantity"];
$tax_class_id = $_POST["tax_class_id"];
$status = $_POST["status"];
$category_id = $_POST["category_id"];
$image_path = 'catalog/products/woolen-pullover.jpg'; // Relative path inside "image/" folder

// 3. Insert into product table
$sql = "INSERT INTO " . DB_PREFIX . "product SET 
    model = '{$oc_conn->real_escape_string($model)}',
    price = '{$price}',
    quantity = '{$quantity}',
    status = '{$status}',
    tax_class_id = '{$tax_class_id}',
    image = '{$oc_conn->real_escape_string($image_path)}',
    date_available = NOW(),
    date_added = NOW(),
    date_modified = NOW()";
$oc_conn->query($sql);
$product_id = $oc_conn->insert_id;

if (!$product_id) {
    die("Error inserting product: " . $oc_conn->error);
}

// 4. Insert into product_description
$sql = "INSERT INTO " . DB_PREFIX . "product_description SET 
    product_id = {$product_id},
    language_id = {$language_id},
    name = '{$oc_conn->real_escape_string($name)}',
    description = '{$oc_conn->real_escape_string($description)}',
    meta_title = '{$oc_conn->real_escape_string($name)}'";

$oc_conn->query($sql);

// 5. Link to store
$oc_conn->query("INSERT INTO " . DB_PREFIX . "product_to_store SET 
    product_id = {$product_id}, 
    store_id = {$store_id}
");

// 6. Link to category
$oc_conn->query("INSERT INTO " . DB_PREFIX . "product_to_category SET 
    product_id = {$product_id}, 
    category_id = {$category_id}
");


echo "? Product inserted successfully with ID: {$product_id}";

