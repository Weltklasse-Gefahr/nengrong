$(function() {
	var param = $.parseQueryParam();

	// 维护客服左边栏no和token
	$(".l-nav-is dl a").add(".l-nav-is .export, .l-nav-is .del").each(function() {
		if(param.no || param.token) {
			$(this).attr("href", $(this).attr("href")+"&no="+param.no+"&token="+param.token);
		}
	});

	// 维护项目投资方，导出按钮no和token
	$(".r-content .btn.export").each(function() {
		if(param.no || param.token) {
			$(this).attr("href", $(this).attr("href")+"&no="+param.no+"&token="+param.token);
		}
	})
});

