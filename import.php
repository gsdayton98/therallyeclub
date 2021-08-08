<?
include "config.php";

$RallyeID = 0;
$ICID = 0;

$import = CHTTPVars::GetValue("import");
$lines = file($import);
if(!is_array($lines))
	redirect("index.php");

// read first line of file, trim, split on ","
// first line[0] must be "!arsexport", floatval of line[1] must be an expected value

$arrExpectedVersions=array(1.0);
$RallyeID=0;

// read the file into an array of lines
// foreach line look for the # sentinal

$c = count($lines);


if($c)
{
	$arr = explode(",",$lines[0]);
	if($arr[0] == "!arsexport" && in_array(floatval($arr[1]),$arrExpectedVersions))
		$Version = $arr[1];
	else
	{
		print("Incorrect verison: exiting");
		exit; // version not supported
	}
}
else
{
	print("Input file corrupted or unprocessable: exiting");
	exit; // corrupted file
}

for($i=1; $i<$c; $i++)
{
	$line = trim($lines[$i]);
	if($line{0} == "#")
	{
		switch($line)
		{
			case "#ars_rallye_base":
			// when you see this line it means that we are creating a new rallye.
			// read the next line and insert it into the ars_rallye_base table
			// then get the rallye ID back with GetLastInsertID()
				$i++;
				$line = trim($lines[$i]);
				if($line{0} == "%")
				{
					$line = substr($line,1);
					$strSQL="INSERT INTO ars_rallye_base (Password,ScoreOnlyPassword,RallyeName,RallyeDate,RallyeMaster,TopX,TopY,CMX,CMY,CPX,CPY,BotX,BotY,CPLoc,Steps) VALUES($line)";
					$oDB->Query($strSQL);
					$RallyeID = $oDB->GetLastInsertID();
				}
				else
				{
					$i--; // roll back one line to allow the main processor to attempt to handle it
				}
				break;

			case "#ars_rallye_cells":
			// when this line is encountered we read lines until we encounter one that does not start with %
			// then roll back one line to allow the main processor to handle it.
				
				do
				{
					$i++;
					$line = trim($lines[$i]);

					if($line{0} == "%")
					{
						$subline = substr($line,1);
						$strSQL = "INSERT INTO ars_rallye_cells (RallyeID,F,X,Y,O,Name,`Value`) VALUES($RallyeID, $subline)";
						$oDB->Query($strSQL);
					}
				}while($line{0} == "%");

				$i--;

				break;

			case "#ars_rallye_impcombo":
			// when this line is encountered we create a new combo id
				$i++;
				$line = trim($lines[$i]);
				if($line{0} == "%")
				{
					$line = substr($line,1);
					$strSQL="INSERT INTO ars_rallye_impcombo (RallyeID, Points, `Trigger`) VALUES($RallyeID, $line)";
					$oDB->Query($strSQL);
					$ICID = $oDB->GetLastInsertID();
				}
				else
				{
					$i--; // roll back one line to allow the main processor to attempt to handle it
				}
				break;

			case "#ars_rallye_impcombo_elements":
			// when this line is encountered insert elements until we hit a non % line
				do
				{
					$i++;
					$line = trim($lines[$i]);

					if($line{0} == "%")
					{
						$subline = substr($line,1);
						$strSQL = "INSERT INTO ars_rallye_impcombo_elements (RallyeID,ICID,F,X,Y,O) VALUES($RallyeID, $ICID,$subline)";
						$oDB->Query($strSQL);
					}
				}while($line{0} == "%");

				$i--;
				break;

			case "#ars_scoresheet":
				do
				{
					$i++;
					$line = trim($lines[$i]);

					if($line{0} == "%")
					{
						$subline = substr($line,1);
						$strSQL = "INSERT INTO ars_scoresheet (RallyeID,CarNumber,CarClass,Driver,Navigator,Passenger,DClub,NClub,PClub,BaseScore,ProtestScore,DEmail,NEmail,PEmail,dadd,nadd,padd) VALUES($RallyeID,$subline) ";
						$oDB->Query($strSQL);
					}
				}while($line{0} == "%");

				$i--;
				break;

			case "#ars_scoresheet_elements":
				do
				{
					$i++;
					$line = trim($lines[$i]);

					if($line{0} == "%")
					{
						$subline = substr($line,1);
						$strSQL = "INSERT INTO ars_scoresheet_elements (RallyeID,CarNumber,F,X,Y,O,Name,Have) VALUES($RallyeID,$subline)";
						$oDB->Query($strSQL);
					}
				}while($line{0} == "%");

				$i--;
				break;

			case "#ars_protest":
				$i++;
				$line = trim($lines[$i]);
				if($line{0} == "%")
				{
					$line = substr($line,1);
					$strSQL="INSERT INTO ars_protest (RallyeID,Points,Reason) VALUES($RallyeID, $line)";
					$oDB->Query($strSQL);
					$ProtestID = $oDB->GetLastInsertID();
				}
				else
				{
					$i--; // roll back one line to allow the main processor to attempt to handle it
				}
				break;

			case "#ars_protest_elements":
				do
				{
					$i++;
					$line = trim($lines[$i]);

					if($line{0} == "%")
					{
						$subline = substr($line,1);
						$strSQL = "INSERT INTO ars_protest_elements (ProtestID,Class,CarNumber,F,X,Y,O,Have,RallyeID) VALUES($ProtestID,$subline,$RallyeID)";
						$oDB->Query($strSQL);
					}
				}while($line{0} == "%");

				$i--;
				break;
		}
	}
}

redirect("index.php");
?>
