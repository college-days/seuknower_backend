#!/usr/bin/python 
#coding=utf-8 

import urllib
import sys
import http.cookiejar

def verify(username, password):
    cookie = http.cookiejar.CookieJar()                                        #保存cookie，为登录后访问其它页面做准备
    cjhdr  =  urllib.request.HTTPCookieProcessor(cookie)             
    opener = urllib.request.build_opener(cjhdr)


    url = "http://my.seu.edu.cn/userPasswordValidate.portal"
    postdata = urllib.parse.urlencode({'Login.Token1': str(username), 'Login.Token2': str(password), 'goto':'http://my.seu.edu.cn/loginSuccess.portal', 'gotoOnFail':'http://my.seu.edu.cn/loginFailure.portal'})
    postdata = postdata.encode('utf-8')

    res = urllib.request.urlopen(url,postdata)
    print(res.status, res.reason)

    result = res.read().decode("utf8")
    if(result == '''<script type="text/javascript">(opener || parent).handleLoginSuccessed();</script>\n'''):
        print(1)
        return 1
    else:
        print(0)
        return 0

