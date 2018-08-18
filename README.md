# whmcs-uniteddomains-module
UnitedDomains Registrar For WHMCS

1). Main Module Features:

1. Register Domain
2. Transfer Domain
3. Renew Domain
4. Delete Domain
5. Modify Contact Details
6. Get EPP Code
7. Get DNS Records
8. Save DNS Records
9. Get Nameservers
10. Save Nameservers
11. Register Nameservers
12. Modify Nameservers
13. Delete Nameservers
14. Get Registrar Lock
14. Save Registrar Lock
15. Domain Cron Synchronization

2). Extension function, Check domain availability

Use: Edit /includes/whoisservers.php      //Less than 6 versions

Add Line: .xx|http://yourdomain.com/query/CheckDomain.php?domain=|HTTPREQUEST-Available

Notice: .xx is the extension of the domain name you specified, be sure to replace it.
