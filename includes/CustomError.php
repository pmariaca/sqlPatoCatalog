<?php
namespace Catalog;
/**
 * Manage errors
 * @author axolote14
 */
class CustomError {
   private $_arrParams = array();
   
   /**
    * 
    * @param type $arrParams
    */
   function __construct($arrParams= array())
   {
      $this->_arrParams = $arrParams;
   }
   
   /**
    * 
    * @param integer $errno
    * @param string $errstr
    * @param string $errfile
    * @param integer $errline
    * @return boolean
    */
   function errorHandler($errno, $errstr, $errfile, $errline)
   {
      if(!(error_reporting() && $errno)){
         return;
      }
//echo "--------------".$errno." ".$errstr." ".$errfile." ".$errline." <hr> ";
      
      switch($errno){
         case E_WARNING:
            $fnd = strpos ($errfile, "CatalogDb.php");
            if($fnd===false){
               echo json_encode(array('error'=>DIR_NOT_WRITABLE.": ".$this->_arrParams[0]));
            }else{
               echo json_encode(array('error'=>$errstr));
            }
            
         case E_USER_ERROR:   // 256
            exit(1);
            break;

         case ($errno == E_USER_WARNING || $errno == E_USER_NOTICE || $errno == E_STRICT ):
         case ($errno == E_NOTICE ):
            echo json_encode(array('error'=>$errno." ".  var_export($this->_arrParams)));
            break;
         default:
            throw new \ErrorException($errstr, $errno, 0, $errfile, $errline);
      }
      return true;
   }
}