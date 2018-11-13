#import requests

# check flag status (WAIT/RUN)
# r = requests.get('http://api.zealot/<endpoint_for_flag>/')

if r == "RUN":
    print "Flag set to: RUN"
    # find all files changed in last x minutes
    # grab files
    
elif r == "WAIT":
    print "Flag set to: WAIT"
    # check back later
else:
    print "Flag not found!"
