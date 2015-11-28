/* 依赖jquery */

$.fn.topFixed = function() {
	var content = $(this),
		height = content.height(),
		ct = $("<div>");
	
	content.before(ct);
	ct.append(content).css({
		height: height+1,
		width: "100%",
		minWidth: "1200px"
	});
//	content.addClass("topFixed");
	
	var st = 0, DELTA = 0;
	
	var win = $(window);
	win.on('scroll resize', _scroll);

	setTimeout(_scroll, 15);

	function _scroll() {
		content.toggleClass('topFixed', win.scrollTop() > DELTA);
	}
};