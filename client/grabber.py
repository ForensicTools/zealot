import time
import datetime
import requests

# get flag status (WAIT/RUN)
# response = requests.get('http://api.zealot/<endpoint_for_flag>/')
response = "WAIT"

while response != "RUN":
    print ("Flag is set to '%s' at: %s" % (response, datetime.datetime.now()))
    time.sleep(5)
    # response = requests.get('http://api.zealot/<endpoint_for_flag>/')
    response = "RUN"

print ("Flag set to '%s' at: %s" % (response, datetime.datetime.now()))
# find all files changed in last x minutes
# grab files

