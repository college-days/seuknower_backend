$(function(){
	$("#finishbtn").click(function(){
		var image = $("#preview img");
		var path = new Array();
		for(var i=1; i<image.length; i++){
			path[i-1] = image.get(i).src;
			path[i-1] = path[i-1].substr(path[i-1].indexOf("Uploads")-1);
		}
		$.post('/market/save_picture', {
			'id': $("#commodity").attr("cid"),
			'path': path
		}, function(data){
			if(data.status){
				window.location.href = "/market/commodity/"+data.data;
			}
			else{
				alert(data.info);
			}
		}, 'json');
	});
});
	
function adjustButtonPos(){
	if($("#preview img").length == 0){
		$("#finishbtn").css("margin-top", "3px");
	}else{
		$("#finishbtn").css("margin-top", "23px");
	}
}

function checkImageCount(){
	length = $("#preview img").length;

	if(length >= 10){
		$("#file_upload").css("display", "none");
		$('#file_upload').uploadify('disable', true);
	}
}