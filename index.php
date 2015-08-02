<?php
include_once ("includes/Catalog.php");

\Catalog\Catalog::registerAutoloader();

$app = new Catalog\Catalog($_REQUEST);
$app->run();
