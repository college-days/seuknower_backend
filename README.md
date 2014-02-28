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
