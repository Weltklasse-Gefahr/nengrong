$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");

	// 跳转项目详情页或编辑页
	$(".list .bd .c0 a").click(function(e) {
		
		var $wrap = $(this).parent().parent();
		if($wrap.data("state") === "submited") {
			location.href = "?c=ProjectProviderMyPro&a=projectInfoView&id=" + $wrap.data("id");
		} else {
			location.href = "?c=ProjectProviderMyPro&a=projectInfoEdit&id=" + $wrap.data("id");
		}
		
		return false;
	});
});