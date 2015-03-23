<?php
define('PATH_SQLCATALOG', str_replace( '\\', '/', realpath(substr(dirname(__FILE__), 0, 0-strlen('includes')))));
include_once (PATH_SQLCATALOG.DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR.'lang'.DIRECTORY_SEPARATOR.'lang.php');
include_once ("CustomError.php");
include_once ("SqlCatalogDb.php");
//echo "<pre>";print_r($_REQUEST);echo "</pre>";
$o = new SqlResult($_REQUEST);
$arrDb = $o->requestDb();

if(empty($arrDb)){return;}
$arrResult = $arrDb[0];
$arrMessage = $arrDb[1];

/* ************************************************************************************** */

/**
 * show query result
 * @author axolote14
 */
class SqlResult {

   private $request;
   
   function __construct($request)
   {
      $error = new customError(array(1));
      set_error_handler(array($error, 'errorHandler'));
      $this->request = $request;  
   }

   /**
    * search in database
    * @return array
    */
   public function requestDb()
   {
      $arrDb = array();
      if (isset($this->request['selectDb']) && $this->request['selectDb']!=""
              && isset($this->request['strSql']) && $this->request['strSql'] != "") {
         
         $srv="";$usr="";$pwd="";
         if($this->request['srv'] && trim($this->request['srv'])!=""){$srv=$this->request['srv'];}
         if($this->request['usr'] && trim($this->request['usr'])!=""){$usr=$this->request['usr'];}
         if($this->request['pwd'] && trim($this->request['pwd'])!=""){$pwd=$this->request['pwd'];}
         $conn = new SqlCatalogDb($this->request['selectDb'], $srv, $usr, $pwd);
         if ($this->request['type'] == 'sqlResult') {   
            $arrDb = $conn->getTblResult($this->request['strSql']);
         }   
         
         if ($this->request['type'] == 'explainSql') {
            $arrDb = $conn->getExplainTables($this->request['strSql']);
         }
      }
      return $arrDb;
   }
}
?>

<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
   <head>
      <meta http-equiv="content-type" content="text/html; charset=UTF-8">
      <link href="dist/css/bootstrap.min.css" rel="stylesheet" media="screen">     
      <link rel="stylesheet" type="text/css" href="css/sqlResult.css" />
   </head>
   <body>
      <div class="container-fluid">
      <?php if(!isset($_REQUEST['selectDb']) ): ?>
         <h3><span class="label label-warning">"No ha seleccionado una basededatos"</span></h3>
      <?php return; endif; ?>
      
      <?php if(array_key_exists('error', $arrMessage) && trim($arrMessage['error'])!=""): ?>
         <div class="alert alert-warning" role="alert"><?php echo $arrMessage['error'];  ?></div>
      <?php endif; ?>
      <p class="badge">
         <?php echo $arrMessage['numRows']; ?>
      </p>
      <table class="table  table-striped table-hover" >
         <thead><tr>
            <?php foreach ($arrResult[0] as $header): ?>
                  <th><?php echo $header; ?></th>
               <?php endforeach; ?>
            </tr></thead>
         <tbody>
            <?php foreach ($arrResult[1] as $arrRow): ?>
               <tr>
                  <?php foreach ($arrRow as $row): ?>
                     <td><?php echo $row; ?></td>
                  <?php endforeach; ?>
               </tr>
            <?php endforeach; ?>
         </tbody>  
      </table>
      </div>
      <!--script src="dist/js/bootstrap.min.js"></script-->
   </body>
</html>