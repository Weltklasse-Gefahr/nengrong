$.extend($, {
	bytesToSize: function(bytes) {
	    if (bytes === 0) return '0 B';
	    var sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'],
	        i = Math.floor(Math.log(bytes) / Math.log(1024));
	   return (bytes / Math.pow(1024, i)).toPrecision(3) + ' ' + sizes[i];
	}
});