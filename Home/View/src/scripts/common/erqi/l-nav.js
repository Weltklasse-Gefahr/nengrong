$(".l-nav dl a").add(".l-nav .export, .l-nav .del").each(function() {
	var param = $.parseQueryParam();
	if(param.no || param.token) {
		$(this).attr("href", $(this).attr("href")+"&no="+param.no+"&token="+param.token);
	}
});