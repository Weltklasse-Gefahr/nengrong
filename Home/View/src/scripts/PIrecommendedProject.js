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

	$(".bd").on("click", "a", function(){
		location.href = "?c=InnerStaff&a=dueDiligence&no="+$(this).data("id")
			+"&token="+$(this).data("idm");
	});

});