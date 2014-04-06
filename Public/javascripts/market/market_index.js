$(function(){
	$("#ordernew").click(function(){
		$.post('/market/store_order', {
			'order': 'new'
		}, function(data){
			window.location.reload();
		}, 'json');
	});

	$("#orderhot").click(function(){
		$.post('/market/store_order', {
			'order': 'hot'
		}, function(data){
			window.location.reload();
		}, 'json');
	});
});