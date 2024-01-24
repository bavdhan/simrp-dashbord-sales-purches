<?php 
	header("Access-Control-Allow-Origin: null"); 
	ini_set('display_errors', 1);
	ini_set('display_startup_errors', 1);
	error_reporting(E_ALL);

	$url = "http://localhost:8169";
	$db = "jia";
	$username = "ks12mobile@gmail.com";
	$password = "1";

	$uid = '';
	try
	{
			require_once('ripcord/ripcord.php');
			$common = ripcord::client("$url/xmlrpc/2/common");
			$uid = $common->authenticate($db, $username, $password, array());
			$models = ripcord::client("$url/xmlrpc/2/object");
			if ( $uid == '' ) {
				echo "";
			}
			else{
				echo "";
			}
			
	}

	catch( Exception $e )
	{
	}
?>