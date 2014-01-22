<?php
return array(
	'SHOW_PAGE_TRACE' => true,
	'URL_MODEL' => 2,                         //URL重写模式
	'URL_CASE_INSENSITIVE' => true,					//大小写不敏感
	'URL_ROUTER_ON' => true, 						//开启路由

	'URL_ROUTE_RULES'=>array(
		'question/:id\d$' => 'Question/detail',
		'question/page/:id\d$' => 'Question/index',
		'question/:type$' => 'Question/index',
		'question/:type/page/:id\d$' => 'Question/index',
		'user/:id\d$' => 'User/index',
		'user/ask/:id\d$' => 'User/ask_question',
		'user/answer/:id\d$' => 'User/answer_question',
		'user/join/:id\d$' => 'User/join_event',
		'user/interest/:id\d$' => 'User/interest_event',
		'user/profile/:id\d$' => 'User/profile',
		'user/updateprofile/' => 'User/updateprofile',
		'user/sellon/:id\d$' => 'User/sell_commodity_on',
		'user/selldone/:id\d$' => 'User/sell_commodity_done',
		'user/buy/:id\d$' => 'User/buy_commodity',
		'login' => 'Account/login',
		'logout' => 'Account/logout',
		'event/page/:id\d$' => 'Event/index',
		'event/:tag/:time$'	=> 'Event/index',
		'event/:tag/:time/page/:id\d$' => 'Event/index',	
		'market/page/:id\d$' => 'Market/index',
		'market/commodity/:id\d$' => 'Market/detail',
		'market/:category$' => 'Market/index',
		'market/:category/page/:id\d$' => 'Market/index',
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

	//自动加载类库
	'APP_AUTOLOAD_PATH'=>'@.TagLib,@.ORG',

	'SESSION_AUTO_START'		=>true,				//会话自启动
	'USER_AUTH_ON'              =>true,				//自动验证
	'USER_AUTH_TYPE'			=>1,				// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY'             =>'authId',			// 用户认证SESSION标记
	'USER_AUTH_MODEL'           =>'User',			// 默认验证数据表模型

);
?>