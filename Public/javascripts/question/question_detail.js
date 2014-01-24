$(function(){
	$(".agree").click(function(){
		var agree = $(this);
		$.post('/answer/add_agree', {'id': $(this).parents("li").attr("aid")}, function(data){
			if(data.status) {
				agree.text(parseInt(agree.text())+1);
			}else{
				window.location.href="/login";
			}
		}, "json");
	});
	
	$(".object").click(function(){
		var object = $(this);
		$.post('/answer/add_object', {'id': $(this).parents("li").attr("aid")}, function(data){
			if(data.status) {
				object.text(parseInt(object.text())+1);
			}else{
				window.location.href="/login";
			}
		}, "json");
	});
});