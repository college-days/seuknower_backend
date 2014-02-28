$(function(){
	var wait = document.getElementById('wait');
	var interval = setInterval(function(){
		var time = --wait.innerHTML;
		if(time <= 0) {
			window.location.href = '/';
			clearInterval(interval);
		};
	}, 1000);
});