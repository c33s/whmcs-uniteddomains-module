# whmcs-uniteddomains-module
UnitedDomains Registrar For WHMCS

<b>1. Main Module Features:</b>

1). Register Domain
2). Transfer Domain
3). Renew Domain
4). Delete Domain
5). Get Contact Details
6). Modify Contact Details
7). Get EPP Code
8). Get DNS Records
9). Save DNS Records
10). Get Nameservers
11). Save Nameservers
12). Register Nameservers
13). Modify Nameservers
14). Delete Nameservers
15). Get Registrar Lock
16). Save Registrar Lock
17). Domain Cron Synchronization

<b>2. Extension function, Check domain availability</b>

Use: Edit /includes/whoisservers.php      //Less than 6 versions

Add Line: <code>.xx|http://yourdomain.com/query/CheckDomain.php?domain=|HTTPREQUEST-Available</code>

Notice: .xx is the extension of the domain name you specified, be sure to replace it.
