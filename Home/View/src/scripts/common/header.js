$(function() {
	$("ul.sub").parent().hover(function() {
		$(this).children("ul.sub").show();
	}, function() {
		$(this).children("ul.sub").hide();
	});

	$("#lang a").click(function(e) {
		e.preventDefault();
		var v = $(this).data("lang");
		document.cookie = "lang=" + v + ";";
		location.reload();
	});
});