import sys
import os
import time

def addTimeStamp(rootDir, time, lasttimestamp):
	for dir in os.listdir(rootDir):
		path = os.path.join(rootDir, dir)
		if os.path.isdir(path):
			addTimeStamp(path, time, lasttimestamp)
		else:
			print path
			html = open(path, "r").read()
			newhtml = html
			if "?t="+str(lasttimestamp) in newhtml:
				print 'yeah!!!'
				newhtml = newhtml.replace(".js?t="+str(lasttimestamp), ".js?t="+str(time))
				newhtml = newhtml.replace(".css?t="+str(lasttimestamp), ".css?t="+str(time))
				newhtml = newhtml.replace("http://static.bshare.cn/b/button.js?t="+str(time), "http://static.bshare.cn/b/button.js")
				newhtml = newhtml.replace("http://static.bshare.cn/b/bshareC0.js?t="+str(time), "http://static.bshare.cn/b/bshareC0.js")
			else:
				newhtml = newhtml.replace(".js", ".js?t="+str(time))
				newhtml = newhtml.replace(".css", ".css?t="+str(time))
				newhtml = newhtml.replace("http://static.bshare.cn/b/button.js?t="+str(time), "http://static.bshare.cn/b/button.js")
				newhtml = newhtml.replace("http://static.bshare.cn/b/bshareC0.js?t="+str(time), "http://static.bshare.cn/b/bshareC0.js")

			file = open(path, "w")
			file.write(newhtml)
			file.close()

if __name__ == '__main__':
	rootDir = "./SEUHome/Tpl/"
	lastTimeStamp = open("./lasttimestamp", "r").readline()
	print lastTimeStamp
	print str(lastTimeStamp)
	time = int(time.time());
	file = open("./lasttimestamp", "w")
	file.write(str(time))
	file.close()
	addTimeStamp(rootDir, time, lastTimeStamp);