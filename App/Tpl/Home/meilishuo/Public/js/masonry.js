$(document).ready(function(){
	/**
	 *瀑布流
	 */
	//获取图片高度
	$.each($(".pic"),function(){
		var imgl = new Image();
		imgl.src = $(this).find(".item_img").attr('src');

		var heightl;
		heightl = (200/imgl.width)*imgl.height;
		
		$(this).css('height',heightl);
		$(this).find('.item_img').attr('height',heightl);
	});
	
	//容器
	var $container = $('.content');
	//瀑布流布局
	var ww = $(document.body).width();
	var sw = ww%240;
	var dw = ww-sw;
	var nav_w=dw-240;
	$(".corner_nav").css('width',nav_w);
	$(".content_fluid").css('width',dw);
	$(".header_top").css('width',dw);
	$container.masonry({
		itemSelector : '.col',
		columnWidth : 120
	});
	//窗口变化
	$(window).resize(function() {
		var ww = $(document.body).width();
		var sw = ww%240;
		var dw = ww-sw;
		var nav_w=dw-240;
		$(".corner_nav").css('width',nav_w);
		$(".content_fluid").css('width',dw);
		$(".header_top").css('width',dw);
	});
});