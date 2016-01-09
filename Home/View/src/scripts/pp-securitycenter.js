$(function(){
	
	$(".l-nav").find(".securityCenter").addClass("active")
		.children("a").attr("href", "javascript:;");

	function warning() {
		$("#warning").show();
	}
	function warning2() {
		$("#warning2").show();
	}

	$("#changebtn").click(function(){ 
		var oldpassval= $.trim($("#oldpassinput").val());
		var newpassval= $.trim($("#newpassinput").val());
		var repeatpassval= $.trim($("#repeatpassinput").val());

		if(oldpassval== newpassval) {
			warning1();
			return ;
		}
		if(newpassval!== repeatpassval) {
			warning2();
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=ProjectProviderMyInfo&a=securityCenter" ,
		    data: {
		    	newpass: newpassval,
		    	rtype:1
		    },
			dataType: "json"
		}).done(function(data){
			location.href="http://www.enetf.com";
		});
	});
});