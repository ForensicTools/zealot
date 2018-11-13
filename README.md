# zealot
A brief exploration into naive DNS examination using metadata, and forensic examination thereafter.

## Background
DNS is an instrumental technology in almost all internet communications, and through DNS we can reveal important information about a host's network activity without intruding directly on that host's connections. This may allow us to make highly accurate decisions about the security of a given host in a resource- and cost-effective manner.

The first network activity that many strains of malware make are DNS lookups to start receiving commands or second-stage payloads from their remote infrastructure. This is seen across simplistic threats as well as contemporary APTs. By evaluating metadata about those DNS requests, we may be able to discern threats without needing a per-threat signature, instead choosing to rely on the network signatures of the protected hosts (which could be established and trained easily over time to reduce false positives). When any suspicious DNS lookups are made, we will automatically invoke an automated forensic investigation of any servers running the zealot client, allowing for fast, self-contained investigation that has a higer chance of capturing artifacts (as threats may be unpacking second stage, still gaining a foothold, etc.).

## Setup
### Server
zealot's server (serving as a DNS server, API, and artifact repository) is designed to be lightweight enough to be run on a SBC such as a Raspberry Pi, or on a low-resource VM with 1 core/512MB RAM/at least 10GB storage. Your storage needs may vary greatly depending on retention of artifacts, alert frequency, etc. - so more is often recommended. When ready to install, clone the zealot repository to a Debian 9 server, then run the following commands as root:
```
cd zealot/server
bash setup.sh
```

### Client

## Operation

## Future Work
