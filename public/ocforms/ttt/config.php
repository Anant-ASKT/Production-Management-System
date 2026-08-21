<?php
// APPLICATION
define('APPLICATION', 'Catalog');

// HTTP
define('HTTP_SERVER', 'https://idistock.handknitindia.com/');

// DIR
define('DIR_OPENCART', '/home/uct7vdczl3wz/public_html/idistock.handknitindia.com/');
define('DIR_APPLICATION', DIR_OPENCART . 'catalog/');
define('DIR_SYSTEM', DIR_OPENCART . 'system/');
define('DIR_EXTENSION', DIR_OPENCART . 'extension/');
define('DIR_IMAGE', DIR_OPENCART . 'image/');
define('DIR_STORAGE', DIR_SYSTEM . 'storage6rp9fqfbvndv/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_PORT', '3306');

define('DB_REMOTEHOSTNAME', 'handknitindia.com');
define('DB_REMOTEUSERNAME', 'i9788889_z2tr1');
define('DB_REMOTEPASSWORD', 'S.dQrLz1VkRZFXxfVPS49');
define('DB_REMOTEDATABASE', 'i9788889_z2tr1');
define('DB_REMOTEPREFIX', 'yrbg_');

define('DB_HOSTNAME', '192.168.1.65');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'Airtel@12345');
define('DB_DATABASE', 'idistockoc');
$oc_rconn = mysqli_connect(DB_REMOTEHOSTNAME, DB_REMOTEUSERNAME, DB_REMOTEPASSWORD, DB_REMOTEDATABASE, DB_PORT);

if ($oc_rconn->connect_error) {
    die("Remote DB connection failed: " . $oc_rconn->connect_error);
}

$oc_conn = mysqli_connect(DB_HOSTNAME, DB_USERNAME, DB_PASSWORD, DB_DATABASE, DB_PORT);

if ($oc_conn->connect_error) {
    die("Local DB connection failed: " . $oc_conn->connect_error);
}