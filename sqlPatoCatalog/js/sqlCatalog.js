$(document).ready(function() {
   // action botons
   $("#findSrv").click(function() {
      var data = $("#form_nav").serializeArray();
      var id = this.id;
      jQuery.ajax({
         url : "includes/SqlCatalog.inc.php?go=db&type=findSrv",
         type: "POST",
         data: data,
         beforeSend: function() {
            startLoad(id); 
            hideMsgs();
            $('#selectDb option').remove();
            $('#divSelectDb').hide();
         },
         success: function(data) {
            if(jQuery.type(data) === "string" && data.substring(0,1)!='['){
               $('#divMsgDb').empty();
               if($.trim(data) != 'null'){
                  $('#divMsgDb').append(data);
                  $('#divMsgDb').show();
               }
            }else{
               var obj = data;
               if (!$.isPlainObject(data)) {obj = JSON.parse(data);}                      
               jQuery.each(obj, function(k, v) {
                  $('#selectDb').append('<option>'+v+'</option>');
               }); 
               $('#divSelectDb').show();
            }
            stopLoad(id);
         },
      });
   });
   
   function msgError(id, data){
      if(jQuery.type(data) === "string" && data.substring(0,1)=='<'){
         $('#divMsgDb').empty();
         if($.trim(data) != 'null'){
            $('#divMsgDb').append(data);
            $('#divMsgDb').show();
            return true;
      }}
      return false;
   }
   function startLoad(id){
      $('#loading').show();
      $('#'+id).button('loading'); 
   }
   function stopLoad(id){
      $('#loading').hide();
      $('#'+id).button('reset');
      $('#'+id).dequeue();
   }
   function hideMsgs(){
      $('#divMsgDb').hide();
      $('#divResultInfo').hide();
      $('#divResultError').hide();
      $('#divExplainError').hide();
   }
//+++++++++++++++++++++++++++++++++++++++++++++++++++ menu2_div 
   
   $("#sendSql").click(function() {
      if($.trim($("#strSql").val())==""){return;}
      if($("#selectDb").val()==null){return;}
      var id = this.id;
      jQuery.ajax({
         url: "includes/SqlCatalog.inc.php?go=db&type=sqlResult",
         type: "POST",
         data: $("#form_content,#form_nav").serializeArray(),
         beforeSend: function() {
            startLoad(id);
            hideMsgs();
         },
         success: function(data) { 
            $('#divResult').html( '<table class="table table-striped table-hover" id="tblResult"></table>' );
            var error = msgError(id, data);
            if(error){stopLoad(id);return;}
            var obj = data;
            if (!$.isPlainObject(data)) {obj = JSON.parse(data);}  
            var header = [];
            jQuery.each(obj['info'], function(k, v) {
               header[k] = {"title":v};
            });
            if (header.length > 0){
               $('#divResultInfo').html(obj['numRows'] );
               $('#divResultInfo').show();
               $('#tblResult').dataTable( {
                       "data": obj['row'],
                       "columns":header,
                       "scrollX": true,
                       "iDisplayLength": 25,
                       "aLengthMenu": [[25, 50, 100, -1], [25, 50, 100, "All"]]
                   } );    
             }else{
                $('#divResultError').html(obj['error'] );
                $('#divResultError').show();
             }
             stopLoad(id);
         },
      });
   });
    
    $("#explainSql").click(function() {
      if($.trim($("#strSql").val())==""){return;}
      if($("#selectDb").val()==null){return;}
      var id = this.id;
      jQuery.ajax({
       url: "includes/SqlCatalog.inc.php?go=db&type=explainSql",
       type: "POST",
       data : $("#form_content,#form_nav").serializeArray(),
       beforeSend: function() {
            startLoad(id);
            hideMsgs();
       },
       success:function(data){
         var error = msgError(id, data);
         stopLoad(id);
         if(error){stopLoad(id);return;}
         var obj = data;
         if (!$.isPlainObject(data)) {obj = JSON.parse(data);}  
          var header = [];
          jQuery.each(obj['info'], function(k, v) {
            header[k] = {"title":v};
         });

         $('#divExplain').html( '<table class="table table-striped table-hover" id="tblExplain"></table>' );
         if (header.length > 0){
            $('#tblExplain').dataTable( {
                 "data": obj['row'],
                 "columns":header,
                 "scrollX": true,
                 "searching": false,
                 "paging": false,
                 "ordering": false,
                 "info": false
             } );     
          }else{
             $('#divExplainError').html(obj['error'] );
             $('#divExplainError').show();
          }
       },
      });
   });
           
   $("#showTbl").click(function() {
   });
   
   $("#addSql").on('click',function() {    
      if($.trim($("#strSql").val())==""){
         bootbox.alert(msg1);
         return;
      }
      bootbox.prompt(msg2, function(title) {
      if (title === null) {return;}
      else{
         jQuery.ajax({
            url : "includes/SqlCatalog.inc.php?go=xml&type=addItem&title="+title,
            type: "POST",
            data : $("#form_content").serializeArray(),
            success:function(msg){
               if(msg.length>1){
                  bootbox.alert(msg);
               }else{
                  $("#menu_div").load(location.href+" #menu_div>*","");
                  $("#accordionMod").load(location.href+" #accordionMod>*","");
               }
            },
           });
         }
      });
  });
  
   $("#bigger").click(function() {
      var curSize= parseInt($('#strSql').css('font-size'));
      if(curSize<=20){
         curSize = curSize+2;
      }
      $('#strSql').css('font-size', curSize);
   });
   
   $("#smoller").click(function() {
      var curSize= parseInt($('#strSql').css('font-size'));
      if(curSize>15){
         curSize = curSize-2;
      }
      $('#strSql').css('font-size', curSize);
   });
  
//+++++++++++++++++++++++++++++++++++++++++++++++++++ nav
  $("#btnMod").on('click', function() { 
     var load = 0;
     var url;
     var data;
     var tabn = $("ul#ttab li.active a").attr('href');
     if(tabn=='#tab1'){
        load = 1;
        if($.trim($("#nameGroup").val())==""){return;}
        data = "";
        url = "includes/SqlCatalog.inc.php?go=xml&type=addGroup&title="+$("#nameGroup").val();
     }else if(tabn=='#tab2'){
        load = 1;
        data = $("#tab2 :input").serializeArray();
        url = "includes/SqlCatalog.inc.php?go=xml&type=delGroup";
     }else if(tabn=='#tab3'){
        load = 2;
        
        if($("#tab3 :input[name='itmAll']").prop('checked')==false
           && $("#tab3 :input[name='itmSrv']").prop('checked')==false
           && $("#tab3 :input[name='itmUsr']").prop('checked')==false
           && $("#tab3 :input[name='itmPass']").prop('checked')==false
           ){$('#mmodal').modal('hide'); return;}
        if($("#tab3 :input[name='itmAll']").prop('checked')==false
           && $("#tab3 :input[name='itemSrv']").prop('value')==""
           && $("#tab3 :input[name='itemUsr']").prop('value')==""
           && $("#tab3 :input[name='itemPass']").prop('value')==""
           ){$('#mmodal').modal('hide'); return;}
        data = $("#tab3 :input").serializeArray();
        url = "includes/SqlCatalog.inc.php?go=db&type=saveSrv";
     }else{
        return;
     }
      jQuery.ajax({
       url : url,
       type: "POST",
       data : data,
       success:function(msg){
          if(msg.length>1){
             bootbox.alert(msg);
          }else{
             if(load==2){
             window.location.reload();
            }
          }
          if(load==1){
            $("#menu_div").load(location.href+" #menu_div>*","");
            $("#accordionA .list-group").load(location.href+" #accordionA .list-group>*","");
            $("#accordionMod").load(location.href+" #accordionMod>*","");
          }
          $('#mmodal').modal('hide');          
       },
      });
  });
  
$("#form_mod #tab2 :checkbox").on('click', function () {
  var arr = this.name.split("_"); 
  if(arr[0]=='grp'){
    $("#accordionMod"+arr[1]+" input").prop('checked', $(this).prop('checked'));
  }else{
    var arr = this.name.split("_"); 
    $("#grp_"+arr[1]).prop('checked', false);
  }
});

$("#form_mod #tab3 :checkbox").on('click', function () {
   if(this.id=='itmAll' && $(this).prop('checked')==true){
      $("#tab3 :input[name='itmSrv'],:input[name='itmUsr'],:input[name='itmPass']").prop('checked', false);
      $("#tab3 input:text").prop('value', "");
      $("#tab3 input:text").prop('disabled', true);
   }else{
      $("#"+this.id+" + input:text").prop('disabled', $(this).prop('checked')?false:true);
      if($(this).prop('checked')==true){
         $("#tab3 :input[name='itmAll']").prop('checked', false);
      }
   }
});

//$('#mmodal').on('hidden.bs.modal', function () {
$('#mmodal').on('show.bs.modal', function () {
   $("#nameGroup").prop('value',"");
   $("#form_mod input:checkbox").prop('checked', false);
   $("#form_mod input:text").prop('value', '');
   $("#form_mod #tab3 input:text").prop('disabled', true);
   $('#ttab a[href="#tab1"]').tab('show');
});
//+++++++++++++++++++++++++++++++++++++++++++++++++++  
  if($("#flg_db").val()==3){
      $('#findSrv').hide();
      $('#usr').hide();
      $('#pwd').hide();
      $('#findSrv').trigger('click');
   }
 });
//+++++++++++++++++++++++++++++++++++++++++++++++++++   
function MM_findObj(n, d) {
  	var p,i,x;
  	if(!d) d=document;
  	if((p=n.indexOf('?'))>0&&parent.frames.length) {
	    d=parent.frames[n.substring(p+1)].document; n=n.substring(0,p);}
  	if(!(x=d[n])&&d.all) x=d.all[n];
  	for (i=0;!x&&i<d.forms.length;i++) x=d.forms[i][n];
  	for(i=0;!x&&d.layers&&i<d.layers.length;i++) x=MM_findObj(n,d.layers[i].document);
  	if(!x && d.getElementById) x=d.getElementById(n);
  	return x;
}
