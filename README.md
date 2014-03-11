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