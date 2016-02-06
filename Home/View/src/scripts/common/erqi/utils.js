$.extend($, {

	bytesToSize: function(bytes) {
	    if (bytes === 0) return '0 B';
	    var sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
	        i = Math.floor(Math.log(bytes) / Math.log(1024));
	   return parseFloat((bytes / Math.pow(1024, i)).toFixed(2)) + sizes[i];
	},

	getCookie: function(name) {
		var cookie_pairs = document.cookie.split(/;\s?/);
			cookie_map = {};
		for(var i=0, len=cookie_pairs.length; i<len; i++) {
			var kv = cookie_pairs[i].split("=");
			cookie_map[kv[0]] = decodeURIComponent(kv[1]);
		}

		return cookie_map[name] || "";
	},

	_param: undefined,
	parseQueryParam: function() {
		if(!$._param) {
			$._param = {};
			var pairs = location.search.substring(1).split("&");
			$.each(pairs, function(i, pair) {
				var t = pair.split("=");
				$._param[t[0]] = decodeURIComponent(t[1]);
			});
		}

		return $._param;
	}
});