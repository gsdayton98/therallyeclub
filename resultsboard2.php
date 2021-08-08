<?
include "config.php";

$EmailAddrs = array();
$ADDTRC = array();

if(!@$sub)
{

	$arrClasses[1] = "First Timer";
	$arrClasses[2] = "Beginner";
	$arrClasses[3] = "Novice";
	$arrClasses[4] = "Senior";
	$arrClasses[5] = "Expert";
	$arrClasses[6] = "Master";
	
	
	$RallyeID = CHTTPVars::GetValue("RallyeID"); 
	$TestMode = CHTTPVars::GetValue("TestMode"); 
	
	
	if(!CHTTPVars::IsEmpty("action"))
	{
		$action = CHTTPVars::GetValue("action");
		
		switch(strtolower($action))
		{
			case "done":
				redirect("score.php?RallyeID=$RallyeID&TestMode=$TestMode");
				break;
				
			default:
				redirect("error.php");
		}
	}
}

// must load this information regardless of weather we are are recording or scoring
$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
if(count($arr))
{
	list($RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
}
	
$strSQL="SELECT CarNumber, CarClass, Driver, Navigator, Passenger, BaseScore, ProtestScore, DClub, NClub,DEmail, NEmail, PEmail, dadd, nadd, padd FROM ars_scoresheet WHERE RallyeID = $RallyeID AND (DClub = 'TRC' OR NClub = 'TRC' OR PClub = 'TRC') ORDER By BaseScore + ProtestScore DESC LIMIT 1";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("CarNumber");
if(isset($arr[0][0])) $TopScore = $arr[0][0];

$strSQL="SELECT CarNumber, CarClass, Driver, Navigator, Passenger, BaseScore, ProtestScore, DClub, NClub,DEmail, NEmail, PEmail, dadd, nadd, padd FROM ars_scoresheet WHERE RallyeID = $RallyeID ORDER By CarClass, CarNumber";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
$ScoreBoard = array();
foreach($arr as $data)
{
	list($CarNumber, $CarClass, $DriverName, $NavigatorName, $PassengerName, $BaseScore, $ProtestScore, $DClubName, $NClubName,$DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd) = $data;
	
	if(trim($DEmail) != "")
		$EmailAddrs[] = $DEmail;
	if(trim($NEmail) != "")
		$EmailAddrs[] = $NEmail;
	if(trim($PEmail) != "")
		$EmailAddrs[] = $PEmail;

	
	if(intval($dadd) != "")
		$ADDTRC[] = $DEmail;
	if(intval($nadd) != "")
		$ADDTRC[] = $NEmail;
	if(intval($padd) != "")
		$ADDTRC[] = $PEmail;

	$ScoreBoard[$CarClass][$CarNumber] = floatval($BaseScore) + floatval($ProtestScore);
	$Driver[$CarNumber] = $DriverName;
	$Navigator[$CarNumber] = $NavigatorName;
	$DClub[$CarNumber] = $DClubName;
	$NClub[$CarNumber] = $NClubName;
	if(trim($PassengerName) != "")
		$Passenger[$CarNumber] = $PassengerName;
	$Protest[$CarNumber] = floatval($ProtestScore);
}

foreach($ScoreBoard as $CarClass => $data)
{
	arsort($ScoreBoard[$CarClass]);
}

if(!@$sub)
{
?>

<HTML>
	<HEAD>
		<TITLE>ARS - Results</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>

		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		<INPUT TYPE=HIDDEN NAME=TestMode VALUE=<?=$TestMode?>>
		<INPUT TYPE=SUBMIT NAME=action VALUE=Done>
		</FORM>
<?
}
?>
<PRE>
<?
	if(count($ADDTRC))
	{
		print("Add these addresses to the email mailing list:\n");
		foreach($ADDTRC as $ADD)
		{
			print("$ADD\n");
		}
		print("\n");
		print("\n");
	}

	print("To: trc-events@therallyeclub.org\n");
	print("BCC:".@implode(";", $EmailAddrs)."\n\n");
	printf("%-20s%-20s%37s\n\n\n",$RallyeMaster, $RallyeName, $RallyeDate);
?>
T = Award Issued
* = Tie Broken
P = Score affected by protest
X = TRC Top Score

C  P        S N
l  l        c o
a  a   C    o t
s  c   a    r e
s  e   r    e s    Driver               Navigator            Passenger
-------------------------------------------------------------------------------
<?
			foreach($ScoreBoard as $CarClass => $data)
			{
				$Ord = 0;
				$sorted = array();
				foreach($ScoreBoard[$CarClass] as $CarNumber => $Score)
				{
					$sorted[] = array($CarNumber, floatval($Score)); // this looks pointless, but I need a way to peek ahead in the ordered array
				}
				
				foreach($sorted as $i => $data)
				{
					list($CarNumber, $Score) = $data;
							$ThisClass=$arrClasses[intval($CarClass)]{0};
							$ThisOrd=++$Ord;
							$ThisCarNumber=$CarNumber;
							$ThisScore=intval($Score);
							// trophied
							$ThisTopScore = " ";
							if($TopScore == $CarNumber) 
							{
								$ThisTopScore = "X";
							}
							if(in_array($CarClass, array(1,2,3)) && in_array($Ord,array(1,2,3)))
							{
								$ThisTrophy = "T";
							}
							else if(in_array($CarClass, array(4,5)) && in_array($Ord,array(1,2)))
							{
								$ThisTrophy = "T";
							}
							else if(in_array($CarClass, array(6)) && in_array($Ord,array(1)))
							{
								$ThisTrophy = "T";
							}
							else
								$ThisTrophy = " ";

							// ties broken
							if(isset($sorted[$i+1]) &&
							  (floatval(intval($Score)) != floatval($Score)) &&
							  (intval($Score) == intval($sorted[$i+1][1]))) // [$i+1][1] is the score of the next car in line
							{
								$ThisTie="*";
							}
							else
								$ThisTie=" ";

							// protest
							if($Protest[$CarNumber] != 0) 
								$ThisProtest = "P";
							else
								$ThisProtest = " ";

											
							$ThisDriver=((trim(@$Driver[$CarNumber]) != "")?$Driver[$CarNumber]:" ");
							$ThisDriver.=(trim(@$DClub[$CarNumber]))?" (".$DClub[$CarNumber].")":"";

							$ThisNavigator=((trim(@$Navigator[$CarNumber]) != "")?$Navigator[$CarNumber]:" ");
							$ThisNavigator.=(trim(@$NClub[$CarNumber]))?" (".$NClub[$CarNumber].")":"";

							$ThisPassenger=(($Passenger[$CarNumber])?str_replace(",",",\n                                                            ",$Passenger[$CarNumber]):" ");

					printf("%s %2s %3s %4s %s%s%s%s %-21s%-21s%-21s\n",$ThisClass,$ThisOrd,$ThisCarNumber,$ThisScore,$ThisTrophy,$ThisTie,$ThisProtest,$ThisTopScore,$ThisDriver,$ThisNavigator,$ThisPassenger);
				}
				print("\n");
			}
?>
</PRE>
	</BODY>
</HTML>
