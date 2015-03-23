<?php
/**
 * Manage errors
 * @author axolote14
 */
class CustomError {
   private $arrParams = array();
   
   function __construct($arrParams= array())
   {
      $this->arrParams = $arrParams;
   }
   
   function errorHandler($errno, $errstr, $errfile, $errline)
   {
      if(!(error_reporting() && $errno)){
         // Este código de error no está incluido en error_reporting
         return;
      }

      switch($errno){
         case E_WARNING:
            $fnd = strpos ($errfile, "SqlCatalogDb.php");
            if($fnd===false){
               //echo '<span class="label label-warning">'.DIR_NOT_WRITABLE.": ".$this->arrParams[0].'</span>';
               echo '<div class="alert alert-warning" role="alert">'.DIR_NOT_WRITABLE.": ".$this->arrParams[0].'</div>';
            }else{
               echo '<div class="alert alert-warning" role="alert">'.$errstr.'</div>';
            }
            
         case E_USER_ERROR:   // 256
            exit(1);
            break;

         case ($errno == E_USER_WARNING || $errno == E_USER_NOTICE || $errno == E_STRICT ):
            break;

         case ($errno == E_NOTICE ):
            break;

         default:
            break;
      }
      return true;
   }

}