var html = '';

$(function(){
	checkMessage();
	setInterval(function() {
		checkMessage();
    }, 30000);
});

function checkMessage(){
	console.log('checkmessage');
	// html = html+'<li><a href="/question/123">title</a></li>';
	$.post('/account/check_message', {}, function(data){
		if(data.status == 1){
			html = '';
			for(var i=0; i<data.data.length; i++){
				if(data.data[i]['type'] == 'question'){
					var newItem = '<li><a style="width:180px;" href="/question/'+data.data[i]['q_id']+'">'+data.data[i]['title']+'</a></li>';
					html = html+newItem;
				}
				if(data.data[i]['type'] == 'event'){
					var newItem = '<li><a style="width:180px;" href="/event/'+data.data[i]['e_id']+'">'+data.data[i]['title']+'</a></li>';
					html = html+newItem;
				}
				if(data.data[i]['type'] == 'commodity'){
					var newItem = '<li><a style="width:180px;" href="/market/commodity/'+data.data[i]['c_id']+'">'+data.data[i]['title']+'</a></li>';
					html = html+newItem;
				}
				if(data.data[i]['type'] == 'answer'){
					var newItem = '<li><a style="width:180px;" href="/question/'+data.data[i]['q_id']+'/answer/'+data.data[i]['a_id']+'">'+data.data[i]['title']+'</a></li>';
					html = html+newItem;
				}
			}
			document.getElementById("messagelist").innerHTML = html;	
			$("#redPoint").show();	
			$("#redPoint").text(data.data.length);
		}
		if(data.status == 0){
			$("#redPoint").hide();
			document.getElementById("messagelist").innerHTML = '<li><a href="#">暂时没有消息</a></li>';
		}
	}, 'json');
}
