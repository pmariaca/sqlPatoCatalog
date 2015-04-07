<?php
include_once ("CustomError.php");
include_once ("ManageFiles.php");

/**
 * SqlCatalogDb: connect and search in database
 * @author axolote14
 */
class SqlCatalogDb {

   private $arrNameHost = array();
   private $Db;
   private $srv;
   private $user;
   private $pass;
   private $mysqli;

   /**
    * define servers
    */
   function __construct($db="", $srv = "", $user = "", $pass = "")
   {
      $error = new CustomError();
      set_error_handler(array($error, 'errorHandler'));
      
      $f = new ManageFiles();
      $flg = $f->findConf();
      $arrHost = $f->findHosts();

      if($flg==0){
         $arrNameHost[] = "";
         $this->srv = $srv;
         $this->user = $user;
         $this->pass = $pass;
      }elseif($flg==3){
         $arrNameHost[] = $arrHost[0]['name']; // ETIQUETA
         $this->srv = $arrHost[0]['srv'];
         $this->user = $arrHost[0]['user'];
         $this->pass = $arrHost[0]['pass'];
      }else{
         // no todos
         $arrNameHost[] = "";
         $this->srv = $srv;
         $this->user = $user;
         $this->pass = $pass;
         if($arrHost[0]['srv']!=""){
            $this->srv = $arrHost[0]['srv'];
         }
         if($arrHost[0]['user']!=""){
            $this->user = $arrHost[0]['user'];
         }
         if($arrHost[0]['pass']!=""){
            $this->pass = base64_decode($arrHost[0]['pass']);
         }
         
      }
      $this->Db = $db;
      $this->arrNameHost = $arrNameHost;
   }

   
   public function testConnection($srv, $user, $pass)
   {
      $this->srv = $srv;
      $this->user = $user;
      $this->pass = $pass;
      $r = $this->connect();
      if(!$r){return $r;}
      $this->close();
      return true;
   }
   
   /**
    * getter
    * @return array
    */
   public function getNameHosts()
   {
      return $this->arrNameHost;
   }

   /**
    * create connection
    */
   private function connect()
   {
      if($this->srv==""){return false;}      
      $mysqli = new mysqli($this->srv, $this->user, $this->pass, $this->Db);
      // verificar coneccion 
      if(mysqli_connect_errno()){
         return false;
      }
      //printf("Host info: %s\n", $mysqli->srv_info);
      $this->mysqli = $mysqli;
      return true;
   }
   
   /**
    * close connection
    */
   private function close()
   {
      $this->mysqli->close();
   }

   /**
    * conect to database
    * @param string $sql
    * @return array
    */
   private function mySql($sql)
   {
      $r = $this->connect();
      if(!$r){return;}
      $this->mysqli->select_db($this->Db);
      $result = $this->findQuery($sql);
      $this->close();
      return $result;
   }
   
   /**
    * 
    * @param type $sql
    * @return array
    */
   private function findQuery($sql)
   {
      //error_reporting(0);
      if(trim($sql) == ""){
         return;
      }
      $sql = stripslashes($sql);

      $arrInfo = array();
      $rows = array();
      $numcols = array();
      $numRows = "";
      //$this->mysqli->query('set profiling=1');
      if($result = $this->mysqli->query($sql)){
         if($result === true){
            if(!is_null($this->mysqli->info)){
               $arrInfo[] = $this->mysqli->info;
            }else{
               $arrInfo[] = "<pre>".var_export($result, true)."</pre>";
            }
         }else{
            $numRows = "numRows: " . $result->num_rows;
            $numcols = mysqli_field_count($this->mysqli);
            while($finfo = mysqli_fetch_field($result)){
               $arrInfo[] = $finfo->name;
            }

            $k = -1;
            while($row = $result->fetch_row()){
               $k++;
               for($i = 0; $i < $numcols; $i++){
                  $rows[$k][] = $row[$i];
               }
            }
//echo "<pre>";var_dump($result);echo "</pre>"; 
            $result->close();
         }
      }
      //$this->mysqli->query('set profiling=0');
    
      $error = mysqli_error($this->mysqli);
      return array('info' => $arrInfo, 
          'row' => $rows,
          'numcols' => $numcols,
          'numRows' => $numRows,
          'error' => $error);
   }
   
   private function findQuery_r($sql)
   {
      //error_reporting(0);
      if(trim($sql) == ""){
         return;
      }
      $sql = stripslashes($sql);

      $arrInfo = array();
      $rows = array();
      $numcols = array();
      $numRows = "";
      //$this->mysqli->query('set profiling=1');
      if($result = $this->mysqli->query($sql)){
         if($result === true){
            if(!is_null($this->mysqli->info)){
               $arrInfo[]['header'] = $this->mysqli->info;
            }else{
               $arrInfo[]['header'] = "<pre>".var_export($result, true)."</pre>";
            }
         }else{
            $numRows = "numRows: " . $result->num_rows;
            $numcols = mysqli_field_count($this->mysqli);
            while($finfo = mysqli_fetch_field($result)){
               $arrInfo[]['header'] = $finfo->name;
            }

            $k = -1;
            while($row = $result->fetch_row()){
               $k++;
               for($i = 0; $i < $numcols; $i++){
                  $rows[$k][] = $row[$i];
               }
            }
//echo "<pre>";var_dump($result);echo "</pre>"; 
            $result->close();
         }
      }
      //$this->mysqli->query('set profiling=0');
    
      $error = mysqli_error($this->mysqli);
      return array('info' => $arrInfo, 
          'row' => $rows,
          'numcols' => $numcols,
          'numRows' => $numRows,
          'error' => $error);
   }
   
   /**
    * list of databases
    * @return arrray
    */
   public function findDB()
   {
      $arrBases = array();
      $r = $this->connect();
      if(!$r){return;}
      $result = $this->mysqli->query("show databases;");
      while($row = $result->fetch_row()){
         $arrBases[] = $row[0];
      }
      $this->mysqli->close();
      return $arrBases;
      return array($arrBases, $this->srv);
   }
   
   /**
    * explain from sql statment
    * @param string $sql
    * @return array
    */
   public function getExplainTables($sql)
   {
      return $this->getTblResult("explain " . $sql);
   }

   /**
    * 
    * @param string $sql
    * @return type
    */
   public function getShwoTables($sql)
   {
      return $this->getTblResult("show tables");
   }

   /**
    * convert an array the query result
    * @param string $sql
    * @return array
    */
   public function getTblResult($sql)
   {
      $arrResult = $this->mySql($sql);     
      return $arrResult;
   }
}
?>