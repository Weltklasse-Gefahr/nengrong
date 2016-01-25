$(function(){
	function warning() {
		$("#warning").show();
	}

	$("#changePwd").click(function(){
		$("#warning").hide();
		var password= $.trim($("#password").val());
		var newPwd= $.trim($("#newPwd").val());
		var confirmNewPwd= $.trim($("#confirmNewPwd").val());

		if(!password || !newPwd || !confirmNewPwd ) {
			warning();
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=Admin&a=changePassword" ,
		    data: {
		    	password: password,
		    	newPwd: newPwd,
		    	confirmNewPwd: confirmNewPwd,
				rtype: 1
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0)
				{$("#myAlert_change_pwd_success").show();}
			else 
				{$("#myAlert_change_pwd_failed").show();}
		});
	});

	$(".myAlert_change_pwd_success_closed").click(function(){
		$("#myAlert_change_pwd_success").hide();
		location.href="?c=Admin&a=changePassword";
	});

	$(".myAlert_change_pwd_failed_closed").click(function(){
		$("#myAlert_change_pwd_failed").hide();
		location.href="?c=Admin&a=changePassword";
	});
});