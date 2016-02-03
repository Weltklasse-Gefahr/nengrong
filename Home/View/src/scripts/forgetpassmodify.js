$(function(){
	var param = $.parseQueryParam();
	function warning(temp) {
		$("#warning").css('visibility','visible').html(temp);
	}

	$("#jumpbtn").click(function(){ 
		var newpassval= $.trim($("#newpassinput").val());
		var repeatpassval= $.trim($("#repeatpassinput").val());

		if (!newpassval) {
			warning("密码不能为空");
			return ;
		};
		if(newpassval!== repeatpassval) {
			warning("密码不一致");
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=user&a=forgetpwdmodify&key="+param.key ,
		    data: {
		    	password: newpassval,
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
