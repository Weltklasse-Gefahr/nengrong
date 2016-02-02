$(function() {
	require("widgets/top-fixed.js");
	$("#topNav").topFixed(); /* 滚动时顶部导航栏停靠在页面上方 */
	
	$("nav li .AboutUs").addClass("am-active");
	
	var l_items = $(".l-nav li");
	l_items.filter(".contact").addClass("active");
	l_items.click(function() {
		location.href = $(this).data("href");
	});
});