/**
 * @author Patricia Mariaca Hajducek (axolote14)
 * @version 1.0.3
 * @license http://opensource.org/licenses/MIT
 */
$(document).ready(function() {
   var urlCatalog = "includes/SqlCatalog.inc.php?";
   var flgIni = 0;
   var tabSendSql; 
   MoreTabs.init('resultShow2');
   MoreTabs.init('resultShow');

   // action botons
   $("#findSrv").on('click', function() {
      if($.trim($("#srv").val()) == "" || $.trim($("#usr").val()) == "" || $.trim($("#pwd").val()) == "")return;
      doAjax('db', this.id, 'json', $("#form_nav").serializeArray());
   });
   
   $("#sendSql").on('click', function() {
      if($.trim(MoreTabs.getTabActive('oTextarea').val()) == "" || $("#selectDb").val() == null)return;
      var data = $("#form_content .selectDb,#form_nav").serializeArray();
      data.push(MoreTabs.getTabActive('oStrSql'));
      tabSendSql = MoreTabs.getTabActive('sIdTab');
      doAjax('db', this.id, 'json', data);
   });

   $("#explainSql").on('click', function() {
      if($.trim(MoreTabs.getTabActive('oTextarea').val()) == "" || $("#selectDb").val() == null)return;
      var data = $("#form_content .selectDb,#form_nav").serializeArray();
      data.push(MoreTabs.getTabActive('oStrSql'));
      $('#mdlExplain').modal('show');
      doAjax('db', this.id, 'json', data);
   });

   $('#mdlExplain').on('show.bs.modal', function() {
      $('#divExplain').empty();
   });

   $("#selectDb").on('change', function() {
      doAjax('db', 'showTbl', 'json', $("#form_content,#form_nav").serializeArray(), ['divShow', 'showList', 250]);
   });

   $("#addItem").on('click', function() {
      var id = this.id;
      if($.trim(MoreTabs.getTabActive('oTextarea').val()) == ""){
         bootbox.alert(MSG_1);
         return;
      }
      bootbox.prompt(MSG_2, function(title) {
         if(title === null || $.trim(title) == "")return;
         else{
            var data = $("#form_content").serializeArray();
            data[0] = MoreTabs.getTabActive('oStrSql');
            doAjax('xml', id, 'html', data, ["addItem&title=" + title]);
         }
      });
   });

   $(document).on("change", "#divShow select", function( ) {
      var strSql = MoreTabs.getTabActive('oTextarea');
      var iniPos = strSql.prop("selectionStart");
      var iniEndSelect = strSql.prop("selectionEnd");
      var txt = " " + $("#divShow li.selected .option-name").text() + " ";
      var endPos = iniPos + txt.length;
      strSql.val(strSql.val().slice(0, iniPos) + txt + strSql.val().slice(iniEndSelect));
      //document.getElementById('strSql').setSelectionRange(endPos,endPos);
   });

   $("#showMoreTab").on('click', function() {
      if($(this).hasClass("active")){
         $('#resultShow2').hide();
         $(this).html(SHOW_MORE_TABS);
         $('#resultShow textarea').focus();
      }else{
         $('#resultShow2').show();
         $(this).html(HIDE_MORE_TABS);
         $('#resultShow2 textarea').focus();
      }
   });

   $("#showProcessList").on('click', function() {
      if($(this).hasClass("active")){
         $(this).attr('value', SHOW_PROCESSLIST);
         $('#navProcList').hide();
         if(!$("#showHelp").hasClass("active")){
            $('#navBottom').hide();
         }
      }else{
         $(this).attr('value', HIDE_PROCESSLIST);
         $('#navProcList').show();
         $('#navBottom').show();
      }
   });

   $("#showHelp").on('click', function() {
      if($(this).hasClass("active")){
         $('#navHelp').hide();
         if(!$("#showProcessList").hasClass("active")){
            $('#navBottom').hide();
         }
      }else{
         if(flgIni == 0){
            flgIni = 1;
            doAjax('db', 'showHlp', 'json', $("#form_content,#form_nav").serializeArray(), ['divShowHelp', 'showListHelp', 90]);
         }
         $('#navHelp').show();
         $('#navBottom').show();
      }
   });

   $(document).on("change", "#divShowHelp select", function( ) {
      var data = $("#form_nav").serializeArray();
      data.push({name: 'showListHelp', value: $("#divShowHelp li.selected .option-name").text()});
      doAjax('db', this.id, 'json', data);
   });

//+++++++++++++++++++++++++++++++++++++++++++++++++++ nav
   $("#btnMod").on('click', function() {
      var tabn = $("ul#ttab li.active a").attr('href');
      if(tabn == '#tab1'){
         if($.trim($("#nameGroup").val()) == "")return;
         doAjax('xml', 'addGroup', 'html', $("#tab2 :input").serializeArray(), ["addGroup&title=" + $("#nameGroup").val()]);

      }else if(tabn == '#tab2'){
         doAjax('xml', 'delGroup', 'html', $("#tab2 :input").serializeArray());

      }else if(tabn == '#tab3'){
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
         doAjax('db', 'saveSrv', 'html', $("#tab3 :input").serializeArray());
      }else if(tabn == '#tab4'){
         doAjax('vw', 'newView', 'html', $("#tab4 :input").serializeArray());
      }
      $('#mmodal').modal('hide');
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

   $('#mmodal').on('show.bs.modal', function() {
      $("#nameGroup").prop('value', "");
      $("#form_mod input:checkbox").prop('checked', false);
      $("#form_mod input:text").prop('value', '');
      $("#form_mod #tab3 input:text").prop('disabled', true);
      $('#ttab a[href="#tab1"]').tab('show');
   });

   if($("#flg_db").val() == 3){
      $('#findSrv').hide();
      $('#usr').hide();
      $('#pwd').hide();
      doAjax('db', 'findSrv', 'json', $("#form_nav").serializeArray());
   }
   //+++++++++++++++++++++++++++++++++++++++++++++++++++  

   function doAjax(go, id, dataType, data) {
      var sendType = id;
      var extraParam;
      if(arguments.length == 5){ // showTbl, showHlp
         extraParam = arguments[4];
         if(go == 'xml'){ // addItem, addGroup
            sendType = arguments[4];
         }
      }

      $.ajax({
         url: urlCatalog + "go=" + go + "&type=" + sendType,
         type: "POST",
         dataType: dataType,
         data: data,
         beforeSend: function() {
            if(id == 'findSrv'){
               beforeFindSrv(id);
            }else if(id == 'sendSql'){
               startLoad(id);      
               hideMsgs(MoreTabs.getTabActive('oResultError'));
            }else if(id == 'explainSql' || id == 'addItem' || id == 'saveSrv'
               || id == 'delGroup' || id == 'addGroup' || id == 'newView'){
               hideMsgs(1);
            }else if(id == 'showTbl' || id == 'showHlp'){
               $('#' + extraParam[1] + ' .list-group').empty();
            }else if(id == 'showListHelp'){
               $('#divExpainHelp').empty();
            }
         },
         success: function(json) {
            if(id == 'findSrv'){
               successFindSrv(id, json);
            }else if(id == 'sendSql'){
               successSendSql(id, json);
            }else if(id == 'explainSql'){
               successExplainSql(id, json);
            }else if(id == 'showTbl' || id == 'showHlp'){
               successShow(json, extraParam[0], extraParam[1], extraParam[2]);
            }else if(id == 'addItem'){
               successAddItem(id, json);
            }else if(id == 'saveSrv' || id == 'newView'){
               if(!msgError(id, json))window.location.reload();
            }else if(id == 'delGroup' || id == 'addGroup'){
               if(!msgError(id, json)){
                  $("#menu_div").load(location.href + " #menu_div>*", "");
                  $("#accordionA .list-group").load(location.href + " #accordionA .list-group>*", "");
                  $("#accordionMod").load(location.href + " #accordionMod>*", "");
               }
            }else if(id == 'showListHelp'){
               successShowListHelp(id, json);
            }
         },
         error: function() {
            //$('#info').html('<p>ups, an error has occurred</p>');
         },
      });
   }
   function beforeFindSrv(id) {
      startLoad(id);
      hideMsgs(1);
      $('#selectDb option').remove();
      $('.divSelectDb').hide();
   }
   function successFindSrv(id, json) {
      stopLoad(id);
      if(msgError(id, json))return;
      jQuery.each(json['row'], function(k, v) {
         $('#selectDb').append('<option>' + v + '</option>');
      });
      $('.divSelectDb').show();
      $('#selectDb').trigger('change');
   }

   function successSendSql(id, json) {
      stopLoad(id);
      $('#' + tabSendSql + ' .divResult').html('<table class="table table-striped table-hover" id="' + tabSendSql + '_tblResult"></table>');      
      if(msgError(id, json))return;
      MoreTabs.createResult(json, tabSendSql);
   }

   function successExplainSql(id, json) {
      $('#divExplain').html('<table class="table table-striped table-hover" id="tblExplain"></table>');
      stopLoad(id);
      if(msgError(id, json))return;
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

   function successAddItem(id, msg) {
      if(msgError(id, msg))return;
      $("#menu_div").load(location.href + " #menu_div>*", "");
      $("#accordionMod").load(location.href + " #accordionMod>*", "");
      $("#accordionA").removeClass("in");
   }

   function successShow(json, div, list, size) {
      $('#' + div).html('<select id="' + list + '" name="' + list + '"></select>');
      jQuery.each(json.row, function(k, v) {
         var d = v[0];
         $('#' + list).append('<option>' + d + '</option>');
      });
      $('#' + div + ' select').selectr({
         title: ' ',
         placeholder: 'Search...',
         width: '100%',
         maxListHeight: size + 'px',
         tooltipBreakpoint: 5
      });
      $('#' + div + ' .selectr input').addClass("input-sm");
      $('#' + div).show();
   }

   function successShowListHelp(id, json) {
      if(msgError(id, json))return;
      var html = json['row'][0][0].replace(/\n/g, "<br />");
      var strUrl = html.slice(html.lastIndexOf("URL: ") + 5, html.length).replace(/<br \/>/g, "");
      html = html.replace(strUrl, '<strong><a href="' + strUrl + '" target="_blank">' + strUrl + '</a></strong>');
      html = html.replace(/\[/g, "<strong>[</strong>");
      var arr = $("#divShowHelp li.selected .option-name").text().split(':');
      $.each([']', '{', '}', $.trim(arr[1])], function(i, value) {
         var search = new RegExp(value, 'g');
         html = html.replace(search, "<strong>" + value + "</strong>");
      });
      $('#divExpainHelp').append(html);
   }
   //+++++++++++++++++++++++++++++++++++++++++++
   function msgError(id, data) {
      //console.log($.type(data));console.log(id); console.log(data);
      if($.trim(data) == "null")return true;

      var json = data;
      if((id == "saveSrv" || id == 'delGroup' || id == "addItem" || id == 'newView')
         && $.type(data) === "string" && $.trim(data) != ""){
         json = JSON.parse(data);
      }

      if($.trim(json.error) != ''){
         if(id == "sendSql"){
            MoreTabs.sendError(json.error, tabSendSql);
         }else if(id == "explainSql"){
            $('.divExplainError').empty();
            $('.divExplainError').append(json.error);
            $('.divExplainError').show();
         }else{
            $('.divMsgDb').empty();
            $('.divMsgDb').append(json.error);
            $('.divMsgDb').show();

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
      if(tabn != 1){
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
