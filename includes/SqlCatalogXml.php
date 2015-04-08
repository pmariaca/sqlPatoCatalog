<?php
include_once ("CustomError.php");

/**
 * SqlCatalogXml: manipulate xml file
 * @author axolote14
 */
class SqlCatalogXml {

   private $file_name = "sqlCatalog.xml";
   private $file = "";
   private $strSql = "";
   private $strTitle = "";

   /**
    * 
    * @param string $strSql
    * @param string $strTitle
    */
   function __construct($strSql = "", $strTitle = "")
   {
      $d = PATH_SQLCATALOG;
      $dir = $d.DIRECTORY_SEPARATOR."files";
      $this->file = $dir.DIRECTORY_SEPARATOR.$this->file_name;
      $this->strSql = $strSql;
      $this->strTitle = $strTitle;
      
      $error = new customError(array($this->file));
      set_error_handler(array($error, 'errorHandler'));
      
      if(file_exists($dir)){
         if(!is_writable($dir)){
         }else{
            if(!file_exists($this->file)){
               $this->newCatalog();
            }
         }
      }
   }

   /**
    * create new catalog if non exist
    */
   public function newCatalog()
   {
      $xml = new DOMDocument("1.0", "utf-8");
      $catalog = $xml->createElement("catalog");
      $xml->appendChild($catalog);

      $arrGrp[0] = array('id' => 1, 'name' => 'UPDATE', 'strTitle' => "update myTable1 ", 'strSql' => "update table set name=\'3\' where id=\'1\' ");
      $arrGrp[1] = array('id' => 2, 'name' => 'SELECT', 'strTitle' => "select myTable1", 'strSql' => "SELECT \'a\' REGEXP \'^[a-d]\' # try this ");
      foreach($arrGrp as $grp){
         $item = $xml->createElement("item");
         $title = $xml->createElement("title");
         $sql = $xml->createElement("sql");
         $titleText = $xml->createTextNode($grp['strTitle']);

         $sqlText = $xml->createTextNode($grp['strSql']);
         $title->appendChild($titleText);
         $item->appendChild($title);
         $sql->appendChild($sqlText);
         $item->appendChild($sql);

         $group = $xml->createElement("group");
         $groupAttr = $xml->createAttribute("id");
         $groupAttr->value = $grp['id'];
         $groupAttr2 = $xml->createAttribute("name");
         $groupAttr2->value = $grp['name'];
         $group->appendChild($groupAttr);
         $group->appendChild($groupAttr2);
         $group->appendChild($item); // vacio
         $catalog->appendChild($group);
      }
      $xml->formatOutput = true;
      //echo "<xmp>". $xml->saveXML() ."</xmp>";
      $xml->save($this->file) or die("");
   }

   /**
    * add new group
    */
   public function addGroup()
   {
      $xml = new DOMDocument();
      $xml->formatOutput = true;
      $xml->preserveWhiteSpace = false;
      $xml->load($this->file) or die("");

      $catalog = $xml->documentElement;
      $nNodes = $catalog->childNodes->length;
      //++++++++++++++++++++++++++++++++++++++++++++
      $grp = array('id' => $nNodes + 1, 'name' => $this->strTitle);    
      $group = $xml->createElement("group");
      $groupAttr = $xml->createAttribute("id");
      $groupAttr->value = $grp['id'];
      $groupAttr2 = $xml->createAttribute("name");
      $groupAttr2->value = $grp['name'];
      $group->appendChild($groupAttr);
      $group->appendChild($groupAttr2);
      $catalog->appendChild($group);
      //++++++++++++++++++++++++++++++++++++++++++++
      //echo "<xmp>". $xml->saveXML() ."</xmp>";
      $xml->save($this->file) or die("");
   }

   /**
    * add new item
    * @param integer $idGroup - group id
    */
   public function addItem($idGroup = 0)
   {
      $strSql = $this->strSql;
      $strSql = str_replace("\\", "&#92;", $strSql);
      $strSql = str_replace('"', '&quot;', $strSql);
      $strSql = str_replace("'", "\'", $strSql);
      $strSql = str_replace("\r\n", "&#182;", $strSql);
      $strSql = str_replace("\n", "&#182;", $strSql);

      $xml = new DOMDocument();
      $xml->formatOutput = true;
      $xml->preserveWhiteSpace = false;
      $xml->load($this->file) or die("");
      $catalog = $xml->documentElement;

      $grupoN = $xml->getElementsByTagName("group")->length;
      if($idGroup == 0 || $idGroup > $grupoN){
         $idGroup = $grupoN;
      }
      $xpath = new DOMXpath($xml);
      $find = $xpath->query("//group[@id='".$idGroup."']");
      //$findNode = $catalog->lastChild; // ultimo grupo
      $findNode = $find->item(0);
      $findNodePlace = $findNode->childNodes->item($findNode->childNodes->length);

      //++++++++++++++++++++++++++++++++++++++++++++
      $grp = array('strTitle' => $this->strTitle, 'strSql' => $strSql);
      $item = $xml->createElement("item");
      $title = $xml->createElement("title");
      $sql = $xml->createElement("sql");
      $titleText = $xml->createTextNode($grp['strTitle']);
      $sqlText = $xml->createTextNode($grp['strSql']);
      $title->appendChild($titleText);
      $item->appendChild($title);
      $sql->appendChild($sqlText);
      $item->appendChild($sql);
      //++++++++++++++++++++++++++++++++++++++++++++

      $findNode->insertBefore($item, $findNodePlace);
      //echo "<xmp>". $xml->saveXML() ."</xmp>";
      $xml->save($this->file) or die("");
   }
      
   /**
    * delete groups
    * @param array $arrGroup - array of groups id
    * @return boolean
    */
   public function deleteGroup($arrGroup)
   {
      $xml = new DOMDocument();
      $xml->formatOutput = true;
      $xml->preserveWhiteSpace = false;
      $xml->load($this->file) or die("");
      
      $xpath = new DOMXpath($xml);
      foreach($arrGroup as $idGroup){
         $find = $xpath->query("//group[@id='".$idGroup."']");
         $findNode = $find->item(0);
         $findNode->parentNode->removeChild($findNode);
      }
      //echo "<xmp>". $xml->saveXML() ."</xmp>";
      $xml->save($this->file) or die("");
      $this->reorderGroup();
      return true;
   }
   
   /**
    * delete items
    * @param type $arrItem
    */
   public function deleteItem($arrItem)
   {
      $xml = new DOMDocument();
      $xml->formatOutput = true;
      $xml->preserveWhiteSpace = false;
      $xml->load($this->file) or die("");
 
      $arrItem_x = array();
      foreach($arrItem as $idGroup=>$var){  
         rsort($var);
         $arrItem_x[$idGroup] = $var;
      }

      $xpath = new DOMXpath($xml);
      foreach($arrItem_x as $idGroup=>$var){        
         $find = $xpath->query("//group[@id='".$idGroup."']");
         $findNode = $find->item(0);
         foreach($var as $item){
            $findNodePlace = $findNode->childNodes->item($item);
            $findNodePlace->parentNode->removeChild($findNodePlace);
         }
      }
      //echo "<xmp>". $xml->saveXML() ."</xmp>";
      $xml->save($this->file) or die("");  
   }

   /**
    * read xml file
    * @return array - catalog's item
    */
   public function readCatalog()
   {
      $catalog = simplexml_load_file($this->file, 'SimpleXMLElement', LIBXML_NOCDATA);
      if(!is_object($catalog)){
         return array();
      }

      $n = 0;
      $collapse = 1;
      $arrAccordion = array();
      for($id = 1; $id < $catalog->group->count() + 1; $id++){
         $n++;
         if($id == $n){
            foreach($catalog->xpath("//group[@id='".$id."']") as $group){
               $arrAccordion[$collapse]['title'] = (string) $group['name'];
            }
         }
         $itm = 0;
         foreach($catalog->xpath("//group[@id='".$id."']/item") as $item){
            $strSql = $item->sql;
            $strSql = str_replace("&#92;", "\\\\", $strSql);
            $strSql = str_replace("&#182;", "\\n", $strSql);
            $arrAccordion[$collapse]['item'][$itm][0] = (string) $item->title;
            $arrAccordion[$collapse]['item'][$itm][1] = $strSql;
            $itm++;
         }
         if($id == $n){
            $collapse++;
         }
      }
      return $arrAccordion;
   }
   
   /**
    * reorder attribute id's in catalog
    */
   private function reorderGroup()
   {
      $xml = new DOMDocument();
      $xml->formatOutput = true;
      $xml->preserveWhiteSpace = false;
      $xml->load($this->file) or die("");
      
      $catalog = $xml->getElementsByTagName( "group" ); 
      $id = 1;
      foreach( $catalog as $ctg )
      {
         $ctg->removeAttribute('id');
         $ctg->setAttribute("id", $id++);
      }
      //echo "<xmp>". $xml->saveXML() ."</xmp>";
      $xml->save($this->file) or die("");
   }
   
}
