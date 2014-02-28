$(function(){
	$(".repeat").click(function(){
		$.post('/account/re_send', {}, function(data){
			if (data.status) {
				$("#title").text("激活邮件重新发送成功");
	        }else{
	        	$("#title").text(data.info);
	        } 
		}, 'json');
	});
});