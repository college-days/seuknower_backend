var isa = false;
var isp = false;

$(function(){
	$('#username').blur(function(){
		var username = $(this).val();
		if(!username.replace(/[ ]/g,"")){
			$("#usernamealert").text('请填写一卡通号');
			isa = false;
		}
		else{
			if(isNaN(username)){
				$("#usernamealert").text("一卡通错误");
				isa = false;
			}else{
				$("#usernamealert").text("");
				isa = true;
			}
		}
	});
	$('#password').blur(function(){
		var password = $(this).val();
		if(!password.replace(/[ ]/g,"")){
			$("#passwordalert").text('请填写密码');
			isp = false;
		}
		else{
			$("#passwordalert").text("");
			isp = true;
		}
	});
	
	//ajax
	$('#submit').click(function(){
		if(isa && isp){
			$.post('/game/check_login', {
				account: $('#username').val()+"@seu.edu.cn", 
				password: $('#password').val(), 
			}, function(data){
					if(data.status == 1){
						window.location.href = '/game/guajiang';
					}else if(data.status == 3){
						alert("你已经抽过奖了");
					}else{
						alert(data.info);	
						$("#title").text(data.info);
					} 
			}, 'json');
		}
	});
});