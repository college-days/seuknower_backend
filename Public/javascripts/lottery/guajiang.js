window.sncode = "null";
window.prize = "谢谢参与";

var zjl = false;
var num = 0;
var goon = true;
$(function() {
	var isshared = $("#isshared").attr("content");
	if(isshared == 1){
		alert("您已经分享过啦,今天没有刮奖机会了哦");
	}else{
		alert("您还没有分享，点击分享可以增加一次刮奖机会哦");
	}

	$("#scratchpad").wScratchPad({
		width: 150,
		height: 40,
		color: "#a9a9a7",
		scratchMove: function() {
			num++;console.debug(num,777)
			if (num == 2) {
				$('#prize').html($("#result").attr("content"));
			}
			if(num>100){
				var notice = $("#notice").css("display") == "none";
				if(notice){
					$("#notice").slideToggle(50);
					// alert("恭喜您中了<font color='blue'>四等奖</font>，请前往大活广场找学生会跳蚤市场工作人员领奖哦！（点击右上角分享可以再获得一次刮奖机会）")
				}
			}

			if (zjl && num > 5 && goon) {
				//$("#zjl").fadeIn();
				goon = false;

				$("#zjl").slideToggle(500);
				//$("#outercont").slideUp(500)
			}
		}
	});

	//$("#prize").html("谢谢参与");
	//loadingObj.hide();
	//$(".loading-mask").remove();

	$("#sharelottery").click(function(){
		var flag = $(this).text().replace(/[ ]/g, "");
		if(flag == "分享"){
			$.post('/game/addLottery', {
				'haha': 'haha'
			}, function(data){
				if(data.status == 1){
					alert("恭喜你又获得了一次抽奖机会");
				}
			}, 'json');
			// $(".bshare-custom").slideDown();
			$("#sharelist").slideDown();
			$(this).text("收起分享");
		}else{
			// $(".bshare-custom").slideUp();
			$("#sharelist").slideUp();
			$(this).text("分享");
		}
	});

	/*
	$("#sharelottery").click(function(){
		$.post('/game/addLottery', {
			'haha': 'haha'
		}, function(data){
			if(data.status == 1){
				alert("恭喜你又获得了两次抽奖机会");
			}
		}, 'json');
	});*/

});

