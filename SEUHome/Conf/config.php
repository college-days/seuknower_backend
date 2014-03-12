<?php
return array(
	'SHOW_PAGE_TRACE' => true,
	'URL_MODEL' => 2,                         //URL重写模式
	'URL_CASE_INSENSITIVE' => true,					//大小写不敏感
	'URL_ROUTER_ON' => true, 						//开启路由

	//正则的$一定要加上，不然貌似会出问题，第一个路由的$就一定要加上
	'URL_ROUTE_RULES'=>array(
		'manage$' => 'Manage/event',
		'question/change_content$' => 'Question/changeContent',
		'question/more_search$' => 'Question/moreSearch',
		'question/domin/:domin$' => 'Question/index',
		'question/domin/:domin/page/:id\d$' => 'Question/index', 
		'question/:id\d$' => 'Question/detail',
		'question/:id\d/answer/:aid\d$' => 'Question/detail',
		'question/new$' => 'Question/addQuestion',
		'question/page/:id\d$' => 'Question/index',
		'question/:type$' => 'Question/index',
		'question/:type/page/:id\d$' => 'Question/index',
		'answer/add_agree' => 'Answer/addAgree',
		'answer/cancel_agree' => 'Answer/cancelAgree',
		'answer/add_object'	=> 'Answer/addObject',
		'answer/add_reply$' => 'Answer/addReply',
		'answer/change_content' => 'Answer/changeContent',
		'answer/cancel_object' => 'Answer/cancelObject',
		'answer/add_answer' => 'Answer/addAnswer',
		'user/:id\d$' => 'User/index',
		'user/ask/:id\d$' => 'User/ask_question',
		'user/ask/:id\d/type/:type$' => 'User/ask_question',
		'user/answer/:id\d$' => 'User/answer_question',
		'user/answer/:id\d/type/:type$' => 'User/answer_question',
		'user/join/:id\d$' => 'User/join_event',
		'user/join/:id\d/type/:type$' => 'User/join_event',
		'user/interest/:id\d$' => 'User/interest_event',
		'user/interest/:id\d/type/:type$' => 'User/interest_event',
		'user/upload_icon$' => 'User/uploadIcon',
		'user/thumb_icon$' => 'User/thumbIcon',
		'user/profile/:id\d$' => 'User/profile',
		'user/update_profile$' => 'User/updateProfile',
		'user/sellon/:id\d$' => 'User/sell_commodity_on',
		'user/sellon/:id\d/type/:type$' => 'User/sell_commodity_on',
		'user/selldone/:id\d$' => 'User/sell_commodity_done',
		'user/selldone/:id\d/type/:type$' => 'User/sell_commodity_done',
		'user/buy/:id\d$' => 'User/buy_commodity',
		'login' => 'Account/login',
		'logout' => 'Account/logout',
		'register' => 'Account/register',
		'account/active_password$' => 'Account/activePassword',
		'account/change_password/:id\d/:code$' => 'Account/changePassword',
		'account/save_password$' => 'Account/savePassword',
		'account/re_send$' => 'Account/reSendActiveEmail',
		'account/start_change$' => 'Account/startChangePassword',
		'account/change_session$' => 'Account/changePasswordSession',
		'account/start_active$' => 'Account/startActiveUser',
		'account/check_login$' => 'Account/checkLogin',
		'account/load_stuinfo$' => 'Account/loadStuInfo',
		'account/check_verify$' => 'Account/checkVerify',
		'account/check_message$' => 'Account/checkMessage',
		'account/check_register$' => 'Account/checkRegister',
		'account/active_user/:id\d/:code$' => 'Account/activeUser',
		'event/page/:id\d$' => 'Event/index',
		'event/:tag/:time$'	=> 'Event/index',
		'event/:tag/:time/page/:id\d$' => 'Event/index',
		'event/:id\d$' => 'Event/detail',
		'event/new$' => 'Event/newEvent',
		'event/upload_poster$' => 'Event/uploadPoster',
		'event/thumb_poster$' => 'Event/thumbPoster',
		'event/add_event$' => 'Event/addEvent',
		'event/add_join' => 'Event/addJoin',
		'event/cancel_join' => 'Event/cancelJoin',
		'event/add_interest' => 'Event/addInterest',
		'event/cancel_interest' => 'Event/cancelInterest',
		'event/add_comment' => 'Event/addComment',
		'market/modify_commodity/:id\d$' => 'Market/modifyCommodity',
		'market/save_commodity$' => 'Market/saveCommodity',
		'market/delete_commodity$' => 'Market/deleteCommodity',
		'market/getsamecate$' => 'Market/getsamecate',
		'market/page/:id\d$' => 'Market/index',
		'market/commodity/:id\d$' => 'Market/detail',
		'market/new$' => 'Market/newCommodity',
		'market/upload_picture$' => 'Market/uploadPicture',
		'market/thumb_picture$' => 'Market/thumbPicture',
		'market/add_commodity$' => 'Market/addCommodity',
		'market/more_picture/:id\d$' => 'Market/morePicture',
		'market/uploadify$' => 'Market/uploadify',
		'market/save_picture$' => 'Market/savePicture',
		'market/add_comment' => 'Market/addComment',
		'market/add_like' => 'Market/addLike',
		'market/cancel_like' => 'Market/cancelLike',
		'market/:category$' => 'Market/index',
		'market/:category/page/:id\d$' => 'Market/index',
	),

	'TMPL_EXCEPTION_FILE' => '404.html',

	//新的路径替换
	'TMPL_PARSE_STRING' =>array(
		'__EDITOR__' => '/Public/kindeditor',
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

	'SESSION_AUTO_START' => true,				//会话自启动
	'USER_AUTH_ON' => true,				//自动验证
	'USER_AUTH_TYPE' => 1,				// 默认认证类型 1 登录认证 2 实时认证
	'USER_AUTH_KEY' => 'authId',			// 用户认证SESSION标记
	'USER_AUTH_MODEL' => 'User',			// 默认验证数据表模型

	//邮件发送配置
    'THINK_EMAIL' => array(
	    'SMTP_HOST'   => 'smtp.126.com', 				//SMTP服务器
	    'SMTP_PORT'   => '25', 						//SMTP服务器端口
	    'SMTP_USER'   => 'seutongchuang@126.com', 		//SMTP服务器用户名
	    'SMTP_PASS'   => 'tongchuang', 					//SMTP服务器密码
	    'FROM_EMAIL'  => 'seutongchuang@126.com', 		//发件人EMAIL
	    'FROM_NAME'   => 'SEUKonwer', 					//发件人名称
	    'REPLY_EMAIL' => '', 							//回复EMAIL（留空则为发件人EMAIL）
	    'REPLY_NAME'  => '', 							//回复名称（留空则为发件人名称）
	),
);
?>