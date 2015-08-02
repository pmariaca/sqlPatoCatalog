<?php
namespace Catalog;

/**
 * Manage files: read and write files
 * @author axolote14
 */
class ManageFiles
{

    private $_iniHost = "configHost.ini";
    private $_iniView = "configView.ini";
    private $_fileHost = "";
    private $_fileView = "";

    /**
     * define the path
     */
    function __construct()
    {
        $this->_fileHost = PATH_SQLCATALOG.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.$this->_iniHost;
        $this->_fileView = PATH_SQLCATALOG.DIRECTORY_SEPARATOR."config".DIRECTORY_SEPARATOR.$this->_iniView;

        set_error_handler([new CustomError([$this->_fileHost]), 'errorHandler']);
    }

    /**
     * get data from config file
     * @return integer
     */
    public function findConfHost()
    {
        return $this->findFile($this->_fileHost);
    }

    /**
     * list of data server
     * @return array
     */
    public function findConfView()
    {
        return $this->findFile($this->_fileView);
    }

    private function findFile($theFile)
    {
        $flg = $f = 0;
        $arrConf = [];
        if ($file = fopen($theFile, 'r')) {
            $n = -1;
            while (($line = fgets($file, 4096)) !== false) {
                if (substr(trim($line), 0, 1) == ';' || trim($line) == "") {
                    continue;
                } elseif (substr(trim($line), 0, 6) == '[cnf]') {
                    $f = 1;
                    continue;
                } elseif (substr(trim($line), 0, 6) == '[srv]') {
                    $f = 2;
                    $n++;
                    continue;
                } elseif (substr(trim($line), 0, 6) == '[view]') {
                    $f = 3;
                    continue;
                }

                $arr = explode(':', $line);
                if ($f == 1) {
                    $flg = trim($arr[1]);
                }
                if ($f == 2) {
                    if (trim($arr[0]) == 'pass') {
                        $arrConf[$n][trim($arr[0])] = $this->decrypt(trim($arr[1]));
                    } else {
                        $arrConf[$n][trim($arr[0])] = trim($arr[1]);
                    }
                }
                if ($f == 3) {
                    $arrConf[trim($arr[0])] = trim($arr[1]);
                }
            }
        }
        fclose($file);
        $arrConf['flg'] = $flg;
        return $arrConf;
    }

    /**
     * save user data for mysql
     * @param array $request
     */
    public function saveConfHost($request)
    {
        $f1 = $f = 0;
        $p = $u = $s = "";
        $itemPass = $itemUsr = $itemSrv = "";
        if (isset($request['itemSrv']) && trim($request['itemSrv']) != "") {
            $f1++;
            $s = "s";
            $itemSrv = $request['itemSrv'];
        }
        if (isset($request['itemUsr']) && trim($request['itemUsr']) != "") {
            $f1++;
            $u = "u";
            $itemUsr = $request['itemUsr'];
        }
        if (isset($request['itemPass']) && trim($request['itemPass']) != "") {
            $f1++;
            $p = "p";
            $itemPass = $request['itemPass'];
        }
        if ($f1 == 3) {
            $f = 3;
        }
        if ($f1 < 3) {
            $f = 1;
        }
        if ($f1 == 0) {
            $f = 0;
        }
        $arr[] = "[cnf]";
        $arr[] = "f:".$f.":".$s.":".$u.":".$p;
        $arr[] = "[srv]";
        $arr[] = "name:1";
        $arr[] = "srv:".$itemSrv;
        $arr[] = "user:".$itemUsr;
        $arr[] = "pass:".$this->encrypt($itemPass);
        $this->saveFile($this->_fileHost, $arr);
    }

    /**
     * save user data for mysql
     * @param array $request
     */
    public function saveConfView($request)
    {
        if (isset($request['less']) && trim($request['less']) != "") {
            $less = $request['less'];
        }
        $arr[] = "[view]";
        $arr[] = "view:1";
        $arr[] = "item:".$less;
        $this->saveFile($this->_fileView, $arr);
    }

    private function saveFile($file, $arr)
    {
        foreach ($arr as $k => $val) {
            if ($k == 0) {
                file_put_contents($file, $val."\n");
            } else {
                file_put_contents($file, $val."\n", FILE_APPEND);
            }
        }
    }

    /**
     * encrypt password
     * @param string $string
     * @return string
     */
    private function encrypt($string)
    {
        $key = "marijuana!";
        $result = '';
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) + ord($keychar));
            $result.=$char;
        }
        return base64_encode($result);
    }

    /**
     * decrypt password
     * @param string $string
     * @return string
     */
    private function decrypt($string)
    {
        $key = "marijuana!";
        $result = '';
        $string = base64_decode($string);
        for ($i = 0; $i < strlen($string); $i++) {
            $char = substr($string, $i, 1);
            $keychar = substr($key, ($i % strlen($key)) - 1, 1);
            $char = chr(ord($char) - ord($keychar));
            $result.=$char;
        }
        return $result;
    }

}
