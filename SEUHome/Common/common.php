<?php
	/**
	 * 图片裁剪函数，支持指定定点裁剪和方位裁剪两种裁剪模式
	 * @param <string>  $src_file       原图片路径
	 * @param <int>     $new_width      裁剪后图片宽度（当宽度超过原图片宽度时，去原图片宽度）
	 * @param <int>     $new_height     裁剪后图片高度（当宽度超过原图片宽度时，去原图片高度）
	 * @param <string>  $dst_file		裁剪后图片路径
	 * @param <int>     $start_x        起始位置X （当选定方位模式裁剪时，此参数不起作用）
	 * @param <int>     $start_y        起始位置Y（当选定方位模式裁剪时，此参数不起作用）
	 * @return <string>                 裁剪图片存储路径
	 */
	function thumb($src_file, $dst_file, $new_width, $new_height, $start_x = 0, $start_y = 0) {
	    $pathinfo = pathinfo($src_file);
	    //$dst_file = $pathinfo['dirname'] . '/' . $pathinfo['filename'] .'_'. $new_width . 'x' . $new_height . '.' . $pathinfo['extension'];
	    if (!file_exists($dst_file)) {
	        if ($new_width < 1 || $new_height < 1) {
	            echo "params width or height error !";
	            exit();
	        }
	        if (!file_exists($src_file)) {
	            echo $src_file . " is not exists !";
	            exit();
	        }
	        // 图像类型
	        $img_type = exif_imagetype($src_file);
	        $support_type = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
	        if (!in_array($img_type, $support_type, true)) {
	            echo "只支持jpg、png、gif格式图片裁剪";
	            exit();
	        }
	        /* 载入图像 */
	        switch ($img_type) {
	            case IMAGETYPE_JPEG :
	                $src_img = imagecreatefromjpeg($src_file);
	                break;
	            case IMAGETYPE_PNG :
	                $src_img = imagecreatefrompng($src_file);
	                break;
	            case IMAGETYPE_GIF :
	                $src_img = imagecreatefromgif($src_file);
	                break;
	            default:
	            echo "载入图像错误!";
	            exit();
	        }
	        /* 获取源图片的宽度和高度 */
	        $src_width = imagesx($src_img);
	        $src_height = imagesy($src_img);
	        /* 计算剪切图片的宽度和高度 */
	        $mid_width = ($src_width < $new_width) ? $src_width : $new_width;
	        $mid_height = ($src_height < $new_height) ? $src_height : $new_height;
	        /* 初始化源图片剪切裁剪的起始位置坐标 */
	       
	        // 为剪切图像创建背景画板
	        $mid_img = imagecreatetruecolor($mid_width, $mid_height);
	        //拷贝剪切的图像数据到画板，生成剪切图像
	        imagecopy($mid_img, $src_img, 0, 0, $start_x, $start_y, $mid_width, $mid_height);
	        // 为裁剪图像创建背景画板
	        $new_img = imagecreatetruecolor($new_width, $new_height);
	        //拷贝剪切图像到背景画板，并按比例裁剪
	        imagecopyresampled($new_img, $mid_img, 0, 0, 0, 0, $new_width, $new_height, $mid_width, $mid_height);

	        /* 按格式保存为图片 */
	        switch ($img_type) {
	            case IMAGETYPE_JPEG :
	                imagejpeg($new_img, $dst_file, 100);
	                break;
	            case IMAGETYPE_PNG :
	                imagepng($new_img, $dst_file, 9);
	                break;
	            case IMAGETYPE_GIF :
	                imagegif($new_img, $dst_file, 100);
	                break;
	            default:
	                break;
	        }
	    }
	    return ltrim($dst_file, '.');
	} 

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