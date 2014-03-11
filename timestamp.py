import sys
import os
import time

def addTimeStamp(rootDir, time):
	for dir in os.listdir(rootDir):
		path = os.path.join(rootDir, dir)
		if os.path.isdir(path):
			addTimeStamp(path, time)
		else:
			print path
			html = open(path, "r").read()
			newhtml = html.replace(".js", ".js?t="+str(time))
			newhtml = newhtml.replace(".css", ".css?t="+str(time))
			file = open(path, "w")
			file.write(newhtml)
			file.close()

if __name__ == '__main__':
	rootDir = "./SEUHome/Tpl/"
	time = int(time.time());
	addTimeStamp(rootDir, time);