/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
var menu = $('<div/>').addClass('menu');
var imgs = [
    '/application/modules/Admin/images/modules/menu.png',
    '/application/modules/Admin/images/modules/news.png',
    '/application/modules/Admin/images/modules/page.png',
    '/application/modules/Admin/images/modules/settings.png',
    '/application/modules/Admin/images/modules/feedback.png',
    '/application/modules/Admin/images/modules/publics.png',
    '/application/modules/Admin/images/modules/item.png',
    '/application/modules/Admin/images/modules/balance.png',
    '/application/modules/Admin/images/modules/review.png',
    '/application/modules/Admin/images/modules/tools.png',
    '/application/modules/Admin/images/modules/user.png'
];

menu.css({'position':'absolute'});
var l=0;
var t=0;
var c = imgs.length;
var c2 = c/2;
$('body').append(menu.css({'top':'200px','left':'600px'}));

for(i in imgs){
    var d = $('<div />').css({'background':'url('+imgs[i]+') no-repeat 50% 50% #FFF','border':'1px solid blue','border-radius':'30px','width':'5px','height':'5px','position':'absolute','left':'50%','top':'50%','cursor':'pointer'});
    d.hover(function(){$(this).css('background-color','#42fdca')},function(){$(this).css('background-color','#fff')})
    menu.append(d);
    l=Math.cos(3.14*i/c2)*100+170;
    t=Math.sin(3.14*i/c2)*100+170;
    d.animate({'width':'60px','height':'60px','left':l+'px','top':t+'px'})
}
menu.animate({'width':'400px','height':'400px','top':'-=200px','left':'-=200px'})