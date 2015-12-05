/* 分页 */
$(".pager span").click(function() {
	var $this = $(this);
	if(!$this.hasClass("active")) {
		location.href = location.href.replace(/&?\bpage=\w+/, "") + "&page=" + $this.data("pageIndex");
	}
	return false;
});