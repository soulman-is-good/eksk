X3['Menu'] = new X3_Module('Menu',{    
    onCreate:function(){
        var model = $.loadJSON('/admin/json/module/Menu',false);
        this.table = model.table;
        this.fieldset = model.fields;
        this.ui = model.ui;
        this.title = 'Меню';        
    },
    onCall:function(caller){        
        X3.status.loading();
        var self = this;
        if(self.template==null)
            self.template = $.loadHTML('/admin/templates/module/Menu',false,{'attributes':['title','link','status']});
        X3.layouts['content'].html(self.template);
        $("#tmpl-main").tmpl(self.table).appendTo($("#container"))
        $('.action-edit').delegate('a','click',function(){
            self.ui.edit.click.call(this,$.tmplItem(this));
        })
        $('.action-delete').delegate('a','click',function(){
            self.ui['delete'].click.call(this,$.tmplItem(this));
        })
        X3.status.done();
    },
    onSort:function(){return 2}
});