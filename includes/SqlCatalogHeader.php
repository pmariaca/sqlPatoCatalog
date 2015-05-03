<?php

$arrWatch = array('watchDarkly', 'watchCosmo', 'watchSlate', 'watchSuperHero');
// no sirve el modal = , 'watchSpacelab',
// letras en help no se ven watchCosmo,, 'watchFlatly'
$arrTheme = array('bootable');
$f = new ManageFiles();
$arrView = $f->findConf(1);
$themeView = $arrView["view"];
$themeItem = $arrView["item"];
if(count($arrWatch) == $themeItem){
   $themeView = 2;
}
//$themeView = 0;
//$themeItem = 0;
$strHead = "";
$themeCss = "";
$themeCss1 = "";
$themeJs = "";
$arrWatch = array_merge($arrWatch, $arrTheme);
if($themeView == 1){
   $themeCss = "dist/less/" . $arrWatch[$themeItem] . "/bootstrap.min.css";
   $themeCss1 = "dist/less/" . $arrWatch[$themeItem] . "/variables.less";
}
if($themeView == 2){
   $themeCss = "dist/themes/bootable/css/styles.css";
   $themeJs = "dist/themes/bootable/js/scripts.js";
}

$arrLink = array(
    "dist/css/bootstrap.min.css",
    $themeCss,
    $themeCss1,
    "dist/DataTables/plug-ins/integration/bootstrap/3/dataTables.bootstrap.css",
    "css/sqlCatalog.css",
    //"css/sqlCatalog_grid.css",
    "css/sqlResult.css"
);
$arrScript = array(
    "js/jquery.js",
    //"js/asuggest/jquery.a-tools-1.4.1.js",
    //"js/asuggest/jquery.asuggest.js",
    "dist/selectr/dist/selectr.js",
    "dist/js/bootstrap.min.js",
    "dist/bootbox/bootbox.min.js",
    $themeJs,
    "dist/DataTables/media/js/jquery.dataTables.min.js",
    "dist/DataTables/extensions/TableTools/js/dataTables.tableTools.js",
    "dist/DataTables/plug-ins/integration/bootstrap/3/dataTables.bootstrap.min.js",
    "js/sqlCatalog.js",
    "js/sqlResult.js"
);
$arrCte = array(
    'MSG_1' => MSG_1,
    'MSG_2' => MSG_2,
    'SHOW_MORE_TABS' => SHOW_MORE_TABS,
    'HIDE_MORE_TABS' => HIDE_MORE_TABS,
    'SHOW_PROCESSLIST' => SHOW_PROCESSLIST,
    'HIDE_PROCESSLIST' => HIDE_PROCESSLIST
);

foreach($arrLink as $css){
   if($css != ""){
      $strHead .= "<link rel='stylesheet' href='" . $css . "' type='text/css'>\n";
   }
}
foreach($arrScript as $js){
   if($js != ""){
      $strHead .= "<script type='text/javascript' src='" . $js . "'></script>\n";
   }
}
$strHead .= "<script>";
foreach($arrCte as $k => $v){
   $strHead .= "var " . $k . "='" . $v . "';\n";
}
$strHead .= "</script>";
