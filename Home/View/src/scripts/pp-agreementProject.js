$(function() {

	$(".l-nav").find(".agreementProject").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");

	/* ·ÖÒ³ */
	$(".pager span").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=ProjectProviderMyPro&a=agreementProject&page="+$this.data("page-index");
		}
		return false;
	});

	// 跳转项目详情页
	$(".bd").on("click", "a", function(){
		var data = $(this).data();
		location.href = "?c=ProjectProviderMyPro&a=projectInfoView&no=" + data.id + "&token=" + data.idm;
		return false;
	});

});