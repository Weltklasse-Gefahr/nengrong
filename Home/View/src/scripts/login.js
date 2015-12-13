$(function(){
	function warning() {
		$("#warning").show();
	}

	$("#loginbtn").click(function(){ 
		var mailval= $.trim($("#mailinput").val());
		var passval= $.trim($("#passinput").val());

		if (!mailval || !/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(mailval)) {
			warning();
			return ;
		};

		if(!passval ) {
			warning();
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=user&a=login" ,
		    data: {
		    	email: mailval,
		    	password: passval,
		    	rtype: 1
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0) {};
			location.href=data.url;
		});
	});
});