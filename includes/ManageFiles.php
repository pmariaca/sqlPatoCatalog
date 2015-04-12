<?php
include_once ("CustomError.php");

/**
 * Manage files: read and write files
 * @author axolote14
 */
class ManageFiles {
   
   private $ini_host = "configHost.ini";
   private $ini_view = "configView.ini";
   private $file_host = "";
   private $file_view = "";
   
   /**
    * define the path
    */
   function __construct()
   {
      $this->file_host = PATH_SQLCATALOG.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.$this->ini_host;
      $this->file_view = PATH_SQLCATALOG.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.$this->ini_view;
      
      $error = new customError(array($this->file_host));
      set_error_handler(array($error, 'errorHandler'));
      
   }
   
   /**
    * 
    * @param type $type
    * @return type
    */
   public function findConf($type=0)
   {
      if($type==0){
         return $this->findConfHost();
      }else{
         return $this->findConfView();
      }
   }

   /**
    * get data from config file
    * @return integer
    */
   private function findConfHost()
   {
      $f = 0;
      $flg = 0;
      $srv = "";
      if($file = fopen($this->file_host, 'r')){
         $n = -1;
         while(($line = fgets($file, 4096)) !== false){
            if(substr(trim($line), 0, 1) == ';' || trim($line)==""){
               continue;
            }
            if(substr(trim($line), 0, 6) == '[cnf]'){
               $f = 1;
               continue;
            }
            if(substr(trim($line), 0, 6) == '[srv]'){
               $f = 2;
               continue;
            }
            $arr = explode(':', $line);
            if($f==1){
               $flg = trim($arr[1]);
            }
            $arr = explode(':', $line);
            if($f==2 && trim($arr[0])=='srv'){
               $srv = trim($arr[1]);
            }
         }
      }
      fclose($file);
      return $flg;
   } 
   
   /**
    * list of data server
    * @return array
    */
   public function findHost()
   {
      $f = 0;
      $arrHosts = array();
      if($file = fopen($this->file_host, 'r')){
         $n = -1;
         while(($line = fgets($file, 4096)) !== false){
            if(substr(trim($line), 0, 1) == ';' || trim($line)==""){
               continue;
            }
            if(substr(trim($line), 0, 6) == '[cnf]'){
               $f = 1;
               continue;
            }
            if(substr(trim($line), 0, 6) == '[srv]'){
               $f = 2;
               $n++;
               continue;
            }
            $arr = explode(':', $line);
            if($f==2){
               if($f==2 && trim($arr[0])=='pass'){
                  $arrHosts[$n][trim($arr[0])] = $this->decrypt(trim($arr[1]));
               }else{
                  $arrHosts[$n][trim($arr[0])] = trim($arr[1]);
            }}}
      }
      fclose($file);
      return $arrHosts;
   }
   
   /**
    * list of data server
    * @return array
    */
   public function findConfView()
   {
      $f = 0;
      $arrView = array();
      if($file = fopen($this->file_view, 'r')){
         while(($line = fgets($file, 4096)) !== false){
            if(substr(trim($line), 0, 1) == ';' || trim($line)==""){
               continue;
            }
            if(substr(trim($line), 0, 6) == '[view]'){
               $f = 1;
               continue;
            }
            if(substr(trim($line), 0, 6) == '[srv]'){
               $f = 2;
               continue;
            }
            $arr = explode(':', $line);
            if($f==1){
               $arrView[trim($arr[0])] = trim($arr[1]);
            }}
      }
      fclose($file);
      return $arrView;
   }
   
   public function saveConf($request, $type=0)
   {
      if($type==0){
         $this->saveConfHost($request);
      }else{
         $this->saveConfView($request);
      }
   }
   
   /**
    * save user data for mysql
    * @param array $request
    */
   private function saveConfHost($request)
   {
      $f=0;$f1=0;$s="";$u="";$p="";$itemSrv="";$itemUsr="";$itemPass="";
      if(isset($request['itemSrv']) && trim($request['itemSrv'])!=""){
         $f1++;
         $s = "s";
         $itemSrv = $request['itemSrv'];
      }
      if(isset($request['itemUsr']) && trim($request['itemUsr'])!=""){
         $f1++;
         $u = "u";
         $itemUsr = $request['itemUsr'];
      }
      if(isset($request['itemPass']) && trim($request['itemPass'])!=""){
         $f1++;
         $p = "p";
         $itemPass = $request['itemPass'];
      }
      if($f1==3){$f=3;}
      if($f1<3){$f=1;}
      if($f1==0){$f=0;}
      $arr[] = "[cnf]";
      $arr[] = "f:".$f.":".$s.":".$u.":".$p;
      $arr[] = "[srv]";
      $arr[] = "name:1";
      $arr[] = "srv:".$itemSrv;
      $arr[] = "user:".$itemUsr;
      $arr[] = "pass:".$this->encrypt($itemPass);
      $this->saveFile($this->file_host, $arr);
   }
   
   /**
    * save user data for mysql
    * @param array $request
    */
   private function saveConfView($request)
   {
      if(isset($request['less']) && trim($request['less'])!=""){
         $less = $request['less'];
      }
      $arr[] = "[view]";
      $arr[] = "view:1";
      $arr[] = "item:".$less;
      $this->saveFile($this->file_view, $arr);
   }
   
   private function saveFile($file, $arr)
   {
      foreach($arr as $k=>$val){
         if($k==0){
            file_put_contents($file, $val."\n");
         }else{
            file_put_contents($file, $val."\n", FILE_APPEND);
         }
      }
   }
   
   /**
    * encrypt password
    * @param string $string
    * @return string
    */
   private function encrypt($string) {
      $key="marijuana!";
      $result = '';
      for($i=0; $i<strlen($string); $i++) {
         $char = substr($string, $i, 1);
         $keychar = substr($key, ($i % strlen($key))-1, 1);
         $char = chr(ord($char)+ord($keychar));
         $result.=$char;
      }
      return base64_encode($result);
   }
   
   /**
    * decrypt password
    * @param string $string
    * @return string
    */
   private function decrypt($string) {
      $key="marijuana!";
      $result = '';
      $string = base64_decode($string);
      for($i=0; $i<strlen($string); $i++) {
         $char = substr($string, $i, 1);
         $keychar = substr($key, ($i % strlen($key))-1, 1);
         $char = chr(ord($char)-ord($keychar));
         $result.=$char;
      }
      return $result;
   }
}