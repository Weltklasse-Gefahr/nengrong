$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active")
		.children("a").attr("href", "javascript:;");

	require("common/erqi/list-opt.js");
	require("common/erqi/pager.js");
});