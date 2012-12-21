const X3_DEBUG = true;
var BIG = '';
function parseFn(json){
    for(i in json){
        if(typeof json[i] == 'object'){
            json[i] = parseFn(json[i]);
        }else if(typeof json[i] == 'string' && (/^function[\(|\s]/i).test(json[i])){
            json[i] = eval('('+json[i]+')');
        }
    }
    return json;
}
$.growl = function(msg,type,t){
    if(t==null) t = 5000;
    if(type==null) type='error';
    if(type == 'error') type = '#FFCECE'
    if(type == 'success' || type == 'ok') type = '#E0FFCE';
    if(typeof $("#x3-log-stack")[0] == 'undefined'){
        $('body').append(
            $('<div />').css({'position':'fixed','left':'10px','top':'10px','width':'400px','z-index':'999'}).attr('id','x3-log-stack')
        );
    }
    var div = $('<div />').css({'background':type,'height':'50px','border-radius':'10px',
        'display':'table','opacity':'0'})
        .append($('<div />').css({'display':'table-cell','vertical-align':'middle','width':'330px','margin':'0 auto','text-align':'center'}).html(msg));
    $('#x3-log-stack').append(div);
    div.click(function(){div.fadeOut(function(){$(this).remove()})})
    div.animate({'opacity':'0.9'},function(){setTimeout(function(){div.fadeOut(function(){$(this).remove()})},t)})
    return div;
}
$.loadCss = function(css){
       $("head").append("<link>");
       cssDom = $("head").children(":last");
       cssDom.attr({rel:  "stylesheet",
                 type: "text/css",
                 href: css
                });  
}
function ajax(args){
    var js = '';
    var callback = null;
    var async = false;
    var res = {};
    var data = {};
    var cc = args['options'].cache;//caching
    var type = args['options'].dataType;//dataType
    var method = args['options'].method;//dataType
    delete args['options'];
    var j = 0;
    for(i in args){
        switch(typeof args[i]){
            case "string":
                if(j==0)
                    js = args[i];
                else
                    data = args[i];
                break;
            case "boolean":
                async = args[i];
                break;
            case "function":
                callback = args[i];
                break;
            case "object":
                data = args[i];
                break;
        }
        j++;
    }
    $.ajax({
        type:method,
        dataType:type,
        url:js,
        async:async,
        cache:cc,
        data:data,
        success:function(m){
            if(type == 'json')
                parseFn(m);
            //if(X3_DEBUG) $.growl('Загрузка '+js+' прошла успешно!','ok');
            if(callback != null) callback.call(this,m);
            res = m;
        },
        error:function(e){
            if(typeof console == 'object' && typeof console.log != 'undefined')
                console.log(e)
            $.growl('Ошибка загрузки '+js);
        }        
    });
    return res;    
}

$.loadJs = function(){
    var args = arguments;
    args['options'] = {'cache':false,'dataType':'script','method':'get'};
    return ajax(args);
}
$.loadJSON = function(){
    var args = arguments;
    args['options'] = {'cache':false,'dataType':'json','method':'post'};
    return ajax(args);
}
$.loadHTML = function(){
    var args = arguments;
    args['options'] = {'cache':true,'dataType':'html','method':'get'};
    return ajax(args);
}

/**
 * Initializing system
 */
//Gloabal sysem objects
var X3_Module = function(name,ops){
    /**@private:*/
    var self = this;
    var options = {
        onCreate:function(){},
        onCall:function(){},
        onSort:function(){return 0;},
        ui:{}
    } 
    /**@public:*/
    self.id = name;
    self.title = name;
    self.table = {};
    self.fieldset = {};
    self.template = null;
    self.weight = 0; //weight in the main menu
    self.ui = {};
    self.ready = function(fn){
        fn.call(self);
    }
    self.call = function(caller){
        X3.current = this;
        options.onCall.call(this,caller);
    }
    self.refresh = function(){
        options.onCall.call(this,null);
    }
    
    /**@constrictor:*/    
    options = $.extend({},options,ops);
    if(options.title!='')
        self.title = options.title;
    self.weight = options.onSort();
    options.onCreate.call(self);
    return this;
}

var X3 = new function(){
    //@private
    var self = this;
    var modules = [];
    var _onReady = []; //after loading callback stack
    
    //@public
    self.layouts = {};
    self.current = null; //current active module
    
    //Initialization application
    self.init = function(callback){
        try{
        modules = $.loadJSON('/admin/modules.json',false)
        for(i in modules){
            $.loadJs('/application/modules/Admin/js/modules/x3.'+modules[i]+'.js',false)            
        }
        $(document).ready(function(){
            self.generateLayouts();
            for(i in _onReady)
                _onReady[i].call(this);
        });
        if(callback!=null) callback.call(this);
        }catch(e){
            $.growl(e);
        }
        return self;
    }
    //Generate layouts array
    self.generateLayouts = function(){        
        $('[x3-layout]').each(function(){
            self.layouts[$(this).attr('x3-layout')] = $(this);
        })
        return self;
    }
    //Display loading status on the page
    self.status = {
        loading:function(){
            //TODO: loading process
        },
        done:function(){
            //TODO: loading process done
        }
    }
    self.eachModule = function(callback){
        for(i in modules){
            var mod = modules[i];
            if(typeof X3[mod] == 'object')
                callback.call(X3[mod],modules[i]);
        }
    }
    self.eachLayout = function(callback){
        for(var i in self.layouts){
            callback.call(self.layouts[i]);
        }
    }
    //onReady stack
    self.ready = function(callback){
        if(typeof callback == 'function')
            _onReady.push(callback);
    }
}; 

$(document).scroll(function(){
    if($(document).scrollTop()>0){
        /*var s = '';
        if(typeof $('.x3-submenu')[0] != 'undefined'){
            s = $('.x3-submenu').clone(true);
            s.css({'position':'absolute','left':'0px','bottom':'-1px'})
        }*/
        
        $('.x3-menu').css({'position':'fixed','top':'0px','left':'0px','width':($(window).width()-40)+'px','z-index':'99'})
    }else
        $('.x3-menu').css({'position':'static','width':'auto'});
})

/**
 *  Ask before delete
 */
$(document).ready(function(){
    /**
     *   Confirmation dialog for deletition item
     */
    $('body').append('<div style="display:none" title="Подтвердите удаление" id="dialog-delete"><p><span class="ui-icon ui-icon-circle-check" style="float:left; margin:0 7px 50px 0;"></span>Вы дейстительно хотите удалить эту запись?<p style="font-weight:bold;color:#DA5252">Все связанные с ней записи тоже могут быть удалены!</p></p></div>');
    $('.action-delete a').each(function(){
        var href = $(this).attr('href');
        $(this).data('href',href)
        $(this).attr('href','#').click(function(){
            var hh = $(this).data('href');
            $('#dialog-delete')
            .dialog({modal:true,buttons:{
                    'Да':function(){
                        location.href = hh;
                    },
                    'Отмена':function(){
                        $(this).dialog('close');
                    }
            }})
            return false;
        })  
    })
    function change_status(el,status){
        $.get('/admin/status',{'attr':el.data('attr'),'pk':el.data('pk'),'status':status,'action':el.data('action')},function(){
            //
        });
    }
    $('[x3-status]').each(function(){
        var pair = $(this).attr('x3-status').split(':');
        $(this).data({'attr':pair[0],'pk':pair[1],'action':pair[2]}).removeAttr('x3-status');
        var stat = parseInt($(this).html());
        $(this).html('')
        var pos1 = $('<input type="radio" id="on'+pair[0]+pair[1]+'" name="status-'+pair[0]+pair[1]+'" />').click(function(){change_status($(this).parent(),1)});
        var pos2 = $('<input type="radio" id="off'+pair[0]+pair[1]+'" name="status-'+pair[0]+pair[1]+'" />').click(function(){change_status($(this).parent(),0)});
        if(stat==1)
            pos1.attr('checked','1');
        else
            pos2.attr('checked','1');
        $(this)
            .append(pos1).append('<label for="on'+pair[0]+pair[1]+'">Вкл.</label>')
            .append(pos2).append('<label for="off'+pair[0]+pair[1]+'">Выкл.</label>')
            .buttonset();
    })
    if(typeof $('#deleteall')[0] != 'undefined'){
        $('#deleteall').data('href',$('#deleteall').attr('href')).attr('href','#');
        $('#deleteall').click(function(){
            ids = [];
            var href = $(this).data('href');
            $('[id^="delete-"]:checked').each(function(){
                ids.push($(this).val());
            })
            if(ids.length>0)
            $.post(href,{'ids':ids},function(){
                location.href = href.replace('/deleteall');
            })
            return false;
        });
    }
})

/*X3.init().ready(function(){
    X3['UI'].call();
});

$(document).keydown(function(e){
    var c = e.keyCode?e.keyCode : (e.which?e.which:null);
    if(c == 116 || (e.ctrlKey && c == 82)){
        try{
            X3.current.refresh()
        }
        catch(e){console.log(e)}
        return false;
    }
})*/
String.prototype.repeat = function( num )
{
    return new Array( num + 1 ).join( this );
}

if (!Array.prototype.indexOf)
{
  Array.prototype.indexOf = function(elt /*, from*/)
  {
    var len = this.length;

    var from = Number(arguments[1]) || 0;
    from = (from < 0)
         ? Math.ceil(from)
         : Math.floor(from);
    if (from < 0)
      from += len;

    for (; from < len; from++)
    {
      if (from in this &&
          this[from] === elt)
        return from;
    }
    return -1;
  };
}

$.fn.label = function(label){
    var lbl = $('<label />').attr('for',$(this).attr('name')).html(label);
    lbl.insertAfter(this)
    return $(this).data('label',lbl);
}

$.fn.getStyles = function(){
    var a = $(this)
    var sheets = document.styleSheets, o = {};
    for(var i in sheets) {
        var rules = sheets[i].rules || sheets[i].cssRules;
        for(var r in rules) {
            if(a.is(rules[r].selectorText)) {
                o = $.extend(o, css2json(rules[r].style), css2json(a.attr('style')));
            }
        }
    }
    return o;

function css2json(css){
        var s = {};
        if(!css) return s;
        if(css instanceof CSSStyleDeclaration) {
			css = css.cssText;
//            for(var i in css) {
//                if((css[i]).toLowerCase && css[i] != "") {
//                    s[(css[i]).toLowerCase()] = (css[css[i]]) || "";
//                }
//            }		
        }
		if(typeof css == "string") {
            css = css.split(";");
            for (var i in css) {
                var l = css[i].replace(/\s/,'').split(":");
                s[l[0].replace(/\s/,'').toLowerCase()] = (l[1]);
            };
        }
        return s;
    }
}


var md5=function(c){function g(a,c){var d,e,b,f,g;b=a&2147483648;f=c&2147483648;d=a&1073741824;e=c&1073741824;g=(a&1073741823)+(c&1073741823);return d&e?g^2147483648^b^f:d|e?g&1073741824?g^3221225472^b^f:g^1073741824^b^f:g^b^f}function h(a,b,c,d,e,f,h){a=g(a,g(g(b&c|~b&d,e),h));return g(a<<f|a>>>32-f,b)}function i(a,b,c,d,e,f,h){a=g(a,g(g(b&d|c&~d,e),h));return g(a<<f|a>>>32-f,b)}function j(a,b,c,d,e,f,h){a=g(a,g(g(b^c^d,e),h));return g(a<<f|a>>>32-f,b)}function k(a,b,c,d,e,f,h){a=g(a,g(g(c^(b|~d), e),h));return g(a<<f|a>>>32-f,b)}function p(a){var b="",c="",d;for(d=0;3>=d;d++)c=a>>>8*d&255,c="0"+c.toString(16),b+=c.substr(c.length-2,2);return b}var f=[],l,m,n,o,a,d,e,b,f=c.replace(/\r\n/g,"\n"),c="";for(l=0;l<f.length;l++)m=f.charCodeAt(l),128>m?c+=String.fromCharCode(m):(127<m&&2048>m?c+=String.fromCharCode(m>>6|192):(c+=String.fromCharCode(m>>12|224),c+=String.fromCharCode(m>>6&63|128)),c+=String.fromCharCode(m&63|128));f=c;c=f.length;l=c+8;m=16*((l-l%64)/64+1);n=Array(m-1);for(a=o=0;a<c;)l= (a-a%4)/4,o=8*(a%4),n[l]|=f.charCodeAt(a)<<o,a++;l=(a-a%4)/4;n[l]|=128<<8*(a%4);n[m-2]=c<<3;n[m-1]=c>>>29;f=n;a=1732584193;d=4023233417;e=2562383102;b=271733878;for(c=0;c<f.length;c+=16)l=a,m=d,n=e,o=b,a=h(a,d,e,b,f[c+0],7,3614090360),b=h(b,a,d,e,f[c+1],12,3905402710),e=h(e,b,a,d,f[c+2],17,606105819),d=h(d,e,b,a,f[c+3],22,3250441966),a=h(a,d,e,b,f[c+4],7,4118548399),b=h(b,a,d,e,f[c+5],12,1200080426),e=h(e,b,a,d,f[c+6],17,2821735955),d=h(d,e,b,a,f[c+7],22,4249261313),a=h(a,d,e,b,f[c+8],7,1770035416), b=h(b,a,d,e,f[c+9],12,2336552879),e=h(e,b,a,d,f[c+10],17,4294925233),d=h(d,e,b,a,f[c+11],22,2304563134),a=h(a,d,e,b,f[c+12],7,1804603682),b=h(b,a,d,e,f[c+13],12,4254626195),e=h(e,b,a,d,f[c+14],17,2792965006),d=h(d,e,b,a,f[c+15],22,1236535329),a=i(a,d,e,b,f[c+1],5,4129170786),b=i(b,a,d,e,f[c+6],9,3225465664),e=i(e,b,a,d,f[c+11],14,643717713),d=i(d,e,b,a,f[c+0],20,3921069994),a=i(a,d,e,b,f[c+5],5,3593408605),b=i(b,a,d,e,f[c+10],9,38016083),e=i(e,b,a,d,f[c+15],14,3634488961),d=i(d,e,b,a,f[c+4],20,3889429448), a=i(a,d,e,b,f[c+9],5,568446438),b=i(b,a,d,e,f[c+14],9,3275163606),e=i(e,b,a,d,f[c+3],14,4107603335),d=i(d,e,b,a,f[c+8],20,1163531501),a=i(a,d,e,b,f[c+13],5,2850285829),b=i(b,a,d,e,f[c+2],9,4243563512),e=i(e,b,a,d,f[c+7],14,1735328473),d=i(d,e,b,a,f[c+12],20,2368359562),a=j(a,d,e,b,f[c+5],4,4294588738),b=j(b,a,d,e,f[c+8],11,2272392833),e=j(e,b,a,d,f[c+11],16,1839030562),d=j(d,e,b,a,f[c+14],23,4259657740),a=j(a,d,e,b,f[c+1],4,2763975236),b=j(b,a,d,e,f[c+4],11,1272893353),e=j(e,b,a,d,f[c+7],16,4139469664), d=j(d,e,b,a,f[c+10],23,3200236656),a=j(a,d,e,b,f[c+13],4,681279174),b=j(b,a,d,e,f[c+0],11,3936430074),e=j(e,b,a,d,f[c+3],16,3572445317),d=j(d,e,b,a,f[c+6],23,76029189),a=j(a,d,e,b,f[c+9],4,3654602809),b=j(b,a,d,e,f[c+12],11,3873151461),e=j(e,b,a,d,f[c+15],16,530742520),d=j(d,e,b,a,f[c+2],23,3299628645),a=k(a,d,e,b,f[c+0],6,4096336452),b=k(b,a,d,e,f[c+7],10,1126891415),e=k(e,b,a,d,f[c+14],15,2878612391),d=k(d,e,b,a,f[c+5],21,4237533241),a=k(a,d,e,b,f[c+12],6,1700485571),b=k(b,a,d,e,f[c+3],10,2399980690), e=k(e,b,a,d,f[c+10],15,4293915773),d=k(d,e,b,a,f[c+1],21,2240044497),a=k(a,d,e,b,f[c+8],6,1873313359),b=k(b,a,d,e,f[c+15],10,4264355552),e=k(e,b,a,d,f[c+6],15,2734768916),d=k(d,e,b,a,f[c+13],21,1309151649),a=k(a,d,e,b,f[c+4],6,4149444226),b=k(b,a,d,e,f[c+11],10,3174756917),e=k(e,b,a,d,f[c+2],15,718787259),d=k(d,e,b,a,f[c+9],21,3951481745),a=g(a,l),d=g(d,m),e=g(e,n),b=g(b,o);return(p(a)+p(d)+p(e)+p(b)).toLowerCase()};