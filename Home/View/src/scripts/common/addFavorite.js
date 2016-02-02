$("#favorites").click(function() {
	var ctrl = (navigator.userAgent.toLowerCase()).indexOf('mac') != -1 ? 'Command/Cmd' : 'CTRL';
	try{
		if (window.external && window.external.addFavorite) {
			window.external.addFavorite(location.href, document.title);
		} else if (window.sidebar && window.sidebar.addPanel) {
			window.sidebar.addPanel(document.title, location.href, "");
		} else {
			alert('请使用快捷键' + ctrl + ' + D 加入到收藏夹');
		}
	} catch (ex) {
		alert('请使用快捷键' + ctrl + ' + D 加入到收藏夹');
	}
	return false;
});