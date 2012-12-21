X3['Grabber'] = new X3_Module('Grabber',{
    onCreate:function(){
        //this.table = $.loadJSON('/admin/json/module/Menu',false);
        this.title = 'Грабер';
    },
    onCall:function(){
        var div = null;
        if(typeof $("#glinks")[0] != 'undefined')
            div = $("#glinks");
        else
            div = $('<div />').css({'margin-top':'5px'}).attr('id','glinks')
        X3.layouts['content'].html('').append("<h2>Грабер</h2>")
        X3.layouts['content'].append('Поиск: <input id="GRAB" type="text" size="20" />')
        .append($('<button />').html('Найти').click(function(){
            var q = $("#GRAB").val();
            var result = $.loadJSON('/admin/grab/?q='+q,false)
            if(result.length>0){
                div.html('');
                for(i in result){
                    var a = $('<a />').attr('href','#').html(result[i]['text'])
                    a.click(function(){
                        var props = $.loadJSON('/admin/grab/?modelid='+result[i]['modelid']+'&hid='+result[i]['hid'],false);
                        return false;
                    })
                    div.append($('<div />').append(a));
                }
                X3.layouts['content'].append(div);
            }
        }))
    },
    onSort:function(){return 1;}
})