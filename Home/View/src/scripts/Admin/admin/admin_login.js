$(function(){
	function warning() {
		$("#warning").show();
	}

	$("#loginbtn").click(function(){
		var username= $.trim($("#userName").val());
		var password= $.trim($("#password").val());
		var form = $("form");

		if(!password || !username ) {
			warning();
			return ;
		}

		$.ajax({
		    type: form.attr("method"),
		    url: form.attr("action"),
		    data: form.serialize(),
			dataType: "json"
		}).done(function(data){
			if (data.code == 0) 
			{
				location.href="?c=Admin&a=getAllInnerStaffInfo";
			}
			else
			{
				warning();
			}
		});
		return false;
	});
});