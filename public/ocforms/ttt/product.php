<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');
require_once('config.php');
require('header.php');
if($companyid!=""){


function getOptions($mysqli, $table, $id_col, $name_col, $order_by = '') {
    $sql = "SELECT $id_col, $name_col FROM $table";
    if ($order_by) $sql .= " ORDER BY $order_by";
    $result = $mysqli->query($sql);
    $options = '';
    while ($row = $result->fetch_assoc()) {
        $options .= "<option value='{$row[$id_col]}'>{$row[$name_col]}</option>";
    }
    return $options;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Insert Product with Image</title>
</head>
<body>
    <h2>Add New Product</h2>
    <form method="post">
    <label>Language:</label>
    <select name="language_id"><?= getOptions($oc_conn, DB_PREFIX . 'language', 'language_id', 'name') ?></select><br>

    <label>Store:</label>
    <select name="store_id"><?= getOptions($oc_conn, DB_PREFIX . 'store', 'store_id', 'name') ?></select><br>

    <label>Name:</label><input type="text" name="name"><br>
    <label>Description:</label><textarea name="description"></textarea><br>
    <label>Model:</label><input type="text" name="model"><br>
    <label>Price:</label><input type="text" name="price"><br>
    <label>Quantity:</label><input type="number" name="quantity"><br>

    <label>Tax Class:</label>
    <select name="tax_class_id"><?= getOptions($oc_conn, DB_PREFIX . 'tax_class', 'tax_class_id', 'title') ?></select><br>

    <label>Status:</label>
    <select name="status"><option value="1">Enabled</option><option value="0">Disabled</option></select><br>

    <label>Category:</label>
    <select name="category_id"><?= getOptions($oc_conn, DB_PREFIX . 'category_description', 'category_id', 'name') ?></select><br>

    <label>Image Path (e.g. catalog/demo/product.jpg):</label>
    <input type="text" name="image_path"><br>

    <button type="submit">Add Product</button>
</form>
</body>
</html>
