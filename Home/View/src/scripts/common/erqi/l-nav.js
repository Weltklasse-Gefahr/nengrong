$(function() {
	var param = $.parseQueryParam();

	// 维护客服左边栏no和token
	$(".l-nav-is dl a").add(".l-nav-is .export, .l-nav-is .del").each(function() {
		if(param.no || param.token) {
			$(this).attr("href", $(this).attr("href")+"&no="+param.no+"&token="+param.token);
		}
	});

	$(".l-nav-is .del").click(function() {
		var $del = $(this);
		$.confirm("删除项目后将无法恢复，是否确认删除？").done(function() {
			$.ajax({
				type: "GET",
				url: $del.attr("href"),
				data: {
					rtype: 1,
				},
				dataType: "json"
			}).done(function(data) {
				if(data.code == "0") {
					location.href = "?c=InnerStaff&a=search";
				} else {
					alert(data.msg || "删除失败！");
				}
			}).fail(function() {
				alert("删除失败！");
			});
		});
		return false;
	});

	// 维护项目投资方，导出按钮no和token
	$(".r-content .btn.export").each(function() {
		if(param.no || param.token) {
			$(this).attr("href", $(this).attr("href")+"&no="+param.no+"&token="+param.token);
		}
	});
});

