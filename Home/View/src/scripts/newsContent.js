$(function() {
	require("widgets/top-fixed.js");
	$("#topNav").topFixed(); /* 滚动时顶部导航栏停靠在页面上方 */
	$("nav li .News").addClass("am-active");
	
	var l_items = $(".l-nav li");
	l_items.filter(".newsList").addClass("active");
	l_items.click(function() {
		location.href = $(this).data("href");
	});
	
	/* 上一篇|下一篇  */
	var pages = $(".pager a");
	pages.click(function(){
		if($(this).hasClass("disabled")) {
			return false;
		}
	});

});