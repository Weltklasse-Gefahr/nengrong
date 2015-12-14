$(function(){

	$("#delete_id").click(function(){
		var item_id = $(this).parent().parent().data("id");
		$("#confirm_id").off("click.delete");
		$("#confirm_id").on("click.delete", function(){
			alert(item_id);
		});
	});

	$("#edit_id").click(function(){
		var item_id = $(this).parent().parent().data("id");
		location.href = "admin_investors_edit.html";
	});
});