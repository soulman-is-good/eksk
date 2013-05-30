/* 
 * Fixes HTML5 placeholder attribute
 * 
 */
$(document).ready(function(){

						$('[placeholder]').each(function(){$(this).attr('origval',$(this).attr('placeholder')).removeAttr('placeholder');});
                        $("[origval]").focus(function() {
                            var input = $(this);
                            if (input.val() == input.attr("origval")) {
                                input.val("");
                                input.removeClass("placeholder");
                            }
                            }).blur(function() {
                            var input = $(this);
                            if (input.val() == "" || input.val() == input.attr("origval")) {
                                input.addClass("placeholder");
                                input.val(input.attr("origval"));
                            }
                            }).blur().parents("form").submit(function() {
                            $(this).find("[origval]").each(function() {
                                var input = $(this);
                                if (input.val() == input.attr("origval")) {
                                input.val("");
                                }
                            })
                            });
});
