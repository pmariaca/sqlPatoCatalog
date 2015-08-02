<?php

/**
 * @copyright Copyright (c) Patricia Mariaca Hajducek
 * @license http://opensource.org/licenses/MIT
 * 
 * The MIT License (MIT)
 * 
 * Copyright (c) Patricia Mariaca Hajducek (axolote14)
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace Catalog;

define('PATH_SQLCATALOG', str_replace('\\', '/', realpath(substr(dirname(__FILE__), 0, 0 - strlen('includes')))));
include_once ("Lang/lang.php");

/* * ************************************************************************************* */

/**
 * 
 *
 * @author Patricia Mariaca Hajducek (axolote14)
 * @version 1.1
 */
class Catalog
{

    private $_request = [];
    private $_srv = "";
    private $_usr = "";
    private $_pwd = "";
    private $_go = "";
    private $_type = "";

    /**
     * Register Catalog's autoloader
     */
    public static function registerAutoloader()
    {
        spl_autoload_register(__NAMESPACE__."\\Catalog::autoload");
    }

    /**
     * Catalog autoloader
     */
    public static function autoload($class)
    {
        $prefix = 'Catalog\\';
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            return;
        }

        $base_dir = PATH_SQLCATALOG.DIRECTORY_SEPARATOR."includes".DIRECTORY_SEPARATOR;
        $relative_class = substr($class, $len);

        $file = $base_dir.str_replace('\\', '/', $relative_class).'.php';
        if (file_exists($file)) {
            include_once $file;
        }
    }

    /**
     * 
     * @param array $request
     */
    function __construct($request = [])
    {
        $requestPost = filter_input_array(INPUT_POST, $request, FILTER_SANITIZE_STRING);
        $requestGet = filter_input_array(INPUT_GET, $request, FILTER_SANITIZE_STRING);

        $this->setVar(['srv', 'usr', 'pwd'], $requestPost, $requestGet);
        $this->setVar(['go', 'type'], $requestGet, $requestPost);
        $this->cleanArray($requestPost);
        $this->cleanArray($requestGet);

        $this->_request = array_merge($requestPost, $requestGet);
    }

    /**
     * create page
     */
    public function run()
    {
        if (empty($this->_request)) {
            // first time
            $this->constructPage();
        } else {
            $this->findTodo();
        }
    }

    private function constructPage()
    {
        $f = new ManageFiles();
        $arrHost = $f->findConfHost();
        $iniXml = new CatalogXml();

        ob_clean();
        $page = new View\Page($f->findConfView());
        $page->setPage(
                [
            'flg' => $arrHost['flg'],
            'srv' => $arrHost[0]['srv'],
            'usr' => $arrHost[0]['user']
                ], $iniXml->readCatalog()
        );
        echo $page->renderPage();
    }

    /**
     * 
     * @return array
     */
    private function findTodo()
    {
        if ($this->_go == 'db') {
            $this->requestDb();
        } elseif ($this->_go == 'xml') {
            $this->requestXml();
        } elseif ($this->_go == 'vw') {
            $this->requestView();
        }
    }

    /**
     * search databases
     * @return array
     */
    private function requestDb()
    {
        if ($this->_type == 'saveSrv') {
            $save = false;
            if (isset($this->_request['itemSrv']) && isset($this->_request['itemUsr']) && isset($this->_request['itemPass'])) {
                $conn = new CatalogDb();
                $save = $conn->testConnection($this->_request['itemSrv'], $this->_request['itemUsr'], $this->_request['itemPass']);
            } else {
                $save = true;
            }
            if ($save === true) {
                $f = new ManageFiles();
                $f->saveConfHost($this->_request);
            }
        } else {
            $this->jsonRequestDb();
        }
    }

    /**
     * 
     */
    private function jsonRequestDb()
    {
        $selectDb = "";
        if (isset($this->_request['selectDb']) && trim($this->_request['selectDb']) != "") {
            $selectDb = $this->_request['selectDb'];
        }

        $conn = new CatalogDb($selectDb, $this->_srv, $this->_usr, $this->_pwd);
        if ($this->_type == 'findSrv') {
            $arrResult = $conn->getDB();
            //$arrResult2 = $conn->getReservedWord();
            //$arrResult = array_merge($arrResult,$arrResult2);
        } elseif ($this->_type == 'sendSql') {
            $arrResult = $conn->getTblResult($this->_request['strSql']);
        } elseif ($this->_type == 'explainSql') {
            $arrResult = $conn->getExplainTables($this->_request['strSql']);
        } elseif ($this->_type == 'showTbl') {
            $arrResult = $conn->getShowTables();
        } elseif ($this->_type == 'showHlp') {
            $arrResult = $conn->getShowHelp();
        } elseif ($this->_type == 'showListHelp') {
            $arr = explode(':', $this->_request['showListHelp']);
            $arrResult = $conn->getShowExpHelp($arr[0]);
        }

        ob_clean();
        echo json_encode($arrResult);
    }

    /**
     * manipulate xml
     */
    private function requestXml()
    {
        $strTitle = $strSql = "";
        if (isset($this->_request['strSql'])) {
            $strSql = $this->_request['strSql'];
        }
        if (isset($this->_request['title'])) {
            $strTitle = $this->_request['title'];
        }

        $conn = new CatalogXml($strSql, $strTitle);
        if ($this->_type == 'addItem' && isset($this->_request['strSql'])) {
            $conn->addItem($this->_request['addRadio']);
        } elseif ($this->_type == 'addGroup') {
            $conn->addGroup();
        } elseif ($this->_type == 'delGroup') {
            $arr = $this->loadGroups($this->_request);
            // delete group
            if (!empty($arr[0])) {
                $conn->deleteGroup($arr[0]);
            }
            // delete item
            if (!empty($arr[1])) {
                $conn->deleteItem($arr[1]);
            }
        }
    }

    /**
     * change view theme
     */
    private function requestView()
    {
        $f = new ManageFiles();
        $f->saveConfView($this->_request);
    }

    /**
     * desglose array
     * @param array $request
     * @return array
     */
    private function loadGroups($request)
    {
        $arrItem = $arrGrp = [];
        $grp = "";
        foreach ($request as $k => $val) {
            if ($val != 'on') {
                continue;
            }
            $arr = explode("_", $k);
            if ($arr[0] == 'grp') {
                $arrGrp[$arr[1]] = $arr[1];
                $grp = $arr[1];
                continue;
            } elseif ($arr[0] == 'itemGrp' && $arr[1] == $grp) {
                continue;
            } else {
                $arrItem[$arr[1]][] = $arr[2];
            }
            $grp = "";
        }
        return [$arrGrp, $arrItem];
    }

    /**
     * set private variables
     * @param array $arr
     * @param array $arr1
     * @param array $arr2
     */
    private function setVar($arr, &$arr1, &$arr2)
    {
        foreach ($arr as $k => $v) {
            $p = "_".$v;
            $this->$p = $arr1[$v];
            unset($arr1[$v]);
            unset($arr2[$v]);
        }
    }

    /**
     * delete empty element of array
     * @param array $arr
     */
    private function cleanArray(&$arr)
    {
        foreach ($arr as $k => $v) {
            if (is_null($v)) {
                unset($arr[$k]);
            }
        }
    }

}
