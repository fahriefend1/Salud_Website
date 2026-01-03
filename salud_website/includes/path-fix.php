<?php
// path-fix.php
define('ROOT_PATH', dirname(__FILE__));
define('BASE_PATH', 'http://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . '/');

function asset($path) {
    return BASE_PATH . $path;
}
?>