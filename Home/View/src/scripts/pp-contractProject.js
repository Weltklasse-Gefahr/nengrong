$(function() {

	$(".l-nav").find(".contractProject").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");

	/* ·ÖÒ³ */
	$(".pager span").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=ProjectProviderMyPro&a=contractProject&page="+$this.data("page-index");
		}
		return false;
	});
});