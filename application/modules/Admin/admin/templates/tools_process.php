<div class="x3-submenu">
    <?=$modules->menu()?>
</div>
   <div class="x3-main-content">
        <div id="x3-buttons" x3-layout="buttons"></div>
        <div id="x3-header" x3-layout="header">Процессы</div>
        <div class="x3-paginator" x3-layout="paginator"></div>
        <div class="x3-functional" x3-layout="functional"></div>
        <div id="x3-container">
            <br/>
            <table id="res" style="margin-top:10px"></table>
        </div>
    </div>
<script>
    var data = <?=json_encode($process)?>;
    jQuery("#res").jqGrid({ 
        datatype: "local", 
        height: 250, 
        colNames:['PID','Имя','CPU%','USER','GROUP'], 
        colModel:[
            {name:'pid',index:'pid',width:100,sorttype:'int'},
            {name:'cmd',index:'cmd',width:100,sorttype:'string'},
            {name:'cpu',index:'cpu',width:100,sorttype:'float'},
            {name:'user',index:'user',width:100,sortable:false},
            {name:'group',index:'group',width:100,sortable:false}
        ],
        sortname:'cpu',
        sortorder:'desc',
        multiselect: false, 
        caption: "Процессы" 
    });
    function update(){
        jQuery("#res").html('');
        for(var i=0;i<=data.length;i++) {
            jQuery("#res").jqGrid('addRowData',i+1,data[i]);
        }
        setTimeout(function(){data = $.loadJSON('/Admin_Tools/process');update();},2000);
    }
    update()
</script>