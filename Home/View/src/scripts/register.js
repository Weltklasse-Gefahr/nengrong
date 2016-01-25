$(function(){
	function warning(temp) {
		$("#warning").css('visibility','visible').html(temp);
	}

	$("#markbox").change(function(){
		if(this.checked){
			$("#registerbtn").removeClass("disabled");
		}
		else{
			$("#registerbtn").addClass("disabled");
		}

	})
	$("#registerbtn").click(function(){ 
		var mailval= $.trim($("#mailinput").val());
		var passval= $.trim($("#passinput").val());
		var repeatpassval= $.trim($("#repeatpassinput").val());

		if (!mailval || !/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(mailval)) {
			warning("用户名格式错误");
			return ;
		};
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
		    url: "?c=User&a=register" ,
		    data: {
		    	email: mailval,
		    	password: passval,
		    	rtype: 1
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0) {
				location.href=data.url;
			}
			else{
				warning(data.msg || "注册失败");
			}
		});
	});
});