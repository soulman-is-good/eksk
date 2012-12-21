var menu = [];
X3.layouts['content'].html('<h1>Добро пожаловать в instinct.CMS!</h1>')
    menu.push($('<a />').html('۩').attr({'href':'#'}).click(function(){
        if($(this).hasClass('active')) return false;
        $(this).addClass('active').siblings('.active').removeClass('active');
        X3.layouts['content'].html('<h1>Добро пожаловать в instinct.CMS!</h1>')
    }).addClass('active').data('weight',0));
X3.eachModule(function(module){
    var self = this;
    var a = $('<a />').html(this.title).attr({'href':'#'}).click(function(){
        if($(this).hasClass('active')) return false;
        $(this).addClass('active').siblings('.active').removeClass('active');
        self.call(this);
    }).data('weight',self.weight);
    menu.push(a);
});


menu.sort(function(a,b){return a.data.weight-b.data.weight})

for(i in menu){
    X3.layouts['menu'].append(menu[i]);
    if(i<menu.length-1) X3.layouts['menu'].append($('<span />').html(' :: '));
}