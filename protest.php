<?
include "config.php";

$ThisStep=48;

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


// this should really be databased
$arrClasses[-1] = "Any";
$arrClasses[1] = "First Timer";
$arrClasses[2] = "Beginner";
$arrClasses[3] = "Novice";
$arrClasses[4] = "Senior";
$arrClasses[5] = "Expert";
$arrClasses[6] = "Master";

$arrHaveNotHave[0] = "";
$arrHaveNotHave[1] = "X"; //"Having";
$arrHaveNotHave[-1] = "O"; //"NOT Having";

//$arrHaveNotHave[1] = "X"; //"Having";
//$arrHaveNotHave[-1] = "O"; //"NOT Having";

$arrHaveNotHaveStyle[0] = "";
$arrHaveNotHaveStyle[1] = "have";
$arrHaveNotHaveStyle[-1] = "nothave";

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
global $IC;
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
	$arr2 = $oDB->GetRecordArray("RallyeID, ICID, F, X, Y, O");
	//var_dump($arr2);
	foreach($arr2 as $data)
	{	
		list($RallyeID, $ICID, $F, $X, $Y, $O) = $data; // the value is stored with each element of the IC just so I don't need to tables because of one lousy column
		
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
	
	$strDisp .= " (On&nbsp;".$IC[$ICID]["T"].")";
	$ICDisp[$ICID] .= $strDisp;
}


if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "delete":
			if(!CHTTPVars::IsEmpty("ProtestID"))
			{
				// if a protest ID was passed in, delete all record of it.
				$ProtestID = CHTTPVars::GetValue("ProtestID");
				$strSQL="DELETE FROM ars_protest WHERE ProtestID = $ProtestID";
				$oDB->Query($strSQL);
				$strSQL="DELETE FROM ars_protest_elements WHERE ProtestID = $ProtestID";
				$oDB->Query($strSQL);
			}
			// call score all
			redirect("store.php?RallyeID=$RallyeID&action=rescoreall&TestMode=$TestMode");
			break;

		case "grant":
			// to grant the protest we need to store it in the database and then call score all

			//var_dump($_REQUEST);

			$CarNumber = -1;
			if(!CHTTPVars::IsEmpty("CarNumber"))
			{
				$CarNumber = CHTTPVars::GetValue("CarNumber");
				$arrCarNumbers = explode(",",$CarNumber);
			}

			$CarClass=intval(CHTTPVars::GetValue("CarClass"));
			$ProtestPoints=floatval(CHTTPVars::GetValue("ProtestPoints"));
			$Reason=CHTTPVars::GetValue("Reason");

			$arrHave=array();
			if(!CHTTPVars::IsEmpty("Have"))
				$arrHave=CHTTPVars::GetValue("Have");

			if(!CHTTPVars::IsEmpty("ProtestID"))
			{
				// if a protest ID was passed in, delete all record of it.
				$ProtestID = CHTTPVars::GetValue("ProtestID");
				$strSQL="DELETE FROM ars_protest WHERE ProtestID = $ProtestID";
				$oDB->Query($strSQL);
				$strSQL="DELETE FROM ars_protest_elements WHERE ProtestID = $ProtestID";
				$oDB->Query($strSQL);
			}

			// create a new protest ID
			$strSQL = "INSERT INTO ars_protest (RallyeID,Points,Reason) VALUES($RallyeID,$ProtestPoints,'".DBEscape($Reason)."');";
			$oDB->Query($strSQL);
			$ProtestID = $oDB->GetLastInsertID();

			// store the elements of the protest into the table
			// store the cars
			foreach($arrCarNumbers as $CarNumber)
			{
				$CarNumber = intval(trim($CarNumber));
				if($CarNumber > 0)
				{
					$strSQL="INSERT INTO ars_protest_elements (ProtestID,CarNumber) VALUES($ProtestID,$CarNumber)";
					$oDB->Query($strSQL);
				}
			}
			// store the class
			if($CarClass > 0)
			{
					$strSQL="INSERT INTO ars_protest_elements (ProtestID,Class) VALUES($ProtestID,$CarClass)";
					$oDB->Query($strSQL);
			}
			// store the CMs
			foreach($arrHave as $F => $arr1)
			{
				foreach($arr1 as $X => $arr2)
				{
					foreach($arr2 as $Y => $arr3)
					{
						foreach($arr3 as $O => $have)
						{
							if(intval($have))
							{
								$strSQL="INSERT INTO ars_protest_elements (ProtestID,F,X,Y,O,Have) VALUES($ProtestID,'$F',$X,$Y,$O,$have)";
								$oDB->Query($strSQL);
							}
						}
					}
				}
			}
			// call score all
			redirect("store.php?RallyeID=$RallyeID&action=rescoreall&TestMode=$TestMode");
			break;

		default:
			redirect("error.php");
	}
}



?>
<HTML>
	<HEAD>
		<TITLE>ARS - Protest</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
		
		<SCRIPT  LANGUAGE=JavaScript>
			function Toggle(elementid)
			{
				element = document.getElementById(elementid);
				if(element)
					element.checked = !element.checked;
			}
		</SCRIPT>
	</HEAD>
	
	<BODY>
<?
		if($TestMode)
			include("configmenu.php");
		include("scoremenu.php");

?>
		<FIELDSET>
			<LEGEND><B>Instructions</B></LEGEND>
		This form allows you to enter protests. Follow these steps for entry:
		<OL><LI> Enter the number of points to be granted.<BR>
		<LI> Enter the car numbers, separated by commas, that are affected by this protes. Leave the "Car #" space blank if car number does not matter. ie. in the event of a class wide protest or tossed gimmick<BR>
		<LI> Select the class that is affected by this protest, set it to "Any" if class does not matter<BR>
		<LI> Select the items that are necessary for this protest. For example: If having A is required select A's check box. If NOT having B is required select B's UnChecked box. If an item doesn't play into the protest, leave its selection blank.<BR>
		<LI> MOST IMPORTANT, fill in the reason for this protest<BR>
		<LI> Press the "Grant" button<BR>
		</OL>
		</FIELDSET>
		<BR>

		<FORM ID=scoreform METHOD=POST ACTION=protest.php>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		<INPUT TYPE=HIDDEN NAME=TestMode VALUE=<?=$TestMode?>>

		<TABLE WIDTH="100%">
			<TR>
				<TD WIDTH="30%" ALIGN=LEFT VALIGN=TOP>
					<B><?=$RallyeMaster?></B>
				</TD>

				<TD WIDTH="40%" ALIGN=CENTER VALIGN=TOP>
					<B><?=$RallyeName?></B>
				</TD>

				<TD WIDTH="30%" ALIGN=RIGHT VALIGN=TOP>
					<B><?=$RallyeDate?></B>
				</TD>
			</TR>
		</TABLE>
		<BR>

		<TABLE WIDTH="100%">
			<TR>
				<TD VALIGN=TOP WIDTH="40%">
					<INPUT TYPE=SUBMIT NAME=action ID=Score VALUE=Grant> 
					<INPUT TYPE=TEXT NAME=ProtestPoints ID=ProtestPoints VALUE="" SIZE=4> points<BR>
					Car # <INPUT TYPE=TEXT NAME=CarNumber ID=CarNumber VALUE="<?=@$CarNumber?>" SIZE=4><BR>
					in <?SelectOption("CarClass",$arrClasses,@$CarClass);?> class<BR>
				</TD>

				<TD VALIGN=TOP ALIGN=LEFT WIDTH="60%">
					for this reason:<BR>
					<TEXTAREA COLS=80 ROWS=3 NAME=Reason></TEXTAREA>
				</TD>
			</TR>
		</TABLE>

		provided:
		<TABLE BORDER=0 WIDTH="100%">
			<TR>
				<TD VALIGN=TOP COLSPAN=2>
					<DIV ID=topdiv>
<?
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=\"100%\">");
						$i=0;
						for($y = 0; $y < $TopY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $TopX; $x++)
							{
								$FrameColor = "black";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									//myprint("<LABEL STYLE=\"background-color: pink; width: 100%; height: 100%\" FOR=\"Have_Top_".$x."_".$y."_0\">"); // putting the lable here only seems to work for opera
									myprint("<TABLE WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>"); //CONSIDER: setting the on click function only if the value of _0 is not null 
												myprint("<DIV ID=Top_".$x."_".$y." ");
												if(trim($Values['Top'][$x][$y][0]) !== "")
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_Top_".$x."_".$y."_0');\"");
												print(">"); // label did not seem to work for IE/Firefox, this may be something that we enable just for IE/Firefox
												for($i = 0; $i < count($Cells['Top'][$x][$y]); $i++)
												{	
													SelectOption("Have[Top][".$x."][".$y."][".$i."]",$arrHaveNotHave,@$Have['Top'][$x][$y][$i],false,6,"havenothave",$arrHaveNotHaveStyle);
													
													myprint(str_replace(" ","&nbsp;",HTMLEscape($Cells['Top'][$x][$y][$i]))); 
													
													if(trim($Values['Top'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['Top'][$x][$y][$i]."pts."); 
													}
													if(trim($Values['Top'][$x][$y][$i]) !== "" || ($Cells['Top'][$x][$y][$i]) !== "")
														myprint("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
									//myprint("</LABEL>"); putting the label here only seems to work for Opera
								myprint("</TD>");
							}
							myprint("</TR>");
						}
						myprint("</TABLE>");
?>						
					</DIV>
				</TD>
			</TR>

			<TR>
				<TD VALIGN=TOP>
					<DIV ID=cmdiv>
<?
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=\"100%\">");
						$i=0;
						for($y = 0; $y < $CMY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $CMX; $x++)
							{
								$FrameColor = "black";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\">");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CM_".$x."_".$y." ");
												if(trim($Values['CM'][$x][$y][0]) !== "")
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_CM_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['CM'][$x][$y]); $i++)
												{	
													SelectOption("Have[CM][".$x."][".$y."][".$i."]",$arrHaveNotHave,@$Have['CM'][$x][$y][$i],false,6,"havenothave",$arrHaveNotHaveStyle);
													
													myprint(str_replace(" ","&nbsp;",HTMLEscape($Cells['CM'][$x][$y][$i])));
													
													if(trim($Values['CM'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['CM'][$x][$y][$i]."pts."); 
													}
													if(trim($Values['CM'][$x][$y][$i]) !== "" || ($Cells['CM'][$x][$y][$i]) !== "")
														myprint("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
								myprint("</LABEL></TD>");
							}
							myprint("</TR>");
						}
						myprint("</TABLE>");
?>						
					</DIV>
				</TD>
<?
if($CPLoc == "R")
{
?>
				<TD VALIGN=TOP WIDTH="<?=(15*$CPX)?>%">
					<DIV ID=cprdiv>
<?
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=\"100%\">");
						$i=0;
						for($y = 0; $y < $CPY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $CPX; $x++)
							{
								$FrameColor = "black";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CP_".$x."_".$y." ");
												if(trim($Values['CP'][$x][$y][0]) !== "")
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_CP_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['CP'][$x][$y]); $i++)
												{	
													SelectOption("Have[CP][".$x."][".$y."][".$i."]",$arrHaveNotHave,@$Have['CP'][$x][$y][$i],false,6,"havenothave",$arrHaveNotHaveStyle);

													myprint(str_replace(" ","&nbsp;",HTMLEscape($Cells['CP'][$x][$y][$i]))); 

													if(trim($Values['CP'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['CP'][$x][$y][$i]."pts."); 
													}
													if(trim($Values['CP'][$x][$y][$i]) !== "" || ($Cells['CP'][$x][$y][$i]) !== "")
														myprint("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
								myprint("</LABEL></TD>");
							}
							myprint("</TR>");
						}
						myprint("</TABLE>");
?>						
					</DIV>
				</TD>
<?
}
?>
			</TR>
<?
// end cp's On the right
//else cp's on the bottom
if($CPLoc == "B")
{
?>
			<TR>
				<TD VALIGN=TOP COLSPAN=2>
					<DIV ID=cpbdiv>
<?
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=\"100%\">");
						$i=0;
						for($y = 0; $y < $CPY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $CPX; $x++)
							{
								$FrameColor = "black";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CP_".$x."_".$y." ");
												if(trim($Values['CP'][$x][$y][0]) !== "")
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_CP_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['CP'][$x][$y]); $i++)
												{	
													SelectOption("Have[CP][".$x."][".$y."][".$i."]",$arrHaveNotHave,@$Have['CP'][$x][$y][$i],false,6,"havenothave",$arrHaveNotHaveStyle);

													myprint(str_replace(" ","&nbsp;",HTMLEscape($Cells['CP'][$x][$y][$i]))); 

													if(trim($Values['CP'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['CP'][$x][$y][$i]."pts."); 
													}
													if(trim($Values['CP'][$x][$y][$i]) !== "" || ($Cells['CP'][$x][$y][$i]) !== "")
														myprint("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
								myprint("</LABEL></TD>");
							}
							myprint("</TR>");
						}
						myprint("</TABLE>");
?>						
					</DIV>
				</TD>
			</TR>
<?
// end cp's On the bottom
}
?>
			<TR>
				<TD VALIGN=TOP COLSPAN=2>
					<DIV ID=botdiv>
<?
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=\"100%\">");
						$i=0;
						for($y = 0; $y < $BotY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $BotX; $x++)
							{
								$FrameColor = "black";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=Bot_".$x."_".$y." ");
												if(trim($Values['Bot'][$x][$y][0]) !== "")
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_Bot_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['Bot'][$x][$y]); $i++)
												{	
													SelectOption("Have[Bot][".$x."][".$y."][".$i."]",$arrHaveNotHave,@$Have['Bot'][$x][$y][$i],false,6,"havenothave",$arrHaveNotHaveStyle);

													myprint(str_replace(" ","&nbsp;",HTMLEscape($Cells['Bot'][$x][$y][$i]))); 
													
													if(trim($Values['Bot'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['Bot'][$x][$y][$i]."pts."); 
													}
													if(trim($Values['Bot'][$x][$y][$i]) !== "" || ($Cells['Bot'][$x][$y][$i]) !== "")
														myprint("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
								myprint("</LABEL></TD>");
							}
							myprint("</TR>");
						}
						myprint("</TABLE>");
?>						
					</DIV>
				</TD>
			</TR>
		</TABLE>
		
		</FORM>
	</BODY>
</HTML>
