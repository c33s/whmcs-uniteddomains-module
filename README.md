# whmcs-uniteddomains-module
UnitedDomains Registrar For WHMCS

<b>1. Main Module Features:</b>

1). Register Domain</br>
2). Transfer Domain</br>
3). Renew Domain</br>
4). Delete Domain</br>
5). Get Contact Details</br>
6). Modify Contact Details</br>
7). Get EPP Code</br>
8). Get DNS Records</br>
9). Save DNS Records</br>
10). Get Nameservers</br>
11). Save Nameservers</br>
12). Register Nameservers</br>
13). Modify Nameservers</br>
14). Delete Nameservers</br>
15). Get Registrar Lock</br>
16). Save Registrar Lock</br>
17). Domain Cron Synchronization</br>

<b>2. Extension function, Check domain availability</b>

Use: Edit /includes/whoisservers.php      //Less than 6 versions

Add Line: <code>.xx|http://yourdomain.com/query/CheckDomain.php?domain=|HTTPREQUEST-Available</code>

Notice: .xx is the extension of the domain name you specified, be sure to replace it.
