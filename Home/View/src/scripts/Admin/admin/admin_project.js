$(function(){

	$("#delete_id").click(function(){
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_id").off("click.delete");
		$("#confirm_id").on("click.delete", function(){
			alert(item_id);
		});
	});

	$(".delete_id").click(function(){ 
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_delete_id").off("click.delete");
		$("#confirm_delete_id").on("click.delete", function(){
			$.ajax({
		    type: "post",
		    url: "?c=Admin&a=deleteProject" ,
		    data: {
				id: item_id
		    },
			dataType: "json"
			}).done(function(data){
				if (data.code== 0)
					{
						$("#myAlert_delete_success").show();
					}
				else 
					{
						$("#myAlert_delete_failed").show();
					}
			});
		});

		
	});

	$(".myAlert_delete_success_closed").click(function(){
		$("#myAlert_delete_success").hide();
		location.href="?c=Admin&a=getAllProjectProviderInfo";
	});

	$(".myAlert_delete_failed_closed").click(function(){
		$("#myAlert_delete_failed").hide();
		location.href="?c=Admin&a=getAllProjectProviderInfo";
	});

	$(".recover_id").click(function(){ 
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_recover_id").off("click.recover");
		$("#confirm_recover_id").on("click.recover", function(){
			$.ajax({
		    type: "post",
		    url: "?c=Admin&a=recoveryProject" ,
		    data: {
				id: item_id
		    },
			dataType: "json"
			}).done(function(data){
				if (data.code== 0)
					{
						$("#myAlert_recover_success").show();
					}
				else 
					{
						$("#myAlert_recover_failed").show();
					}
			});
		});

		
	});

	$(".myAlert_recover_success_closed").click(function(){
		$("#myAlert_recover_success").hide();
		location.href="?c=Admin&a=getAllProjectProviderInfo";
	});

	$(".myAlert_recover_failed_closed").click(function(){
		$("#myAlert_recover_failed").hide();
		location.href="?c=Admin&a=getAllProjectProviderInfo";
	});

});