/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$.fn.fctabs = function(){
    var hash = false;
    var self = this;
    if(typeof location.hash != 'undefined')
        hash = location.hash;
    if(!hash)
        hash = $(this).find('ul a:first-child').attr('href');
    $(this).find('.tab').css('display','none');
    $(hash).css('display','block');
    $(this).find('[href="'+hash+'"]').addClass('active').parent().addClass('active');
    $(this).find('ul a').each(function(){
        $(this).click(function(){
            if($(this).hasClass('active')) return false;
            var href = $(this).attr('href');
            if(typeof $(href)[0] == 'undefined')
                return false;
            $(self).find('[href="'+hash+'"]').removeClass('active').parent().removeClass('active');
            $(self).find('[href="'+href+'"]').addClass('active').parent().addClass('active');
            $(hash).css('display','none');
            $(href).css('display','block');
            hash = href;
            return false;
        })
    });
}

$(function(){
    $('[fctabs]').fctabs();
})
