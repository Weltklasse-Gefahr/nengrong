$(function() {

	$(".l-nav").find(".recommendedProject").addClass("active");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");

	/* ·ÖÒ³ */
	$(".pager a").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=ProjectInvestorMyPro&a=recommendedProject&page="+$this.data("pageno");
		}
		return false;
	});
});