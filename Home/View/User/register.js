$(function(){
	$("#registerbtn").click(function(){
		var mailval= $("#mailinput").val();
		var passval= $("#passinput").val();

		$.ajax({
		    type: "GET",
		    url: "mock.json" ,
		    data: {
		    	mail: mailval,
		    	pass: passval
		    },
			dataType: "json"
		}).done(function(data){
			location.href="http://www.baidu.com";
			var aa=1;
		});
	});
});