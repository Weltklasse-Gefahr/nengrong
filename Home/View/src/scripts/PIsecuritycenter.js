$(function(){
	$(".l-nav").find(".securitycenter").addClass("active").children("a").attr("href", "javascript:;");;

	function warning(temp) {
		$("#warning").show().html(temp);
	}

	$("#changebtn").click(function(){ 
		var oldpassval= $.trim($("#oldpassinput").val());
		var newpassval= $.trim($("#newpassinput").val());
		var repeatpassval= $.trim($("#repeatpassinput").val());

		if(oldpassval== newpassval) {
			warning("新旧密码不能一样");
			return ;
		}
		if(newpassval!== repeatpassval) {
			warning("确认密码不一致");
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=ProjectInvestorMyInfo&a=securityCenter" ,
		    data: {
		    	password：oldpassval,
		    	newPassword: newpassval,
		    	rtype:1
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0) {
				warning(data.msg || "修改成功");
				location.href='?c=User&a=login';
			}
			else{
				warning(data.msg || "修改失败");
			}
		});
	});
});