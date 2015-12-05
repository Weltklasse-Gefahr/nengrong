$(function() {

	require("common/erqi/header.js"); // 登出，设置公司名称等

	$(".l-nav").find(".dpgxm").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");
});