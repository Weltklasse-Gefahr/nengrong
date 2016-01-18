$(function() {

	// 判断身份，更新顶部信息
	var userName = $.getCookie("userName");
	if(userName) {
		$(".header .identityName").html("，" + userName);
	}
	if($.getCookie("userType") == "2") {
		$(".header .innerStaff").show();
	}

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