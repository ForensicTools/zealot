from pathlib import Path
import time
import datetime
import requests
 
# get flag status (WAIT/RUN)
# response = requests.get('http://api.zealot/<endpoint_for_flag>/')
response = "WAIT"

while response != "RUN":
    print ("Flag is set to '%s'" % (response)) # debug print
    # log flag state
    # print ("Flag is set to '%s' at: %s" % (response, datetime.datetime.now()), file=open("zealotlog.txt", "a"))

    time.sleep(5)   # wait x seconds
    # response = requests.get('http://api.zealot/<endpoint_for_flag>/') # query flag, update value
    response = "RUN"

print ("Flag is set to '%s'" % (response)) # debug print
# log flag state
# print ("Flag set to '%s' at: %s" % (response, datetime.datetime.now()), file=open("zealotlog.txt", "a"))

# find all files changed in last x minutes
# grab files
