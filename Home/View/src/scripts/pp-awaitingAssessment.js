$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");

	// 跳转项目详情页
	$(".list .bd .c0 a").click(function(e) {
		location.href = "?c=ProjectProviderMyPro&a=projectInfoEdit&id=" + $(this).parent().parent().data("id");
		return false;
	});
});