<?
if(!defined('CONFIGURED')) 
{
	date_default_timezone_set("America/Los_Angeles");
	define('CONFIGURED',true);

session_start();

$_APPENV["db_host"] = 'localhost';
$_APPENV["db_database"] = 'arsdb';
$_APPENV["db_user"] = 'root';
$_APPENV["db_password"] = 'none';

$_APPENV["tmpdir"] = "/tmp";

// include other  classes and function libraries,
// this is done down here because the classes and libs 
// may depend on APPENV
include "clsCBaseDB.php";
include "clsCHttpVars.php";

include "incFunctions.php";

global $oDB;
$oDB = new CBaseDB();
$oDB->connect($_APPENV["db_database"], $_APPENV["db_host"], $_APPENV["db_user"], $_APPENV["db_password"]);

function OnShutdown()
{
	global $oDB;
	
	// ensure that mySQL is closed properly
	$oDB->Close();
}

register_shutdown_function ("OnShutdown");

} // all config information needs to be inside this bracket
?>
