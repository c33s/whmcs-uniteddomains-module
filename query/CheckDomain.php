<?php

require("../dbconnect.php");
require("../includes/functions.php");
require("../includes/clientareafunctions.php");
require("../includes/registrarfunctions.php");
require("../modules/registrars/uniteddomains/uniteddomains.php");

$uniteddomains_config = uniteddomains_config(getregistrarconfigoptions("uniteddomains"));

$command = array(
	"COMMAND" => "CheckDomain",
	"DOMAIN" => $_REQUEST["domain"]
);
$response = uniteddomains_call($command, $uniteddomains_config);
if ( preg_match('/^210/', $response["CODE"]) ) {
			$result["status"] = "Available";
		}
		elseif ( preg_match('/^211/', $response["CODE"]) ) {
			$result["status"] = "Registered";
		}
		elseif ( preg_match('/^5/', $response["CODE"]) ) {
			$result["status"] = "Registered";
		}
		else {
			$result["status"] = "Registered";
		}
echo $result["status"];
?>
