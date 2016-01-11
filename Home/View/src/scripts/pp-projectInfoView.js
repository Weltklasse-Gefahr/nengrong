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
	
	// 签署意向书
	require("common/erqi/dialog");
	$('.content2 input[type="submit"]').click(function() {
		if(!$(this).is('[data-optype="agree"]')) {
			return false;
		}
		$.confirm({
			content: "绑定后能融网会为您寻找融资渠道。确认绑定？",
			width: "450px"
		}).done(function() {
			$.ajax({
				url: location.href,
				data: {
					rtype: 1
				}
			}).done(function(data) {
				if(data.code == "0") {
					location.reload();
				} else {
					alert(data.msg ||　"操作失败！");
				}
			}).fail(function() {
				alert("操作失败！");
			});
		});
		return false;
	});
});