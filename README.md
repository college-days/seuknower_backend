## deployment

### requirements

* Apache : 2.4.x (_2.4.4 recommended_)
* MySQL : 5.6.x (_5.6.12 recommended_) 
* PHP : 5.4.x (_5.4.12 recommended_)

### project settings (_in SEUHome_)

* 要注释config.php下面的showpagetrace
* 修改config.php下面的数据库密码
* 修改commonutil.class.php下面的查询文件是否存在的绝对路径前缀

### apache settings (_in httpd.conf_)
如果要使用thinkphp的路由重写模式需要配置一下apache的httpd.conf文件，将其中

```
LoadModule rewrite_module modules/mod_rewrite.so
```

前的注释去掉，也就是要开启apache的重写模式

### php settings (_in php.ini_)

* common.php/thumb 

to use ```exif_imagetype``` function

change

```
;extension=php_mbstring.dll
;extension=php_exif.dll
```

to

```
extension=php_mbstring.dll
extension=php_exif.dll
```

## timestamp

* 根目录下有一个python文件用来为stylesheet和javascript添加时间戳，防止有修改后需要用户手动清空浏览器缓存才能加载新的效果，现在浏览器根据新的get参数自动加载带有新的时间戳的静态文件

* 无依赖，python2.7.x is ok (_python2.7.6 recommended_)

## common bugs

* 假如部署之后发现页面上多出一块空白，很可能是出现了二次编辑时在文本编辑器中增加的bom头，只要直接用URL访问运行```clearBOM.php```或者```delbom.php```即可

* 由于现在限制了apache写路径下文件的权限，所以要清除bom头首先要开启apache可以修改和写入SEUHome/Tpl，Public的权限，清除掉bom头之后再把权限去掉，保留只读权限

* 无法出现验证码也是因为bom头引起的，注意去掉所有和前端文件有关的文件的写权限限制，然后清楚bom头再加上写权限限制

## 路径依赖

* 所有上传的图片都会保存在```SEUHome/```路径的同级路径```Uploads/```中，由于部署之后每天会产生大量的新增图片，并且有将所有上传图片放入oss进行管理，并进行cdn加速的计划，所以不将此路径放入版本控制

##### Uploads路径结构

* Uploads
	* Images
		* Answer
			* image(_存放的是带有日期时间戳的通过kindeditor插件上传的图片，由于涉及原因，非回答但是kindeditor上传的也会在这里_)
		* Event
			* Poster
				* Raw(_原图_)
				* Thumb(_缩略图_)
		* Group(_现有代码中这个路径应该是废弃了_)
		* Market
			* Picture
				* Raw(_原图_)
				* Thumb(_缩略图_)
		* Topic(_现有代码中这个路径应该也是废弃了_)
		* User
			* Icon(_缩略图在Raw路径的同级路径上_)
				* Raw(_原图_)

## doge game

* 依赖于```Public/images/doge/```和```Public/stylesheets/img/```两个路径下的图片

## sns share

* 直接嵌入的bshare的js，所以离线情况下肯定没法出现分享按钮的