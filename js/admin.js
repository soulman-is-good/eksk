/* 
 * Super admin menu renderer
 */
$(function(){
    var menu = $('<div />');
    var hs = $('<a />').attr({'href':'#hide-show'})
    .css({'color':'#FFF','text-decoration':'none'}).append('O');
    var links = $('<ul />').css({'display':'none'});
    var ahrefs = [];
    links.addLink = function(link){
        var li = $('<li />');
        li.append(link);
        links.append(li);
    }
    menu.showMenu = function(){
        hs.click(function(){menu.hideMenu();return false;})
        links.css({'display':'block'});
        menu.height(links.outerHeight()+50)
        menu.width(300)
        hs.html('X');
    }
    menu.hideMenu = function(){
        hs.click(function(){menu.showMenu();return false;})
        links.css({'display':'none'});
        menu.height(20)
        menu.width(20)
        hs.html('O');
    }
    menu.css({
        'background':'#1A54A6',
        'position':'fixed',
        'width':'20px',
        'height':'20px',
        'max-height':'600px',
        'overflow':'hidden',
        'bottom':'0px',
        'left':'0px',
        'border':'3px solid #FFF',
        'border-radius':'0px 10px 0px 0px'
    })
    menu.append(links)
        .append(hs);
    hs.click(function(){
        menu.showMenu()
        return false;
    })
    $.get('/admin/links',function(m){
        for(i in m){
            var a = $('<a />').attr({'href':i}).html(m[i]).css({
                'color':'#FFF'
            });
            links.addLink(a);
        }
    },'json')
    $('body').append(menu);
})
