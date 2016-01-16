$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");

	// 跳转项目详情页或编辑页
	$(".list .bd a").click(function(e) {
		var data = $(this).data();
		if(data.status == 11) {
			location.href = "?c=ProjectProviderMyPro&a=projectInfoEdit&no=" + data.id + "&token=" + data.idm;
		} else {
			location.href = "?c=ProjectProviderMyPro&a=projectInfoView&no=" + data.id + "&token=" + data.idm;
		}
		return false;
	});
});