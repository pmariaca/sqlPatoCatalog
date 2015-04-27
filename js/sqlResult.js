;(function(MoreTabs, $, undefined) {
   var numTab = 1;
   var idDivContainer = '';

   /**
    * create first tab
    * @param {string} idDiv, name of container
    * @returns {undefined}
    */
   MoreTabs.init = function(idDiv)
   {
      idDivContainer = idDiv;
      numTab = 1;
      var ul = $('<ul>').addClass('nav nav-tabs resultTab').attr('id', idDivContainer+'resultTab');
      var li = $('<li>').addClass('tab_plus').attr('id', idDivContainer+'tab_plus').appendTo(ul);
      $('<span>').attr('aria-hidden', 'true').addClass('glyphicon glyphicon-plus-sign').appendTo(li);
      ul.appendTo('#' + idDivContainer);
      $('<div>').addClass('tab-content').appendTo('#' + idDivContainer);
      addTab();
   };

   /**
    * load active tab
    * @param {string} type
    * @returns {$|_L1.MoreTabs.getTabActive.Anonym$0}
    */
   MoreTabs.getTabActive = function(type){
      return tabActive(type);
   }
   
   function tabActive(type){
      var tabActive = $('ul#'+idDivContainer+'resultTab li.active a');
      if(type==='sIdTab'){
         return tabActive.attr('name');
       
      }else if(type==='oStrSql'){
         return  {name:'strSql', value:$(tabActive.attr('href') + ' textarea').val()};
         
      }else if(type==='oTextarea'){
         return $(tabActive.attr('href') + ' textarea');
         
      }else if(type==='oResultError'){
         return $(tabActive.attr('href') + ' .divResultError');
         
      }else if(type==='oResultInfo'){
         return $(tabActive.attr('href') + ' .divResultInfo');
         
      }
   };
   
   /**
    * create DataTable for result
    * @param {JSON} json
    * @returns {undefined}
    */
   MoreTabs.createResult = function(json){
      // clean
      $('#'+tabActive('sIdTab')+' .divResult').html('<table class="table table-striped table-hover" id="' + tabActive('sIdTab') + '_tblResult"></table>');
      var header = [];
      $.each(json.info, function(k, v) {
         header[k] = {"title": v};
      });
      //tabActive('oResultInfo').html(json.numRows);
      //tabActive('oResultInfo').show();
      // create table
      $('#'+tabActive('sIdTab')+'_tblResult').dataTable({
          'dom': 'T<"clear">lfrtip',
          'tableTools': {
          'sSwfPath': 'dist/DataTables/extensions/TableTools/swf/copy_csv_xls_pdf.swf',
          'aButtons':    [ 'copy','csv', 'pdf' ]
          },                
         'data': json.row,
         'columns': header,
         'scrollX': true,
         'ordering': false,
         'iDisplayLength': 10,
         'aLengthMenu': [[5, 10, 15, 25, 50, 100, -1], [5, 10, 15, 25, 50, 100, 'All']]
      });
      $( "div.DTTT a" ).addClass( "input-sm" );
   }
   
   MoreTabs.sendError = function(error){
      tabActive('oResultError').empty();
      tabActive('oResultError').append(error);
      tabActive('oResultError').show();
      //tabActive('oResultInfo').hide();
   }
   
   /**
    * create new tab
    * @returns {undefined}
    */
   function addTab()
   {
      if($('#'+idDivContainer+'resultTab li').length==2){numTab=2;}
      if(numTab == 1){
         var li = $('<li>').addClass('active');
      }else{
         var li = $('<li>');
      }
      var a = $('<a>').attr('data-toggle', 'tab').attr('name', idDivContainer+'tab_' + numTab).attr('href', '#'+idDivContainer+'tab_' + numTab).addClass('tabRst').text('tab_' + numTab).appendTo(li);
      if(numTab != 1){
         $('<span>').attr('aria-hidden', 'true').addClass('glyphicon glyphicon-remove-sign closeTab').appendTo(a);
      }
      li.insertBefore($('#'+idDivContainer+'tab_plus'));
      if(numTab == 1){
         var div1 = $('<div>').attr('id', idDivContainer+'tab_' + numTab).addClass('tab-pane active in');
      }else{
         var div1 = $('<div>').attr('id', idDivContainer+'tab_' + numTab).addClass('tab-pane');
      }
      /*
       // tool-bar
       var div2 = $('<div>').addClass('divToolBar').appendTo(div1); 
       var btn1 = $('<button>').attr('type', 'button').addClass('btn btn-default bigger').appendTo(div2); 
       $('<span>').attr('aria-hidden', 'true').addClass('glyphicon glyphicon-zoom-in').appendTo(btn1);
       
       var btn2 = $('<button>').attr('type', 'button').addClass('btn btn-default smoller').appendTo(div2);
       $('<span>').attr('aria-hidden', 'true').addClass('glyphicon glyphicon-zoom-out').appendTo(btn2);
       */
      $('<textarea>').attr('rows', '5').attr('placeholder', 'SELECT * FROM').addClass('strSql').appendTo(div1);
      $('<div>').addClass('alert alert-warning divResultError').appendTo(div1);
      //$('<p>').addClass('badge divResultInfo').appendTo(div1);
      $('<div>').addClass('divResult').appendTo(div1);

      div1.appendTo($('#' + idDivContainer + ' .tab-content'));
      numTab++;
   }
   
   $(document).on("click", ".tabRst", function() {
      idDivContainer = $(this).closest('div').attr('id');
   });
   
   $(document).on("click", "textarea", function() {
      idDivContainer = $(this).parents('.resultShow').attr('id');
   });
   
   $(document).on("focus", "textarea", function() {
      idDivContainer = $(this).parents('.resultShow').attr('id');
   });
   
   $(document).on("click", ".tab_plus", function() {
      idDivContainer = $(this).closest('div').attr('id');
      addTab();
      $('#'+idDivContainer + ' .resultTab a:last').tab('show');
      tabActive('oTextarea').focus();
   });

   $(document).on("click", '.closeTab', function() {
      idDivContainer = $(this).closest('div').attr('id');
      var tabId = $(this).parents('li').children('a').attr('href');
      $(this).parents('li').remove('li');
      $(tabId).remove();
      $('#'+idDivContainer+'resultTab a:last').tab('show');
   });

   $(document).on('click', '.bigger', function() {
      var curSize = parseInt(tabActive('oTextarea').css('font-size'));
      if(curSize <= 20){
         curSize = curSize + 2;
      }
      tabActive('oTextarea').css('font-size', curSize);
   });

   $(document).on('click', '.smoller', function() {
      var curSize = parseInt(tabActive('oTextarea').css('font-size'));
      if(curSize > 15){
         curSize = curSize - 2;
      }
      tabActive('oTextarea').css('font-size', curSize);
   });

}(window.MoreTabs = window.MoreTabs || {}, jQuery));
