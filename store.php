<?
include "config.php";

$RallyeID = CHTTPVars::GetValue("RallyeID");
$TestMode = intval(CHTTPVars::GetValue("TestMode"));
if(!ScorePasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");

if(!IsSetStep(16,$RallyeID)) // have I completed the previous step?
	redirect("configdone.php?RallyeID=$RallyeID");

function myprint($str)
{
	print($str."\n");
	//print($str);
}

function MultiArraySum($arr)
{
	//var_dump($arr);
	
	$Sum = 0;
	if(is_array($arr)) 
	{
		foreach($arr as $element)
		{
			$Sum += MultiArraySum($element);
		}
	}
	else
	{
		$element = floatval($arr);
		$Sum += $element;
		//var_dump($element);
	}

	return($Sum);	
}



//TODO: this should really be databased
global $arrClasses;
$arrClasses[1] = "First Timer";
$arrClasses[2] = "Beginner";
$arrClasses[3] = "Novice";
$arrClasses[4] = "Senior";
$arrClasses[5] = "Expert";
$arrClasses[6] = "Master";

global $Cells;	
global $Values;	
global $IC;
global $ICDisp;



function DoScore($RallyeID, $CarClass, $CarNumber, $Have, $DriverName, $NavgtrName, $PasngrName, $DClub, $NClub, $PClub, $DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd)
{
	global $oDB;
	global $Cells;	
	global $Values;	
	global $IC;
	global $ICDisp;
	global $arrClasses;

	// store scoresheet in database
	$strSQL="DELETE FROM ars_scoresheet_elements WHERE RallyeID = $RallyeID AND CarNumber = $CarNumber";
	//print("$strSQL<BR>");
	$oDB->Query($strSQL);

	if(is_array($Have)) foreach($Have as $Section => $arrSection)
	{
		foreach($arrSection as $x => $arr1)
		{
			foreach($arr1 as $y => $arr2)
			{
				foreach($arr2 as $o => $data)
				{
					// find out what the old value was
					$strSQL="INSERT ars_scoresheet_elements (RallyeID, CarNumber, F, X, Y, O, Name, Have) VALUES($RallyeID, $CarNumber, '$Section', $x, $y, $o, '".DBEscape($Cells[$Section][$x][$y][$o])."', 1)";
					//print("$strSQL<BR>");
					$oDB->Query($strSQL);
					
					$V = $Values[$Section][$x][$y][$o];
					//var_dump($V);
					$Score[$Section][$x][$y][$o] = $V; // Score now contains all the values that are scored
				}
			}	
			if(isset($Score[$Section]))
				$ret[$Section] = MultiArraySum($Score[$Section]);
		}
	}
	
	//var_dump($Score);

	$ret['Fin'] = floatval(@$Values['Fin'][0][0][0]);
				
	// first pass
	$ret['PreComboScore'] = MultiArraySum($Score) + floatval(@$Values['Fin'][0][0][0]);


	// apply combo rules
	foreach($IC as $ICID => $data)
	{
		$Trigger = $IC[$ICID]["T"];
		$Mark=0;
		foreach($IC[$ICID]["G"] as $data)
		{
			list($F,$X,$Y,$O) = $data;
			
			if(isset($Have[$F][$X][$Y][$O]))
				$Mark++;
		}
		if($Mark >= $Trigger)
		{
			$OutAdjustment = 0;
			foreach($IC[$ICID]["G"] as $data)
			{
				list($F,$X,$Y,$O) = $data;
				
				$OutAdjustment += floatval($Score[$F][$X][$Y][$O]);
				$Score[$F][$X][$Y][$O] = 0; // this is actually the right thing to do
			}
			
			$Score['IC'][$ICDisp[$ICID]] = floatval($IC[$ICID]["V"]) - $OutAdjustment;
			$ret['ComboScore']  = @$ret['ComboScore'] + floatval($IC[$ICID]["V"]) - $OutAdjustment;
			$strSQL = "INSERT INTO ars_scoresheet_elements (RallyeID, CarNumber, F, X, Y, O, Name) VALUES($RallyeID, $CarNumber, 'Combo', $ICID, 0, 0, '$ICDisp[$ICID]');";
			//print("$strSQL<BR>");
			$oDB->Query($strSQL);
		}
	}

	//var_dump($Score);

	// store base score
	$ret['BaseScore'] = $ret['PreComboScore'] + $ret['ComboScore'];
	$ret['ProtestScore'] = 0;
	
	//var_dump($PasngrName);
	
	
	// check for protests
	$ProtestScore = 0;
	$strSQL = "SELECT ProtestID,Points,Reason FROM ars_protest WHERE RallyeID = $RallyeID";
	$oDB->Query($strSQL);
	$arrProtests = $oDB->GetRecordArray(false);
	foreach($arrProtests as $arrProtest)
	{
		list($ProtestID, $ProtestPoints, $ProtestReason) = $arrProtest;
		$strSQL = "SELECT Class, CarNumber,F,X,Y,O,Have FROM ars_protest_elements WHERE ProtestID = $ProtestID";
		$oDB->Query($strSQL);
		$arr = $oDB->GetRecordArray(false);
		$arrProtestClasses = array();
		$arrProtestCarNumbers = array();
		$arrProtestElements = array();
		foreach($arr as $data)
		{
			list($ProtestClass, $ProtestCarNumber, $ProtestF, $ProtestX, $ProtestY, $ProtestO, $ProtestHave) = $data;
			$ProtestClass = intval($ProtestClass);
			$ProtestCarNumber = intval($ProtestCarNumber);

			if($ProtestClass > 0)
			{
				$arrProtestClasses[] = $ProtestClass;
			}

			if($ProtestCarNumber > 0)
			{
				$arrProtestCarNumbers[] = $ProtestCarNumber;
			}

			if(trim($ProtestF) != "")
			{
				$arrProtestElements[] = array($ProtestF, $ProtestX, $ProtestY, $ProtestO, $ProtestHave);
			}
		}
		

		// does this car qualify?
		if(!count($arrProtestCarNumbers) || in_array($CarNumber,$arrProtestCarNumbers)) // either you are a valid car number or there are no car number restrictions
		{
			if(!count($arrProtestClasses) || in_array($CarClass,$arrProtestClasses)) // are you in the class
			{
				// now do you have the required CM's
				// we will assume you do until we prove you don't
				$Grant=true;
				foreach($arrProtestElements as $arrProtestElement)
				{
					list($ProtestF, $ProtestX, $ProtestY, $ProtestO, $ProtestHave) = $arrProtestElement;
					if(!isset($Have[$ProtestF][$ProtestX][$ProtestY][$ProtestO]) && intval($ProtestHave) > 0)
					{
						$Grant=false;
						break;
					}

					if(isset($Have[$ProtestF][$ProtestX][$ProtestY][$ProtestO]) && intval($ProtestHave) < 0)
					{
						$Grant=false;
						break;
					}
				}

				// add protest points
				if($Grant)
				{
					$ret["ProtestScore"] = @$ret["ProtestScore"] + floatval($ProtestPoints);
					//$Score = @$Score + floatval($ProtestPoints);
				}
			}
		}
	}

	$strSQL="REPLACE INTO ars_scoresheet (RallyeID, CarNumber, CarClass, Driver, Navigator, Passenger, BaseScore, ProtestScore, DClub, NClub, PClub, DEmail, NEmail, PEmail, dadd, nadd, padd) VALUES($RallyeID, $CarNumber, $CarClass,'".DBEscape($DriverName)."','".DBEscape($NavgtrName)."','".DBEscape($PasngrName)."', ".$ret['BaseScore'].",".$ret["ProtestScore"].",'".DBEscape($DClub)."','".DBEscape($NClub)."','".DBEscape($PClub)."', '".DBEscape($DEmail)."', '".DBEscape($NEmail)."', '".DBEscape($PEmail)."', $dadd, $nadd, $padd)";
	$oDB->Query($strSQL);

	$ret["Score"]=$Score;
	
	//var_dump($ret);
	return($ret);
}	


// must load this information regardless of weather we are are recording or scoring
$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
if(count($arr))
{
	list($RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
}

$strSQL="SELECT * FROM ars_rallye_cells WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("F, X, Y, O, Name, Value");
foreach($arr as $data)
{
	list($F, $X, $Y, $O, $Name, $Value) = $data;
	$Cells[$F][$X][$Y][$O] = $Name;	
	$Values[$F][$X][$Y][$O] = $Value;	
}


// load combos
$strSQL="SELECT * FROM ars_rallye_impcombo WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeID, ICID, Points, Trigger");
$IC = array();
foreach($arr as $data)
{
	list($RallyeID, $ICID, $Value, $Trigger) = $data;
	$IC[$ICID]["V"] = intval($Value);
	$IC[$ICID]["T"] = intval($Trigger);
	$IC[$ICID]["G"] = array();

	$strSQL="SELECT * FROM ars_rallye_impcombo_elements WHERE RallyeID = $RallyeID AND ICID = $ICID ORDER BY F, X, Y, O";
	$oDB->Query($strSQL);
	$arr2 = $oDB->GetRecordArray("ICID, F, X, Y, O");
	//var_dump($arr2);
	foreach($arr2 as $data)
	{	
		list($ICID, $F, $X, $Y, $O) = $data; // the value is stored with each element of the IC just so I don't need to tables because of one lousy column
		
		$IC[$ICID]["G"][]=array($F,$X,$Y,$O);
		
	}
	//var_dump($IC);
	
	$strDisp = "";
	$sep="";
	
	foreach($IC[$ICID]["G"] as $data)
	{
		list($F,$X,$Y,$O) = $data;

		$strDisp .= $sep.$Cells[$F][$X][$Y][$O];
		$sep = ", ";		
	}
	
	$strDisp .= " (On ".$IC[$ICID]["T"].")";
	$ICDisp[$ICID] .= $strDisp;
}



if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "continue scoring":
			redirect("score.php?RallyeID=$RallyeID&TestMode=$TestMode");
			break;

		case "delete this car":
			$CarNumber = CHTTPVars::GetValue("CarNumber");
			$strSQL="DELETE FROM ars_scoresheet WHERE CarNumber=$CarNumber AND RallyeID=$RallyeID";
			$oDB->Query($strSQL);
			$strSQL="DELETE FROM ars_scoresheet_elements WHERE CarNumber=$CarNumber AND RallyeID=$RallyeID";
			$oDB->Query($strSQL);
			redirect("score.php?RallyeID=$RallyeID&TestMode=$TestMode");
			break;

		case "recall":
			$RallyeID = CHTTPVars::GetValue("RallyeID");
			$CarNumber = CHTTPVars::GetValue("CarNumber");
			redirect("score.php?RallyeID=$RallyeID&TestMode=$TestMode&CarNumber=$CarNumber&action=recall");
			break;

		case "score":
			// Extract scoresheet from POST
			
			$CarNumber = intval(CHTTPVars::GetValue("CarNumber"));
			$CarClass = intval(CHTTPVars::GetValue("CarClass"));
			$IsTRC = intval(CHTTPVars::GetValue("IsTRC"));
			$DriverName = CHTTPVars::GetValue("DriverName");
			$NavgtrName = CHTTPVars::GetValue("NavgtrName");
			$PasngrName = CHTTPVars::GetValue("PasngrName");
			if(IntVal(CHTTPVars::GetValue("DClubCB")))
				$DClub = "TRC";
			else
				$DClub = trim(CHTTPVars::GetValue("DClub"));

			if(IntVal(CHTTPVars::GetValue("NClubCB")))
				$NClub = "TRC";
			else
				$NClub = trim(CHTTPVars::GetValue("NClub"));

			if(IntVal(CHTTPVars::GetValue("PClubCB")))
				$PClub = "TRC";
			else
				$PClub = trim(CHTTPVars::GetValue("PClub"));

			$DEmail = trim(CHTTPVars::GetValue("DEmail"));
			$NEmail = trim(CHTTPVars::GetValue("NEmail"));
			$PEmail = trim(CHTTPVars::GetValue("PEmail"));

			$dadd = intval(CHTTPVars::GetValue("DADD"));
			$nadd = intval(CHTTPVars::GetValue("NADD"));
			$padd = intval(CHTTPVars::GetValue("PADD"));
			//var_dump($PasngrName);
			
			
			$Have = CHTTPVars::GetValue("Have");
			$arrBreakDown = DoScore($RallyeID, $CarClass, $CarNumber, $Have, $DriverName, $NavgtrName, $PasngrName, $DClub, $NClub, $PClub, $DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd);
			$BaseScore = $arrBreakDown['BaseScore'];
			$TopScore  = floatval(@$arrBreakDown['Top']);
			$CMScore  = floatval(@$arrBreakDown['CM']);
			$CPScore  = floatval(@$arrBreakDown['CP']);
			$BotScore  = floatval(@$arrBreakDown['Bot']);
			$FinishPoints  = floatval(@$arrBreakDown['Fin']);
			$PreComboScore = $arrBreakDown['PreComboScore'];
			$ProtestScore = $arrBreakDown['ProtestScore'];
			$Score = $arrBreakDown['Score'];

			break;
		
		case "rescoreall":
			$strSQL="SELECT CarClass, CarNumber, Driver, Navigator, Passenger, DClub, NClub, PClub, DEmail, NEmail, PEmail, dadd, nadd, padd  FROM ars_scoresheet WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			$arr = $oDB->GetRecordArray(false);
			
			foreach($arr as $data)
			{
				list($CarClass, $CarNumber, $DriverName, $NavgtrName, $PasngrName, $DClub, $NClub, $PClub, $DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd) = $data;
				$CarClass = intval($CarClass);
				$CarNumber = intval($CarNumber);
				$IsTRC = 0;
				
				$strSQL = "SELECT F, X, Y, O FROM ars_scoresheet_elements WHERE RallyeID = $RallyeID AND CarNumber=$CarNumber AND F != 'Combo'";
				$oDB->Query($strSQL);
				$arr2 = $oDB->GetRecordArray(false);
				
				$Have = array();
				foreach($arr2 as $data)
				{
					list($f,$x,$y,$o) = $data;
					$Have[$f][$x][$y][$o] = 1;
				}
				
				$x=DoScore($RallyeID, $CarClass, $CarNumber, $Have, $DriverName, $NavgtrName, $PasngrName, $DClub, $NClub, $PClub, $DEmail, $NEmail, $PEmail, $dadd, $nadd, $padd);
				//var_dump($x);
			}
		
			redirect("score.php?RallyeID=$RallyeID&TestMode=$TestMode");
			break;
		
		default:
			if(substr(strtolower($action),0,6) == "recall")
			{
				$CarNumber = intval(CHTTPVars::GetValue("CarNumber"));
				redirect("score.php?RallyeID=$RallyeID&TestMode=$TestMode&action=recall&CarNumber=$CarNumber");
			}

			redirect("error.php");
			break;
	}
}



?>
<HTML>
	<HEAD>
		<TITLE>ARS Scoring</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>
		<TABLE BORDER=0 WIDTH=100% border=0>
			<TR>
				<TD WIDTH=30% ALIGN=LEFT VALIGN=TOP>
					<B><?=$RallyeMaster?></B>
				</TD>

				<TD WIDTH=40% ALIGN=CENTER VALIGN=TOP>
					<B><?=$RallyeName?></B>
				</TD>

				<TD WIDTH=30% ALIGN=RIGHT VALIGN=TOP>
					<B><?=$RallyeDate?></B>
				</TD>
			<TR>
		</TABLE>
		<BR>
		<TABLE WIDTH=100%>
			<TR>
				<TD WIDTH=50%>
					Car #: <?=$CarNumber?><BR>
					Class: <?=$arrClasses[$CarClass]?><BR>
					TRC: <?=($IsTRC?"Yes":"No")?><BR>
				</TD>

				<TD WIDTH=50%>
					Driver Name: <?=$DriverName?> <?=(trim($DClub)!=""?"($DClub)":"")?><BR>
					Navgtr Name: <?=$NavgtrName?> <?=(trim($NClub)!=""?"($NClub)":"")?><BR>
					Pasngr Name: <?=$PasngrName?> <?=(trim($PClub)!=""?"($PClub)":"")?><BR>
				</TD>
			<TR>
		</TABLE>
		
<?
		myprint("<table>");
			if(isset($Cells['Top']))
			{
				myprint("<TR>");
					myprint("<TD ALIGN=RIGHT>");
						myprint("<B>Top Area:");
					myprint("</TD>");

					myprint("<TD ALIGN=RIGHT>");
						myprint($TopScore."pts.");
					myprint("</TD>");
				myprint("</TR>");
			}
	
			if(isset($Cells['CM']))
			{
				myprint("<TR>");
					myprint("<TD ALIGN=RIGHT>");
						myprint("<B>CMs:");
					myprint("</TD>");

					myprint("<TD ALIGN=RIGHT>");
						myprint($CMScore."pts.");
					myprint("</TD>");
				myprint("</TR>");
			}
	
			if(isset($Cells['CP']))
			{
				myprint("<TR>");
					myprint("<TD ALIGN=RIGHT>");
						myprint("<B>CPs:");
					myprint("</TD>");

					myprint("<TD ALIGN=RIGHT>");
						myprint($CPScore."pts.");
					myprint("</TD>");
				myprint("</TR>");
			}
	
			if(isset($Cells['Bot']))
			{
				myprint("<TR>");
					myprint("<TD ALIGN=RIGHT>");
						myprint("<B>Bottom Area:");
					myprint("</TD>");

					myprint("<TD ALIGN=RIGHT>");
						myprint($BotScore."pts.");
					myprint("</TD>");
				myprint("</TR>");
			}

		
			if(@$Values['Fin'][0][0][0] != "")
			{
				myprint("<TR>");
					myprint("<TD ALIGN=RIGHT>");
						myprint("<B>Finish Points:");
					myprint("</TD>");

					myprint("<TD ALIGN=RIGHT>");
						myprint($FinishPoints."pts.");
					myprint("</TD>");
				myprint("</TR>");
			}

			print("<TR>");
				myprint("<TD ALIGN=RIGHT>");
					myprint("<B>Subtotal:");
				myprint("</TD>");

				myprint("<TD ALIGN=RIGHT>");
					myprint($PreComboScore."pts.");
				myprint("</TD>");
			myprint("</TR>");

			if(isset($Score['IC']))
			{
				myprint("<TR>");
					myprint("<TD ALIGN=RIGHT>");
						myprint("<B>Adjustments:");
					myprint("</TD>");
				myprint("</TR>");
	
				foreach($Score['IC'] as $key => $val)
				{
					myprint("<TR>");
						myprint("<TD ALIGN=RIGHT>");
							myprint($key.":");
						myprint("</TD>");
	
						myprint("<TD ALIGN=RIGHT>");
							myprint($val."pts.");
						myprint("</TD>");
					myprint("</TR>");
				}
			}
	
	// Protests
				myprint("<TR>");
					myprint("<TD ALIGN=RIGHT>");
						myprint("<B>Protest:");
					myprint("</TD>");

					myprint("<TD ALIGN=RIGHT>");
						myprint($ProtestScore."pts.");
					myprint("</TD>");
				myprint("</TR>");
?>
		<TR>
			<TD ALIGN=RIGHT>
				<B>Final Score:</B>
			</TD>

			<TD ALIGN=RIGHT>
				<B><?=$BaseScore+$ProtestScore?>pts.</B>
			</TD>
		</TR>
	</TABLE>
	<BR>

	<FORM METHOD=POST ACTION=store.php>
	<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
	<INPUT TYPE=HIDDEN NAME=TestMode VALUE=<?=$TestMode?>>
	<INPUT TYPE=HIDDEN NAME=CarNumber VALUE=<?=$CarNumber?>>

		<TABLE WIDTH=100%>
			<TR>
				<TD WIDTH=33% ALIGN=LEFT>
					<INPUT TYPE=SUBMIT NAME=action VALUE="Continue Scoring">
				</TD>

				<TD WIDTH=34% ALIGN=LEFT>
					<INPUT TYPE=SUBMIT NAME=action VALUE="Recall Car <?=$CarNumber?>">
				</TD>

				<TD WIDTH=33% ALIGN=RIGHT>
					<INPUT TYPE=SUBMIT NAME=action VALUE="DELETE THIS CAR">
				</TD>
			</TR>
		</TABLE>
	</FORM>
	<BR><BR>
<?
//$sub=1;
//include("scoreboard.php");
//myprint("<BR>");
//include("stats1.php");
?>	


	</BODY>
</HTML>
