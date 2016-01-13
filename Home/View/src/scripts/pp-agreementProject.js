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

	$(".bd").on("click", "a", function(){
		location.href = "?c=InnerStaff&a=dueDiligence&no="+$(this).data("id")
			+"&token="+$(this).data("idm");
	});

});