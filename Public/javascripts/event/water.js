	$(function(){
		$("body").css("minHeight", $(document).height());
		$("#wrap").css("minHeight", $(".introduce").height());
		$("#cate_type a").hover(function(){
			var nowCur = $(this).text().replace(/[ ]/g,"");						
			var link_to = "/event/" + nowCur + "/" + cateTime;
			$(this).attr("href", link_to);
		});

		$("#cate_time a").hover(function(){
			var nowCur = $(this).text().replace(/[ ]/g,"");
			var link_to = "/event/" + cateTag + "/" + nowCur;
			$(this).attr("href", link_to);
		});

		$("#goTop").click(function(){
			$("body,html").stop().animate({scrollTop:0},250);
		})
	});

	var id = 0,
		cateTag = $("#cate_type .active").text().replace(/[ ]/g,""),
		cateTime = $("#cate_time .active").text().replace(/[ ]/g,""),
		loadMore = $(".load");
	var waterFull = function (options) {
		var id = options.id,
			notice = options.notice,
			columnMarginRight = 21,
			cellClientWidth = 220,
			obody = $("body")[0],
			owrap = $("#"+id)[0],
			noticeHeight = $("."+notice)[0].offsetHeight,
			// 用于记录当前插入的格子数量
			nowNum = 0,
			eleLocateHigh = [noticeHeight + columnMarginRight ,0 ,0 ,0],
			postersArr = []; // 用于记录每个单独层对象

		// 获取列数
		function getColumnNum() {
			// 总共的列数
			var columnNum = Math.ceil(owrap.clientWidth / (cellClientWidth + columnMarginRight));
			return columnNum;
		}

		// 插入数据
		function insert(data) {
			var fragElement = document.createDocumentFragment(),
				len = data.length;
			if (len > 0) {
				$.each(data, function(i,item){
					var node = $('<div class="poster_grid"></div>');
					// var html = '<div class="title"><a href="/event/' + item.id + '">'+ item.title +'</a></div><div class="intro">时间：'+ item.time + '<br />地点：' + item.location +'</div><div class="pa">'+ item.join_count +'人参加<span class="sp">|</span>'+ item.interest_count +'人感兴趣</div>';
					var html = '<div class="title"><a href="/event/' + item.id + '">'+ item.title +'</a></div><div class="intro">时间：'+ item.time + '<br />地点：' + item.location +'</div><div class="pa">'+ item.interest_count +'人感兴趣</div>';
					if(item.poster !== "notexists"){
						var pic = '<div class="pic"><a href="/event/' + item.id + '"><img src="'+ item.poster +'" /></a></div>';
						html = pic + html;
					}
					node.html(html);
					if(item.poster !== "notexists"){
						$(".pic img",$(node)).load(function(){
							addNode($(node)[0]);
						});
					}else{
						addNode($(node)[0]);
					}
				})
				function addNode(node){
					fragElement.appendChild(node);
					postersArr.push(node);
					len--;
					if(len == 0){
						owrap.appendChild(fragElement);
						// 插入后再排序
						sort();
					}
				}
			}
		}

		//排序
		function sort(){
			var num = getColumnNum(), left, top, column;
			eleLocateHigh.length = num;
			//nowNum的作用是不让已经加载的数据重新计算定位排列
			for (var j = nowNum, k = postersArr.length; j < k; j++, nowNum++) {
				var minPos = minVal(eleLocateHigh);
				$.each(eleLocateHigh, function(i,v){
					if(minPos == v){
						postersArr[j].style.top = v + "px";
						postersArr[j].style.left = i * (cellClientWidth + columnMarginRight) + "px";
						eleLocateHigh[i] = postersArr[j].offsetTop + postersArr[j].offsetHeight + columnMarginRight;
						return false;
					}
				})
			}
			owrap.style.height = maxVal(eleLocateHigh)-columnMarginRight + 'px';
		}

		function minVal(arr){
			return Math.min.apply(Math, arr);
		}

		function maxVal(arr){
			return Math.max.apply(Math, arr);
		}

		return {
			insert:insert
		}

	};
	var tool = {
		on:function (element, type, func) {
			if (element.addEventListener) {
				element.addEventListener(type, func, false); //false
			} else if (element.attachEvent) {
				element.attachEvent('on' + type, func);
			} else {
				element['on' + type] = func;
			}
		},
		unbind:function (element, type, func) {
			if (element.removeEventListener) {
				element.removeEventListener(type, func, false); //false
			} else if (element.dettachEvent) {
				element.dettachEvent('on' + type, func);
			} else {
				element['on' + type] = func;
			}
		},
		getPageHeight:function () {
			return document.documentElement.scrollHeight || document.body.scrollHeight;
		},
		// 获取页面卷去的高度
		getScrollTop:function () {
			return document.documentElement.scrollTop || document.body.scrollTop;
		},
		// 获取页面可视区域宽度
		getClientHeigth:function () {
			return document.documentElement.clientHeight || document.body.clientHeight;
		},
		getData: function(){
			id = id+1;
			$.post('/event/load',{'tag':cateTag,'time':cateTime,'id':id},function(data){
				var data = $.parseJSON(data);
				if(data.status){
					myWater.insert(data.data);
					if(data.data.length<6){
						loadMore.addClass("end");
					}
				}else{
					loadMore.addClass("end");
 				}
			}).done(function(){
				loadMore.hide();
			})
		},
		timer:null
	};
	var myWater = waterFull({id:'wrap', notice: 'introduce'});
	// 初始化的数据
	tool.getData();
	tool.on(window, 'scroll', function () {
		clearTimeout(tool.timer); //清除上一次，性能优化
		tool.timer = setTimeout(function () {
			var height = tool.getPageHeight();
			var scrollTop = tool.getScrollTop();
			var clientHeight = tool.getClientHeigth();
			if(loadMore.hasClass("end")){
				loadMore.hide();
				tool.unbind(window, 'scroll');
				return;
			}
			loadMore.show();
			// 加载
			if (scrollTop + clientHeight > height - 50){
				tool.getData();
			}
		}, 500);
	});