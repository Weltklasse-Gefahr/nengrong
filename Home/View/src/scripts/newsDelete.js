$(function() {
	require("widgets/top-fixed.js");
	$("#topNav").topFixed(); /* 滚动时顶部导航栏停靠在页面上方 */
	
	$("nav li .News").addClass("am-active");
	
	var l_items = $(".l-nav li");
	l_items.filter(".newsList").addClass("active");
	l_items.click(function() {
		location.href = $(this).data("href");
	});
	
	/* 查看/删除 新闻详情  */
	var r_items = $(".bd .list li");
	r_items.click(function(e){
		if($(e.target).hasClass("del")) {
			var token = prompt("请输入token");
			if(token) {
				$.ajax({
					url: "?c=News&a=deletenews",
					type: "POST",
					dataType: "json",
		            data: {
		            	id: $(this).data("id"),
		            	token: token
		            }
				}).done(function(data) {
					if(data.code == "0") {
						alert("删除成功！");
						location.reload();
					} else {
						alert("删除失败！\n"+data.errmsg);
					}
				});
			}
			return;
		}


		location.href = "?c=News&a=newsContent&id="+$(this).data("id");
	});
	
	/* 分页 */
	$(".pager a").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=News&a=newsDelete&page="+$this.data("pageno");
		}
		return false;
	});

});