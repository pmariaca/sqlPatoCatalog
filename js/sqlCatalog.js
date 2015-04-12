$(document).ready(function() {
   // action botons
   $("#findSrv").click(function() {
      var data = $("#form_nav").serializeArray();
      var id = this.id;
      jQuery.ajax({
         url: "includes/SqlCatalog.inc.php?go=db&type=findSrv",
         type: "POST",
         //dataType: "json", // na-nais
         data: data,
         beforeSend: function() {
            startLoad(id);
            hideMsgs();
            $('#selectDb option').remove();
            $('#selectDb').hide();
         },
         success: function(data) {
            var error = msgError(id, data);
            if(error){
               stopLoad(id);
               return;
            }
            var obj = data;
            if(!$.isPlainObject(data)){obj = JSON.parse(data);}
            jQuery.each(obj, function(k, v) {
               $('#selectDb').append('<option>' + v + '</option>');
            });
            $('#selectDb').show();
            $("#selectDb").trigger('change');
            stopLoad(id);
         }, });
   });

//+++++++++++++++++++++++++++++++++++++++++++++++++++ menu2_div   

   $("#sendSql").click(function() {
      if($.trim($("#strSql").val()) == "" || $("#selectDb").val() == null){return;}
      var id = this.id;
      jQuery.ajax({
         url: "includes/SqlCatalog.inc.php?go=db&type=sqlResult",
         type: "POST",
         dataType: "json",
         data: $("#form_content,#form_nav").serializeArray(),
         beforeSend: function() {
            startLoad(id);
            hideMsgs();
         },
         success: function(json) {
            stopLoad(id);
            $('#divResult').html('<table class="table table-striped table-hover" id="tblResult"></table>');
            var error = msgError(id, json);
            if(error){
               $('#divResultError').html(json['error']);
               $('#divResultError').show();
               return;
            }
            var header = [];
            jQuery.each(json['info'], function(k, v) {
               header[k] = {"title": v};
            });
            $('#divResultInfo').html(json['numRows']);
            $('#divResultInfo').show();
            $('#tblResult').dataTable({
               /*    "dom": 'T<"clear">lfrtip',
                "tableTools": {
                "sSwfPath": "copy_csv_xls_pdf.swf",
                "aButtons": [
                "copy",
                "print",
                {
                "sExtends":    "collection",
                "sButtonText": "Save",
                "aButtons":    [ "csv", "xls", "pdf" ]
                }
                ]
                },*/
               'data': json['row'],
               'columns': header,
               'scrollX': true,
               'ordering': false,
               'iDisplayLength': 15,
               'aLengthMenu': [[5, 10, 15, 25, 50, 100, -1], [5, 10, 15, 25, 50, 100, 'All']]
            });
            //$( ".dataTables_paginate ul" ).addClass( "pagination-sm" );
         }
      });
   });
   
   $("#explainSql").click(function() {
      if($.trim($("#strSql").val()) == "" || $("#selectDb").val() == null){return;}
      var id = this.id;
      jQuery.ajax({
         url: "includes/SqlCatalog.inc.php?go=db&type=explainSql",
         type: "POST",
         dataType: "json",
         data: $("#form_content,#form_nav").serializeArray(),
         beforeSend: function() {
            startLoad(id);
            hideMsgs();
         },
         success: function(json) {
            $('#divExplain').html('<table class="table table-striped table-hover" id="tblExplain"></table>');
            stopLoad(id);
            var error = msgError(id, json);
            if(error){
               $('#divExplainError').html(json['error']);
               $('#divExplainError').show();
               return;
            }
            var header = [];
            jQuery.each(json['info'], function(k, v) {
               header[k] = {'title': v};
            });
            $('#tblExplain').dataTable({
               'data': json['row'],
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
   
   $("#selectDb").on('change', function() {
      jQuery.ajax({
         url: "includes/SqlCatalog.inc.php?go=db&type=showTbl",
         type: "POST",
         dataType: "json",
         data: $("#form_content,#form_nav").serializeArray(),
         beforeSend: function() {
            $('#showList .list-group').empty();
         },
         success: function(json) {
            $('#divShow').html('<select id="showList" name="showList"></select>');
            jQuery.each(json['row'], function(k, v) {$('#showList').append('<option >' + v + '</option>');});
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
 
$( "xtextarea" ).bind({
keypress: function(event) {   
      if(event.which==13){
         console.log('----ENTER----');
         findPos = $(this).val().lastIndexOf("#");
         endRow = $(this).val().lastIndexOf("\n");
         var str = $(this).val().substring(findPos, endRow); 
         if(findPos!=-1 && findPos<endRow){
            console.log(str + '  ' +findPos+'  '+endRow);
            }
      }
   }
});

   $(document).on("change", "#divShow select", function( ) {
      var iniPos = $('#strSql').prop("selectionStart");
      var txt = " " + $("#divShow li.selected .option-name").text() + " ";
      var endPos = iniPos + txt.length;
      $("#strSql").val($("#strSql").val().slice(0, iniPos) + txt + $("#strSql").val().slice( iniPos));
      document.getElementById('strSql').setSelectionRange(endPos,endPos);
   });

   $("#addSql").on('click', function() {
      if($.trim($("#strSql").val()) == ""){
         bootbox.alert(msg1);
         return;
      }
      bootbox.prompt(msg2, function(title) {
         if(title === null || $.trim(title)==""){
            return;
         }
         else{           
            jQuery.ajax({
               url: "includes/SqlCatalog.inc.php?go=xml&type=addItem&title=" + title,
               type: "POST",
               data: $("#form_content").serializeArray(),
               success: function(msg) {
                  if(msg.length > 1){
                     bootbox.alert(msg);
                  }else{
                     $("#menu_div").load(location.href + " #menu_div>*", "");
                     $("#accordionMod").load(location.href + " #accordionMod>*", "");
                     $("#accordionA").removeClass("in");
                  }
               },
            });
         }
      });
   });

   $("#bigger").click(function() {
      var curSize = parseInt($('#strSql').css('font-size'));
      if(curSize <= 20){
         curSize = curSize + 2;
      }
      $('#strSql').css('font-size', curSize);
   });

   $("#smoller").click(function() {
      var curSize = parseInt($('#strSql').css('font-size'));
      if(curSize > 15){
         curSize = curSize - 2;
      }
      $('#strSql').css('font-size', curSize);
   });

   $("#bold").click(function() {
      $('#strSql').css('font-weight', 'bold');
   });
   $("#italic").click(function() {
      $('#strSql').css('font-style', 'italic');
   });
   $("#color").click(function() {
      $('#strSql').css('color', 'red');
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
         url = "includes/SqlCatalog.inc.php?go=xml&type=addGroup&title=" + $("#nameGroup").val();
      }else if(tabn == '#tab2'){
         load = 1;
         data = $("#tab2 :input").serializeArray();
         url = "includes/SqlCatalog.inc.php?go=xml&type=delGroup";
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
         url = "includes/SqlCatalog.inc.php?go=db&type=saveSrv";
      }else if(tabn == '#tab4'){
         load = 2;
         data = $("#tab4 :input").serializeArray();
         url = "includes/SqlCatalog.inc.php?go=vw&type=newView";
      }else{return;}
      
      jQuery.ajax({
         url: url,
         type: "POST",
         data: data,
         beforeSend: function() {
            hideMsgs();
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

   function msgError(id, json) {
//console.log( $.type(json) );console.log(id);console.log(json);
      if(jQuery.type(json) === "string" && json.substring(0, 1) == '['){
         return false;
      }
      if(id=="btnMod" && $.trim(json) == ""){return false;}
      if($.trim(json) == "null"){return true;}
      if(jQuery.type(json) === "string"){
         json = JSON.parse('{"error":["' + json + '"]}');
      }
      if($.trim(json['error']) != ''){
         $('#divMsgDb').empty();
         $('#divMsgDb').append(json['error']);
         $('#divMsgDb').show();
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
   function hideMsgs() {
      $('#divMsgDb').hide();
      $('#divResultInfo').hide();
      $('#divResultError').hide();
      $('#divExplainError').hide();
   }


});