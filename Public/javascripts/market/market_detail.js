$(function(){
	$(".addlike").click(function(){
		var id = $("#likeinfo").attr("cid");
		var like = $(this);
		var likeCount = $("#likecount");
		alert('addlike');
		if(like.attr("class") == "addlike"){
			alert('addlike');
			$.post('/market/add_like', {'id': id}, function(data){
				if(data.status) {
					likeCount.text(parseInt(likeCount.text())+1);
					like.attr("class", "cancellike");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			alert('cancellike');
			$.post('/market/cancel_like', {'id': id}, function(data){
				if(data.status){
					likeCount.text(parseInt(likeCount.text())-1);
					like.attr("class", "addlike");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
	});

	$(".cancellike").click(function(){
		var id = $("#likeinfo").attr("cid");
		var like = $(this);
		var likeCount = $("#likecount");
		if(like.attr("class") == "addlike"){
			$.post('/market/add_like', {'id': id}, function(data){
				if(data.status) {
					likeCount.text(parseInt(likeCount.text())+1);
					like.attr("class", "cancellike");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}else{
			$.post('/market/cancel_like', {'id': id}, function(data){
				if(data.status){
					likeCount.text(parseInt(likeCount.text())-1);
					like.attr("class", "addlike");
				}else{
					window.location.href="/login";
				}
			}, "json");
		}
	});
});