function wnd(content,modal,width,height,closeOnTap){
	this.object = null;
	this.content = content || '';
	this.width = width || null;
	this.height = height || null;
	this.blackscreen = null;
	this.modal = modal || false;
        this.closeOnTap = closeOnTap !== false;
	this.posX = 0;
	this.posY = 0;
	this.relObj = null;
	this.relDest = null;
	this.shiftX = null;
	this.shiftY = null;
	this.title = null;
	var self=this;
	var closeText = 'Закрыть';
	
	this.setContent = function(content,speed){
		speed=speed || 0;
		this.content=content;
		if(this.object!=null){
			if(this.title != null)
				content = this.title + this.content;
			else
				content = this.content;
			$(this.object).html(content);
			if(speed>=0)
				this.move(speed);
		}
	}

	this.setTitle = function(title, close){
		close = close || true;
		if (close == true) 
			clTxt = '<div class="wnd_title_right"><span class="closeMywnd closeLink">'+closeText+'</span></div>';
		else
			clTxt = '';
		this.title = '<div class="wnd_title"><div class="wnd_title_left">'+title+'</div>'+clTxt+'</div>';
		this.setContent(this.content, -1);
	}

	this.getContent = function(){
		return this.content;
	}

	this.setZindex = function(zIndex){
		$(this.object).css('z-index',zIndex);
		if(modal){$(this.blackscreen).css('z-index',zIndex-1);}
	}

	this.setSize = function(width,height){
		if(width){
			this.width=width;
			this.object.css('width',width+'px')
		}
		if(height){
			this.height=height;
			this.object.css('height',height+'px')
		}
	}

	this.closeTimeOut = function(timeout, action){
		action = action || null
		setTimeout(this.close, timeout);
		if(action!=null)
			action();
	}

	this.moveXY = function(speed){
		speed=speed || 0;
		if(this.object!=null){
			$(this.object).css({'left':this.posX+'px','top':this.posY+'px'});
		}
	}

	this.move = function(speed){
		if(this.relObj){
			this.relMove();
			this.moveXY(speed);
		}else{
			this.moveXY(speed);
		}
	}

	this.setPositionXY = function(x,y){
		if(x!=null){this.posX=x;}
		if(y!=null){this.posY=y;}
		this.relObj = null;
		this.move();
	}
	
	this.moveUpDown = function(y){
		this.posY=this.posY+y;
		this.moveXY();
	}

	this.moveLeftRight = function(y){
		this.posX=this.posX+x;
		this.moveXY();
	}

	this.setRelativePosition = function(rel,shiftX,shiftY,obj){
		this.relObj=obj || $(window);
		this.shiftX=shiftX || 0;
		this.shiftY=shiftY || 0;
		this.relDest=rel;
		this.move();
	}

	this.relMove = function(){
		if(this.shiftX=='+'){this.shiftX=($(this.object).outerWidth());}
		if(this.shiftX=='-'){this.shiftX=-($(this.object).outerHeight());}
		if(this.shiftY=='+'){this.shiftY=($(this.object).outerWidth());}
		if(this.shiftY=='-'){this.shiftY=-($(this.object).outerHeight());}
		if(this.relObj.offset()){
			lft=this.relObj.offset().left;
			tp=this.relObj.offset().top;
		}else{
			lft=0;
			tp=0;
		}
		if(this.relDest=='center'){
			this.posX=(lft)+(this.relObj.width() / 2)-($(this.object).outerWidth() / 2)+(this.relObj.scrollLeft() || 0)+this.shiftX;
			this.posY=(tp)+(this.relObj.height() / 2)-($(this.object).height() / 2)+(this.relObj.scrollTop() || 0)+this.shiftY;
		}
		if(this.relDest=='bottom'){
			this.posX=(lft)+(this.relObj.width() / 2)-($(this.object).outerWidth() / 2)+(this.relObj.scrollLeft() || 0)+this.shiftX;
			this.posY=(tp)+(this.relObj.height())+(this.relObj.scrollTop() || 0)+this.shiftY;
		}
		if(this.relDest=='top'){
			this.posX=(lft)+(this.relObj.width() / 2)-($(this.object).outerWidth() / 2)+(this.relObj.scrollLeft() || 0)+this.shiftX;
			this.posY=(tp)+(this.relObj.scrollTop() || 0)+this.shiftY;
		}
		if(this.relDest=='left'){
			this.posX=(lft)-($(this.object).outerWidth())+this.shiftX;
			this.posY=(tp)+(this.relObj.height() / 2)-($(this.object).height() / 2)+(this.relObj.scrollTop() || 0)+this.shiftY;
		}
		if(this.relDest=='right'){
			this.posX=(lft)+(this.relObj.width())+this.shiftX;
			this.posY=(tp)+(this.relObj.height() / 2)-($(this.object).height() / 2)+(this.relObj.scrollTop() || 0)+this.shiftY;
		}
		if(this.posY<this.relObj.scrollTop())
			this.posY=this.relObj.scrollTop()+10;
		if(this.posX<0)
			this.posX=10;		
	}

	this.ajaxLoad = function(addr,post,action,json){
		action = action || null;
		post = post || '';
		json = json || false;
		self.setContent('<div class="wnd_title" align="center" ><img src="/static/css/loader01.gif" alt="" /></div>');
		$.ajax({
			type: "POST",
			url: addr,
			data: post,
			success: function(data){
				if(!json)
					self.setContent(data,300);

				if(action!=null)
					action(data);

				self.move();
			}
		});
	}
	
	this.close = function(){
		$(self.object).fadeOut(300, function(){
			$(self.object).remove();
			
		});
		$(self.blackscreen).fadeOut(200, function(){
			$(self.blackscreen).remove();
		})
	}
	this.draw = function(){
                //Предлагаю заменить на это, чтобы можно было вставлять "обвешаный" контент с ивентами
		this.object=$('<div />').addClass("mywnd").append(this.content).appendTo('body');
		this.setSize(this.width,this.height);
		$(this.object).bind('click',function(e){
			if($(e.target).hasClass('closeMywnd')){self.close();}
		})
		/*$(window).scroll(function(){
			if($(self.object).height()<($(window).height()))
				self.move();
		})*/
		if(this.modal){
                    this.blackscreen=$('<div class="blackscreen"></div>')
                    .appendTo('body');
                    //Предлагаю повесить событие на клик на подложку чтобы исчезало все.
                    if(this.closeOnTap)
                        this.blackscreen.click(function(){self.close();return false;});
                }
	}
	this.draw();
}

    $.rusWindows = {};
(function($){
    /**
     * jQuery plugin for wnd function<br/><br/>
     * 
     * Usage:<br/>
     * <i>$('.needed_content').rusWindow({modal:false,width:400,height:300});</i><br/>
     * The options at this expample set as default values in the plugin.<br/>
     * So you could do it like this: <i>$('.needed_content').rusWindow()</i><br/>
     * @augments options json array of options as {modal:(true|false),width:[number],height:[number]}
     */
    $.fn.rusWindow = function(options){
        var id = $(this).selector;
        if(id=='') 
            id = '#'+$(this).attr('id');
        var ops = {
            'modal':false,
            'width':400,
            'height':300,
            'title':false,
            'template':'<div class="show_wnd"><div class="wnd_content" style="padding-right:0px;"><div class="wnd_title" logic="title"></div><div logic="content" style="height:100%"></div><div class="wnd_footer" logic="footer"></div></div></div>',
            'position':'center',
            'footer':false
        };
        ops = $.extend({}, ops, options);
        var content = $(ops.template);
        if(ops.title === false){
            content.find('[logic="title"]').remove();
        }else{
            content.find('[logic="title"]')
            .append(ops.title)
            .append($('<div class="wnd_title_right"></div>').append($('<a href="#close" title="Закрыть" class="closeLink">&times;</a>')
                    .click(function(){$.rusWindows[id].close();return false;})));
        }
        if(ops.footer === false){
            content.find('[logic="footer"]').remove();
        }else{
            //TODO: footer logic: buttons, html, statusbar
            content.find('[logic="footer"]').append(ops.footer);
        }
        content.find('[logic="content"]').prepend(this);
        $.rusWindows[id] = new wnd(content,ops.modal,ops.width,ops.height);
        if(typeof ops.position == 'string'){
            $.rusWindows[id].setRelativePosition(ops.position);
        }else
            $.rusWindows[id].setPositionXY(ops.position.x,ops.position.y);
        return $.rusWindows[id];
    }
    $.dialog = function(content,title){
        var ops = {
            'modal':true,
            'width':400,
            'height':140,
            'title':false,
            'template':'<div class="show_wnd"><div class="dialog_title" logic="title"></div><div class="dialog_content" logic="content"></div><div class="dialog_footer" logic="footer"></div></div>',
            'position':'center',
            'footer':false
        };
        if(typeof title == 'undefined')
            title = 'MAG';
        if(typeof content == 'undefined')
            content = '';
        ops.title = title;
        var div = $('<div />');
        var footer = $('<div />').css({'padding':'5px'});
        div.append(content);
        ops.footer = footer;
        var zindex = 1*parseInt($('.blackscreen:last').css('z-index')) + 10;
        var rwnd = div.rusWindow(ops);
        $(rwnd.blackscreen).css('z-index',zindex);
        footer.append($('<button />').attr({'type':'button'}).html('Ok').prepend('<i></i>').append('<b></b>').click(function(){rwnd.close()}));
        $(rwnd.content).parent().css('z-index',zindex+1)
        return rwnd;
    }
    $.alert = function(content,title){
        var img = $('<span />').append(content).wrap('<div />');
        $('<img />').attr({'src':'/images/alert1.png','alt':'!','height':'48px'}).css({'font-size':'48px','color':'#9494ff','float':'left','margin-right':'20px'})
        .insertBefore(img);
        return $.dialog(img.parent(),title);
    }
    
    $.loader = function(){
        if(typeof $.rusWindows['@loader'] !== 'undefined'){
            $.rusWindows['@loader'].close();
            $.rusWindows['@loader'] = undefined;
            return true;
        }
        var content = $('<img />').attr({'src':'/static/css/loader02.gif'});
        var zindex = 1*parseInt($('.blackscreen:last').css('z-index')) + 11;
        var rwnd = new wnd(content,true,content.width(),content.height(),false);
        rwnd.setZindex(zindex);
        rwnd.setRelativePosition('center');
        $.rusWindows['@loader'] = rwnd;
        return rwnd;
    }
    
})(jQuery);