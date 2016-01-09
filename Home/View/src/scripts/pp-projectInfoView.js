$(function() {

	$(".l-nav").find(".awaitingAssessment").addClass("active");

	// 点击进度条
	$(".step > span").click(function() {
		var totalStep = $(this).parent().parent().attr("class").match(/step(\d+)/)[1],
			currentStep = $(this).attr("class").match(/s(\d+)/)[1];
		if(currentStep <= totalStep) {
			$(".r-content .content" + currentStep).show().siblings(".content").hide();
		}

		return false;
	});
	
});