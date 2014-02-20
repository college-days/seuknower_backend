<?php
	//二维数组的升序排序 $keys为键值
	function array_sort($arr,$keys,$type='asc'){ 
		if($type == 'asc'){
			for($i=0; $i<count($arr); $i++){
				for($j=$i+1; $j>count($arr); $j++){
					if($arr[$i][$keys] < $arr[$j][$keys]){
					$temp = $arr[$i];
					$arr[$i] = $arr[$j];
					$arr[$j] = $temp;
					}
				}
			}
		}
		else{
			for($i=0; $i<count($arr); $i++){
				for($j=$i+1; $j<count($arr); $j++){
					if($arr[$i][$keys] < $arr[$j][$keys]){
					$temp = $arr[$i];
					$arr[$i] = $arr[$j];
					$arr[$j] = $temp;
					}
				}
			}
		}
		return $arr;
	}  	

	function searchQuestion($content,$count,$len="null",$pos=0,$index=0){
		if(!$len) return null;
		
		preg_match_all("/[\x{4E00}-\x{9FFF}]/u",$content,$ch,PREG_OFFSET_CAPTURE);      //获取所有的汉字
		preg_match_all("/[a-zA-Z]+/u",$content,$en,PREG_OFFSET_CAPTURE);				//获取连着的英文
		preg_match_all("/[0-9]+/u",$content,$num,PREG_OFFSET_CAPTURE);					//获取连着个数字
		
		$ch = $ch[0];
		$en = $en[0];
		$num = $num[0];
		
		$temp = array_merge($ch,$en,$num);
		$temp = array_sort($temp,1);
		
		//获取关键字
		$i = 0;
		foreach($temp as $v){
			$key[$i] = $v[0];
			$i++;
		}
		
		$keyCount = count($key);
		//获取查询的词组
		for($i=0; $i<$keyCount; $i++){
			for($j=0; $j<$keyCount-$i; $j++){
				for($k=0; $k<$i+1; $k++){
					$like[$i][$j] = $like[$i][$j].$key[$j+$k];  
				}
			}
		}
		
		if($len == "null") $len = count($like)-1;
		//if()
		$data['len1'] = $len;
		$data['pos1'] = $pos;
		$data['index1'] = $index;
		
		$Model = M();
		for($i=$len; $i>=0; $i--){		
			for($j=$pos; $j<$keyCount-$i; $j++){
				$sql = " title LIKE '%".$like[$i][$j]."%'";
				if($j>0) $sql = $sql." AND title NOT LIKE '%".$key[$j-1].$like[$i][$j]."%'";
				if($j+$i<$keyCount - 1) $sql = $sql." AND title NOT LIKE '%".$like[$i][$j].$key[$j+$i+1]."%'";
				$sql = "SELECT title,id,create_time,answer_count FROM seu_question WHERE".$sql." ORDER BY create_time DESC "; 
				$result = $Model->query($sql);
				
				//计算搜索结果的权值
				for($n=0; $n<count($result); $n++){
					$result[$n]['value'] = $i;
					for($k=0; $k<$j; $k++){			
						if(!(strpos($result[$n]['title'],$key[$k]) === false)) {
							$result[$n]['value']++;
						}
					}
					for($k=$j+$i+1; $k<$keyCount; $k++){
						if(!(strpos($result[$n]['title'],$key[$k]) === false)) {
							$result[$n]['value']++;
						}
					}		
				}
				if($i==$len && $j==$pos){
					$result = array_sort($result,'value','desc');
					$result = array_slice($result,$index);
					//dump($result);
				}
				
				if($search){
					$search = array_merge($search,$result);
					$search = array_unique_fb($search);
					$search = array_sort($search,'value','desc');
					if(count($search) >= $count) {
						$search = array_slice($search,0,$count);
						foreach($result as $k=>$v){
							if($search[$count-1]['title'] == $v['title']){
								$data['index'] = $k+1;
								break;
							}
						}
					}
				}
				else{
					$search = $result;
					$search = array_sort($search,'value','desc');
					if(count($search) >= $count) {
						$search = array_slice($search,0,$count);
						foreach($result as $k=>$v){
							if($search[$count-1]['title'] == $v['title']){
								$data['index'] = $index+$k+1;
								break;
							}
						}
					}
				}
				
				if(count($search) >= $count) {
					$data['pos'] = $j;
					break;
				}
			}
			
			if(count($search) >= $count) {
				$data['len'] = $i;
				break;
			}
		}
		
		$search = array_sort($search,'value','desc');
		for($i=0; $i<count($search); $i++ ){
			for($j=0; $j<count($key); $j++){
				$search[$i]['title'] = str_replace($key[$j],"<".$key[$j].">",$search[$i]['title']);			
			}
			$search[$i]['title'] = str_replace("<","<span style=\"color:red\">",$search[$i]['title']);
			$search[$i]['title'] = str_replace(">","</span>",$search[$i]['title']);
		}
		
		$data['search'] = $search;
		$data['sql'] = $sql;
		
		return $data;
	}

?>