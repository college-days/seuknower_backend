	$(function(){
		var load = 1;
		$("#cate_type a").hover(function(){
			var tag = $(this).text().replace(/[ ]/g,"");
			var time = $("#cate_time .active").text().replace(/[ ]/g,"");						
			var link_to = "/event/" + tag + "/" + time;
			$(this).attr("href", link_to);
		});

		$("#cate_time a").hover(function(){
			var tag = $("#cate_type .active").text().replace(/[ ]/g,"");
			var time = $(this).text().replace(/[ ]/g,"");
			var link_to = "/event/" + tag + "/" + time;
			$(this).attr("href", link_to);
		});

		$("#goTop").click(function(){
			$("body,html").stop().animate({scrollTop:0},250);
		})
	});

	var id = 0;
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
				datas = data.data,
				len = datas.length;
			if (len > 0) {
				$.each(datas, function(i,item){
					// var html = '<div class="title"><a href="/event/' + item.id + '">'+ item.title +'</a></div><div class="intro">时间：'+ item.time + '<br />地点：' + item.location +'</div><div class="pa">'+ item.join_count +'人参加<span class="sp">|</span>'+ item.interest_count +'人感兴趣</div>';
					var html = '<div class="title"><a href="/event/' + item.id + '">'+ item.title +'</a></div><div class="intro">时间：'+ item.time + '<br />地点：' + item.location +'</div><div class="pa">'+ item.interest_count +'人感兴趣</div>';

					if(item.poster.length>0){
						var pic = '<div class="pic"><a href="/event/' + item.id + '"><img src="'+ item.poster +'" /></a></div>';
						html = pic + html;
					}
					var node = $('<div class="poster_grid"></div>').html(html);
					fragElement.appendChild($(node)[0]);
					$(".pic img",$(node)).load(function(){
						postersArr.push($(node)[0]);
						len--;
						if(len == 0){
							owrap.appendChild(fragElement);
							// 插入后再排序
							sort();
						}
					});
				})
			}
		}

		//排序
		function sort(){
			var num = getColumnNum(), left, top, column;
			//nowNum的作用是不让已经加载的数据重新计算定位排列
			for (var j = nowNum, k = postersArr.length; j < k; j++, nowNum++) {
				num = j < 9 ? 3 : 4;
				// 初始化top的值
				top = 0;
				// 获取当前为第几列
				column = j < num ? j : j % num;

				// 计算可以得到当前列的LEFT值
				if(num === 3){
					left = (column+1) * (cellClientWidth + columnMarginRight);
				}else if(num == 4){
					left = (column) * (cellClientWidth + columnMarginRight);
				}
				postersArr[j].style.left = left + 'px';
				if (j < num) {
					// 第一列top值为0
					postersArr[j].style.top = '0px';
				} else {
					if(j == 9){
						postersArr[j].style.left = "0";
						postersArr[j].style.top = noticeHeight + columnMarginRight + 'px';
						continue;
					}
					var m = j;
					if(j<13){
						m = m - 3;
					}else if(j == 13){
						m = m - 7;
					}else{
						m = m - 4;
					}
					top = postersArr[m].offsetTop + postersArr[m].offsetHeight + columnMarginRight;
					postersArr[j].style.top = top + 'px';
				}
			}
			owrap.style.height = top + 97 + 'px';
		}

		// resize 重新排列
		function resort() {
			// 设置nowNum=0即可重排
			nowNum = 0;
			// 重排
			sort();
		}
		return {
			insert:insert,
			resort:resort
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
			var tag = $("#cate_type .active").text().replace(/[ ]/g,"");
			var time = $("#cate_time .active").text().replace(/[ ]/g,"");
			$.post('/event/load',{'tag':tag,'time':time,'id':id},function(data){
				var data = $.parseJSON(data);
				if(data.status){
					myWater.insert(data);
				}
			})
		},
		timer:null,
		timer2:null
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
			// 加载
			if (scrollTop + clientHeight > height - 50){
				tool.getData();
			}
		}, 500);
	});
	tool.on(window, 'resize', function () {
		clearTimeout(tool.timer2);
		tool.timer2 = setTimeout(function () {
			myWater.resort();
		}, 500)
	})
