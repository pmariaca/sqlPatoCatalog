<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html lang="en">
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta name="description" content="catalog form mysql queries" />
      <meta name="keywords" content="sql, catalog, mysql, mysqli, edit, save" />
      <meta name="author" content="Patricia Mariaca" />
      <title>sqlPatoCatalog</title>
      <?php echo $getHeader; ?>
   </head>
   <body>
   <nav class="navbar navbar-default navbar-static-top">
      <div class="container-fluid">
         <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mynavbar">
               <span class="sr-only">Toggle navigation</span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
               <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="#"></a>
         </div>
         <div class="collapse navbar-collapse" id="mynavbar">
            <form id="form_nav" class="navbar-form navbar-right" role="search">
               <!-- solo se usa para visualizacion -->
               <input type="hidden" id="flg_db" name="flg_db" value="<?= $flg; ?>">

               <div class="form-group">
                  <input type="text" class="form-control" placeholder="server ip" id="srv" name="srv" value="<?= $srv; ?>">
                  <input type="text" class="form-control" placeholder="Username" id="usr" name="usr" value="<?= $usr; ?>">
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
   <div class="modal bs-mody-modal-lg" tabindex="-1" role="dialog" aria-labelledby="mmodalLabel" aria-hidden="true" id="mmodal">
      <div class="modal-dialog modal-sm">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
               <h4 class="modal-title"></h4>
            </div>
            <div class="modal-body">
               <div class="bs-mody">
                  <ul class="nav nav-tabs" id="ttab">
                     <li class="active"><a data-toggle="tab" href="#tab1" name="tab1"><?= NEW_GROUP ?></a></li>
                     <li><a data-toggle="tab" href="#tab2" name="tab2"><?= DELETE_GROUP ?></a></li>
                     <li><a data-toggle="tab" href="#tab3" name="tab3"><?= SRV_GROUP ?></a></li>
                     <li><a data-toggle="tab" href="#tab4" name="tab4"><?= CHANGE_VIEW ?></a></li>
                  </ul>
                  <form id="form_mod" class="tab-content form-horizontal" role="form">
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                     <div id="tab1" class="tab-pane fade in active">
                        <input type="text" class="form-control" placeholder="New group name" id="nameGroup" name="nameGroup">
                     </div>
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                     <div id="tab2" class="tab-pane fade">
                        <div id="accordionMod" class="myaccordion">
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
                                 <input type="text" class="form-control input-sm" disabled="disabled" aria-label="true" placeholder="server ip" name="itemSrv" id="itemSrv">
                              </li>
                              <li class="list-group-item">
                                 <input type="checkbox" aria-label="true" name="itmUsr" id="itmUsr"><?= SRV_GROUP_2 ?>
                                 <input type="text" class="form-control input-sm" disabled="disabled" aria-label="true" placeholder="login" name="itemUsr" id="itemUsr">
                              </li>
                              <li class="list-group-item">
                                 <input type="checkbox" aria-label="true" name="itmPass" id="itmPass"><?= SRV_GROUP_3 ?>
                                 <input type="text" class="form-control input-sm" disabled="disabled" aria-label="true" placeholder="pass" name="itemPass" id="itemPass" >
                              </li>
                              <li class="list-group-item">
                                 <input type="checkbox" aria-label="true" name="itmAll" id="itmAll"><?= SRV_GROUP_4 ?>
                              </li>
                           </ul>
                        </div>
                     </div>
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                     <div id="tab4" class="tab-pane fade">
                        <div class="input-group-sm">                    
                           <ul class="list-group ">                             
                           <?php foreach($arrWatch as $k => $less): ?>
                              <?php $checked = "";
                              if($k == $themeItem){
                                 $checked = "checked";
                              }; ?>
                              <li class="list-group-item">
                                 <input type="radio" name="less" <?= $checked; ?> value="<?= $k; ?>"><?= $less; ?>
                              </li>    
                           <?php endforeach; ?> 
                           </ul>
                        </div>
                     </div>
                     <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
                  </form>
               </div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-sm" data-dismiss="modal"><?= CANCEL ?></button>
               <button type="button" class="btn btn-primary btn-sm" id="btnMod" name="btnMod"><?= ACCEPT ?></button>
            </div>
         </div>
      </div>
   </div>

   <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
   <div class="modal " tabindex="-1" role="dialog" aria-labelledby="mdlExplainLabel" id="mdlExplain">
      <div class="modal-dialog modal-lg">
         <div class="modal-content">
            <div class="modal-header">
               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
               <h4 class="modal-title">explain SQL</h4>
            </div>
            <div class="modal-body">
               <div class="alert alert-warning divExplainError" role="alert"></div>
               <div id="divExplain" ></div>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Close</button>
            </div>
         </div>
      </div>
   </div>

   <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
   <div class="container-fluid">
      <form id="form_content" class="form-inline" role="form"> 

         <!-- ---------------------------- CENTRAL PART  -------------------------------------------- -->          
         <div id="main_div" class="main_div" >
            <div class="divSelectDb" >
               <select class="form-control selectDb" id="selectDb" name="selectDb">
               <?php if(is_array($arrDb) && !empty($arrDb)): ?>
                  <?php foreach($arrDb as $db): ?>
                     <?= "<option>" . $db . "</option>"; ?>
                  <?php endforeach; ?>
               <?php endif; ?>
               </select>

               <input type="button" class="btn btn-default" id="sendSql" name="sendSql" value="<?= FIND ?>">
               <input type="button" class="btn btn-default" id="explainSql" name="explainSql" value=" explain SQL">            
               <input type="button" data-toggle="button" class="btn btn-default" id="showProcessList" name="showProcessList" value="<?= SHOW_PROCESSLIST ?>">
               <button type="button" data-toggle="button" class="btn btn-default" id="showHelp" name="showHelp">
                  <span class="glyphicon glyphicon-info-sign" aria-hidden="true"></span>
               </button>
            </div>

            <div  class="alert alert-warning divMsgDb" role="alert"></div>

            <div id="loading" name="loading" style="display: none;">
               <div class="progress" >
                  <div class="progress-bar progress-bar-info progress-bar-striped active" role="progressbar" 
                       aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%">
                     <span class="sr-only">LOADING...</span>
                  </div>
               </div>
            </div>
            <div id="divResultShow">
               <div id="resultShow" class="resultShow"></div>   
               <div id="resultShow2" class="resultShow"></div>   
            </div>
         </div>

         <!-- ---------------------------- MENUS -------------------------------------------- -->          
         <div class="menu_div myaccordion" id="menu_div">
            <div class="panel-group" id="accordionN">    
            <?php foreach($arrAccordion as $collapse => $group): ?>
               <div class="panel panel-default">
                  <div class="panel-heading">
                     <a data-toggle="collapse" data-parent="#accordionN" href="#n_collapse<?= $collapse; ?>" ><?= $group['title']; ?></a>
                  </div>
                  <div id="n_collapse<?= $collapse; ?>" class="panel-collapse collapse">
                     <div class="panel-body">
                        <ul class="list-group ">
                        <?php if(array_key_exists('item', $group)): ?>
                           <?php foreach($group['item'] as $k => $item): ?>
                              <li class="list-group-item">
                                 <INPUT class="btn btn-link" type="button" onClick="putSql('<?= $item[1] ?>');" value="<?= $item[0] ?>" />
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

            <input type="button" class="btn btn-default" id="addItem" name="addItem" value="<?= SAVE ?>">
            <button type="button" class="btn btn-default" data-toggle="collapse" data-target="#accordionA" id="btnAddItem">
               <span class="glyphicon glyphicon-save" aria-hidden="true"></span>
            </button>
            <div id="accordionA" class="collapse ">  
               <div class="panel panel-default">
                  <div class="panel-body">
                     <ul class="list-group radio">
                        <?php foreach($arrAccordion as $collapse => $group): ?>
                           <li class="list-group-item">
                              <input type="radio" name="addRadio" checked value="<?= $collapse; ?>"><?= $group['title']; ?>
                           </li>    
                        <?php endforeach; ?>     
                     </ul>
                  </div>
               </div>
            </div>                    
            <div id="divShow" class="divShow"></div>
            <button type="button" class="btn btn-default" data-toggle="button" aria-pressed="false" autocomplete="off" id="showMoreTab" name="showMoreTab">
            <?= SHOW_MORE_TABS ?>
            </button>
            <!-- +++++++++++++++++++++++++++++++++++++++++++++++++ -->
         </div>
      </form>   
   </div>

   <div class="navbar navbar-default navbar-fixed-bottom" id="navBottom">
      <div class="container-fluid">
         <form id="form_bottom" > 
            <div class="row" id="navProcList">
               <p class="navbar-text">My footer content goes here...  navProcList</p>
            </div>
            <div class="row" id="navHelp">
               <div class="col-sm-4 col-md-4">
                  <div id="divShowHelp" class="divShow"></div>
               </div>
               <div class="col-sm-8 col-md-8">
                  <div id="divExpainHelp"></div>
               </div>
            </div>
         </form>
      </div>
   </div>

</body>
</html>