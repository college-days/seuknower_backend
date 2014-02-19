$(function(){
	$(".new a").click(function(){
		alert('cleantha');
		if($(".win").length > 0) {
			$(".mask").css("display","block");
			$(".win").css("display","block");
		}
		else{
			newQuestion();
		}
	});
	
	
});


function newQuestion(){
	var newMask = document.createElement("div");
	newMask.className = "mask";
	newMask.style.width = document.body.scrollWidth + "px";
	newMask.style.height = document.body.scrollHeight + "px";
	document.body.appendChild(newMask);
		
	var newWin = document.createElement("div");
	newWin.className = "win";
	newWin.style.left = (parseInt(document.body.scrollWidth) - 544)/2 + "px";
	document.body.appendChild(newWin);
		
	var html = '<div class="win"><div class="title-bar"><span>提问</span><div class="close"></div></div><div class="content"><div class="search"><p>提问之前请先搜索</p><input type="text" class="value"/><ul><li><a href="#">有没四牌楼校区的风景图 </a></li><li><a href="#">有没四牌楼校区的风景图 </a></li><li><a href="#">有没四牌楼校区的风景图 </a></li><li><a href="#">有没四牌楼校区的风景图 </a></li><li class="ask">我要提问</li></ul>	</div><div class="question-new"><p>问题</p><input type="text" class="value"/><p>问题描述</p><textarea></textarea><p>问题类型</p><select><option value="校园生活">校园生活</option><option value="学习考试">学习考试</option><option value="电脑技术">电脑技术</option><option value="其他">其他</option></select><div><input type="checkbox" class="check"/><span>匿名提问</span><input type="button" value="确定" class="btn"/></div></div></div></div>';
		
	$(".win").append(html);
	
	$(".close").click(function(){
		$(".win").css("display","none");
		$(".mask").css("display","none");
	});
	
	$(".mask").click(function(){
		$(".win").css("display","none");
		$(".mask").css("display","none");
	});
	
	$(".ask").click(function(){
		$(".win .search").slideUp();
		$(".question-new").css("display","block");
	});
}