<?php
$arrWatch = array('watchDarkly','watchCosmo','watchSlate','watchSuperHero','watchFlatly');
$arrTheme = array('bootable');
$f = new ManageFiles();
$arrView = $f->findConf(1);
$themeView = $arrView["view"];
$themeItem = $arrView["item"];
if(count($arrWatch)==$themeItem){
   $themeView = 2;
}
$arrWatch = array_merge($arrWatch, $arrTheme);
$strHead =  "<link rel='stylesheet' href='dist/css/bootstrap.min.css' type='text/css'>";
if($themeView==1){
   $strHead .=  "<link rel='stylesheet' href='dist/less/".$arrWatch[$themeItem]."/bootstrap.min.css' type='text/css'>";
   $strHead .=  "<link rel='stylesheet' href='dist/less/".$arrWatch[$themeItem]."/variables.less' type='text/css'>";
}
if($themeView==2){
   $strHead .=  "<link rel='stylesheet' href='dist/themes/bootable/css/styles.css' type='text/css'>";
}
$strHead .=  "<link rel='stylesheet' href='dist/DataTables/plug-ins/integration/bootstrap/3/dataTables.bootstrap.css' type='text/css'>";
$strHead .=  "<link rel='stylesheet' href='dist/DataTables/extensions/TableTools/css/dataTables.tableTools.min.css' type='text/css'>";
$strHead .=  "<link rel='stylesheet' href='css/sqlCatalog.css' type='text/css'>";

$strHead .=  "<script type='text/javascript' src='js/jquery.js'></script>";
$strHead .=  "<script type='text/javascript' src='dist/selectr/dist/selectr.js'></script>";
$strHead .=  "<script type='text/javascript' src='dist/js/bootstrap.min.js'></script>";
if($themeView==2){
   $strHead .=  "<script type='text/javascript' src='dist/themes/bootable/js/scripts.js'></script>";
}
$strHead .=  "<script type='text/javascript' src='dist/bootbox/bootbox.min.js'></script>";
$strHead .=  "<script type='text/javascript' src='dist/DataTables/media/js/jquery.dataTables.min.js'></script>";
$strHead .=  "<script type='text/javascript' src='dist/DataTables/extensions/TableTools/js/dataTables.tableTools.js'></script>";
$strHead .=  "<script type='text/javascript' src='dist/DataTables/plug-ins/integration/bootstrap/3/dataTables.bootstrap.min.js'></script>";
$strHead .=  "<script type='text/javascript' src='js/sqlCatalog.js'></script>";
$strHead .= "<script>var msg1 = '".MSG_1."'; var msg2 = '".MSG_2."';</script>";
