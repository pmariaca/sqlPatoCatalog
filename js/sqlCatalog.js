
$(document).ready(function() {
   var urlCatalog = "includes/SqlCatalog.inc.php?";
   MoreTabs.init('resultShow2');
   MoreTabs.init('resultShow');

   // action botons
   $("#findSrv").on('click', function() {
      var data = $("#form_nav").serializeArray();
      var id = this.id;
      $.ajax({
         url: urlCatalog+"go=db&type=findSrv",
         type: "POST",
         dataType: "json",
         data: data,
         beforeSend: function() {
            startLoad(id);
            hideMsgs(1);
            $('#selectDb option').remove();
            $('.divSelectDb').hide();
         },
         success: function(json) {
            stopLoad(id);
            if(msgError(id, json)){return;}
            jQuery.each(json, function(k, v) {
               $('#selectDb').append('<option>' + v + '</option>');
            });
            $('.divSelectDb').show();
            $("#selectDb").trigger('change');
         }, });
   });

//+++++++++++++++++++++++++++++++++++++++++++++++++++ menu2_div    


   $("#sendSql").on('click', function(event) {
       event.preventDefault();
      if($.trim(MoreTabs.getTabActive('oTextarea').val()) == "" || $("#selectDb").val() == null){return;}
      var srzArray = $("#form_content .selectDb,#form_nav").serializeArray();
      srzArray.push(MoreTabs.getTabActive('oStrSql'));
      var id = this.id;
      $.ajax({
         url: urlCatalog+"go=db&type=sqlResult",
         type: "POST",
         dataType: "json",
         data: srzArray,
         beforeSend: function() {
            startLoad(id);
            hideMsgs(MoreTabs.getTabActive('oResultError'));
         },
         success: function(json) {
            stopLoad(id);      
            $('#'+MoreTabs.getTabActive('sIdTab')+' .divResult').html('<table class="table table-striped table-hover" id="' + MoreTabs.getTabActive('sIdTab') + '_tblResult"></table>');
            if(msgError(id, json)){return;}  
            MoreTabs.createResult(json);
         },
         error: function() {
            //$('#info').html('<p>ups, an error has occurred</p>');
         },
      });
   });
      
   $("#explainSql").on('click', function() {
      if($.trim(MoreTabs.getTabActive('oTextarea').val()) == "" || $("#selectDb").val() == null){return;}
      var srzArray = $("#form_content .selectDb,#form_nav").serializeArray();
      srzArray.push(MoreTabs.getTabActive('oStrSql'));
      var id = this.id;
      $.ajax({
         url: urlCatalog+"go=db&type=explainSql",
         type: "POST",
         dataType: "json",
         data: srzArray,
         beforeSend: function() {
            hideMsgs(1);
         },
         success: function(json) {
            $('#divExplain').html('<table class="table table-striped table-hover" id="tblExplain"></table>');
            if(msgError(id, json)){return;}
            var header = [];
            jQuery.each(json.info, function(k, v) {
               header[k] = {'title': v};
            });
            $('#tblExplain').dataTable({
               'data': json.row,
               'columns': header,
               'scrollX': true,
               'searching': false,
               'paging': false,
               'ordering': false,
               'info': false
            });
         }
      });
   });
   
   $('#mdlExplain').on('show.bs.modal', function() {
      $('#divExplain').empty();
   });
   
   $("#selectDb").on('change', function() {
      $.ajax({
         url: urlCatalog+"go=db&type=showTbl",
         type: "POST",
         dataType: "json",
         data: $("#form_content,#form_nav").serializeArray(),
         beforeSend: function() {
            $('#showList .list-group').empty();
         },
         success: function(json) {
            $('#divShow').html('<select id="showList" name="showList"></select>');
            jQuery.each(json.row, function(k, v) {$('#showList').append('<option >' + v + '</option>');});
            $("#divShow select").selectr({
               title: ' ',
               placeholder: 'Search...',
               width: '100%',
               maxListHeight: '250px',
               tooltipBreakpoint: 5
            });
            $( ".selectr input" ).addClass( "input-sm" );
            $("#divShow").show();
         },
      });
   });

   $("#addSql").on('click', function() {
      var id = this.id;
      if($.trim(MoreTabs.getTabActive('oTextarea').val()) == ""){
         bootbox.alert(msg1);
         return;
      }
      bootbox.prompt(msg2, function(title) {
         if(title === null || $.trim(title)==""){
            return;
         }
         else{           
            var srzArray = $("#form_content").serializeArray();
            srzArray[0] = MoreTabs.getTabActive('oStrSql');
            $.ajax({
               url: urlCatalog+"go=xml&type=addItem&title=" + title,
               type: "POST",
               data: srzArray,
               beforeSend: function() {
                  hideMsgs(1);
               },
               success: function(msg) {
                  if(msgError(id, msg)){return;}
                  $("#menu_div").load(location.href + " #menu_div>*", "");
                  $("#accordionMod").load(location.href + " #accordionMod>*", "");
                  $("#accordionA").removeClass("in");                  
               },
            });
         }
      });
   });
   
   $(document).on("change", "#divShow select", function( ) {
      var strSql = MoreTabs.getTabActive('oTextarea');
      var iniPos = strSql.prop("selectionStart");
      var iniEndSelect = strSql.prop("selectionEnd");
      var txt = " " + $("#divShow li.selected .option-name").text() + " ";
      var endPos = iniPos + txt.length;
      strSql.val(strSql.val().slice(0, iniPos) + txt + strSql.val().slice( iniEndSelect));
      //document.getElementById('strSql').setSelectionRange(endPos,endPos);
   });
   
   $("#showMoreTab").on('click', function() {
      var isActive = $(this).hasClass("active");
      if(isActive){
         $('#resultShow2').hide();
         $(this).html(SHOW_MORE_TABS);
         $('#resultShow textarea').focus(); 
      }else{
         $('#resultShow2').show();
         $(this).html(HIDE_MORE_TABS);
         $('#resultShow2 textarea').focus(); 
      }
   });
   
//+++++++++++++++++++++++++++++++++++++++++++++++++++ nav
   $("#btnMod").on('click', function() {
      var id = this.id;
      var load = 0;
      var url;
      var data;
      var tabn = $("ul#ttab li.active a").attr('href');
      if(tabn == '#tab1'){
         load = 1;
         if($.trim($("#nameGroup").val()) == ""){return;}
         data = "";
         url = urlCatalog+"go=xml&type=addGroup&title=" + $("#nameGroup").val();
      }else if(tabn == '#tab2'){
         load = 1;
         data = $("#tab2 :input").serializeArray();
         url = urlCatalog+"go=xml&type=delGroup";
      }else if(tabn == '#tab3'){
         load = 2;
         if($("#tab3 :input[name='itmAll']").prop('checked') == false
            && $("#tab3 :input[name='itmSrv']").prop('checked') == false
            && $("#tab3 :input[name='itmUsr']").prop('checked') == false
            && $("#tab3 :input[name='itmPass']").prop('checked') == false
            ){
            $('#mmodal').modal('hide');
            return;
         }
         if($("#tab3 :input[name='itmAll']").prop('checked') == false
            && $("#tab3 :input[name='itemSrv']").prop('value') == ""
            && $("#tab3 :input[name='itemUsr']").prop('value') == ""
            && $("#tab3 :input[name='itemPass']").prop('value') == ""
            ){
            $('#mmodal').modal('hide');
            return;
         }
         data = $("#tab3 :input").serializeArray();
         url = urlCatalog+"go=db&type=saveSrv";
      }else if(tabn == '#tab4'){
         load = 2;
         data = $("#tab4 :input").serializeArray();
         url = urlCatalog+"go=vw&type=newView";
      }else{return;}
      
      $.ajax({
         url: url,
         type: "POST",
         //dataType: "json", // nopi
         data: data,
         beforeSend: function() {
            hideMsgs(1);
         },
         success: function(msg) {
            var error = msgError(id, msg);
            if(error){$('#mmodal').modal('hide');return;}
            if(load == 2){window.location.reload();}
            if(load == 1){
               $("#menu_div").load(location.href + " #menu_div>*", "");
               $("#accordionA .list-group").load(location.href + " #accordionA .list-group>*", "");
               $("#accordionMod").load(location.href + " #accordionMod>*", "");
            }
            $('#mmodal').modal('hide');
         },
      });
   });

   $("#form_mod #tab2 :checkbox").on('click', function() {
      var arr = this.name.split("_");
      if(arr[0] == 'grp'){
         $("#accordionMod" + arr[1] + " input").prop('checked', $(this).prop('checked'));
      }else{
         var arr = this.name.split("_");
         $("#grp_" + arr[1]).prop('checked', false);
      }
   });

   $("#form_mod #tab3 :checkbox").on('click', function() {
      if(this.id == 'itmAll' && $(this).prop('checked') == true){
         $("#tab3 :input[name='itmSrv'],:input[name='itmUsr'],:input[name='itmPass']").prop('checked', false);
         $("#tab3 input:text").prop('value', "");
         $("#tab3 input:text").prop('disabled', true);
      }else{
         $("#" + this.id + " + input:text").prop('disabled', $(this).prop('checked') ? false : true);
         if($(this).prop('checked') == true){
            $("#tab3 :input[name='itmAll']").prop('checked', false);
         }
      }
   });

//$('#mmodal').on('hidden.bs.modal', function () {
   $('#mmodal').on('show.bs.modal', function() {
      $("#nameGroup").prop('value', "");
      $("#form_mod input:checkbox").prop('checked', false);
      $("#form_mod input:text").prop('value', '');
      $("#form_mod #tab3 input:text").prop('disabled', true);
      $('#ttab a[href="#tab1"]').tab('show');
   });
   //+++++++++++++++++++++++++++++++++++++++++++++++++++  
   if($("#flg_db").val() == 3){
      $('#findSrv').hide();
      $('#usr').hide();
      $('#pwd').hide();
      $('#findSrv').trigger('click');
   }

   //+++++++++++++++++++++++++++++++++++++++++++++++++++  
   function msgError(id, data) {
//console.log( $.type(json) );console.log(id);console.log(json);
      if(jQuery.type(data) === "string" && data.substring(0, 1) == '['){
         return false;
      }
      
      if(id=="findSrv" && data == null){return true;}
      if($.trim(data) == "null"){return true;}

      var json = data;
      if( (id=="btnMod" || id=="addSql") && jQuery.type(data) === "string" && jQuery.type(data) != "undefined"){
         json = {'error':data};
       // json = JSON.parse(data); 
      }
      if($.trim(json.error) != ''){
         if(id=="btnMod" || id=="findSrv" || id=="addSql"){
            $('.divMsgDb').empty();
            $('.divMsgDb').append(json.error);
            $('.divMsgDb').show();
         }else if(id=="sendSql"){
            MoreTabs.sendError(json.error);
            
         }else if(id=="explainSql"){
            $('.divExplainError').empty();
            $('.divExplainError').append(json.error);
            $('.divExplainError').show();
         }
         return true;
      }
      return false;
   }
   function startLoad(id) {
      $('#loading').show();
      $('#' + id).button('loading');
   }
   function stopLoad(id) {
      $('#loading').hide();
      $('#' + id).button('reset');
      $('#' + id).dequeue();
   }
   function hideMsgs(tabn) {
      if(tabn!=1){
         tabn.hide();
      }
      $('.divMsgDb').hide();
      $('.divExplainError').hide();
   }


});
function putSql(sql)
{
   MoreTabs.getTabActive('oTextarea').val(sql);
}
