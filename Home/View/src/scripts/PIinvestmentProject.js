$(function() {

	$(".l-nav").find(".investmentProject").addClass("active");


	/* ·ÖÒ³ */
	$(".pager span").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=ProjectInvestorMyPro&a=investmentProject&page="+$this.data("page-index");
		}
		return false;
	});
});