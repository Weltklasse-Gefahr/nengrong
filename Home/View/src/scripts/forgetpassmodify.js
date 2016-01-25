$(function(){
	function warning(temp) {
		$("#warning").css('visibility','visible').html(temp);
	}

	$("#jumpbtn").click(function(){ 
		var newpasslval= $.trim($("#newpassinput").val());
		var repeatpasslval= $.trim($("#repeatpassinput").val());

		if (!passval) {
			warning("密码不能为空");
			return ;
		};
		if(passval!== repeatpassval) {
			warning("密码不一致");
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=user&a=forgetpassmodify" ,
		    data: {
		    	email: mailval,
		    	password: newpasslval,
		    	rtype: 1
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0) {
				location.href=data.url;
			}
			else{
				warning(data.msg || "跳转失败");
			}
		});
	});
});
