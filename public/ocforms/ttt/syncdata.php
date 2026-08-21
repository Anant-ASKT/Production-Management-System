<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
// CONFIGURATION: include OpenCart's main config
require_once('config.php');

// DB connection (adjust if needed)
$db = new \mysqli(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

// Error check
if ($db->connect_error) {
    die(json_encode(['success' => false, 'error' => 'DB connection failed: ' . $db->connect_error]));
}

// Set header
//header('Content-Type: application/json');

// Query master tables
$categories = [];
$result = $db->query("SELECT * FROM " . DB_PREFIX . "category_description");

while ($row = $result->fetch_assoc()) {

    $categories[] = $row;
}
$cleaned_categories = utf8ize($categories);
$json = json_encode(['success' => true, 'categories' => $cleaned_categories]);
if ($json === false) {
    echo "JSON Error: " . json_last_error_msg();
} else {
    echo $json;
}


function utf8ize($mixed) {
    if (is_array($mixed)) {
        foreach ($mixed as $key => $value) {
            $mixed[$key] = utf8ize($value);
        }
    } elseif (is_string($mixed)) {
        // Remove BOM and force UTF-8 conversion
        $mixed = preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $mixed);
        $mixed = mb_convert_encoding($mixed, 'UTF-8', 'UTF-8');
    }
    return $mixed;
}
