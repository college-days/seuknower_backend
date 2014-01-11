$(function(){
	//
	$('.market .meau img').mouseenter(function(){
		var src = $(this).attr("src");
		$(this).attr("src",src.replace("normal","hover"));
	});
	$('.market .meau img').mouseleave(function(){
		var src = $(this).attr("src");
		$(this).attr("src",src.replace("hover","normal"));
	});
	$('.go img').mouseenter(function(){
		var src = $(this).attr("src");
		$(this).attr("src",src.replace("normal","hover"));
	});
	$('.go img').mouseleave(function(){
		var src = $(this).attr("src");
		$(this).attr("src",src.replace("hover","normal"));
	});
})