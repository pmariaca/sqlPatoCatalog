<?php include_once ("includes/SqlCatalog.inc.php"); ?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="description" content="catalog form queries" />
      <meta name="keywords" content="sql, catalog, mysql, edit, save" />
      <meta name="author" content="Patricia Mariaca" />

      <title>sqlPatoCatalog</title>
      <link rel="stylesheet" type="text/css" href="css/sqlCatalog.css">

      <script type="text/javascript" src="js/jquery.js"></script>
      <script src="dist/js/bootstrap.min.js"></script>
      <script type="text/javascript" src="dist/bootbox/bootbox.min.js"></script>
      <script src="dist/DataTables/media/js/jquery.dataTables.min.js"></script>
      <script src="dist/DataTables/plug-ins/integration/bootstrap/3/dataTables.bootstrap.min.js"></script>
      <!-- bootable capitalCity  -->
      <!--script type="text/javascript" src="dist/themes/bootable/js/scripts.js"></script-->      
      <script type="text/javascript" src="js/sqlCatalog.js"></script>
      <script>
         var msg1 = "<?php echo MSG_1; ?>" ;
         var msg2 = "<?php echo MSG_2; ?>" ;
      </script>
   </head>
   <body >

      <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
   <nav class="navbar navbar-default">
      <div class="container-fluid">
         <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-navy-navbar-collapse-1">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
         </div>
         <div class="collapse navbar-collapse" id="bs-navy-navbar-collapse-1">
            <form id="form_nav" class="navbar-form navbar-right" role="search">
               <!-- temp hidden, quitar, se va a leer del archivo -->
               <input type="hidden" id="flg_db" name="flg_db" value="<?= $flg; ?>">

               <div class="form-group">
                  <input type="text" class="form-control" placeholder="server ip" id="srv" name="srv" value="<?php echo $srv; ?>">
                  <input type="text" class="form-control" placeholder="Username" id="usr" name="usr" value="<?php echo $usr; ?>">
                  <input type="password" class="form-control" placeholder="password" id="pwd" name="pwd" value="">
                  <input type="button" class="btn btn-default" id="findSrv" name="findSrv" value=" GO ">
               </div>
            </form>
            <ul class="nav navbar-nav navbar-left">
               <li class="active"><a href="#mmodal" data-toggle="modal" data-target=".bs-mody-modal-lg"><?= MANEGE_GROUP ?></a></li>
            </ul>
         </div>
      </div>
   </nav>
   <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ --> 
   <div class="modal bs-mody-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true" id="mmodal">
      <div class="modal-dialog modal-sm">
         <div class="modal-content">
            <div class="modal-body">
               <div class="bs-mody">
                  <ul class="nav nav-tabs" id="ttab">
                     <li class="active"><a data-toggle="tab" href="#tab1" name="tab1"><?= NEW_GROUP ?></a></li>
                     <li><a data-toggle="tab" href="#tab2" name="tab2"><?= DELETE_GROUP ?></a></li>
                     <li><a data-toggle="tab" href="#tab3" name="tab3"><?= SRV_GROUP ?></a></li>
                  </ul>
                  <form id="form_mod" class="tab-content form-horizontal" role="form">
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                     <div id="tab1" class="tab-pane fade in active">
                        <input type="text" class="form-control" placeholder="New group name" id="nameGroup" name="nameGroup">
                     </div>
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                     <div id="tab2" class="tab-pane fade">
                        <div id="accordionMod">
                           <?php foreach($arrAccordion as $collapse => $group): ?>
                              <div class="panel-group" id="accordionMod<?= $collapse; ?>">    
                                 <div class="panel panel-default">
                                    <div class="panel-heading" >
                                       <input type="checkbox" class="grp" id="grp_<?= $collapse; ?>" name="grp_<?= $collapse; ?>" />
                                       <a data-toggle="collapse" data-parent="#accordionMod<?= $collapse; ?>" href="#nn_collapse<?= $collapse; ?>" ><?= $group['title']; ?></a>
                                    </div>

                                    <div id="nn_collapse<?= $collapse; ?>" class="panel-collapse collapse">
                                       <div class="panel-body">
                                          <ul class="list-group ">
                                             <?php if(array_key_exists('item', $group)): ?>
                                                <?php foreach($group['item'] as $k => $item): ?>
                                                   <li class="list-group-item">
                                                      <input type="checkbox" name="itemGrp_<?= $collapse; ?>_<?= $k; ?>"  />
                                                      <?= $item[0] ?>
                                                   </li>
                                                <?php endforeach; ?>
                                             <?php endif; ?>
                                          </ul>
                                       </div>
                                    </div>
                                 </div>    
                              </div>
                           <?php endforeach; ?> 
                        </div>                         
                     </div>
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                     <div id="tab3" class="tab-pane fade"><?= SRV_GROUP_INFO ?>
                        <span><?= SRV_GROUP_INFO2 ?></span>
                         <div class="input-group-sm">
                         <ul class="list-group ">
                           <li class="list-group-item">
                               <input type="checkbox" aria-label="true" name="itmSrv" id="itmSrv"><?= SRV_GROUP_1 ?>
                               <input type="text" class="form-control" disabled="disabled" aria-label="true" placeholder="server ip" name="itemSrv" id="itemSrv">
                           </li>
                           <li class="list-group-item">
                               <input type="checkbox" aria-label="true" name="itmUsr" id="itmUsr"><?= SRV_GROUP_2 ?>
                               <input type="text" class="form-control" disabled="disabled" aria-label="true" placeholder="login" name="itemUsr" id="itemUsr">
                           </li>
                           <li class="list-group-item">
                               <input type="checkbox" aria-label="true" name="itmPass" id="itmPass"><?= SRV_GROUP_3 ?>
                               <input type="text" class="form-control" disabled="disabled" aria-label="true" placeholder="pass"name="itemPass" id="itemPass" >
                           </li>
                           <li class="list-group-item">
                               <input type="checkbox" aria-label="true" name="itmAll" id="itmAll"><?= SRV_GROUP_4 ?>
                           </li>
                        </ul>
                        </div>
                     </div>
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                  </form>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default" data-dismiss="modal"><?= CANCEL ?></button>
               <button type="button" class="btn btn-primary" id="btnMod" name="btnMod"><?= ACCEPT ?></button>
            </div>
         </div>
      </div>
   </div>

   <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
   <div class="modal " tabindex="-1" id="mdlExplain">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">explain SQL</h4>
      </div>
      <div class="modal-body">
         <div class="alert alert-warning" role="alert" id="divExplainError" style="display: none;"></div>
         <div id="divExplain" ></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
   </div>
   </div>

   <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
   <div class="container-fluid">
       <form id="form_content" role="form"> 

           <!-- ---------------------------- CENTRAL PART  -------------------------------------------- -->          

           <div id="main_div" class="main_div" >
               <div id="divSelectDb" style="display: none;">
                   <span class="label label-default"></span>
                   <select class="form-control" id="selectDb" name="selectDb">
                       <?php if (is_array($arrDb)): ?>
                          <?php foreach ($arrDb as $db): ?>
                             <?= "<option>" . $db . "</option>"; ?>
                          <?php endforeach; ?>
                       <?php endif; ?>
                   </select>
               </div>  
               <div  class="alert alert-warning" role="alert" id="divMsgDb" style="display: none;"></div>

               <div id="loading" name="loading" style="display: none;">
                   <div class="progress" >
                       <div class="progress-bar progress-bar-success progress-bar-striped active" role="progressbar" 
                            aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                           <span class="sr-only">LOADING...</span>
                       </div>
                   </div>
               </div>

               <textarea id="strSql" name="strSql" rows="5" placeholder="SELECT * FROM"></textarea>
               <br/>
               <div class="alert alert-warning" role="alert" id="divResultError" style="display: none;"></div>
               <p class="badge" id="divResultInfo"></p>
               <div id="divResult" ></div>  
               
           </div>


           <!-- ---------------------------- MENUS -------------------------------------------- -->          
           <div class="menu_div" id="menu_div">
               <div class="panel-group" id="accordionN">    
                   <?php foreach ($arrAccordion as $collapse => $group): ?>
                      <div class="panel panel-default">
                          <div class="panel-heading">
                              <a data-toggle="collapse" data-parent="#accordionN" href="#n_collapse<?= $collapse; ?>" >
                                  <?= $group['title']; ?></a>
                          </div>
                          <div id="n_collapse<?= $collapse; ?>" class="panel-collapse collapse">
                              <div class="panel-body">
                                  <ul class="list-group ">
                                      <?php if (array_key_exists('item', $group)): ?>
                                         <?php foreach ($group['item'] as $k => $item): ?>
                                            <li class="list-group-item">
                                                <INPUT class="btn btn-link" type="button" onclick="MM_findObj('strSql').value = '<?= $item[1] ?>'" value="<?= $item[0] ?>" />
                                            </li>
                                         <?php endforeach; ?>
                                      <?php endif; ?>
                                  </ul>
                              </div>

                          </div>
                      </div>
                   <?php endforeach; ?>
               </div>  

           </div>


           <div class="menu2_div" id="menu2_div">
               <input type="button" class="btn btn-default" data-toggle="modal" data-target="#mdlExplain" id="explainSql" name="explainSql" value=" explain SQL">
               <br><br>
               <input type="button" class="btn btn-default" id="sendSql" name="sendSql" value="<?= FIND ?>">
               <br><br>
               <button type="button" class="btn btn-default" id="bigger" name="bigger">
                   <span class="glyphicon glyphicon-zoom-in" aria-hidden="true"></span>
               </button>
               <button type="button" class="btn btn-default" id="smoller" name="smoller">
                   <span class="glyphicon glyphicon-zoom-out" aria-hidden="true"></span>
               </button>

               <br><br>           

               <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#accordionA" id="btnAddSql">
                   <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
               </button>
               <div id="accordionA" class="collapse ">  
                   <div class="panel panel-default">
                       <div class="panel-body">
                           <ul class="list-group radio">
                               <?php foreach ($arrAccordion as $collapse => $group): ?>
                                  <li class="list-group-item">
                                      <input type="radio" name="addRadio" checked value="<?= $collapse; ?>"><?= $group['title']; ?>
                                  </li>    
                               <?php endforeach; ?>     
                           </ul>
                       </div>
                       <div id="div_add" name="div_add">
                           <input type="button" class="btn btn-default btn-sm" id="addSql" name="addSql" value="<?= SAVE ?>">
                       </div>
                   </div>
               </div>
           </div>
       </form>   
   </div>
  
</body>
</html>


