<?php
//API Documents: https://ote.domainreselling.de/v2/download/API-Manual.pdf
//PHP_API_Example: https://ote.domainreselling.de/v2/download/PHP_API_Example.zip

/* ***********************************************************************
 * Customization Development Services by QuYu.Net                        *
 * Copyright (c) ShenZhen QuYu Tech CO.,LTD, All Rights Reserved         *
 * (2013-09-23, 12:16:25)                                                *
 *                                                                       *
 *                                                                       *
 *  CREATED BY QUYU,INC.           ->       http://www.quyu.net          *
 *  CONTACT                        ->       support@quyu.net             *
 *                                                                       *
 *                                                                       *
 *                                                                       *
 *                                                                       *
 * This software is furnished under a license and may be used and copied *
 * only  in  accordance  with  the  terms  of such  license and with the *
 * inclusion of the above copyright notice.  This software  or any other *
 * copies thereof may not be provided or otherwise made available to any *
 * other person.  No title to and  ownership of the  software is  hereby *
 * transferred.                                                          *
 *                                                                       *
 *                                                                       *
 * ******************************************************************** */
 
function uniteddomains_getConfigArray() {
	$configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"UnitedDomains (UD-Reselling)"),
//   "Description" => array("Type" => "System", "Value"=>"Not Got a HEXONET Account? Get one here: <a href='https://www.hexonet.net/sign-up' target='_blank'>www.hexonet.net/sign-up</a>"),
	 "Username" => array( "Type" => "text", "Size" => "20", "Description" => "Enter your ISPAPI Login ID", ),
	 "Password" => array( "Type" => "password", "Size" => "20", "Description" => "Enter your ISPAPI Password ", ),
	 "UseSSL" => array( "Type" => "yesno", "Description" => "Use HTTPS for API Connections" ),
	 "TestMode" => array( "Type" => "yesno", "Description" => "Connect to OT&amp;E (Test Environment)" ),
	 //"ProxyServer" => array( "Type" => "text", "Description" => "Optional (HTTP(S) Proxy Server)" ),
//	 "SyncNextDueDate" => array( "Type" => "yesno", "Description" => "Deprecated (ispapisync.php should not be used anymore)" ),
	 //"ConvertIDNs" => array( "Type" => "dropdown", "Options" => "API,PHP", "Default" => "API", "Description" => "Use API or PHP function (idn_to_ascii)" ),
	);

	/*
	if ( !function_exists('idn_to_ascii') ) {
		$configarray["ConvertIDNs"] = array( "Type" => "dropdown", "Options" => "API", "Default" => "API", "Description" => "Use API (PHP function idn_to_ascii not available)" );
	}
	*/
	return $configarray;
}

function uniteddomains_GetRegistrarLock($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "StatusDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] == 200 ) {
		if ( isset($response["PROPERTY"]["TRANSFERLOCK"]) ) {
			if ( $response["PROPERTY"]["TRANSFERLOCK"][0] )
				return "locked";
			return "unlocked";
		}
		return "";
	}
	else {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}

function uniteddomains_SaveRegistrarLock($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "ModifyDomain",
		"DOMAIN" => $domain,
		"TRANSFERLOCK" => ($params["lockenabled"] == "locked")? "1" : "0"
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}




function uniteddomains_GetEPPCode($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";

	if ( $params["tld"] == "de" ) {
		$command = array(
			"COMMAND" => "DENIC_CreateAuthInfo1",
			"DOMAIN" => $domain
		);
		$response = uniteddomains_call($command, uniteddomains_config($params));
	}

	$command = array(
		"COMMAND" => "StatusDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] == 200 ) {
		if ( strlen($response["PROPERTY"]["AUTH"][0]) ) {
			$values["eppcode"] = htmlspecialchars($response["PROPERTY"]["AUTH"][0]);
		}
		else {
			$values["error"] = "No AuthInfo code assigned to this domain!";
		}
	}
	else {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}


function uniteddomains_GetNameservers($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "StatusDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] == 200 ) {
		$values["ns1"] = htmlspecialchars($response["PROPERTY"]["NAMESERVER"][0]);
		$values["ns2"] = htmlspecialchars($response["PROPERTY"]["NAMESERVER"][1]);
		$values["ns3"] = htmlspecialchars($response["PROPERTY"]["NAMESERVER"][2]);
		$values["ns4"] = htmlspecialchars($response["PROPERTY"]["NAMESERVER"][3]);
		$values["ns5"] = htmlspecialchars($response["PROPERTY"]["NAMESERVER"][4]);
	}
	else {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}


function uniteddomains_SaveNameservers($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	//if ($defaultNs = true){
	//$command = array(
	//	"COMMAND" => "ModifyDomain",
	//	"DOMAIN" => $domain,
	//	"NAMESERVER0" => "ns1.dodns.net",
	//	"NAMESERVER1" => "ns2.dodns.net",
	//	"NAMESERVER2" => "ns3.dodns.net",
	//);
	//}
	//else {
	$command = array(
		"COMMAND" => "ModifyDomain",
		"DOMAIN" => $domain,
		"NAMESERVER0" => $params["ns1"],
		"NAMESERVER1" => $params["ns2"],
		"NAMESERVER2" => $params["ns3"],
		"NAMESERVER3" => $params["ns4"],
		"NAMESERVER4" => $params["ns5"],
	);
	//}
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}




function uniteddomains_GetDNS($params) {
	$dnszone = $params["sld"].".".$params["tld"].".";
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "QueryDNSZoneRRList",
		"DNSZONE" => $dnszone,
		//"SHORT" => 1,
		//"EXTENDED" => 1
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
//print_r($response);
	$hostrecords = array();

	if ( $response["CODE"] == 200 ) {
		$i = 0;
		foreach ( $response["PROPERTY"]["RR"] as $rr ) {
			$fields = explode(" ", $rr);
			$domain = array_shift($fields);
			$domain = str_replace('.'.$dnszone, '', $domain);
			$domain = str_replace($dnszone, '', $domain);
			$domain = str_replace('@', '', $domain);
			$ttl = array_shift($fields);
			$class = array_shift($fields);
			$rrtype = array_shift($fields);

			if ( $rrtype == "A" ) {
				$hostrecords[$i] = array( "hostname" => $domain, "type" => $rrtype, "address" => $fields[0], );

				if ( preg_match('/^mxe-host-for-ip-(\d+)-(\d+)-(\d+)-(\d+)$/i', $domain, $m) ) {
					unset($hostrecords[$i]);
					$i--;
				}
				$i++;
			}

			if ( $rrtype == "AAAA" ) {
				$hostrecords[$i] = array( "hostname" => $domain, "type" => "A", "address" => $fields[0], );
				$i++;
			}

			if ( $rrtype == "MX" ) {
				/*
				if ( preg_match('/^mxe-host-for-ip-(\d+)-(\d+)-(\d+)-(\d+)($|\.)/i', $fields[1], $m) ) {
					$hostrecords[$i] = array( "hostname" => $domain, "type" => "MXE", "address" => $m[1].".".$m[2].".".$m[3].".".$m[4], );
				}
				else {
					$hostrecords[$i] = array( "hostname" => $domain, "type" => $rrtype, "address" => $fields[1], "priority" => $fields[0] );
				}
				*/
				$hostrecords[$i] = array( "hostname" => $domain, "type" => $rrtype, "address" => $fields[1], "priority" => $fields[0] );
				$i++;
			}

			if ( $rrtype == "TXT" ) {
				$hostrecords[$i] = array( "hostname" => $domain, "type" => $rrtype, "address" => implode(" ", $fields), );
				$i++;
			}

			if ( $rrtype == "CNAME" ) {
				$hostrecords[$i] = array( "hostname" => $domain, "type" => $rrtype, "address" => $fields[0], );
				$i++;
			}

			if ( $rrtype == "X-HTTP" ) {
				if ( preg_match('/^\//', $fields[0]) ) {
					$domain .= array_shift($fields);
				}
				$url_type = array_shift($fields);
				if ( $url_type == "REDIRECT" ) $url_type = "URL";

				$hostrecords[$i] = array( "hostname" => $domain, "type" => $url_type, "address" => implode(" ",$fields), );
				$i++;
			}
		}
	}
	else {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $hostrecords;
}


function uniteddomains_SaveDNS($params) {
	//print_r($params);
	$dnszone = $params["sld"].".".$params["tld"].".";
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "UpdateDNSZone",
		"DNSZONE" => $dnszone,
		//"INCSERIAL" => 1,
		//"EXTENDED" => 1,
		//"DELRR" => array("% A", "% AAAA", "% CNAME", "% TXT", "% MX", "% X-HTTP", "% X-SMTP"),
		//"ADDRR" => array(),
	);

	$mxe_hosts = array();
	$index = 0;
	foreach ($params["dnsrecords"] as $key => $values) {
		$hostname = $values["hostname"].'.'.$dnszone;
		$type = strtoupper($values["type"]);
		$address = $values["address"];
		$priority = $values["priority"];

		if ( strlen($hostname) && strlen($address) ) {
			if ( $type == "A" ) {
				if ( preg_match('/:/', $address ) ) {
					$type = "AAAA";
				}
				$command["RR".$index++] = "$hostname $type $address";
			}
			if ( $type == "CNAME" ) {
				$command["RR".$index++] = "$hostname $type $address";
			}
			if ( $type == "TXT" ) {
				$command["RR".$index++] = "$hostname $type $address";
			}
			if ( $type == "MXE" ) {
				$mxpref = 100;
				if ( preg_match('/^([0-9]+) (.*)$/', $address, $m ) ) {
					$mxpref = $m[1];
					$address = $m[2];
				}
				if ( preg_match('/^([0-9]+)$/', $priority) ) {
					$mxpref = $priority;
				}

				if ( preg_match('/^(\d+)\.(\d+)\.(\d+)\.(\d+)$/', $address, $m) ) {
					$mxe_host = "mxe-host-for-ip-$m[1]-$m[2]-$m[3]-$m[4]";
					$ip = $m[1].".".$m[2].".".$m[3].".".$m[4];
					$mxe_hosts[$ip] = $mxe_host;
					$command["RR".$index++] = "$hostname MX $mxpref $mxe_host";
				}
				else {
					$address = "$mxpref $address";
					$type = "MX";
				}
			}
			if ( $type == "MX" ) {
				$mxpref = 100;
				if ( preg_match('/^([0-9]+) (.*)$/', $address, $m ) ) {
					$mxpref = $m[1];
					$address = $m[2];
				}
				if ( preg_match('/^([0-9]+)$/', $priority) ) {
					$mxpref = $priority;
				}

				$command["RR".$index++] = "$hostname $type $mxpref $address";
			}
			if ( $type == "FRAME" ) {
				$redirect = "FRAME";
				if ( preg_match('/^([^\/]+)(.*)$/', $hostname, $m) ) {
					$hostname = $m[1];
					$redirect = $m[2]." ".$redirect;
				}
				$command["RR".$index++] = "$hostname X-HTTP $redirect $address";
			}
			if ( $type == "URL" ) {
				$redirect = "REDIRECT";
				if ( preg_match('/^([^\/]+)(.*)$/', $hostname, $m) ) {
					$hostname = $m[1];
					$redirect = $m[2]." ".$redirect;
				}
				$command["RR".$index++] = "$hostname X-HTTP $redirect $address";
			}
		}
	}
	foreach ( $mxe_hosts as $address => $hostname ) {
		$command["RR".$index++] = "$hostname A $address";
	}

	$response = uniteddomains_call($command, uniteddomains_config($params));
//print_r($command);print_r($response);exit;
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}



function uniteddomains_GetEmailForwarding($params) {
	$dnszone = $params["sld"].".".$params["tld"].".";
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "QueryDNSZoneRRList",
		"DNSZONE" => $dnszone,
		"SHORT" => 1,
		"EXTENDED" => 1
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));

	$result = array();

	if ( $response["CODE"] == 200 ) {
		foreach ( $response["PROPERTY"]["RR"] as $rr ) {
			$fields = explode(" ", $rr);
			$domain = array_shift($fields);
			$ttl = array_shift($fields);
			$class = array_shift($fields);
			$rrtype = array_shift($fields);

			if ( ($rrtype == "X-SMTP") && ($fields[1] == "MAILFORWARD") ) {
				if ( preg_match('/^(.*)\@$/', $fields[0], $m) ) {
					$address = $m[1];
					if ( !strlen($address) ) {
						$address = "*";
					}
				}
				$result[] = array("prefix" => $address, "forwardto" => $fields[2]);
			}
		}
	}
	else {
		$values["error"] = $response["DESCRIPTION"];
	}

	return $result;
}

function uniteddomains_SaveEmailForwarding($params) {

	//Bug fix - Issue WHMCS
	//###########
	if( is_array($params["prefix"][0]) )
		$params["prefix"][0] = $params["prefix"][0][0];
	if( is_array($params["forwardto"][0]) )
		$params["forwardto"][0] = $params["forwardto"][0][0];
	//###########

	$username = $params["Username"];
	$password = $params["Password"];
	$testmode = $params["TestMode"];
	$tld = $params["tld"];
	$sld = $params["sld"];
	foreach ($params["prefix"] as $key=>$value) {
		$forwardarray[$key]["prefix"] =  $params["prefix"][$key];
		$forwardarray[$key]["forwardto"] =  $params["forwardto"][$key];
	}
	# Put your code to save email forwarders here

	$dnszone = $params["sld"].".".$params["tld"].".";
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "UpdateDNSZone",
		"DNSZONE" => $dnszone,
		"INCSERIAL" => 1,
		"EXTENDED" => 1,
		"DELRR" => array("@ X-SMTP"),
		"ADDRR" => array(),
	);

	foreach ($params["prefix"] as $key=>$value) {
		$prefix = $params["prefix"][$key];
		$target = $params["forwardto"][$key];
		if ( strlen($prefix) && strlen($target) ) {
			$redirect = "MAILFORWARD";
			if ( $prefix == "*" ) {
				$prefix = "";
			}
			$redirect = $prefix."@ ".$redirect;
			$command["ADDRR"][] = "@ X-SMTP $redirect $target";
		}
	}

	$response = uniteddomains_call($command, uniteddomains_config($params));

	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}

function uniteddomains_GetContactDetails($params) {
	$domain = $params["sld"].".".$params["tld"];
	$values = array();
	$command = array(
		"COMMAND" => "StatusDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	//print_r($response);//exit;

	if ( $response["CODE"] == 200 ) {
		$values["Registrant"] = uniteddomains_get_contact_info($response["PROPERTY"]["OWNERCONTACT"][0], $params);
		//$values["Registrant"]["First Name"] .= $response["PROPERTY"]["OWNERCONTACT"][0];

		$values["Admin"] = uniteddomains_get_contact_info($response["PROPERTY"]["ADMINCONTACT"][0], $params);
		//$values["Admin"]["First Name"] .= $response["PROPERTY"]["ADMINCONTACT"][0];

		$values["Technical"] = uniteddomains_get_contact_info($response["PROPERTY"]["TECHCONTACT"][0], $params);
		//$values["Technical"]["First Name"] .= $response["PROPERTY"]["TECHCONTACT"][0];

		$values["Billing"] = uniteddomains_get_contact_info($response["PROPERTY"]["BILLINGCONTACT"][0], $params);
		//$values["Billing"]["First Name"] .= $response["PROPERTY"]["BILLINGCONTACT"][0];
		if ( preg_match('/[.]ca|at|it$/i', $domain) ) {
			unset($values["Registrant"]["First Name"]);
			unset($values["Registrant"]["Last Name"]);
			unset($values["Registrant"]["Company Name"]);
		}
	}
	return $values;
}

function uniteddomains_SaveContactDetails($params) {
	$config = array();
    $origparams = $params;

    if ( isset($params["original"]) ) {
        $params = $params["original"];
    }

	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";

	$map = array(
		"OWNERCONTACT0" => "Registrant",
		"ADMINCONTACT0" => "Admin",
		"TECHCONTACT0" => "Technical",
		"BILLINGCONTACT0" => "Billing",
	);

	foreach ( $map as $ctype => $ptype ) {
		if ( isset($params["contactdetails"][$ptype]) ) {
			$p = $params["contactdetails"][$ptype];
			$command[$ctype] = array(
				"FIRSTNAME" => $p["First Name"],
				"LASTNAME" => $p["Last Name"],
				"ORGANIZATION" => $p["Company Name"],
				"STREET" => $p["Address"],
				"CITY" => $p["City"],
				"STATE" => $p["State"],
				"ZIP" => $p["Postcode"],
				"COUNTRY" => $p["Country"],
				"PHONE" => $p["Phone"],
				"FAX" => $p["Fax"],
				"EMAIL" => $p["Email"],
			);
			if ( strlen($p["Address 2"]) ) {
				$command[$ctype]["STREET"] .= " , ".$p["Address 2"];
			}
		}
	}

	//print_r($command);exit;

	$contact_info = $command;

	foreach($contact_info as $key=>$val){
		$contact_cmd = $val;
		$contact_cmd["COMMAND"] = 'AddContact';
		$contact_cmd["NEW"] = '1';
		$contact_resp = uniteddomains_call($contact_cmd, uniteddomains_config($origparams));
		if ( !($contact_resp["CODE"] == 200) ) {
			$values["error"] = $key.":".$contact_resp["DESCRIPTION"];
			return $values;
		}else{
			$contact_id[$key] = $contact_resp["PROPERTY"]["CONTACT"][0];
		}
	}
	//print_r($contact_id);exit;

	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "ModifyDomain",
		"DOMAIN" => $domain,
		"OWNERCONTACT0" => $contact_id["OWNERCONTACT0"],
		"ADMINCONTACT0" => $contact_id["ADMINCONTACT0"],
		"TECHCONTACT0" => $contact_id["TECHCONTACT0"],
		"BILLINGCONTACT0" => $contact_id["BILLINGCONTACT0"],
	);
	//print_r($command);exit;
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
		return $values;
	}
	return $values;
}


function uniteddomains_RegisterNameserver($params) {
	$nameserver = $params["nameserver"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "AddNameserver",
		"NAMESERVER" => $nameserver,
		"IPADDRESS0" => $params["ipaddress"],
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	//print_r($command);
	//print_r($response);
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}

function uniteddomains_ModifyNameserver($params) {
	$nameserver = $params["nameserver"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "ModifyNameserver",
		"NAMESERVER" => $nameserver,
		"DELIPADDRESS0" => $params["currentipaddress"],
		"ADDIPADDRESS0" => $params["newipaddress"],
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}

function uniteddomains_DeleteNameserver($params) {
	$nameserver = $params["nameserver"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "DeleteNameserver",
		"NAMESERVER" => $nameserver,
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}


function uniteddomains_IDProtectToggle($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "ModifyDomain",
		"DOMAIN" => $domain,
		"X-ACCEPT-WHOISTRUSTEE-TAC" => ($params["protectenable"])? "1" : "0"
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}


function uniteddomains_RegisterDomain($params) {
    $origparams = $params;
	$params = uniteddomains_get_utf8_params($params);

	$domain = $params["sld"].".".$params["tld"];

	//$values["error"] = "";

	$registrant = array(
		"FIRSTNAME" => $params["firstname"],
		"LASTNAME" => $params["lastname"],
		"ORGANIZATION" => $params["companyname"],
		"STREET" => $params["address1"],
		"CITY" => $params["city"],
		"STATE" => $params["state"],
		"ZIP" => $params["postcode"],
		"COUNTRY" => $params["country"],
		"PHONE" => $params["phonenumber"],
		"EMAIL" => $params["email"]
	);
	if ( strlen($params["address2"]) ) {
		$registrant["STREET"] .= " , ".$params["address2"];
	}

	$admin = array(
		"FIRSTNAME" => $params["adminfirstname"],
		"LASTNAME" => $params["adminlastname"],
		"ORGANIZATION" => $params["admincompanyname"],
		"STREET" => $params["adminaddress1"],
		"CITY" => $params["admincity"],
		"STATE" => $params["adminstate"],
		"ZIP" => $params["adminpostcode"],
		"COUNTRY" => $params["admincountry"],
		"PHONE" => $params["adminphonenumber"],
		"EMAIL" => $params["adminemail"]
	);
	if ( strlen($params["adminaddress2"]) ) {
		$admin["STREET"] .= " , ".$params["adminaddress2"];
	}

	$contact_id = array(
		"OWNERCONTACT0" => "",
		"ADMINCONTACT0" => "",
		"TECHCONTACT0" => "",
		"BILLINGCONTACT0" => "",
	);
	$contact_info = array(
		"OWNERCONTACT0" => $registrant,
		"ADMINCONTACT0" => $admin,
		"TECHCONTACT0" => $admin,
		"BILLINGCONTACT0" => $admin,
	);
	foreach($contact_info as $key=>$val){
		$contact_cmd = $val;
		$contact_cmd["COMMAND"] = 'AddContact';
		$contact_cmd["NEW"] = '1';
		$contact_resp = uniteddomains_call($contact_cmd, uniteddomains_config($origparams));
		if ( !($contact_resp["CODE"] == 200) ) {
			$values["error"] = $key.":".$contact_resp["DESCRIPTION"];
			return $values;
		}else{
			$contact_id[$key] = $contact_resp["PROPERTY"]["CONTACT"][0];
		}
	}
	//print_r($contact_id);exit;

	$command = array(
		"COMMAND" => "AddDomain",
		"DOMAIN" => $domain,
		"PERIOD" => $params["regperiod"],
		"NAMESERVER0" => $params["ns1"],
		"NAMESERVER1" => $params["ns2"],
		"NAMESERVER2" => $params["ns3"],
		"NAMESERVER3" => $params["ns4"],
		"OWNERCONTACT0" => $contact_id["OWNERCONTACT0"],
		"ADMINCONTACT0" => $contact_id["ADMINCONTACT0"],
		"TECHCONTACT0" => $contact_id["TECHCONTACT0"],
		"BILLINGCONTACT0" => $contact_id["BILLINGCONTACT0"],
	);
	//print_r($command);//exit;

//没有这个选项
	//if ( $params["dnsmanagement"] ) {
	//	$command["INTERNALDNS"] = 1;
	//}

	//if ( $params["idprotection"] ) {
	//	$command["X-ACCEPT-WHOISTRUSTEE-TAC"] = 1;
	//}

//没有这个选项
	//uniteddomains_use_additionalfields($params, $command);

	//print_r($command);exit;
	$response = uniteddomains_call($command, uniteddomains_config($origparams));
//print_r($response);
	if ( !($response["CODE"] == 200) ) {
		$values["error"] = $response["DESCRIPTION"];
	}else{
		/*
		//再指定一次联系人
		$domain = $params["sld"].".".$params["tld"];
		//$values["error"] = "";
		$command = array(
			"COMMAND" => "ModifyDomain",
			"DOMAIN" => $domain,
			"OWNERCONTACT0" => $contact_id["OWNERCONTACT0"],
			"ADMINCONTACT0" => $contact_id["ADMINCONTACT0"],
			"TECHCONTACT0" => $contact_id["TECHCONTACT0"],
			"BILLINGCONTACT0" => $contact_id["BILLINGCONTACT0"],
		);
		$response = uniteddomains_call($command, uniteddomains_config($params));
		if ( $response["CODE"] != 200 ) {
			$values["error"] = $response["DESCRIPTION"];
			return $values;
		}
		*/
	}
	return $values;
}


function uniteddomains_query_additionalfields(&$params) {
	$result = mysql_query("SELECT name,value FROM tbldomainsadditionalfields
		WHERE domainid='".mysql_real_escape_string($params["domainid"])."'");
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		$params['additionalfields'][$row['name']] = $row['value'];
	}
}


function uniteddomains_use_additionalfields($params, &$command) {
	include dirname(__FILE__).DIRECTORY_SEPARATOR.
		"..".DIRECTORY_SEPARATOR.
		"..".DIRECTORY_SEPARATOR.
		"..".DIRECTORY_SEPARATOR.
		"includes".DIRECTORY_SEPARATOR."additionaldomainfields.php";

	$myadditionalfields = array();
	if ( is_array($additionaldomainfields) && isset($additionaldomainfields[".".$params["tld"]]) ) {
		$myadditionalfields = $additionaldomainfields[".".$params["tld"]];
	}

	$found_additionalfield_mapping = 0;
	foreach ( $myadditionalfields as $field_index => $field ) {
		if ( isset($field["Ispapi-Name"]) || isset($field["Ispapi-Eval"]) ) {
			$found_additionalfield_mapping = 1;
		}
	}

	if ( !$found_additionalfield_mapping ) {
		include dirname(__FILE__).DIRECTORY_SEPARATOR."additionaldomainfields.php";
		if ( is_array($additionaldomainfields) && isset($additionaldomainfields[".".$params["tld"]]) ) {
			$myadditionalfields = $additionaldomainfields[".".$params["tld"]];
		}
	}

	foreach ( $myadditionalfields as $field_index => $field ) {
		if ( !is_array($field["Ispapi-Replacements"]) ) {
			$field["Ispapi-Replacements"] = array();
		}

		if ( isset($field["Ispapi-Options"]) && isset($field["Options"]) )  {
			$options = explode(",", $field["Options"]);
			foreach ( explode(",", $field["Ispapi-Options"]) as $index => $new_option ) {
				$option = $options[$index];
				if ( !isset($field["Ispapi-Replacements"][$option]) ) {
					$field["Ispapi-Replacements"][$option] = $new_option;
				}
			}
		}

		$myadditionalfields[$field_index] = $field;
	}

	foreach ( $myadditionalfields as $field ) {

		if ( isset($params['additionalfields'][$field["Name"]]) ) {
			$value = $params['additionalfields'][$field["Name"]];

			$ignore_countries = array();
			if ( isset($field["Ispapi-IgnoreForCountries"]) ) {
				foreach ( explode(",", $field["Ispapi-IgnoreForCountries"]) as $country ) {
					$ignore_countries[strtoupper($country)] = 1;
				}
			}

			if ( !$ignore_countries[strtoupper($params["country"])] ) {

				if ( isset($field["Ispapi-Replacements"][$value]) ) {
					$value = $field["Ispapi-Replacements"][$value];
				}

				if ( isset($field["Ispapi-Eval"]) ) {
					eval($field["Ispapi-Eval"]);
				}

				if ( isset($field["Ispapi-Name"]) ) {
					if ( strlen($value) ) {
						$command[$field["Ispapi-Name"]] = $value;
					}
				}
			}
		}
	}
}


function uniteddomains_TransferDomain($params) {
    $origparams = $params;
	$params = uniteddomains_get_utf8_params($params);

	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";

	/*
	$registrant = array(
		"FIRSTNAME" => $params["firstname"],
		"LASTNAME" => $params["lastname"],
		"ORGANIZATION" => $params["companyname"],
		"STREET" => $params["address1"],
		"CITY" => $params["city"],
		"STATE" => $params["state"],
		"ZIP" => $params["postcode"],
		"COUNTRY" => $params["country"],
		"PHONE" => $params["phonenumber"],
		"EMAIL" => $params["email"]
	);
	if ( strlen($params["address2"]) ) {
		$registrant["STREET"] .= " , ".$params["address2"];
	}

	$admin = array(
		"FIRSTNAME" => $params["adminfirstname"],
		"LASTNAME" => $params["adminlastname"],
		"ORGANIZATION" => $params["admincompanyname"],
		"STREET" => $params["adminaddress1"],
		"CITY" => $params["admincity"],
		"STATE" => $params["adminstate"],
		"ZIP" => $params["adminpostcode"],
		"COUNTRY" => $params["admincountry"],
		"PHONE" => $params["adminphonenumber"],
		"EMAIL" => $params["adminemail"]
	);
	if ( strlen($params["adminaddress2"]) ) {
		$admin["STREET"] .= " , ".$params["adminaddress2"];
	}

	$contact_id = array(
		"OWNERCONTACT0" => "",
		"ADMINCONTACT0" => "",
		"TECHCONTACT0" => "",
		"BILLINGCONTACT0" => "",
	);
	$contact_info = array(
		"OWNERCONTACT0" => $registrant,
		"ADMINCONTACT0" => $admin,
		"TECHCONTACT0" => $admin,
		"BILLINGCONTACT0" => $admin,
	);
	foreach($contact_info as $key=>$val){
		$contact_cmd = $val;
		$contact_cmd["COMMAND"] = 'AddContact';
		$contact_cmd["NEW"] = '1';
		$contact_resp = uniteddomains_call($contact_cmd, uniteddomains_config($origparams));
		if ( !($contact_resp["CODE"] == 200) ) {
			$values["error"] = $contact_resp["DESCRIPTION"];
			return $values;
		}else{
			$contact_id[$key] = $contact_resp["PROPERTY"]["CONTACT"][0];
		}
	}
	*/

	$command = array(
		"COMMAND" => "TransferDomain",
		"DOMAIN" => $domain,
		//"PERIOD" => $origparams["regperiod"],
		//"NAMESERVER0" => $params["ns1"],
		//"NAMESERVER1" => $params["ns2"],
		//"NAMESERVER2" => $params["ns3"],
		//"NAMESERVER3" => $params["ns4"],
		//"OWNERCONTACT0" => $contact_id["OWNERCONTACT0"],
		//"ADMINCONTACT0" => $contact_id["ADMINCONTACT0"],
		//"TECHCONTACT0" => $contact_id["TECHCONTACT0"],
		//"BILLINGCONTACT0" => $contact_id["BILLINGCONTACT0"],
		"AUTH" => $origparams["transfersecret"],
		"ACTION" => "request",
	);//print_r($command);

	//don't send owner admin tech billing contact for .CA domains
	if (preg_match('/[.]ca$/i', $domain) || preg_match('/[.]us$/i', $domain)) {
		unset($command["OWNERCONTACT0"]);
		unset($command["ADMINCONTACT0"]);
		unset($command["TECHCONTACT0"]);
		unset($command["BILLINGCONTACT0"]);
	}

	$response = uniteddomains_call($command, uniteddomains_config($origparams));

	//Bug fix Issue WHMCS #4166
	//############
	if ( preg_match('/Authorization failed/', $response["DESCRIPTION"]) && preg_match('/&#039;/', $origparams["transfersecret"]) ) {
		$command["AUTH"] = htmlspecialchars_decode($origparams["transfersecret"], ENT_QUOTES);
		$response = uniteddomains_call($command, uniteddomains_config($origparams));
	}
	//############

	if ( preg_match('/USERTRANSFER/', $response["DESCRIPTION"]) ) {
		$command["ACTION"] = "USERTRANSFER";
		$response = uniteddomains_call($command, uniteddomains_config($origparams));
	}

	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}

function uniteddomains_RenewDomain($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "RenewDomain",
		"DOMAIN" => $domain,
		"PERIOD" => $params["regperiod"]
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));

	if ( $response["CODE"] == 510 ) {
		$command["COMMAND"] = "PayDomainRenewal";
		$response = uniteddomains_call($command, uniteddomains_config($params));
	}

	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}


function uniteddomains_ReleaseDomain($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "PushDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));

	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}



function uniteddomains_RequestDelete($params) {
	$domain = $params["sld"].".".$params["tld"];
	//$values["error"] = "";
	$command = array(
		"COMMAND" => "DeleteDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));

	if ( $response["CODE"] != 200 ) {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}



function uniteddomains_TransferSync($params) {
	$domain = $params["sld"].".".$params["tld"];
	$values = array();
	$command = array(
		"COMMAND" => "StatusDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] == 200 ) {

		$expdate = $response["PROPERTY"]["PAIDUNTILDATE"][0];
		$duedate = $response["PROPERTY"]["ACCOUNTINGDATE"][0];

		$values['completed'] = true; #  when transfer completes successfully

		$expdate = preg_replace('/ .*/', '', $expdate);
		$duedate = preg_replace('/ .*/', '', $duedate);

		$values['expirydate'] = $duedate;
	}
	elseif ( ($response["CODE"] == 545) || ($response["CODE"] == 531) ) {
		$command = array("COMMAND" => "StatusDomainTransfer", "DOMAIN" => $domain);
		$response = uniteddomains_call($command, uniteddomains_config($params));
		if ( ($response["CODE"] == 545) || ($response["CODE"] == 531) ) {
			$values['failed'] = true;
			$values['reason'] = "Transfer Failed";

			$loglist_command = array("COMMAND" => "QueryObjectLogList", "OBJECTCLASS" => "DOMAIN", "OBJECTID" => $domain, "ORDERBY" => "LOGDATEDESC", "LIMIT" => 1);
			$loglist_response = uniteddomains_call($loglist_command, uniteddomains_config($params));
			if ( isset($loglist_response["PROPERTY"]["LOGINDEX"]) ) {
				foreach ( $loglist_response["PROPERTY"]["LOGINDEX"] as $index => $logindex ) {
					$type = $loglist_response["PROPERTY"]["OPERATIONTYPE"][$index];
					$status = $loglist_response["PROPERTY"]["OPERATIONSTATUS"][$index];
					if ( ($type == "INBOUND_TRANSFER") && ($status == "FAILED") ) {
						$logstatus_command = array("COMMAND" => "StatusObjectLog", "LOGINDEX" => $logindex);
						$logstatus_response = uniteddomains_call($logstatus_command, uniteddomains_config($params));
						if ( $logstatus_response["CODE"] == 200 ) {
							$values['reason'] = implode("\n", $logstatus_response["PROPERTY"]["OPERATIONINFO"]);
						}
					}
				}
			}
		}
	}
	else {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}



function uniteddomains_Sync($params) {
	$domain = $params["sld"].".".$params["tld"];
	$values = array();
	$command = array(
		"COMMAND" => "StatusDomain",
		"DOMAIN" => $domain
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));
	if ( $response["CODE"] == 200 ) {
		$status = $response["PROPERTY"]["STATUS"][0];
		if ( preg_match('/ACTIVE/i', $status) ) {
			$values["active"] = true;
		}
		elseif ( preg_match('/DELETE/i', $status) ) {
			$values['expired'] = true;
		}
		$expdate = $response["PROPERTY"]["PAIDUNTILDATE"][0];
		$duedate = $response["PROPERTY"]["ACCOUNTINGDATE"][0];

		$expdate = preg_replace('/ .*/', '', $expdate);
		$duedate = preg_replace('/ .*/', '', $duedate);

		$values['expirydate'] = $duedate;
	}
	elseif ( $response["CODE"] == 531 ) {
		$values['expired'] = true;
	}
	elseif ( $response["CODE"] == 545 ) {
		$values['expired'] = true;
	}
	else {
		$values["error"] = $response["DESCRIPTION"];
	}
	return $values;
}





/* Helper functions */


function uniteddomains_get_utf8_params($params) {
    if ( isset($params["original"]) ) {
        return $params["original"];
    }
	$config = array();
	$result = mysql_query("SELECT setting, value FROM tblconfiguration;");
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		$config[strtolower($row['setting'])] = $row['value'];
	}
	if ( (strtolower($config["charset"]) != "utf-8") && (strtolower($config["charset"]) != "utf8") )
		return $params;

	$result = mysql_query("SELECT orderid FROM tbldomains WHERE id='".mysql_real_escape_string($params["domainid"])."' LIMIT 1;");
	if ( !($row = mysql_fetch_array($result, MYSQL_ASSOC)) )
		return $params;

	$result = mysql_query("SELECT userid,contactid FROM tblorders WHERE id='".mysql_real_escape_string($row['orderid'])."' LIMIT 1;");
	if ( !($row = mysql_fetch_array($result, MYSQL_ASSOC)) )
		return $params;

	if ( $row['contactid'] ) {
		$result = mysql_query("SELECT firstname, lastname, companyname, email, address1, address2, city, state, postcode, country, phonenumber FROM tblcontacts WHERE id='".mysql_real_escape_string($row['contactid'])."' LIMIT 1;");
		if ( !($row = mysql_fetch_array($result, MYSQL_ASSOC)) )
			return $params;
		foreach ( $row as $key => $value ) {
			$params[$key] = $value;
		}
	}
	elseif ( $row['userid'] ) {
		$result = mysql_query("SELECT firstname, lastname, companyname, email, address1, address2, city, state, postcode, country, phonenumber FROM tblclients WHERE id='".mysql_real_escape_string($row['userid'])."' LIMIT 1;");
		if ( !($row = mysql_fetch_array($result, MYSQL_ASSOC)) )
			return $params;
		foreach ( $row as $key => $value ) {
			$params[$key] = $value;
		}
	}

	if ( $config['registraradminuseclientdetails'] ) {
		$params['adminfirstname'] = $params['firstname'];
		$params['adminlastname'] = $params['lastname'];
		$params['admincompanyname'] = $params['companyname'];
		$params['adminemail'] = $params['email'];
		$params['adminaddress1'] = $params['address1'];
		$params['adminaddress2'] = $params['address2'];
		$params['admincity'] = $params['city'];
		$params['adminstate'] = $params['state'];
		$params['adminpostcode'] = $params['postcode'];
		$params['admincountry'] = $params['country'];
		$params['adminphonenumber'] = $params['phonenumber'];
	}
	else {
		$params['adminfirstname'] = $config['registraradminfirstname'];
		$params['adminlastname'] = $config['registraradminlastname'];
		$params['admincompanyname'] = $config['registraradmincompanyname'];
		$params['adminemail'] = $config['registraradminemailaddress'];
		$params['adminaddress1'] = $config['registraradminaddress1'];
		$params['adminaddress2'] = $config['registraradminaddress2'];
		$params['admincity'] = $config['registraradmincity'];
		$params['adminstate'] = $config['registraradminstateprovince'];
		$params['adminpostcode'] = $config['registraradminpostalcode'];
		$params['admincountry'] = $config['registraradmincountry'];
		$params['adminphonenumber'] = $config['registraradminphone'];
	}

	$result = mysql_query("SELECT name,value FROM tbldomainsadditionalfields
		WHERE domainid='".mysql_real_escape_string($params["domainid"])."'");
	while ( $row = mysql_fetch_array($result, MYSQL_ASSOC) ) {
		$params['additionalfields'][$row['name']] = $row['value'];
	}

	return $params;
}



function uniteddomains_get_contact_info($contact, &$params) {
	if ( isset($params["_contact_hash"][$contact]) )
		return $params["_contact_hash"][$contact];

	$domain = $params["sld"].".".$params["tld"];

	$values = array();
	$command = array(
		"COMMAND" => "StatusContact",
		"CONTACT" => $contact
	);
	$response = uniteddomains_call($command, uniteddomains_config($params));

	if ( 1 || $response["CODE"] == 200 ) {
		$values["First Name"] = htmlspecialchars($response["PROPERTY"]["FIRSTNAME"][0]);
		$values["Last Name"] = htmlspecialchars($response["PROPERTY"]["LASTNAME"][0]);
		$values["Company Name"] = htmlspecialchars($response["PROPERTY"]["ORGANIZATION"][0]);
		$values["Address"] = htmlspecialchars($response["PROPERTY"]["STREET"][0]);
		$values["Address 2"] = htmlspecialchars($response["PROPERTY"]["STREET"][1]);
		$values["City"] = htmlspecialchars($response["PROPERTY"]["CITY"][0]);
		$values["State"] = htmlspecialchars($response["PROPERTY"]["STATE"][0]);
		$values["Postcode"] = htmlspecialchars($response["PROPERTY"]["ZIP"][0]);
		$values["Country"] = htmlspecialchars($response["PROPERTY"]["COUNTRY"][0]);
		$values["Phone"] = htmlspecialchars($response["PROPERTY"]["PHONE"][0]);
		$values["Fax"] = htmlspecialchars($response["PROPERTY"]["FAX"][0]);
		$values["Email"] = htmlspecialchars($response["PROPERTY"]["EMAIL"][0]);

		if ( (count($response["PROPERTY"]["STREET"]) < 2)
			and preg_match('/^(.*) , (.*)/', $response["PROPERTY"]["STREET"][0], $m) ) {
			$values["Address"] = $m[1];
			$values["Address 2"] = $m[2];
		}

		// handle imported .ca domains properly
		if ( preg_match('/[.]ca$/i', $domain) && isset($response["PROPERTY"]["X-CA-LEGALTYPE"]) ) {
			if ( preg_match('/^(CCT|RES|ABO|LGR)$/i', $response["PROPERTY"]["X-CA-LEGALTYPE"][0]) ) {
				// keep name/org
			}
			else {
				if ( (!isset($response["PROPERTY"]["ORGANIZATION"])) || !$response["PROPERTY"]["ORGANIZATION"][0] ) {
					$response["PROPERTY"]["ORGANIZATION"] = $response["PROPERTY"]["NAME"];
				}
			}
		}

	}
	$params["_contact_hash"][$contact] = $values;
	return $values;
}


function uniteddomains_logModuleCall($registrar, $action, $requeststring, $responsedata, $processeddata = NULL, $replacevars = NULL) {
	if ( !function_exists('logModuleCall') ) {
		return;
	}
	return logModuleCall($registrar, $action, $requeststring, $responsedata, $processeddata, $replacevars);
}


function uniteddomains_config($params) {
	$url = "http://api.domainreselling.de/api/call.cgi";
	if ( $params["UseSSL"] == "on" ) {
		$url = "https://api.domainreselling.de/api/call.cgi";
	}
	$user = $params["Username"];
	$pass = $params["Password"];
	$s_entity = '';
	if ( $params["TestMode"] == "on" ) {
		$s_entity = '1234';
		$mreg_config = array('socket' => $url.'?s_entity='.$s_entity.'&s_login='.$user.'&s_pw='.$pass);
	}else{
		$mreg_config = array('socket' => $url.'?s_login='.$user.'&s_pw='.$pass);
	}




	return $mreg_config;
}


function uniteddomains_call($command, $config) {
	$oMREG = new mreg;
	return $oMREG->mreg_call( $command, $config );

	//return uniteddomains_parse_response(uniteddomains_call_raw($command, $config));
}


function uniteddomains_call_raw($command, $config) {
	global $uniteddomains_module_version;
	$args = array();
	$url = $config["url"];
	if ( isset($config["login"]) )
		$args["s_login"] = $config["login"];
	if ( isset($config["password"]) )
		$args["s_pw"] = $config["password"];
	if ( isset($config["user"]) )
		$args["s_user"] = $config["user"];
	if ( isset($config["entity"]) )
		$args["s_entity"] = $config["entity"];
	$args["s_command"] = uniteddomains_encode_command($command);

	# Convert IDNs via API
	/*
	if ( 1 ) {
		$new_command = array();
		foreach ( explode("\n", $args["s_command"]) as $line ) {
			if ( preg_match('/^([^\=]+)\=(.*)/', $line, $m) ) {
				$new_command[strtoupper(trim($m[1]))] = trim($m[2]);
			}
		}
		if ( strtoupper($new_command["COMMAND"]) != "CONVERTIDN" ) {
			$replace = array();
			$domains = array();
			foreach ( $new_command as $k => $v ) {
				if ( preg_match('/^(DOMAIN|NAMESERVER|DNSZONE)([0-9]*)$/i', $k) ) {
					if ( preg_match('/[^a-z0-9\.\- ]/i', $v) ) {
						$replace[] = $k;
						$domains[] = $v;
					}
				}
			}
			if ( count($replace) ) {
				if ( $config["idns"] == "PHP" ) {
					foreach ( $replace as $index => $k ) {
						$new_command[$k] = uniteddomains_to_punycode($new_command[$k]);
					}
				}
				else {
					$r = uniteddomains_call(array("COMMAND" => "ConvertIDN", "DOMAIN" => $domains), $config);
					if ( ($r["CODE"] == 200) && isset($r["PROPERTY"]["ACE"]) ) {
						foreach ( $replace as $index => $k ) {
							$new_command[$k] = $r["PROPERTY"]["ACE"][$index];
						}
						$args["s_command"] = uniteddomains_encode_command($new_command);
					}
				}
			}
		}
	}
	*/

	$config["curl"] = curl_init($url);
	if ( $config["curl"] === FALSE ) {
		return "[RESPONSE]\nCODE=423\nAPI access error: curl_init failed\nEOF\n";
	}
	$postfields = array();
	foreach ( $args as $key => $value ) {
		$postfields[] = urlencode($key)."=".urlencode($value);
	}
	$postfields = implode('&', $postfields);
	die($url.'&'.$postfields);
	curl_setopt( $config["curl"], CURLOPT_POST, 1 );
	curl_setopt( $config["curl"], CURLOPT_POSTFIELDS, $postfields );

	curl_setopt( $config["curl"], CURLOPT_HEADER, 0 );
	curl_setopt( $config["curl"], CURLOPT_RETURNTRANSFER , 1 );
	if ( strlen($config["proxy"]) ) {
		curl_setopt( $config["curl"], CURLOPT_PROXY, $config["proxy"] );
	}
	curl_setopt($config["curl"], CURLOPT_USERAGENT, "ISPAPI/$uniteddomains_module_version WHMCS/".$GLOBALS["CONFIG"]["Version"]." PHP/".phpversion()." (".php_uname("s").")");
	curl_setopt($config["curl"], CURLOPT_REFERER, $GLOBALS["CONFIG"]["SystemURL"]);
	$response = curl_exec($config["curl"]);

	if ( preg_match('/(^|\n)\s*COMMAND\s*=\s*([^\s]+)/i', $args["s_command"], $m) ) {
		$command = $m[2];
		// don't log read-only requests
		if ( !preg_match('/^(Check|Status|Query|Convert)/i', $command) ) {
			uniteddomains_logModuleCall($config["registrar"], $command, $args["s_command"], $response);
		}
	}

	return $response;
}


function uniteddomains_to_punycode($domain) {
	if ( !strlen($domain) ) return $domain;
	if ( preg_match('/^[a-z0-9\.\-]+$/i', $domain) ) {
		return $domain;
	}

	$charset = $GLOBALS["CONFIG"]["Charset"];
	if ( function_exists("idn_to_ascii") ) {
		$punycode = idn_to_ascii($domain, $charset);
		if ( strlen($punycode) ) return $punycode;
	}
	return $domain;
}


function uniteddomains_encode_command( $commandarray ) {
    if (!is_array($commandarray)) return $commandarray;
    $command = "";
    foreach ( $commandarray as $k => $v ) {
        if ( is_array($v) ) {
	    $v = uniteddomains_encode_command($v);
            $l = explode("\n", trim($v));
            foreach ( $l as $line ) {
                $command .= "$k$line\n";
		    }
        }
        else {
            $v = preg_replace( "/\r|\n/", "", $v );
            $command .= "$k=$v\n";
        }
    }
    return $command;
}



function uniteddomains_parse_response ( $response ) {
    if (is_array($response)) return $response;
    $hash = array(
		"PROPERTY" => array(),
		"CODE" => "423",
		"DESCRIPTION" => "Empty response from API"
	);
    if (!$response) return $hash;
    $rlist = explode( "\n", $response );
    foreach ( $rlist as $item ) {
        if ( preg_match("/^([^\=]*[^\t\= ])[\t ]*=[\t ]*(.*)$/", $item, $m) ) {
            $attr = $m[1];
            $value = $m[2];
            $value = preg_replace( "/[\t ]*$/", "", $value );
            if ( preg_match( "/^property\[([^\]]*)\]/i", $attr, $m) ) {
                $prop = strtoupper($m[1]);
                $prop = preg_replace( "/\s/", "", $prop );
                if ( in_array($prop, array_keys($hash["PROPERTY"])) ) {
                    array_push($hash["PROPERTY"][$prop], $value);
                }
                else {
                     $hash["PROPERTY"][$prop] = array($value);
                }
            }
            else {
                $hash[strtoupper($attr)] = $value;
            }
        }
    }
	if ( (!$hash["CODE"]) || (!$hash["DESCRIPTION"]) ) {
		$hash = array(
			"PROPERTY" => array(),
			"CODE" => "423",
			"DESCRIPTION" => "Invalid response from API"
		);
	}
    return $hash;
}


///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @staticvar GLOBAL_STREAM_TIMEOUT Timeout (in Sec.) for Socket-Stream
 */
define( 'GLOBAL_STREAM_TIMEOUT',	300 );
/**
 * @staticvar IGNORE_SSL_WARNING Ingore SSL-Certificate Warnings
 */
define( 'IGNORE_SSL_WARNING',	'OFF' );
/**
 * @staticvar HTTTP_USER_AGENT UserAgent to send to httpd
 */
define( 'HTTTP_USER_AGENT',			'libmreg/PHP' );

abstract class mregTools {
	/**
	* Configuration ( Socket-URL, use SSL .. )
	* @var array
	*/
	private $aConfig;

    abstract public function mreg_call_raw( $aCommand, $aConfig = null );
    abstract public function __construct();

	/**
	* mreg_call
	* Exec an Call in socket.
	*
	* @param  array	$aCommand		Command to Exec
	* @param  array	$aConfig		Socket (URL) to use
	* @return mixed					returns Result of Command (array) or ErrorMsg if failed.
	*/
	public function mreg_call( $aCommand, $aConfig ) {
		return $this->mreg_parse_response(					// parse result
				$this->mreg_call_raw( $aCommand, $aConfig )	// run command
				);
	} // end func mreg_call

	public function genSocket( &$aConfig, &$sCommand ) {

		$sSocket = $aConfig['socket'];	// get global 'Socket' URL
		$bFirst = false;
		if( strpos( $sSocket, '?' ) === false ) {
			$sSocket .= '?';
			$bFirst = true;
		}

		foreach( array( 's_login' => 'user', 's_pw' => 'pass', 's_user' => 'subuser' ) as $sKey => $sConfigKey ) {
			if( isset( $aConfig[$sConfigKey] ) ) {
				$sSocket .= ( $bFirst ? '' : '&' ) .$sKey.'='.rawurlencode( $aConfig[$sConfigKey] );
				$bFirst = false;
			}
		}

		if( isset( $aConfig['GET'] ) ) {
			$sSocket .= ( $bFirst ? '' : '&' ) . $sCommand;
		}
		return $sSocket;
	}

	/**
	* mreg_create_command
	* add every "command-item" to one long URL
	*
	* @param  array	$aCmdArr	Command-Array
	* @return string				Returns the parsed and added Command-String
	*/
	public function mreg_create_command( $aCmdArr ) {
		if (!is_array( $aCmdArr ) ) {
			return $aCmdArr;
		}
		$sCmd = '';
		foreach ( $aCmdArr as $sKey => $sValue ) {
			if ( is_array($sValue) ) {
				$sCmd .= $this->mreg_create_command( $sValue );
			} else {
				$sValue	= str_replace( array("\r","\n") , '', $sValue );
				$sCmd	.= rawurlencode($sKey).'='.rawurlencode($sValue)."&";
			}
		}
		if( substr( $sCmd, -1, 1 ) == '&' ) {
			$sCmd = substr($sCmd, 0, -1 );
		}

		return $sCmd;
	} // end func mreg_create_command

	/**
	* mreg_parse_response
	* Parses the String of mreg_call_raw_http
	*
	* @param  string	$sResponse	Result-String of mreg_call_raw_http
	* @return array				Array with all Propertys of the response
	*/
	public function mreg_parse_response ( $sResponse ) {
		// if no response, return empty array.
		if( empty($sResponse) ) {
			return array();
		}

		// add all entrys below the 'PROPERTY' hash
		$aHash = array( 'PROPERTY' => array() );

		// split response by breakline
		foreach ( explode( "\n", $sResponse ) as $sItem ) {
			// and split line by regex into Attribute and Value
			if ( preg_match('/^([^\=]*[^\t\= ])[\t ]*=[\t ]*(.*)$/', $sItem, $aMatch) ) {
				$sAttr	= $aMatch[1];
				$sValue	= $aMatch[2];
				$sValue	= preg_replace( '/[\t ]*$/', '', $sValue );	// kill tabs

				// add only proppertys
				if ( preg_match( '/^property\[([^\]]*)\]/i', $sAttr, $aMatch) ) {
					$sProp = str_replace( array(' ', "\t") , '', strtoupper($aMatch[1]) );
					if ( in_array($sProp, array_keys($aHash['PROPERTY'])) ) {
						array_push($aHash['PROPERTY'][$sProp], $sValue);
					} else {
						$aHash['PROPERTY'][$sProp] = array($sValue);
					}
				} else {
					$aHash[strtoupper($sAttr)] = $sValue;
				}
			}
		}
		// final result parsed hash
		return $aHash;
	} // end func mreg_parse_response

	/**
	* mreg_parse_command
	* parses the command-string to an array
	*
	* @param  string	$sCommand	Command-String to parse
	* @return array				Array with all commandpropertys
	*/
	public function mreg_parse_command( $sCommand ) {

		if (is_array($sCommand)) {
			return $sCommand;
		}

		// replace incorrect chars
		$aReplaceBy	= array ( "\r\n", "\n\r", "\r" );
		$sCommand	= str_replace( $aReplaceBy, "\n",					$sCommand );
		$sCommand	= preg_replace( "/[\t ]*(\n|$)[\t ]*/", "\n",		$sCommand );
		$sCommand	= preg_replace( "/(^|\n)\[[^\n]+(\n|$)/", "\\1",	$sCommand );
		$sCommand	= preg_replace( "/(^|\n)EOF(\n|$)/", "\\1",			$sCommand );

		// split command by breakline
		$aCmdLines = explode( "\n", $sCommand );

		// split command by '=' into key and value
		$m = '';
		$aCmdArray = array();
		foreach( $aCmdLines as $sCmdLine ) {
			preg_match("/^(.+?)=(.+)$/is", $sCmdLine, $m );
			$aCmdArray[ trim(strtolower($m[1]))] = trim($m[2]);
		}

		// return parsed commandstring
		return $aCmdArray;
	}//end func mreg_parse_command

} // end class mregTools

/**
 * Define mregCurl-Class
 * @package       mreg
 */
class mreg extends mregTools {
    /**
     * libCurl instance
     * @var object
     */
	private $oCurl;

    /**
     * __construct
     * Initialize all Class-Variables.
     *
     * @return void
     */
	public function __construct() {
		$this->aConfig			= array();
		$this->oCurl			= curl_init();
		curl_setopt( $this->oCurl, CURLOPT_USERAGENT,		HTTTP_USER_AGENT );
		curl_setopt( $this->oCurl, CURLOPT_TIMEOUT,			GLOBAL_STREAM_TIMEOUT );
		curl_setopt( $this->oCurl, CURLOPT_HEADER, 			FALSE );
		curl_setopt( $this->oCurl, CURLOPT_SSL_VERIFYPEER,	IGNORE_SSL_WARNING );
		curl_setopt( $this->oCurl, CURLOPT_RETURNTRANSFER,	TRUE ); // get the response as a string from curl_exec(), rather than echoing it
	} // end func __construct

    /**
     * mreg_call_raw
     * Exec an Call in socket.
     *
     * @param  array	$aCommand		Command to Exec
     * @param  array	$aConfig		Config for mreg_call
     * @return mixed					returns Result of Command (string) or ErrorMsg if failed.
     */
	public function mreg_call_raw( $aCommand, $aConfig = null ) {
		if( $aConfig != null ) {
			$this->aConfig = $aConfig;					// set Config global.
		}
		$sCommand	= $this->mreg_create_command( $aCommand );	// generate command-string

		// replace incorrect chars
		$aReplaceBy	= array ( "\r\n", "\n\r", "\r" );
		$sCommand	= str_replace( $aReplaceBy, "\n",					$sCommand );
		$sCommand	= preg_replace( "/[\t ]*(\n|$)[\t ]*/", "\n",		$sCommand );
		$sCommand	= preg_replace( "/(^|\n)\[[^\n]+(\n|$)/", "\\1",	$sCommand );
		$sCommand	= preg_replace( "/(^|\n)EOF(\n|$)/", "\\1",			$sCommand );

		// parse url for protocol-type
		if( strpos( $this->aConfig['socket'], ':' ) === false ) {
			die('Invalid URL:'.$this->aConfig['socket']);
		}

		$sSocket = $this->genSocket( $this->aConfig, $sCommand );

		// set socket-url and post-variables
		curl_setopt( $this->oCurl, CURLOPT_URL,				$sSocket );
 		if( !isset( $this->aConfig['GET'] ) ) {
			curl_setopt( $this->oCurl, CURLOPT_POST,			TRUE );
			curl_setopt( $this->oCurl, CURLOPT_POSTFIELDS,		$sCommand );
		} else {
			curl_setopt( $this->oCurl, CURLOPT_POST,			FALSE );
		}

		// start transfer
		$sResult = curl_exec( $this->oCurl );

		// transfer correct?
		if( !$sResult ) {
			$curlError = curl_error( $this->oCurl );
			return("code = 999\ndescription = Socket-Verbindung nicht moeglich! " . $curlError);
		}

		// get http-errorcode result
		$iHTTPCode = curl_getinfo( $this->oCurl, CURLINFO_HTTP_CODE );

		// if http-request wasn't valid
		if( $iHTTPCode != 200 ) {
			return("code = 998\ndescription = Socket-Verbindung nicht moeglich! HTTP Error-Code " . $iHTTPCode );
		}

		// return response
		return $sResult;
	} // end func mreg_call_raw

} // end class mreg


?>
