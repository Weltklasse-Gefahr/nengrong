$(function(){
	function warning(temp) {
		$("#warning").css('visibility','visible').html(temp);
	}

	$("#jumpbtn").click(function(){ 
		var mailval= $.trim($("#mailinput").val());

		if (!mailval || !/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(mailval)) {
			warning("邮箱格式错误");
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=user&a=forgetpassword" ,
		    data: {
		    	email: mailval,
		    	rtype: 1
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0) {
				location.href="?c=user&a=login";
			}
			else{
				warning(data.msg || "跳转失败");
			}
		});
	});
});
