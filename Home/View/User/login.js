$(function(){
	$("#loginbtn").click(function(){
		var mailval= $("#mailinput").val();
		var passval= $("#passinput").val();

		$.ajax({
		    type: "GET",
		    url: "http://www.enetf.com/?c=User&a=signIn" ,
		    data: {
		    	email: mailval,
		    	password: passval
		    },
			dataType: "jsonp"
		}).done(function(data){
			location.href="http://www.enetf.com";
			var aa=1;
		});
	});
});