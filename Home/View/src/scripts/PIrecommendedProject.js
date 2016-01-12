$(function() {

	$(".l-nav").find(".recommendedProject").addClass("active");
	

	/* ·ÖÒ³ */
	$(".pager span").click(function() {
		var $this = $(this);
		if(!$this.hasClass("active")) {
			location.href = "?c=ProjectInvestorMyPro&a=recommendedProject&page="+$this.data("page-index");
		}
		return false;
	});
});