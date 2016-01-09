$(function() {

	$("#logout").click(function() {
		$.ajax({
			url: "?c=User&a=logout&rtype=1",
			type: "POST",
			dataType: "json"
		}).done(function(data) {
			if(data && data.code == "0") {
				location.href = "?c=User&a=login";
			}
		});
		return false;
	});

});