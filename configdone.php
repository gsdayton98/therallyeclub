<?
// STEP 1
// Present the user with a list of existing rallyes or create a new one
//
// Get the name and date of the rallye
// create an empty entry in ars_rallye_base

include "config.php";

$ThisStep = 16;
$RallyeID = CHTTPVars::GetValue("RallyeID");
if(!PasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");

if(!IsSetStep(4,$RallyeID)) // have I completed the previous step?
	redirect("value.php?RallyeID=$RallyeID");

SetStep($ThisStep,$RallyeID);

if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "score":
			redirect("score.php?RallyeID=$RallyeID&TestMode=1");
			break;
			
		default:
			redirect("error.php");
	}
}


?>
<HTML>
	<HEAD>
		<TITLE>ARS - Config Done</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>
<?
		include("configmenu.php");
?>
		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>

		<fieldset>
		<legend><B>Instructions</B></legend>
		You have finished configuring your rallye. 
		You may now use the template to score your rallye.
		<BR><BR>
		Upgrade Wish List:
		<UL>
		<LI> Add Phidget RFID to the program, requires a user database with Perminant Car Number, Name, and Culb. There may be some issue with Role (Driverr/Navigator) but I'm sure I'll think of something everyone will hate.
		<LI> Fix up scoring cells so that they  stay better aligned and are less picky about clicking
		<LI> Create a way to auto fill cell names with alternate standards AA-AZ, Numbers, Roman Numberals, non 13x4 grid, etc.
		<LI> Ensure that HTML/Database dangerous characters; like &gt;, &lt;, &amp;, ", and '; are made safe
		<LI> Add Check All/None in Column/Row to Scoring template
		<LI> Add various sorting modes to statistics: scoresheet order (just print out column wise), runsheet order (will need sparate page to define an ordinal)
		<LI> Contestant Score Tracking/Championship scoring
		</UL>
		</fieldset>
		<BR>
		<BR>
		
		<CENTER>
		<INPUT NAME=action TYPE=SUBMIT VALUE=Score>
		</CENTER>
		</FORM>
	</BODY>
</HTML>
