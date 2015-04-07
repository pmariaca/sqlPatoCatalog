<?php
include_once ("CustomError.php");
/**
 * Manage files: read and write files
 * @author axolote14
 */
class ManageFiles {
   
   private $file_name = "configHost.ini";
   private $dirFile = "";
   
   function __construct()
   {
      $this->dirFile = PATH_SQLCATALOG.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.$this->file_name;
      
      $error = new customError(array($this->dirFile));
      set_error_handler(array($error, 'errorHandler'));
      
   }

   /**
    * get data from config file
    * @return integer
    */
   public function findConf()
   {
      $f = 0;
      $flg = 0;
      $srv = "";
      if($gestor = fopen($this->dirFile, 'r')){
         $n = -1;
         while(($line = fgets($gestor, 4096)) !== false){
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
      fclose($gestor);
      return $flg;
   } 
   
   /**
    * list of data server
    * @return array
    */
   public function findHosts()
   {
      $f = 0;
      $arrHosts = array();
      if($gestor = fopen($this->dirFile, 'r')){
         $n = -1;
         while(($line = fgets($gestor, 4096)) !== false){
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
      fclose($gestor);
      return $arrHosts;
   }
   
   /**
    * 
    * @param array $request
    */
   public function saveConf($request)
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
      $xcnf = "[cnf]";
      $cnf = "f:".$f.":".$s.":".$u.":".$p;
      $xsrv = "[srv]";
      $name = "name:1";
      $srv = "srv:".$itemSrv;
      $user = "user:".$itemUsr;
      $pass = "pass:".$this->encrypt($itemPass);
      file_put_contents($this->dirFile, $xcnf."\n");
      file_put_contents($this->dirFile, $cnf."\n", FILE_APPEND);
      file_put_contents($this->dirFile, $xsrv."\n", FILE_APPEND);
      file_put_contents($this->dirFile, $name."\n", FILE_APPEND);
      file_put_contents($this->dirFile, $srv."\n", FILE_APPEND);
      file_put_contents($this->dirFile, $user."\n", FILE_APPEND);
      file_put_contents($this->dirFile, $pass."\n", FILE_APPEND);
   }
   
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