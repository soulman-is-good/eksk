/* 
 * jQuery plugin for facecom.kz
 * decorates <select> form tags
 * 
 */
jQuery.expr[':'].contains = function(a, i, m) {
  return jQuery(a).text().toUpperCase()
      .indexOf(m[3].toUpperCase()) >= 0;
};
(function($){
    $.fn.attrs = function(){
        var attributes = this[0].attributes;
        var result = {};
        for(i in attributes){
            result[attributes[i].name] = attributes[i].value;
        }
        return result;
    }
    $.fn.fcselect = function(useroptions){
        
        if($(this).data('fcselect')!=null)
            return $(this).data('fcselect');
        var data = $(this).data();
        var ops = {
            emulateChangeAction:true, //emulate original select onchane event behavior. The event isn't called on same value selection
            multiselect:false, //rebulids one's group checkboxes into multiselect dropdown
            autocomplete:false, //makes autocomplete logic
            data:[],
            value:'',
            selectableText:false, //TODO: text can be selected. Click event goes to an arrow
            editableText:false, //TODO: text can be edited to filter results, overloads selectableText property
            width:false, //width of the select element. Could be 'fit' or standart css
            fcclass:'select'
        }
        var temp_css = {};
        ops = $.extend(ops,useroptions,data);
        var self = this;
        
        var container = $('<div />').addClass(ops.fcclass);
        var selected = $('<div />').addClass('selected');
        var options = $('<div />').addClass('options');
        
        /**
         * Helps to convert select tag, radio buttons to 
         * checkboxes if multiselect is chosen and otherwise
         */
        this.normalizeTag = function(){
            if(ops.multiselect){
                if(self[0].tagName == 'SELECT'){
                    var rep = $('<div />');
                    $(self).children('option').each(function(){
                        if(this.hasAttribute('selected'))
                            $(this).removeAttr('selected').attr('checked',true);
                        var input = $('<input />').attr($(this).attrs()).attr({'type':'checkbox'});
                        //TODO: handle events from SELECT to INPUT
                        rep.append(input)
                        .append($('<label />').attr({'for':$(this).attr('name')}).html($(this).html()));
                    })
                    $(self).replaceWith(rep);
                }
            }else{
                //TODO: backward convert
            }
        }
        this.destroy = function(){
            self.removeAttr('style');
            container.remove();
        }
        this.showOptions = function(e){
            $('[fcselect]').each(function(){$(this).data('fcselect').hideOptions()})
            options.toggle();
            options.css('top',(container.innerHeight())+'px');
            $(document).bind('click',self.hideOptions)
        }
        this.hideOptions = function(e){
            if(typeof e != 'undefined' && (
                    ($(e.target).hasClass('select') && $(e.target).hasClass('options'))
                    || e.which != 1)
              )
                return false;
            if(options.is(":visible")){
                $(document).unbind('click',self.hideOptions);
                options.toggle();
            }
        }
        this.redraw = function(){
            container.html('');
            container.unbind('click');
            options.html('');
            selected.html('');
            this.draw();
        }
        /**
         * Renders facecom select element based on options
         */
        this.draw = function(){
            if(ops.multiselect){
                if(!container.hasClass('multi'))
                    container.addClass('multi');
                $(self).children('input[type="checkbox"]').each(function(){
                    //TODO: rude title fetching. we must assure that id tag is set and if not try to get by name.
                    var title = $(this).parent().children('label[for="'+$(this).attr('id')+'"]').text();
                    var inp = this;
                    var option = $('<div />').addClass('option').attr({'val':$(this).val()})
                    .append(title).unbind('click');
                    option.bind('click',function(e){
                        e.stopPropagation();
                        if($(this).hasClass('active')){
                            $(this).data('fcs-link').click();
                        }else{
                            var link = $('<a href="#remove" />').html('<img alt="X" src="/static/css/cross.png" />').click(function(){
                                var p = $(this).parent()
                                p.data('fcs-input').attr('checked',false).data('fcs-option').removeClass('active');
                                p.fadeOut('fast',function(){$(this).remove();});
                                return false;
                            });
                            selected.find('i').remove();
                            $(inp).attr('checked',true).click();
                            $(this).data('fcs-link',link).addClass('active');
                            selected.append($('<span />').data('fcs-input',$(inp)).append(title).append(link).fadeIn(function(){
                                options.css('top',(container.outerHeight())+'px');
                            }))
                        }
                    });
                    options.append(option);
                    $(this).data('fcs-option',option);
                    if($(this).is(':checked')){
                        option.addClass('active')
                        var link = $('<a href="#remove" />').html('<img alt="X" src="/static/css/cross.png" />').click(function(){
                            var p = $(this).parent()
                            p.data('fcs-input').attr('checked',false).click().data('fcs-option').removeClass('active').data('fcs-link',null);
                            p.fadeOut('fast',function(){
                                $(this).unbind('click').remove();
                                if(selected.children().length == 0){
                                    selected.append('<i>Ничего не выбрано</i>')
                                }
                                options.css('top',(container.outerHeight())+'px');
                            });
                            return false;
                        })
                        option.data('fcs-link',link);
                        selected.append($('<span />').data('fcs-input',$(this)).append(title).append(link))
                    }
                })
                if(selected.children().length == 0){
                    selected.append('<i>Ничего не выбрано</i>')
                }
            }else if(ops.autocomplete){
                container.addClass('autocomplete').css({'background':'#fff'});
                ops.emulateChangeAction = false;
                if(self[0].tagName === 'SELECT' && (typeof ops.data == 'object')){
                    var data = [];
                    var active = '';
                    self.children('option').each(function(){
                        var k = $(this).val();
                        var v = $(this).text();
                        if($(this).is(':selected')){
                            active = v;
                        }
                        data.push({key:k,value:v});
                    });
                    ops.data = data;
                }
                var input = $('<input />').attr({'type':'text','value':active}).css({'padding':'0','border':'none','box-shadow':'none'});
                var xhr = null;
                container.append(input);
                //input.val(ops.value);
                input.bind('keydown',function(e){
                    if(options.is(':visible') && e.keyCode == 13){
                        var val = $(this).val();
                        var a = options.find('.active');
                        if(typeof a[0] != 'undefined'){
                            a.click();
                        }else if(val!=''){
                            
                            options.find(":contains('"+val.toLowerCase()+"')").click();
                        }
                            return false;
                    }
                })
                input.bind('keyup',function(e){
                    var val = $(this).val();
                    if(e.keyCode == 13){
                        return false;
                    }
                    if(e.keyCode == 40 || e.keyCode == 38){
                        if(options.is(':visible')){
                            if(e.keyCode == 38){
                                var a = options.find('.active').prev();
                                if(typeof a[0] == 'undefined'){
                                    a = options.children().last();
                                }
                                options.find('.active').removeClass('active');
                                a.addClass('active');
                            }
                            if(e.keyCode == 40){
                                var a = options.find('.active').next();
                                if(typeof a[0] == 'undefined'){
                                    a = options.children().eq(0);
                                }
                                options.find('.active').removeClass('active');
                                a.addClass('active');
                            }
                        }
                        return false;
                    }
                    container.addClass('loading');
                    if(typeof ops.data == 'string'){
                        if(xhr != null){
                            xhr.abort();
                            xhr = null;
                        }
                        xhr = $.get(ops.data,{q:val},function(res){
                            options.html('');
                            self.showOptions();
                            for(i in res){
                                var option = $('<div />').addClass('option').attr({'val':i}).append(res[i]);
                                option.bind('click',function(e){
                                    e.stopPropagation();
                                    if($(this).hasClass('active') && ops.emulateChangeAction){
                                        self.hideOptions();
                                        return false;
                                    }
                                    $(self).val($(this).attr('val'));
                                    options.children('.active').removeClass('active');
                                    $(this).addClass('active');
                                    input.val($(this).text());
                                    self.hideOptions();
                                    self.change();
                                    return false;
                                })
                                options.append(option);
                                container.removeClass('loading');
                            }
                        },'json');
                    }else if(typeof ops.data === 'object'){
                        options.html('');
                        self.showOptions();
                        for(i in ops.data){
                            if(ops.data[i].value.toLowerCase().indexOf(val.toLowerCase())===-1) continue;
                            var option = $('<div />').addClass('option').attr({'val':ops.data[i].key}).append(ops.data[i].value);
                            option.bind('click',function(e){
                                e.stopPropagation();
                                if($(this).hasClass('active') && ops.emulateChangeAction){
                                    self.hideOptions();
                                    return false;
                                }
                                $(self).val($(this).attr('val'));
                                options.children('.active').removeClass('active');
                                $(this).addClass('active');
                                input.val($(this).text());
                                self.hideOptions();
                                self.change();
                                return false;
                            });
                            options.append(option);
                            container.removeClass('loading');
                        }
                    }
                });
                container
                //    .append(self)
                    .append(selected)
                    .append(options);
                //check options box height
                options.css({'display':'block','visibility':'hidden'});
                if(options.height()>$(window).height()/2)
                    options.css({'height':($(window).height()/2)+'px','overflow-y':'auto','overflow-x':'hidden'});
                options.css({'display':'none','visibility':'visible'});
                options.css('top',(container.innerHeight())+'px');
                if(!ops.selectableText)
                    container.bind('click',function(e){
                        if(options.is(':visible')){
                            options.toggle();
                            $(document).unbind('click',self.hideOptions)
                        }else{
                            $('[fcselect]').each(function(){$(this).data('fcselect').hideOptions()})
                            options.toggle();
                            options.css('top',(container.innerHeight())+'px');
                            $(document).bind('click',self.hideOptions)
                        }
                        e.stopPropagation();
                        return false;
                    })
            }else {
                $(self).children('option').each(function(){
                    var title = $(this).text();
                    var selfopt = this;
                    var option = $('<div />').addClass('option').attr({'val':$(this).val()}).append(title);
                    option.bind('click',function(e){
                        e.stopPropagation();
                        if($(this).hasClass('active') && ops.emulateChangeAction){
                            self.hideOptions();
                            return false;
                        }
                        $(self).children(':selected').attr('selected',false);
                        $(selfopt).attr('selected',true);
                        options.children('.active').removeClass('active');
                        $(this).addClass('active');
                        selected.html($(this).text());
                        self.hideOptions();
                        self.change();
                        return false;
                    })
                    options.append(option);
                    if($(this).is(':selected')){
                        option.addClass('active')
                        selected.html(title);
                    }
                    
                })
            }
            container
//                .append(self)
                .append(selected)
                .append(options);
            //check options box height
            options.css({'display':'block','visibility':'hidden'});
            if(options.height()>$(window).height()/2)
                options.css({'height':($(window).height()/2)+'px','overflow-y':'auto','overflow-x':'hidden'});
            options.css({'display':'none','visibility':'visible'});
            options.css('top',(container.outerHeight())+'px');
            if(!ops.selectableText && !ops.autocomplete)
                container.bind('click',function(e){
                    if(self.is(':disabled')) return false;
                    if(options.is(':visible')){
                        options.toggle();
                        $(document).unbind('click',self.hideOptions)
                    }else{
                        $('[fcselect]').each(function(){$(this).data('fcselect').hideOptions()})
                        options.toggle();
                        options.css('top',(container.outerHeight())+'px');
                        $(document).bind('click',self.hideOptions)
                    }
                    e.stopPropagation();
                    return false;
                })
        }
        
        //Initialize
        this.normalizeTag();
        $(self).css({'visibility':'hidden','overflow':'hidden','width':'0px','height':'0px','position':'absolute','left':'-9999px','top':'-9999px'});
        container.html('');
        container.insertAfter(self);
        this.draw();
//        container.css({'top':'0px'});
        if(ops.width !== false){
            if(!ops.multiselect && ops.width == 'fit')
                container.css({'width':(options.width())+'px'});
            else if(ops.width != 'fit')
                container.css({'width':ops.width});
            if(ops.multiselect || ops.width != 'fit')
                options.css({'right':'0px'});
        }
//        options.css({'margin-top':(ops.multiselect?'0':'21')+'px'});
        selected.css({'width':'auto'})
        if(!$(this)[0].hasAttribute('fcselect'))
            $(this).attr({'fcselect':true});
        $(this).data('fcselect',this);
        return this;
    }
    $(function(){
        $('input[fcselect]').each(function(){
            var ops = {};
            if($(this).attr('fcselect') != '')
                ops = eval('({'+$(this).attr('fcselect').replace(';',',')+'})');
            if(ops==null)
                ops = {};
            ops.autocomplete = true;
            $(this).fcselect(ops);
        })
        $('select[fcselect]').each(function(){
            if($(this).attr('fcselect')==='autocomplete')
                $(this).fcselect({autocomplete:true});
            else
                $(this).fcselect();
        })
        $('span[fcselect], div[fcselect]').each(function(){
            $(this).fcselect({multiselect:true});
        })
    })
})(jQuery)