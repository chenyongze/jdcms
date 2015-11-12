$(document).ready(function(){
	/**
	 *瀑布流
	 */
	//容器
	var $container = $('.content');
	//获取图片高度
	$.each($(".pic"),function(){
		var imgl = new Image();
		imgl.src = $(this).find(".item_img").attr('src');

		var heightl;
		heightl = (225/imgl.width)*imgl.height;
		
		$(this).css('height',heightl);
		$(this).find('.item_img').attr('height',heightl);
	});
	//瀑布流布局
	$container.masonry({
		itemSelector : '.col',
		columnWidth : 120
	});
})