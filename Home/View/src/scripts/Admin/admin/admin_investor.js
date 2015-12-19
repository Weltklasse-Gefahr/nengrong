$(function(){

	$("#delete_id").click(function(){
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_id").off("click.delete");
		$("#confirm_id").on("click.delete", function(){
			alert(item_id);
		});
	});

	$(".edit_id").click(function(){
		var item_id = $(this).parent().parent().data("id");
		location.href="?c=Admin&a=getEditUserInfo&id="+item_id;
	});

	$("#edit_save").click(function(){ 
		var edit_id= $.trim($("#edit_id").val());
		var edit_email= $.trim($("#edit_email").val());
		var edit_company_name= $.trim($("#edit_company_name").val());

		if(!edit_email || !edit_company_name ) {
			warning();
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=Admin&a=changeProjectInvestorInfo" ,
		    data: {
				id: edit_id,
		    	email: edit_email,
		    	companyName: edit_company_name
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0)
				{$("#myAlert_edit_success").show();}
			else 
				{$("#myAlert_edit_failed").show();}
		});
	});

	$(".myAlert_edit_success_closed").click(function(){
		$("#myAlert_edit_success").hide();
	});

	$(".myAlert_edit_failed_closed").click(function(){
		$("#myAlert_edit_failed").hide();
	});

	$(".reset_id").click(function(){ 
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_reset_id").off("click.reset");
		$("#confirm_reset_id").on("click.reset", function(){
			$.ajax({
		    type: "post",
		    url: "?c=Admin&a=resetPassword" ,
		    data: {
				id: item_id
		    },
			dataType: "json"
			}).done(function(data){
				if (data.code== 0)
					{$("#myAlert_reset_success").show();}
				else 
					{$("#myAlert_reset_failed").show();}
			});
		});

		
	});

	$(".myAlert_reset_success_closed").click(function(){
		$("#myAlert_reset_success").hide();
	});

	$(".myAlert_reset_failed_closed").click(function(){
		$("#myAlert_reset_failed").hide();
	});

	$(".delete_id").click(function(){ 
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_delete_id").off("click.delete");
		$("#confirm_delete_id").on("click.delete", function(){
			$.ajax({
		    type: "post",
		    url: "?c=Admin&a=deleteUser" ,
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
		location.href="?c=Admin&a=getAllProjectInvestorInfo";
	});

	$(".myAlert_delete_failed_closed").click(function(){
		$("#myAlert_delete_failed").hide();
		location.href="?c=Admin&a=getAllProjectInvestorInfo";
	});


	$("#add_investor").click(function(){ 
		var email= $.trim($("#add_mail").val());
		var company_name= $.trim($("#add_company_name").val());

		if(!email || !company_name) {
			warning();
			return ;
		}

		$.ajax({
		    type: "post",
		    url: "?c=Admin&a=addProjectInvestor" ,
		    data: {
		    	email: email,
		    	companyName: company_name
		    },
			dataType: "json"
		}).done(function(data){
			if (data.code== 0)
				{
					$("#modal-add-event").hide();
					$("#myAlert_add_success").show();
				}
			else 
				{
					$("#myAlert_add_failed").show();
				}
		});

		
	});

	$(".myAlert_add_success_closed").click(function(){
		$("#myAlert_add_success").hide();
		location.href="?c=Admin&a=getAllProjectInvestorInfo";
	});

	$(".myAlert_add_failed_closed").click(function(){
		$("#myAlert_add_failed").hide();
		location.href="?c=Admin&a=getAllProjectInvestorInfo";
	});

});