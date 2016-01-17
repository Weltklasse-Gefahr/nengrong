$(function() {

	$(".l-nav").find(".contractProject").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/pager.js");

	// 跳转项目详情页
	$(".bd").on("click", "a", function(){
		var data = $(this).data();
		location.href = "?c=ProjectProviderMyPro&a=projectInfoView&no=" + data.id + "&token=" + data.idm;
		return false;
	});
});