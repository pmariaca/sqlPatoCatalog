<?php
namespace Catalog;

/**
 * CatalogXml: manipulate xml file
 * @author axolote14
 */
class CatalogXml
{

    private $_fileName = "sqlCatalog.xml";
    private $_file = "";
    private $_strSql = "";
    private $_strTitle = "";

    /**
     * 
     * @param string $strSql
     * @param string $strTitle
     */
    function __construct($strSql = "", $strTitle = "")
    {
        $d = PATH_SQLCATALOG;
        $dir = $d.DIRECTORY_SEPARATOR."files";
        $this->_file = $dir.DIRECTORY_SEPARATOR.$this->_fileName;
        $this->_strSql = $strSql;
        $this->_strTitle = $strTitle;

        set_error_handler([new CustomError([$this->_file]), 'errorHandler']);

        if (file_exists($dir) && is_writable($dir) && !file_exists($this->_file)) {
            $this->newCatalog();
        }
    }

    /**
     * create new catalog if non exist
     */
    public function newCatalog()
    {
        $xml = new \DOMDocument("1.0", "utf-8");
        $catalog = $xml->createElement("catalog");
        $xml->appendChild($catalog);

        $arrGrp[0] = [
            'id' => 1, 
            'name' => 'UPDATE', 
            'strTitle' => "update myTable1 ", 
            'strSql' => "update table set name=\'3\' where id=\'1\' "
            ];
        $arrGrp[1] = [
            'id' => 2, 
            'name' => 'SELECT', 
            'strTitle' => "select myTable1", 
            'strSql' => "SELECT \'a\' REGEXP \'^[a-d]\' # try this "
            ];
        foreach ($arrGrp as $grp) {
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
        $xml->save($this->_file) or die("");
    }

    /**
     * add new group
     */
    public function addGroup()
    {
        $xml = $this->checkFile();
        $catalog = $xml->documentElement;
        $nNodes = $catalog->childNodes->length;
        //++++++++++++++++++++++++++++++++++++++++++++
        $grp = ['id' => $nNodes + 1, 'name' => $this->_strTitle];
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
        $xml->save($this->_file) or die("");
    }

    /**
     * add new item
     * @param integer $idGroup - group id
     */
    public function addItem($idGroup = 0)
    {
        $strSql = $this->_strSql;
        $strSql = str_replace("\\", "&#92;", $strSql);
        $strSql = str_replace('"', '&quot;', $strSql);
        $strSql = str_replace("'", "\'", $strSql);
        $strSql = str_replace("\r\n", "&#182;", $strSql);
        $strSql = str_replace("\n", "&#182;", $strSql);

        $xml = $this->checkFile();
        $grupoN = $xml->getElementsByTagName("group")->length;
        if ($idGroup == 0 || $idGroup > $grupoN) {
            $idGroup = $grupoN;
        }
        $xpath = new \DOMXpath($xml);
        $find = $xpath->query("//group[@id='".$idGroup."']");
        $findNode = $find->item(0);
        $findNodePlace = $findNode->childNodes->item($findNode->childNodes->length);

        //++++++++++++++++++++++++++++++++++++++++++++
        $grp = ['strTitle' => $this->_strTitle, 'strSql' => $strSql];
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
        $xml->save($this->_file) or die("");
    }

    /**
     * delete groups
     * @param array $arrGroup - array of groups id
     * @return boolean
     */
    public function deleteGroup($arrGroup)
    {
        $xml = $this->checkFile();
        $xpath = new \DOMXpath($xml);
        foreach ($arrGroup as $idGroup) {
            $find = $xpath->query("//group[@id='".$idGroup."']");
            $findNode = $find->item(0);
            $findNode->parentNode->removeChild($findNode);
        }
        //echo "<xmp>". $xml->saveXML() ."</xmp>";
        $xml->save($this->_file) or die("");
        $this->reorderGroup();
        return true;
    }


    /**
     * delete items
     * @param type $arrItem
     */
    public function deleteItem($arrItem)
    {
        $xml = $this->checkFile();
        $arrItem_x = [];
        foreach ($arrItem as $idGroup => $var) {
            rsort($var);
            $arrItem_x[$idGroup] = $var;
        }

        $xpath = new \DOMXpath($xml);
        foreach ($arrItem_x as $idGroup => $var) {
            $find = $xpath->query("//group[@id='".$idGroup."']");
            $findNode = $find->item(0);
            foreach ($var as $item) {
                $findNodePlace = $findNode->childNodes->item($item);
                $findNodePlace->parentNode->removeChild($findNodePlace);
            }
        }
        //echo "<xmp>". $xml->saveXML() ."</xmp>";
        $xml->save($this->_file) or die("");
    }

    /**
     * read xml file
     * @return array - catalog's item
     */
    public function readCatalog()
    {
        $catalog = simplexml_load_file($this->_file, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!is_object($catalog)) {
            return [];
        }

        $n = 0;
        $collapse = 1;
        $arrAccordion = [];
        for ($id = 1; $id < $catalog->group->count() + 1; $id++) {
            $n++;
            if ($id == $n) {
                foreach ($catalog->xpath("//group[@id='".$id."']") as $group) {
                    $arrAccordion[$collapse]['title'] = (string) $group['name'];
                }
            }
            $itm = 0;
            foreach ($catalog->xpath("//group[@id='".$id."']/item") as $item) {
                $strSql = str_replace("&#92;", "\\\\", $item->sql);
                $strSql = str_replace("&#182;", "\\n", $strSql);
                $arrAccordion[$collapse]['item'][$itm][0] = (string) $item->title;
                $arrAccordion[$collapse]['item'][$itm][1] = $strSql;
                $itm++;
            }
            if ($id == $n) {
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
        $xml = $this->checkFile();
        $catalog = $xml->getElementsByTagName("group");
        $id = 1;
        foreach ($catalog as $ctg) {
            $ctg->removeAttribute('id');
            $ctg->setAttribute("id", $id++);
        }
        //echo "<xmp>". $xml->saveXML() ."</xmp>";
        $xml->save($this->_file) or die("");
    }

    /**
     * 
     * @return \DOMDocument
     */
    private function checkFile()
    {
        $xml = new \DOMDocument();
        $xml->formatOutput = true;
        $xml->preserveWhiteSpace = false;
        $xml->load($this->_file) or die("");
        return $xml;
    }

}
