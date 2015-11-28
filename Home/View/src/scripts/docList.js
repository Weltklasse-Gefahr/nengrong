$(function() {
	require("widgets/top-fixed.js");
	$("#topNav").topFixed(); /* 滚动时顶部导航栏停靠在页面上方 */
	$("nav li .News").addClass("am-active");
	
	var l_items = $(".l-nav li");
	l_items.filter(".docList").addClass("active");
	l_items.click(function() {
		location.href = $(this).data("href");
	});
	
	/* 资料下载  */
	var r_items = $(".bd .list li");
	r_items.click(function(){
//		location.href = "?c=news&a=downloadDoc&docid="+$(this).data("id");
		window.open("?c=news&a=downloadDoc&docid="+$(this).data("id"));
	});
	
	/* 分页 */
	$(".pager a").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=News&a=docList&page="+$this.data("pageno");
		}
		return false;
	});

});