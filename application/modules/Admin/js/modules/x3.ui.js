X3['UI'] = new X3_Module('UI',{
    onCreate:function(){
        //default page
        this.template = $.loadHTML('/admin/templates/get/main',false);
        this.prototype = {menu:[]};
    },
    onCall:function(){
        var menu = this.prototype.menu;
        var self = this;
        X3.current = null;
        if(menu.length==0){
        X3.layouts['content'].html(this.template);        
        menu.push($('<a />').html('۩').attr({'href':'#'}).click(function(){
            if($(this).hasClass('active')) return false;
            $(this).addClass('active').siblings('.active').removeClass('active');
            X3.layouts['content'].html(self.template);
        }).addClass('active').data('weight',0));                
        
        X3.eachModule(function(module){
            if(module == 'UI') return false;
            var x3i = this;
            var a = $('<a />').html(this.title).attr({'href':'#'}).click(function(){
                if($(this).hasClass('active')) return false;
                $(this).addClass('active').siblings('.active').removeClass('active');
                x3i.call(this);
            }).data('weight',x3i.weight);
            //console.log(x3i.weight)
            menu.push(a);
        });
        menu.sort(function(a,b){return (a.data.weight-b.data.weight<0)?1:-1})
        }
        for(var i in menu){
            X3.layouts['menu'].append(menu[i]);
            if(i<menu.length-1) X3.layouts['menu'].append($('<span />').html(' :: '));
        }
        this.prototype.menu = menu;
    }
})

