<?php
	/**
	* 生成指定长度的随机数函数，用于生成邮箱验证码
	* @param int $length    随机数函数
	* @return string 
	*/
	function createKey($length){
		$numchar = array('0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
		for ($i=0; $i<$length; $i++){
			$key = $key.$numchar[rand(0,61)];
		}
		return $key;
	}

	/**
	* 根据学生一卡通号获得学生其他信息
	* @param string $studentId    学生的一卡通号
	* @return string arrary 
	*		  'dept'			  院系
	*		  'major'			  专业
	*		  'stuNum'            学号
	*		  'stuId'			  一卡通号
	*		  'name'			  姓名	
	*/
	function getNameById($studentId){
		import('Common.simple_html_dom',APP_PATH,'.php');
		if(substr($studentId,0,3) == "213"){
			$queryStudentId = $studentId;
			$date = date('Y');
			$date = substr($date,2);
			$queryAcademicYear = $date-1;
			$queryAcademicYear = $queryAcademicYear.'-'.$date.'-'.'1';
			$url = "http://xk.urp.seu.edu.cn/jw_service/service/stuCurriculum.action?queryStudentId=$queryStudentId&queryAcademicYear=$queryAcademicYear";
			$html = file_get_html($url);
			$i = 0;
			foreach($html->find("table td[align=left]") as $element){
				$str = explode(":",$element->plaintext);
				$temp[$i] = trim($str[1]);
				$i++;
			}
			$info['dept'] = explode(']',$temp[0])[1];
			$info['major'] = explode(']',$temp[1])[1];
			$info['stuNum'] = $temp[2];
			$info['stuId'] = $temp[3];
			$info['name'] = $temp[4];
		}
		else{
			$studentNum = substr($studentId,3);
			$url = "http://202.119.4.150/nstudent/ggxx/xsggxxinfo.aspx?xh=".$studentNum;
			$html = file_get_html($url);
			//$info['url'] = $url;
			//$info['html'] = $html;
			$info['stuId'] = $studentId;
			$info['stuNum'] = $studentNum ;
			$info['name'] = $html->find("span[id=lblxm]", 0)->plaintext;
			$info['dept'] = $html->find("span[id=lblyx]", 0)->plaintext;
			$info['major'] = $html->find("span[id=lblzymc]", 0)->plaintext;
		}
		return $info;
	}

	function getPaocaoMessage(){
		import('Common.simple_html_dom',APP_PATH,'.php');
		$url = "http://112.124.68.27/tyxmessage";
		$html = file_get_html($url);
		return $html;
	}

	function verifyFromMySeu($username, $password){
		import('Common.php_python',APP_PATH,'.php');

		//"ppython"是框架"php_python.php"提供的函数，用来调用Python端服务
		//调用Python的testModule模块的add函数，并传递2个参数。
		$ret = ppython("verifyfrommyseu::verify", $username, $password);
		return $ret;
	}

	/**
	* 系统邮件发送函数
	* @param string $to    接收邮件者邮箱
	* @param string $name  接收邮件者名称
	* @param string $subject 邮件主题 
	* @param string $body    邮件内容
	* @param string $attachment 附件列表
	* @return boolean 
	*/
	function think_send_mail($to, $name, $subject = '', $body = '', $attachment = null){
		$config = C('THINK_EMAIL');
		vendor('PHPMailer.class#phpmailer'); //从PHPMailer目录导class.phpmailer.php类文件
		$mail             = new PHPMailer(); //PHPMailer对象
		$mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
		$mail->IsSMTP();  // 设定使用SMTP服务
		$mail->IsHTML(true);
		$mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能
                                               // 1 = errors and messages
                                               // 2 = messages only
		$mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
		if($config['SMTP_PORT'] != 25){
			//$mail->SMTPSecure = 'ssl'; 
		}
		//$mail->SMTPSecure = 'ssl';                 // 使用安全协议
		$mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
		$mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
		$mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
		$mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
		$mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
		$replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
		$replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
		$mail->AddReplyTo($replyEmail, $replyName);
		$mail->Subject    = $subject;
		// $mail->IsHTML(true);
		// $mail->Body ="<a href='www.google.com' target='_blank'>google</a>";
		$mail->MsgHTML($body);
		$mail->AddAddress($to, $name);
		if(is_array($attachment)){ // 添加附件
			foreach ($attachment as $file){
				is_file($file) && $mail->AddAttachment($file);
			}
		}
		return $mail->Send() ? true : $mail->ErrorInfo;
	}

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

	//二维数组去掉重复值
	function array_unique_fb($array2D)
	{
		$keys = array_keys($array2D[0]);
		foreach ($array2D as $v)
		{
			$v = join(",",$v);  //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
			$arr[] = $v;
		}
	
		$arr = array_unique($arr);    //去掉重复的字符串,也就是重复的一维数组
		foreach ($arr as $k => $v)
		{
			$temp = explode(",",$v); //再将拆开的数组重新组装
			for($i=0; $i<count($temp); $i++){
				$result[$k][$keys[$i]] = $temp[$i];
			} 
		}
		return $result;
	}

	/*function searchQuestion($content,$count,$len="null",$pos=0,$index=0){
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
	}*/

	//page为0时 获取所有满足条件的总数
	function search($table,$content,$count,$page){
	
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
		
		$len = count($like)-1;

		
		$Model = M();
		for($i=$len; $i>=0; $i--){		
			for($j=0; $j<$keyCount-$i; $j++){
				$sql = " title LIKE '%".$like[$i][$j]."%'";
				
				if($j>0) $sql = $sql." AND title NOT LIKE '%".$key[$j-1].$like[$i][$j]."%'";
				if($j+$i<$keyCount - 1) $sql = $sql." AND title NOT LIKE '%".$like[$i][$j].$key[$j+$i+1]."%'";
				if($table == "seu_question"){
					$sql = "SELECT title,id,create_time,answer_count FROM seu_question WHERE".$sql." ORDER BY create_time DESC "; 
				}
				else if($table == "seu_event"){
					$sql = "SELECT * FROM seu_event WHERE ".$sql." ORDER BY create_time DESC "; 
				}
				else if($table == "seu_commodity"){
					$sql = "SELECT * FROM seu_commodity WHERE onsale = 1 and ".$sql." ORDER BY create_time DESC "; 
				}
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
				
				if($search){
					$search = array_merge($search,$result);
					$search = array_unique_fb($search);
					$search = array_sort($search,'value','desc');
				}
				else{
					$search = $result;
					$search = array_sort($search,'value','desc');
				}
				//dump($search);
				if($page > 0 && count($search) > $count*$page) break;
			}
			if($page > 0 && count($search) > $count*$page) break;
		}
		if($table == "seu_question"){
			for($i=0; $i<count($search); $i++ ){
				for($j=0; $j<count($key); $j++){
					$search[$i]['title'] = str_replace($key[$j],"!_!".$key[$j]."^_^",$search[$i]['title']);			
				}
				$search[$i]['title'] = str_replace("!_!","<span style=\"color:red\">",$search[$i]['title']);
				$search[$i]['title'] = str_replace("^_^","</span>",$search[$i]['title']);
			}
		}
		if($page == 0){
			return $search;
		}
		else{
			return array_slice($search,$count*($page-1),$count);
		}
	}
?>