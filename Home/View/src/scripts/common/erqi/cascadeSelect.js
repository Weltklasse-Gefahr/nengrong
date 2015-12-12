$(function($) {

	function dealData(data) {
		var ret = {
			'1': { name: 'root', code: "1", children: []}
		};

		for (var id in data) {
			var item = data[id],
				pid = item[1],
				pitem = ret[pid];

			ret[id] = {
				name: item[0],
				code: id,
				parent: pid
			};

			if (pitem) {
				if (!pitem.children) {
					pitem.children = [];
				}
				pitem.children.push(id);
			}
		}
		return ret;
	}

	$.fn.cascadeSelect = function(data) {
		var self = this,
			cascadeData = dealData(data);

		// 填充第一级
		var first_options =  self.get(0).options,
			first_items = cascadeData['1'].children;
		$.each(first_items, function(i, item) {
			first_options.add(new Option(cascadeData[item].name, item));
		});

		// 级联
		self.change(function() {
			var $nextSelect = $(this).next("select");
			if($nextSelect.length) {
				$nextSelect[0].length = 1;
				var nextItems = cascadeData[this.value] && cascadeData[this.value].children;
				if(nextItems && nextItems.length) {
					$.each(nextItems, function(i, item) {
						$nextSelect[0].options.add(new Option(cascadeData[item].name, item));
					});
				}
				$nextSelect[0].selectedIndex = 0;
				$nextSelect.trigger("change");
			}
		});

	};
});