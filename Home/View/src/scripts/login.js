$(function(){
	function warning() {
		$("#warning").show();
	}
	function warning2() {
		$("#warning2").show();
	}

	$("#loginbtn").click(function(){ 
		var mailval= $.trim($("#mailinput").val());
		var passval= $.trim($("#passinput").val());
		var dynamiccodeval= $.trim($("#dynamiccodeinput").val());

		if (!mailval || !/^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/.test(mailval)) {
			warning();
			return ;
		};

		if(!passval ) {
			warning();
			return ;
		}

		if(!dynamiccodeval ) {
			warning2();
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=user&a=login" ,
		    data: {
		    	email: mailval,
		    	password: passval,
		    	dynamicCode:dynamiccodeval
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0) {};
			location.href=data.url;
		});
	});
});