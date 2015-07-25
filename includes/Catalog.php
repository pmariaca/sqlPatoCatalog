<?php
/**
 * @copyright Copyright (c) Patricia Mariaca Hajducek
 * @license http://opensource.org/licenses/MIT
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) Patricia Mariaca Hajducek (axolote14)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */
namespace Catalog;
define('PATH_SQLCATALOG', str_replace('\\', '/', realpath(substr(dirname(__FILE__), 0, 0 - strlen('includes')))));
include_once (PATH_SQLCATALOG . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'lang' . DIRECTORY_SEPARATOR . 'lang.php');
include_once ("CustomError.php");
include_once ("view/Page.php");
include_once ("ManageFiles.php");
include_once ("CatalogXml.php");
include_once ("CatalogDb.php");

if(!empty($_REQUEST)){
   $o = new Catalog($_REQUEST);
   $o->findTodo();  
}
/* * ************************************************************************************* */

/**
 * 
 *
 * @author Patricia Mariaca Hajducek (axolote14)
 * @version 1.1
 */
class Catalog {

   private $_request = array();
   private $_srv = "";
   private $_usr = "";
   private $_pwd = "";
   private $_page;

   /**
    * 
    * @param array $request
    */
   function __construct($request=array())
   {
      $this->_request = $request;
      if(isset($this->_request['srv']) && trim($this->_request['srv']) != ""){
         $this->_srv = $this->_request['srv'];
      }
      if(isset($this->_request['usr']) && trim($this->_request['usr']) != ""){
         $this->_usr = $this->_request['usr'];
      }
      if(isset($this->_request['pwd']) && trim($this->_request['pwd']) != ""){
         $this->_pwd = $this->_request['pwd'];
      }
      if(empty($request)){
         $this->constructPage();
      }
   }

   public function renderPage()
   {
      echo $this->_page->renderPage();
   }
   
   private function constructPage()
   {
      $f = new ManageFiles();
      $arrHost = $f->findConfHost();
      $iniXml = new CatalogXml();

      ob_clean();
      $this->_page = new View\Page($f->findConfView());
      $this->_page->setPage(array('flg'=>$arrHost['flg'], 'srv'=>$arrHost[0]['srv'], 'usr'=>$arrHost[0]['user']), $iniXml->readCatalog());
   }

   /**
    * 
    * @return array
    */
   public function findTodo()
   {
      if(isset($this->_request['go'])){
         if($this->_request['go'] == 'db'){
            $this->requestDb();
            
         }elseif($this->_request['go'] == 'xml'){
            $this->requestXml();
            
         }elseif($this->_request['go'] == 'vw'){
            $this->requestView();
         }
      }
   }

   /**
    * search databases
    * @return array
    */
   private function requestDb()
   {
      if($this->_request['type'] == 'saveSrv'){
         $save = false;
         if(isset($this->_request['itemSrv']) && isset($this->_request['itemUsr']) && isset($this->_request['itemPass'])){
            $conn = new CatalogDb();
            $save = $conn->testConnection($this->_request['itemSrv'], $this->_request['itemUsr'], $this->_request['itemPass']);
            
         }else{
            $save = true;
         }
         if($save===true){
            $f = new ManageFiles();
            $f->saveConfHost($this->_request);
         }
      }else{
         $this->jsonRequestDb();
      }
   }

   /**
    * 
    */
   private function jsonRequestDb()
   {
      $selectDb = "";
      if(isset($this->_request['selectDb']) && trim($this->_request['selectDb']) != ""){
         $selectDb = $this->_request['selectDb'];
      }

      $conn = new CatalogDb($selectDb, $this->_srv, $this->_usr, $this->_pwd);
      if($this->_request['type'] == 'findSrv'){
         $arrResult = $conn->getDB();
         //$arrResult2 = $conn->getReservedWord();
         //$arrResult = array_merge($arrResult,$arrResult2);
         
      }elseif($this->_request['type'] == 'sendSql'){
         $arrResult = $conn->getTblResult($this->_request['strSql']);
         
      }elseif($this->_request['type'] == 'explainSql'){
         $arrResult = $conn->getExplainTables($this->_request['strSql']);
         
      }elseif($this->_request['type'] == 'showTbl'){
         $arrResult = $conn->getShowTables();
         
      }elseif($this->_request['type'] == 'showHlp'){
         $arrResult = $conn->getShowHelp();
         
      }elseif($this->_request['type'] == 'showListHelp'){
         $arr = explode(':', $this->_request['showListHelp']);
         $arrResult = $conn->getShowExpHelp($arr[0]);
         
      }
      
      ob_clean();
      echo json_encode($arrResult);
   }

   /**
    * manipulate xml
    */
   private function requestXml()
   {
      $strTitle = $strSql = "";
      if(isset($this->_request['strSql'])){
         $strSql = $this->_request['strSql'];
      }
      if(isset($this->_request['title'])){
         $strTitle = $this->_request['title'];
      }
      $conn = new CatalogXml($strSql, $strTitle);

      if($this->_request['type'] == 'addItem' && isset($this->_request['strSql']) && $this->_request['strSql'] != ""){
         $conn->addItem($this->_request['addRadio']);
         
      }elseif($this->_request['type'] == 'addGroup'){
         $conn->addGroup();
         
      }elseif($this->_request['type'] == 'delGroup'){
         $arr = $this->loadGroups($this->_request);
         $arrGrp = $arr[0];
         $arrItem = $arr[1];
         if(!empty($arrGrp)){
            $conn->deleteGroup($arrGrp);
         }
         if(!empty($arrItem)){
            $conn->deleteItem($arrItem);
         }
      }
   }

   /**
    * change view theme
    */
   private function requestView()
   {
      $f = new ManageFiles();
      $f->saveConfView($this->_request);
   }

   /**
    * desglose array
    * @param array $request
    * @return array
    */
   private function loadGroups($request)
   {
      $arrItem = $arrGrp = array();
      $grp = "";
      foreach($request as $k => $val){
         if($val != 'on'){
            continue;
         }
         $arr = explode("_", $k);
         if($arr[0] == 'grp'){
            $arrGrp[$arr[1]] = $arr[1];
            $grp = $arr[1];
            continue;
         }elseif($arr[0] == 'itemGrp' && $arr[1] == $grp){
            continue;
         }else{
            $arrItem[$arr[1]][] = $arr[2];
         }
         $grp = "";
      }
      return array($arrGrp, $arrItem);
   }

}
