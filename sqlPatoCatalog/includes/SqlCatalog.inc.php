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

define('PATH_SQLCATALOG', str_replace( '\\', '/', realpath(substr(dirname(__FILE__), 0, 0-strlen('includes')))));
include_once (PATH_SQLCATALOG.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'lang.php');
include_once ("ManageFiles.php");
include_once ("SqlCatalogXml.php");
include_once ("SqlCatalogDb.php");

$f = new ManageFiles();
$flg = $f->findConf();
$arr = $f->findHosts();
$srv = $arr[0]['srv'];
$usr = $arr[0]['user'];

$arrResult=array();
$arrAccordion = array();
$o = new SqlCatalog($_REQUEST);
$arr = $o->findTodo();
if($arr['t']==1){
   $arrAccordion = $arr['arrAccordion'];
}
   
/* ************************************************************************************** */

/**
 * 
 *
 * @author Patricia Mariaca Hajducek (axolote14)
 * @version 1.0.3
 */
class SqlCatalog {

   private $request;
   private $srv = "";
   private $usr = "";
   private $pwd = "";
   
   function __construct($request)
   {
      $this->request = $request;  
      if($this->request['srv'] && trim($this->request['srv'])!=""){$this->srv=$this->request['srv'];}
      if($this->request['usr'] && trim($this->request['usr'])!=""){$this->usr=$this->request['usr'];}
      if($this->request['pwd'] && trim($this->request['pwd'])!=""){$this->pwd=$this->request['pwd'];}
   }

   /**
    * 
    * @return array
    */
   public function findTodo()
   {  
      $t = 0;
      $alert = "";
      $arrAccordion = array();
  //echo "<pre>";print_r($this->request);echo "</pre>";    
      if (empty($this->request)) {   
         $iniXml = new SqlCatalogXml();
         $arrAccordion = $iniXml->readCatalog();
         $t = 1;
      }else{
         
         if( isset($this->request['go']) && $this->request['go'] == 'db') {
            $this->requestDb();
         }
         if( isset($this->request['go']) && $this->request['go'] == 'xml') {
            $this->requestXml();
         }
      }
      return array('t'=>$t ,'arrAccordion'=>$arrAccordion);
   }
   
   /**
    * search databases
    * @return array
    */
   private function requestDb()
   {
      if ($this->request['type'] == 'findSrv') {
         $conn = new SqlCatalogDb('', $this->srv, $this->usr,$this->pwd);
         $arr = $conn->findDB();
         ob_clean();
         echo json_encode($arr);
  
      }elseif ($this->request['type'] == 'saveSrv') {
// echo "<pre>";print_r($this->request);echo "</pre>";   
         // verificar que esta correcto
         if(isset($this->request['itemSrv']) && isset($this->request['itemUsr']) && isset($this->request['itemPass'])){       
            $conn = new SqlCatalogDb();           
            $r = $conn->testConnection($this->request['itemSrv'], $this->request['itemUsr'], $this->request['itemPass']);
            if($r===true){
               $f = new ManageFiles();
               $f->saveConf($this->request);
            }else{
               $str = "<pre>srv:".$this->request['itemSrv'];
               $str .= ", usr:".$this->request['itemUsr'];
               $str .= ", pass:".$this->request['itemPass']."</pre>"; 
               echo $str;
            }
         }else{    
            $f = new ManageFiles();
            $f->saveConf($this->request);
         }
      }elseif ($this->request['type'] == 'sqlResult') {
         $conn = new SqlCatalogDb($this->request['selectDb'], $this->srv, $this->usr,$this->pwd);
         $arrResult = $conn->getTblResult($this->request['strSql']);
         ob_clean();
         echo json_encode($arrResult);

      }elseif ($this->request['type'] == 'explainSql') {
         $conn = new SqlCatalogDb($this->request['selectDb'], $this->srv, $this->usr,$this->pwd);
         $arrResult = $conn->getExplainTables($this->request['strSql']);
         ob_clean();
         echo json_encode($arrResult);
      }
   }
   
   /**
    * manipulate xml
    */
   private function requestXml()
   {
      $strSql = ""; $strTitle = "";
      if(isset($this->request['strSql'])){$strSql = $this->request['strSql'];}
      if(isset($this->request['title'])){$strTitle = $this->request['title'];}
      $conn = new SqlCatalogXml($strSql, $strTitle);       
      
      if ($this->request['type'] == 'addItem'
         && isset($this->request['strSql']) && $this->request['strSql'] != "") {
         $conn->addItem($this->request['addRadio']);

      } elseif ($this->request['type'] == 'addGroup') {
         $conn->addGroup();

      } elseif ($this->request['type'] == 'delGroup') {
         $arr = $this->loadGroups($this->request);
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
    * desglose array
    * @param array $request
    * @return array
    */
   private function loadGroups($request)
   {
      $arrGrp = array();
      $arrItem = array();
      $grp = "";
      foreach($request as $k=>$val){
         if($val!='on'){continue;}
         $arr = explode("_",$k);                  
         if($arr[0]=='grp'){
           $arrGrp[$arr[1]] = $arr[1];
           $grp = $arr[1];
           continue;
         }
         if($arr[0]=='itemGrp' && $arr[1]==$grp){
           continue;
         }else{
            $arrItem[$arr[1]][] = $arr[2];
         }
         $grp = "";
      }
      return array($arrGrp, $arrItem);
   }
}
?>

