# Zealot
A brief exploration into naive DNS examination using metadata, and forensic examination thereafter.

## Authors
Chris Partridge: cxp2269@rit.edu

Geoff Kanteles: gdk7676@rit.edu

## Background
DNS is an instrumental technology in almost all internet communications, and through DNS we can reveal important information about a host's network activity without intruding directly on that host's connections. This may allow us to make highly accurate decisions about the security of a given host in a resource- and cost-effective manner.

The first network activity that many strains of malware make are DNS lookups to start receiving commands or second-stage payloads from their remote infrastructure. This is seen across simplistic threats as well as contemporary APTs. By evaluating metadata about those DNS requests, we may be able to discern threats without needing a per-threat signature, instead choosing to rely on the network signatures of the protected hosts (which could be established and trained easily over time to reduce false positives). When any suspicious DNS lookups are made, we will automatically invoke an automated forensic investigation of any servers running the zealot client, allowing for fast, self-contained investigation that has a higer chance of capturing artifacts (as threats may be unpacking second stage, still gaining a foothold, etc.).

## Operation
Our tool was developed in two separate modules: a server, runs a DNS server, builds profiles of users' DNS activity, and provides a repository for the secure storage of forensic evidence; and a client, which aggregates live forensic data on user systems for exfiltration to the server. The two communicate through HTTP-based APIs to evaluate states as well as push forensic information to secure repositories.

## Setup
### Server
zealot's server is designed to be lightweight enough to be run on a SBC such as a Raspberry Pi, or on a low-resource VM with 1 core/512MB RAM/at least 10GB storage. Your storage needs may vary greatly depending on retention of artifacts, alert frequency, etc. - so more is often recommended. When ready to install, clone the zealot repository to a Debian 9 server in the /root directory, then run the following commands as root:
```
cd /root/zealot/server
bash setup.sh
```
The setup script will install all dependencies, flush out any previous installations of zealot, configure the system and components for zealot use, and then reboot for sanity sake.

### Client
Once the server has been set up, to set up the client side portion of the tool, place the grabber.py script into the directory of your choice (the script will gather files for forensic analysis from the current directory and all sub-directories). To start the tool, run the script with Python 3. You will need to have the Requests Python library installed in order for this script to function correctly.

Once you have done that, configure the client to use the server you have set up as its DNS server, and the two softwares will begin communicating automatically. No further interaction is required on your part. Any data captured by the client in response to anomalous DNS traffic is automatically uploaded to /var/zealot and timestamped for later forensic review.

## Future Work
Most notably, we are considering expanding the scope for this project down the line, and moving it from existing at a purely DNS level (where it can be easily bypassed) to operating on a gateway, and inspecting traffic in a MITM fashion to make more accurate decisions. Additionally, we are looking into creating more robust DNS fingerprinting options that reduce or entirely eliminate the need for blacklists in weighting results of our modelling.

Of course, this project could benefit from threat intelligence enrichment, which Machines Never Sleep LLC currently considers its main focus, and that may be integrated as well in production-ready variants of software such as this.

## Security Warning
Please note that good practices (not running composer as root, not running the zealot server as root, not running programs in the /root directory, etc.) are not being followed. In the tradeoff of secure, performant, and "done," we picked the latter two. Ironic that a security research group should be so full of bad practices, but for limited-scope prototypes with no live deployments we find this "acceptable." This serves as a warning to not run this anywhere near production, any instances started are your sole responsibility, and you should consider this entire repository to not just be exploitable but actively malicious. This is a prototype for research purposes only.
