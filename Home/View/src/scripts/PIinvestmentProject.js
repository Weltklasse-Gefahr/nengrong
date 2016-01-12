$(function() {

	$(".l-nav").find(".investmentProject").addClass("active");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");

	/* ·ÖÒ³ */
	$(".pager span").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=InnerStaff&a=pushProject&page="+$this.data("page-index");
		}
		return false;
	});
});