/* 对列表内容进行过滤 */
$(".list-opt .filter span").click(function() {
	var $this = $(this);
	if(!$this.hasClass("active")) {
		location.href = location.href.replace(/&?\bfilter=\w+/, "") + "&filter=" + $this.data("filter");
	}
	return false;
});