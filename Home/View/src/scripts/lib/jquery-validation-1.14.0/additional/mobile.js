$.validator.addMethod("mobile", function(value, element) {
	return value && /^1[\d]{10}$/.test($.trim(value));
});
