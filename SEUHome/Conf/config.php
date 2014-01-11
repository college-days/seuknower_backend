<?php
return array(
	'URL_MODEL'       =>2,                         //URL重写模式
	'URL_CASE_INSENSITIVE' =>true,					//大小写不敏感
	'URL_ROUTER_ON'   => true, 						//开启路由

	'URL_ROUTE_RULES'=>array(
		'question/page/:id\d$' => 'Question/index',
	),

	//新的路径替换
	'TMPL_PARSE_STRING' =>array(
		'__IMAGE__' => '/Public/images', // 增加新癿JS 类库路徂替换觃则
		'__JS__' => '/Public/javascripts', // 增加新癿JS 类库路徂替换觃则
		'__CSS__' => '/Public/stylesheets', // 增加新癿JS 类库路徂替换觃则
		'__UPLOAD__' => '/Uploads', // 增加新癿上传路徂替换觃则
	),

	// 添加数据库配置信息
	'DB_TYPE' => 'mysql',      			   //数据库类型
	'DB_HOST' => 'localhost',			   //服务器地址
	'DB_NAME' => 'seu',                     //数据库名
	'DB_USER' => 'root',                    //用户名
	'DB_PWD'  => '',                 //密码
	'DB_PORT' => 3306,					   //端口
	'DB_PREFIX' => 'seu_',                    //数据库表前缀
);
?>