function openLogin() {
	document.getElementById("win").style.display = "";
	document.body.style.overflow = "hidden";
}
function closeLogin() {
	document.getElementById("win").style.display = "none";
	document.body.style.overflow = "auto";
}

/**
 * 图片轮播
 */
$(function() {
	require("widgets/top-fixed.js");
	$("#topNav").children(".splitBar").hide();
	$("#topNav").topFixed(); /* 滚动时顶部导航栏停靠在页面上方 */
	$("nav li .Index").addClass("active");
	
	$('#your-slider').flexslider(
			{
				playAfterPaused : 8000,
				slideshowSpeed : 3000,
				before : function(slider) {
					if (slider._pausedTimer) {
						window.clearTimeout(slider._pausedTimer);
						slider._pausedTimer = null;
					}
				},
				after : function(slider) {
					var pauseTime = slider.vars.playAfterPaused;
					if (pauseTime && !isNaN(pauseTime) && !slider.playing) {
						if (!slider.manualPause && !slider.manualPlay
								&& !slider.stopped) {
							slider._pausedTimer = window.setTimeout(function() {
								slider.play();
							}, pauseTime);
						}
					}
				}
			// 设置其他参数
			});
	$('#your-slider2').flexslider(
			{
				playAfterPaused : 8000,
				slideshowSpeed : 5000,
				before : function(slider) {
					if (slider._pausedTimer) {
						window.clearTimeout(slider._pausedTimer);
						slider._pausedTimer = null;
					}
				},
				after : function(slider) {
					var pauseTime = slider.vars.playAfterPaused;
					if (pauseTime && !isNaN(pauseTime) && !slider.playing) {
						if (!slider.manualPause && !slider.manualPlay
								&& !slider.stopped) {
							slider._pausedTimer = window.setTimeout(function() {
								slider.play();
							}, pauseTime);
						}
					}
				},
			// 设置其他参数
			});
	$('#your-slider3').flexslider(
			{
				playAfterPaused : 8000,
				slideshowSpeed : 8000,
				before : function(slider) {
					if (slider._pausedTimer) {
						window.clearTimeout(slider._pausedTimer);
						slider._pausedTimer = null;
					}
				},
				after : function(slider) {
					var pauseTime = slider.vars.playAfterPaused;
					if (pauseTime && !isNaN(pauseTime) && !slider.playing) {
						if (!slider.manualPause && !slider.manualPlay
								&& !slider.stopped) {
							slider._pausedTimer = window.setTimeout(function() {
								slider.play();
							}, pauseTime);
						}
					}
				}
			// 设置其他参数
			});
});