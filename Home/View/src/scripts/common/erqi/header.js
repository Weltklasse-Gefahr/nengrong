$(function() {

	$("#logout").click(function() {
		$.ajax({
			url: "?c=User&a=logout",
			type: "POST",
			dataType: "json"
		}).done(function(data) {
			if(data && data.code == 0) {
				location.href = "?c=Index";
			}
		});
		return false;
	});

});