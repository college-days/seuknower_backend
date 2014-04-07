window.sncode = "null";
window.prize = "谢谢参与";

var zjl = false;
var num = 0;
var goon = true;
$(function() {
	$("#scratchpad").wScratchPad({
		width: 150,
		height: 40,
		color: "#a9a9a7",
		scratchMove: function() {
			num++;
			if (num == 2) {
				document.getElementById('prize').innerHTML = $("#result").attr("content");
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

	/*$("#sharelottery").click(function(){
		var flag = $(this).text().replace(/[ ]/g, "");
		if(flag == "分享"){
			$.post('/game/addLottery', {
				'haha': 'haha'
			}, function(data){
				if(data.status == 1){
					alert("恭喜你又获得了两次抽奖机会");
				}
			}, 'json');
			// $(".bshare-custom").slideDown();
			$("#renrenshare").slideDown();
			$(this).text("收起分享");
		}else{
			// $(".bshare-custom").slideUp();
			$("#renrenshare").slideUp();
			$(this).text("分享");
		}
	});*/
	$("#sharelottery").click(function(){
		alert("cleantha");
		$.post('/game/addLottery', {
			'haha': 'haha'
		}, function(data){
			if(data.status == 1){
				alert("恭喜你又获得了两次抽奖机会");
			}
		}, 'json');
	});

});

