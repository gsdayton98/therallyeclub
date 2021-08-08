<?
include "config.php";

function csvoutput($arr)
{
	$sep="";
	foreach($arr as $element)
	{
		$element = addslashes($element);
		print($sep."'".$element."'");
		$sep=",";
	}
	print("\n");
}

$RallyeID = CHTTPVars::GetValue("RallyeID");
if(!PasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");


// for each rallye table print the name of the table
// and then export the data sans rallye ID or any other table ids
// in the event that we have a subordinated table where there is a 
// dependantcy then interleave the lines



$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);

$arr = $oDB->GetRecordArray("Password,ScoreOnlyPassword,RallyeName,RallyeDate,RallyeMaster,TopX,TopY,CMX,CMY,CPX,CPY,BotX,BotY,CPLoc,Steps");

$rallyefilename = preg_replace("/[^A-Za-z_-]/","",$arr[0][2]);

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Content-type: application/x-download");
header("Content-Disposition: attachment; filename=\"".$rallyefilename.".ars\"");
header("Content-Transfer-Encoding: text");

print("!arsexport,1.0\n"); // table lines begin with #

print("#ars_rallye_base\n"); // table lines begin with #
foreach($arr as $data)
{
	print("%"); // data lines begin with %
	csvoutput($data);
}


print("#ars_rallye_cells\n"); // table lines begin with #
$strSQL="SELECT * FROM ars_rallye_cells WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);

$arr = $oDB->GetRecordArray("F,X,Y,O,Name,Value");
foreach($arr as $data)
{
	print("%"); // data lines begin with %
	csvoutput($data);
}


$strSQL="SELECT * FROM ars_rallye_impcombo WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);

$arr = $oDB->GetRecordArray("ICID,Points,Trigger");
foreach($arr as $data)
{
	list($ICID,$Points,$Trigger) = $data;
	$data = array($Points,$Trigger);
	
	print("#ars_rallye_impcombo\n"); // output the main table row, this will trigger the creation of an ICID when imported
	print("%"); // data lines begin with %
	csvoutput($data);

	print("#ars_rallye_impcombo_elements\n"); // output the subordinated elements.
	$strSQL="SELECT * FROM ars_rallye_impcombo_elements WHERE ICID = $ICID";
	$oDB->Query($strSQL);
	
	$arr = $oDB->GetRecordArray("F,X,Y,O");
	foreach($arr as $data)
	{
		print("%"); // data lines begin with %
		csvoutput($data);
	}

}

print("#ars_scoresheet\n"); // table lines begin with #
$strSQL="SELECT * FROM ars_scoresheet WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);

$arr = $oDB->GetRecordArray("CarNumber,CarClass,Driver,Navigator,Passenger,DClub,NClub,PClub,BaseScore,ProtestScore,DEmail,NEmail,PEmail,dadd,nadd,padd");
foreach($arr as $data)
{
	print("%"); // data lines begin with %
	csvoutput($data);
}

print("#ars_scoresheet_elements\n"); // table lines begin with #
$strSQL="SELECT * FROM ars_scoresheet_elements WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);

$arr = $oDB->GetRecordArray("CarNumber,F,X,Y,O,Name,Have");
foreach($arr as $data)
{
	print("%"); // data lines begin with %
	csvoutput($data);
}

$strSQL="SELECT * FROM ars_protest WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);

$arr = $oDB->GetRecordArray("ProtestID,Points,Reason");
foreach($arr as $data)
{
	list($ProtestID,$Points,$Reason) = $data;
	$data = array($Points,$Reason);
	
	print("#ars_protest\n"); // output the main table row, this will trigger the creation of an ProtestID when imported
	print("%"); // data lines begin with %
	csvoutput($data);

	print("#ars_protest_elements\n"); // output the subordinated elements.
	$strSQL="SELECT * FROM ars_protest_elements WHERE ProtestID = $ProtestID";
	$oDB->Query($strSQL);
	
	$arr = $oDB->GetRecordArray("Class,CarNumber,F,X,Y,O,Have");
	foreach($arr as $data)
	{
		print("%"); // data lines begin with %
		csvoutput($data);
	}

}
?>
