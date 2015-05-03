<?php

/**
 * SqlCatalogDb: connect and search in database
 * @author axolote14
 */
class SqlCatalogDb {

   private $arrNameHost = array();
   private $db;
   private $srv;
   private $user;
   private $pass;
   private $mysqli;

   /**
    * in constructor are define if data connection are in config file or given by the user
    * @param string $db - database name
    * @param string $srv - host name
    * @param string $user - user name
    * @param string $pass - user password
    */
   function __construct($db = "", $srv = "", $user = "", $pass = "")
   {
      $error = new CustomError();
      set_error_handler(array($error, 'errorHandler'));

      $f = new ManageFiles();
      $flg = $f->findConf();
      $arrHost = $f->findHost();

      if($flg == 0){
         // from user
         $arrNameHost[] = "";
         $this->srv = $srv;
         $this->user = $user;
         $this->pass = $pass;
      }elseif($flg == 3){
         // from config file
         $arrNameHost[] = $arrHost[0]['name']; // ETIQUETA
         $this->srv = $arrHost[0]['srv'];
         $this->user = $arrHost[0]['user'];
         $this->pass = $arrHost[0]['pass'];
      }else{
         // some not all from config file
         $arrNameHost[] = "";
         $this->srv = $srv;
         $this->user = $user;
         $this->pass = $pass;
         if($arrHost[0]['srv'] != ""){
            $this->srv = $arrHost[0]['srv'];
         }
         if($arrHost[0]['user'] != ""){
            $this->user = $arrHost[0]['user'];
         }
         if($arrHost[0]['pass'] != ""){
            $this->pass = base64_decode($arrHost[0]['pass']);
         }
      }
      $this->db = $db;
      $this->arrNameHost = $arrNameHost;
   }

   /**
    * verify connection with user data
    * @param string $srv
    * @param string $user
    * @param string $pass
    * @return boolean
    */
   public function testConnection($srv, $user, $pass)
   {
      $this->srv = $srv;
      $this->user = $user;
      $this->pass = $pass;
      $r = $this->connect();
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
    * @return boolean
    */
   private function connect()
   {
      $mysqli = new mysqli($this->srv, $this->user, $this->pass, $this->db);
      // verificar coneccion 
      if(mysqli_connect_errno()){
         return false;
      }
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
    * conect to selected database
    * @param string $sql - query string
    * @return array - query result
    */
   private function mySql($sql, $db = "")
   {
      if($db == ""){
         $db = $this->db;
      }
      $r = $this->connect();
      if(!$r){
         return;
      }
      $this->mysqli->select_db($db);
      $result = $this->findQuery($sql);
      $this->close();
      return $result;
   }

   /**
    * convert an array the query result
    * @param string $sql - query string
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
               $arrInfo[] = "<pre>" . var_export($result, true) . "</pre>";
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
                  $rows[$k][] = utf8_encode($row[$i]);
               }
            }
//echo "<pre>";var_dump($result);echo "</pre>"; 
            $result->close();
         }
      }
      //$this->mysqli->query('set profiling=0');

      $error = mysqli_error($this->mysqli);
      return array(
          'info' => $arrInfo,
          'row' => $rows,
          'numcols' => $numcols,
          'numRows' => $numRows,
          'error' => $error);
   }

   /**
    * list of databases
    * @return arrray - list of databases and host name
    */
   public function getDB()
   {
      return $this->mySql("show databases;");
   }

   /**
    * 
    * @param string $sql
    * @return array - query result
    */
   public function getTblResult($sql)
   {
      return $this->mySql($sql);
   }

   /**
    * explain from sql statment
    * @param string $sql - query string
    * @return array - query result
    */
   public function getExplainTables($sql)
   {
      return $this->mySql("EXPLAIN " . $sql);
   }

   /**
    * 
    * @param string $sql - query string
    * @return array - query result
    */
   public function getShowTables()
   {
      return $this->mySql("SHOW tables");
   }

   /**
    * 
    * @param string $sql - query string
    * @return array - query result
    */
   public function getShowHelp()
   {
      $sql = "SELECT CONCAT(t.name, ': ', k.name,'  (', c.name,')' ) as name
FROM  help_keyword as k
JOIN help_relation as r ON k.help_keyword_id=r.help_keyword_id
JOIN   help_topic as t ON r.help_topic_id=t.help_topic_id
JOIN  help_category as c ON t.help_category_id=c.help_category_id
ORDER BY t.name "; // WHERE t.help_category_id in (24, 16, 28)
      return $this->mySql($sql, "mysql");
   }

   public function getShowExpHelp($topic)
   {
      $sql = "SELECT description FROM help_topic WHERE  name='{$topic}' ";
      return $this->mySql($sql, "mysql");
   }

   /**
    * 
    * @param string $sql
    * @return array - query result
    */
   public function getReservedWord()
   {
      $sql = "SELECT k.name
FROM  help_keyword as k
JOIN help_relation as r ON k.help_keyword_id=r.help_keyword_id
JOIN   help_topic as t ON r.help_topic_id=t.help_topic_id
JOIN  help_category as c ON t.help_category_id=c.help_category_id
WHERE t.help_category_id=28 
GROUP BY k.name "; // #16, 24
      return $this->mySql($sql, "mysql");
   }

}
