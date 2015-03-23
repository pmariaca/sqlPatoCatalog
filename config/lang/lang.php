<?php
$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
switch ($lang){
   case "es":
      define('ACCEPT','Guardar');
      define('CANCEL','Cerrar');
      define('SAVE','Guardar sql');
      define('FIND','Buscar SQL!!!');
      define('NEW_GROUP','Nuevo grupo');
      define('MANEGE_GROUP','Personaliza tu catálogo');
      define('DELETE_GROUP','Borrar grupo/item');
      define('SRV_GROUP','Guardar login');
      define('SRV_GROUP_1','Guardar ip');
      define('SRV_GROUP_2','Guardar login');
      define('SRV_GROUP_3','Guardar password');
      define('SRV_GROUP_4','Borrar configuracion actual');
      define('SRV_GROUP_INFO','Los datos seleccionados se guardarán en un archivo (configHost.ini), de otra manera siempre se requeriran.');
      define('SRV_GROUP_INFO2','Por ahora, solo los datos de una ip se puede guardar');
      define('MSG_1','escribir busqueda');
      define('MSG_2','etiqueta para el sql');
      
      define('FILE_NOT_EXIST','No existe el archivo');
      define('DIR_NOT_EXIST','No existe el directorio');
      define('DIR_NOT_WRITABLE','No se puede escribir en el directorio, verifique permisos');
      break;       
   default:
      define('ACCEPT','Save changes');
      define('CANCEL','Close');
      define('SAVE','Save sql');
      define('FIND','Find SQL!!!');
      define('NEW_GROUP','New group');
      define('MANEGE_GROUP','Manage your catalog');
      define('DELETE_GROUP','Delete group/item');
      define('SRV_GROUP','Save login');
      define('SRV_GROUP_1','Save ip');
      define('SRV_GROUP_2','Save login');
      define('SRV_GROUP_3','Save password');
      define('SRV_GROUP_4','Delete all actual data from conf');
      define('SRV_GROUP_INFO','The selected data will be saved in a file (configHost.ini), otherwise always will be asked for.');
      define('SRV_GROUP_INFO2','For now, just the content of one ip can be saved');
      define('MSG_1','put some query');
      define('MSG_2','name for sql');
     
      define('FILE_NOT_EXIST','File doesn\'t exist');
      define('DIR_NOT_EXIST','Directory doesn\'t exists ');
      define('DIR_NOT_WRITABLE','Directory is not writable, verify permissions');
      break;
}
?>