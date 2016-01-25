$(function(){
	var keepFlag= 1;
	function warning(temp) {
		$("#warning").css('visibility','visible').html(temp);
	}

	$("#markbox").change(function(){
		if(this.checked){
			var keepFlag= 1;
		}
		else{
			var keepFlag= 0;
		}

	})

	$("#loginbtn").click(function(){ 
		var mailval= $.trim($("#mailinput").val());
		var passval= $.trim($("#passinput").val());

		if (!mailval || !/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(mailval)) {
			warning("用户名格式错误");
			return ;
		}

		if(!passval ) {
			warning("密码不能为空");
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=user&a=login" ,
		    data: {
		    	email: mailval,
		    	password: passval,
		    	keepFlag: keepFlag,
		    	rtype: 1
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0) {
				location.href=data.url;
			}
			else{
				warning(data.msg || "登录失败");
			}
		});
	});
});