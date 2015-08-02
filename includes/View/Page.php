<?php
namespace Catalog\View;

class Page
{
    private $_arrH = "";
    private $_strScript = "";
    private $_arrAccordion = [];
    private $_arrLink = [];
    private $_arrScript = [];
    private $_arrCte = [
        'MSG_1' => MSG_1,
        'MSG_2' => MSG_2,
        'SHOW_MORE_TABS' => SHOW_MORE_TABS,
        'HIDE_MORE_TABS' => HIDE_MORE_TABS,
        'SHOW_PROCESSLIST' => SHOW_PROCESSLIST,
        'HIDE_PROCESSLIST' => HIDE_PROCESSLIST
    ];
    private $_arrWatch0 = ['watchDarkly', 'watchCosmo', 'watchSlate', 'watchSuperHero'];
    private $_arrTheme = ['bootable'];
    private $_arrWatch = [];
    private $_themeItem = 0;
    private $_arrSugest = ['ALL', 'AS', 'ASC', 'CASE', 'CROSS', 'DELETE', 'DESC', 'DISTINCT', 'DISTINCTROW', 'DUMPFILE', 'DUPLICATE', 'ELSE', 'ELSEIF', 'FORCE', 'FROM', 'GROUP', 'GROUP_CONCAT', 'HAVING', 'INDEX', 'INNER', 'INSERT', 'INTO', 'JOIN', 'LEFT', 'LIMIT', 'ORDER', 'OUTER', 'REPLACE', 'RIGHT', 'SELECT', 'SHOW', 'THEN', 'UNION', 'UPDATE', 'USING', 'WHEN', 'WHERE', 'WHILE'];

    function __construct($arrView)
    {
        // no sirve el modal = , 'watchSpacelab',
        // letras en help no se ven watchCosmo,, 'watchFlatly'
        $themeView = $arrView["view"];
        $this->_themeItem = $themeItem = $arrView["item"];
        if (count($this->_arrWatch0) == $themeItem) {
            $themeView = 2;
        }
        // cuando no hay conexion a internet, es el unico que no llama googlapis
        //$themeView = 0; 
        //$themeItem = 0;
        $themeCss = "";
        $themeCss1 = "";
        $themeJs = "";
        $this->_arrWatch = array_merge($this->_arrWatch0, $this->_arrTheme);
        if ($themeView == 1) {
            $themeCss = "dist/less/".$this->_arrWatch[$themeItem]."/bootstrap.min.css";
            $themeCss1 = "dist/less/".$this->_arrWatch[$themeItem]."/variables.less";
        } elseif ($themeView == 2) {
            $themeCss = "dist/themes/bootable/css/styles.css";
            $themeJs = "dist/themes/bootable/js/scripts.js";
        }
        $this->createArray($themeCss, $themeCss1, $themeJs);
        $this->createScript();
    }

    /**
     * 
     * @param type $themeCss - css links to files themes watch
     * @param type $themeCss1 - css links to others themes
     * @param type $themeJs - js links to others themes
     */
    private function createArray($themeCss, $themeCss1, $themeJs)
    {
        $this->_arrLink = [
            "dist/css/bootstrap.min.css",
            $themeCss,
            $themeCss1,
            "dist/DataTables/plug-ins/integration/bootstrap/3/dataTables.bootstrap.css",
            "css/catalog.css",
            //"css/sqlCatalog_grid.css",
            "css/sqlResult.css"
        ];
        $this->_arrScript = [
            "js/jquery.js",
            "js/asuggest/jquery.a-tools-1.4.1.js",
            "js/asuggest/jquery.asuggest.js",
            "dist/selectr/dist/selectr.js",
            "dist/js/bootstrap.min.js",
            "dist/bootbox/bootbox.min.js",
            $themeJs,
            "dist/DataTables/media/js/jquery.dataTables.min.js",
            "dist/DataTables/extensions/TableTools/js/dataTables.tableTools.js",
            "dist/DataTables/plug-ins/integration/bootstrap/3/dataTables.bootstrap.min.js",
            "js/catalog.js",
            "js/sqlResult.js"
        ];
    }

    /**
     * joins css & css links to create header
     */
    private function createScript()
    {
        $strScript = "";
        foreach ($this->_arrLink as $css) {
            if ($css != "") {
                $strScript .= "<link rel='stylesheet' href='".$css."' type='text/css'>\n";
            }
        }
        foreach ($this->_arrScript as $js) {
            if ($js != "") {
                $strScript .= "<script type='text/javascript' src='".$js."'></script>\n";
            }
        }
        $strScript .= "<script>";
        foreach ($this->_arrCte as $k => $v) {
            $strScript .= "var ".$k."='".$v."';\n";
        }
        $strScript .= "var suggests = ".$this->wordSuggest().";\n";
        $strScript .= "</script>\n";
        $this->_strScript = $strScript;
    }

    private function wordSuggest()
    {
        $arr = [];
        foreach ($this->_arrSugest as $val) {
            $arr[] = strtolower($val);
        }
        return json_encode(array_merge($this->_arrSugest, $arr));
    }

    /**
     * 
     * @param type $arrH - init conf variables
     * @param type $arrAccordion - init xml data
     */
    public function setPage($arrH, $arrAccordion)
    {
        $this->_arrH = $arrH;
        $this->_arrAccordion = $arrAccordion;
    }

    public function renderPage()
    {
        $arr = array_merge($this->_arrH);
        $arr['arrAccordion'] = $this->_arrAccordion;
        $arr['themeItem'] = $this->_themeItem;
        $arr['getHeader'] = $this->_strScript;
        $arr['arrWatch'] = $this->_arrWatch;
        extract($arr);
        ob_start();
        include('catalogPage.php');
        $content = ob_get_contents();
        ob_end_clean();
        return $content;
    }

}
