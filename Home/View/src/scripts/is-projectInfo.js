$(function(){
	var param = $.parseQueryParam();
	$(".l-nav").find(".projectInfo").addClass("active");

	$("#commentbtn").click(function(){ 
		var commenttexval= $.trim($("#commenttex").val());

		$.ajax({
		    type: "post",
		    url: "?c=InnerStaff&a=projectInfo&no="+param.no+"&token="+param.token,
		    data: {	
		    	comment: commenttexval,
		    	rtype: 1
		    },
			dataType: "json"
		}).done(function(data){});
	});
});