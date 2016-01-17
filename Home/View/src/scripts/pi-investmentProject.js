$(function() {

	$(".l-nav").find(".investmentProject").addClass("active");

	require("common/erqi/pager.js");
	
	// 跳转项目详情页
	$(".bd").on("click", "a", function(){
		var data = $(this).data();
		location.href = "?c=ProjectInvestorMyPro&a=projectInfoView&no=" + data.id + "&token=" + data.idm;
		return false;
	});
});