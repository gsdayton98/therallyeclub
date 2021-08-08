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


function IsIC($F,$X,$Y)
{
	global $IC;
	
	foreach($IC as $ICID => $ICData)
	{
		foreach($ICData["G"] as $data)
		{
			list($f,$x,$y) = $data; // O is not needed since it is the cell that is being highlighted
			
			if($f == $F && $x == $X && $y == $Y)
				return true;
		}
	}
	
	return false;
}

// Since in HTML rows come first, we have a funky order

// this should really be databased
$arrClasses[1] = "First Timer";
$arrClasses[2] = "Beginner";
$arrClasses[3] = "Novice";
$arrClasses[4] = "Senior";
$arrClasses[5] = "Expert";
$arrClasses[6] = "Master";

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
	@$ICDisp[$ICID] .= $strDisp;
}


if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(substr(strtolower($action),0,6))
	{
		case "recall":
			//TODO: redirect to score with the CarNumber provided
			$CarNumber = CHTTPVars::GetValue("CarNumber");
			$strSQL="SELECT CarClass, Driver, Navigator, Passenger, DClub, NClub, PClub, DEmail, NEmail, PEmail, dadd, nadd, padd FROM ars_scoresheet Where RallyeID = $RallyeID AND CarNumber = $CarNumber";
			$oDB->Query($strSQL);
			$arr = $oDB->GetRecordArray(false);
			if(count($arr))
			{
				list($CarClass,$DriverName,$NavgtrName,$PasngrName,$DClub,$NClub,$PClub, $DEmail, $NEmail, $PEmail,$dadd,$nadd,$padd) = $arr[0];
			}
			

			$strSQL="SELECT F, X, Y, O, Have FROM ars_scoresheet_elements WHERE RallyeID = $RallyeID AND CarNumber = $CarNumber";
			$oDB->Query($strSQL);
			$arr = $oDB->GetRecordArray(false);
			foreach($arr as $data)
			{
				list($F, $X, $Y, $O, $IHave) = $data;
				$IHave = intval($IHave);
				
				if($IHave)
				{
					$Have[$F][$X][$Y][$O] = $IHave;
				}
			}
			break;

		
		default:
			redirect("error.php");
	}
}



?>
<HTML>
	<HEAD>
		<TITLE>ARS - Score</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
		
		<SCRIPT  LANGUAGE=JavaScript>
			function Toggle(elementid)
			{
				element = document.getElementById(elementid);
				if(element)
					element.checked = !element.checked;
			}
			
			function EnableScore()
			{
				Car = document.getElementById("CarNumber");
				ScoreButton = document.getElementById("Score");
				RecallButton = document.getElementById("Recall");
				
				if(parseInt(Car.value))
				{
					ScoreButton.disabled = false;
					RecallButton.disabled = false;
				}
				else
				{
					ScoreButton.disabled = true;
					RecallButton.disabled = true;
				}

			}
		</SCRIPT>
	</HEAD>
	
	<BODY>
<?
		if($TestMode)
			include("configmenu.php");
		include("scoremenu.php");

?>
		<FORM ID=scoreform METHOD=POST ACTION=store.php>
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
				<TD VALIGN=BOTTOM>
					Car #: <INPUT TYPE=TEXT NAME=CarNumber ID=CarNumber VALUE="<?=@$CarNumber?>" OnKeyUp="EnableScore()" SIZE=4> <INPUT TYPE=SUBMIT NAME=action ID=Recall VALUE=Recall><BR>
					Class: <?SelectOption("CarClass",$arrClasses,@$CarClass);?><BR>
				</TD>

				<TD VALIGN=BOTTOM>
<?
					$CHECKED="";
					if($DClub == "TRC")
					{
						$CHECKED="CHECKED";
						$DClub = "";
					}
?>
					Driver Name: <INPUT TYPE=TEXT NAME=DriverName VALUE="<?=$DriverName?>" SIZE=25> TRC:<INPUT TYPE=CHECKBOX VALUE=1 <?=$CHECKED?> NAME=DClubCB> Other:<INPUT TYPE=TEXT NAME=DClub VALUE="<?=$DClub?>" SIZE=10> Email: <INPUT TYPE=TEXT NAME=DEmail VALUE="<?=$DEmail?>" SIZE=30><INPUT TYPE=CHECKBOX VALUE=1 <?=intval($dadd)?"CHECKED":""?> NAME=DADD><BR>
<?
					$CHECKED="";
					if($NClub == "TRC")
					{
						$CHECKED="CHECKED";
						$NClub = "";
					}
?>
					Navgtr Name: <INPUT TYPE=TEXT NAME=NavgtrName VALUE="<?=$NavgtrName?>" SIZE=25> TRC:<INPUT TYPE=CHECKBOX VALUE=1 <?=$CHECKED?> NAME=NClubCB> Other:<INPUT TYPE=TEXT NAME=NClub VALUE="<?=$NClub?>" SIZE=10> Email: <INPUT TYPE=TEXT NAME=NEmail VALUE="<?=$NEmail?>" SIZE=30><INPUT TYPE=CHECKBOX VALUE=1 <?=intval($nadd)?"CHECKED":""?> NAME=NADD><BR>

<?
					$CHECKED="";
					if($PClub == "TRC")
					{
						$CHECKED="CHECKED";
						$PClub = "";
					}
?>
					Pasngr Name: <INPUT TYPE=TEXT NAME=PasngrName VALUE="<?=$PasngrName?>" SIZE=25> TRC:<INPUT TYPE=CHECKBOX VALUE=1 <?=$CHECKED?> NAME=PClubCB> Other:<INPUT TYPE=TEXT NAME=PClub VALUE="<?=$PClub?>" SIZE=10> Email: <INPUT TYPE=TEXT NAME=PEmail VALUE="<?=$PEmail?>" SIZE=30><INPUT TYPE=CHECKBOX VALUE=1 <?=intval($padd)?"CHECKED":""?> NAME=PADD><BR>
				</TD>
			</TR>
		</TABLE>

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
								if(IsIC("Top", $x, $y)) $FrameColor = "red";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									//myprint("<LABEL STYLE=\"background-color: pink; width: 100%; height: 100%\" FOR=\"Have_Top_".$x."_".$y."_0\">"); // putting the lable here only seems to work for opera
									myprint("<TABLE WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>"); //CONSIDER: setting the on click function only if the value of _0 is not null 
												myprint("<DIV ID=Top_".$x."_".$y." ");
												if(count(@$Values['Top'][$x][$y]) == 1)
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_Top_".$x."_".$y."_0');\"");
												print(">"); // label did not seem to work for IE/Firefox, this may be something that we enable just for IE/Firefox
												for($i = 0; $i < count($Cells['Top'][$x][$y]); $i++)
												{	
													if(trim($Values['Top'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$Have['Top'][$x][$y][$i])
															$CHECKED="CHECKED";
																		
														myprint("<INPUT NAME=Have[Top][".$x."][".$y."][".$i."] ID=\"Have_Top_".$x."_".$y."_".$i."\" VALUE=1 $CHECKED TYPE=CHECKBOX");
														if($i == 0 && count(@$Values['Top'][$x][$y]) == 1) // added to allow the check box to click
														{
															print(" OnClick=\"Toggle('Have_Top_".$x."_".$y."_0');\"");
														}
														print(">");
													}
													else if($Cells['Top'][$x][$y][$i] !== "")
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}
													
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
								if(IsIC("CM", $x, $y)) $FrameColor = "red";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\">");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CM_".$x."_".$y." ");
												if(count(@$Values['CM'][$x][$y]) == 1)
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_CM_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['CM'][$x][$y]); $i++)
												{	
													if(trim($Values['CM'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$Have['CM'][$x][$y][$i])
															$CHECKED="CHECKED";
															
														print("<INPUT NAME=Have[CM][".$x."][".$y."][".$i."] ID=Have_CM_".$x."_".$y."_".$i."  VALUE=1 ");
														print("$CHECKED TYPE=CHECKBOX");
														if($i == 0 && count(@$Values['CM'][$x][$y]) == 1) // added to allow the check box to click
														{
															print(" OnClick=\"Toggle('Have_CM_".$x."_".$y."_0');\"");
														}
														print(">");
													}
													else if($Cells['CM'][$x][$y][$i] !== "")
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}
													
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
								if(IsIC("CP", $x, $y)) $FrameColor = "red";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CP_".$x."_".$y." ");
												if(count(@$Values['CP'][$x][$y]) == 1)
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_CP_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['CP'][$x][$y]); $i++)
												{	
													if(trim($Values['CP'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$Have['CP'][$x][$y][$i])
															$CHECKED="CHECKED";
															
														myprint("<INPUT NAME=Have[CP][".$x."][".$y."][".$i."] ID=Have_CP_".$x."_".$y."_".$i." VALUE=1 $CHECKED TYPE=CHECKBOX");
														if($i == 0 && count(@$Values['CP'][$x][$y]) == 1) // added to allow the check box to click
														{
															print(" OnClick=\"Toggle('Have_CP_".$x."_".$y."_0');\"");
														}
														print(">");
													}
													else if($Cells['CP'][$x][$y][$i] !== "")
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}

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
								if(IsIC("CP", $x, $y)) $FrameColor = "red";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CP_".$x."_".$y." ");
												if(count(@$Values['CP'][$x][$y]) == 1)
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_CP_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['CP'][$x][$y]); $i++)
												{	
													if(trim($Values['CP'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$Have['CP'][$x][$y][$i])
															$CHECKED="CHECKED";
															
														myprint("<INPUT NAME=Have[CP][".$x."][".$y."][".$i."] ID=Have_CP_".$x."_".$y."_".$i." VALUE=1 $CHECKED TYPE=CHECKBOX");
														if($i == 0 && count(@$Values['CP'][$x][$y]) == 1) // added to allow the check box to click
														{
															print(" OnClick=\"Toggle('Have_CP_".$x."_".$y."_0');\"");
														}
														print(">");
													}
													else if($Cells['CP'][$x][$y][$i] !== "")
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}

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
								if(IsIC("Bot", $x, $y)) $FrameColor = "red";
								myprint("<TD STYLE=\"border: 1px solid $FrameColor;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=\"100%\">");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=Bot_".$x."_".$y." ");
												if(count(@$Values['Bot'][$x][$y]) == 1)
													print("STYLE=\"cursor: hand; width:100%; height:100%;\" OnClick=\"Toggle('Have_Bot_".$x."_".$y."_0');\"");
												print(">");
												for($i = 0; $i < count($Cells['Bot'][$x][$y]); $i++)
												{	
													if(trim($Values['Bot'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$Have['Bot'][$x][$y][$i])
															$CHECKED="CHECKED";
															
														myprint("<INPUT NAME=Have[Bot][".$x."][".$y."][".$i."] ID=Have_Bot_".$x."_".$y."_".$i." VALUE=1 $CHECKED TYPE=CHECKBOX");
														if($i == 0 && count(@$Values['Bot'][$x][$y]) == 1) // added to allow the check box to click
														{
															print(" OnClick=\"Toggle('Have_Bot_".$x."_".$y."_0');\"");
														}
														print(">");
													}
													else if($Cells['Bot'][$x][$y][$i] !== "")
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}

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
		
		<TABLE>
			<TR>
				<TD  VALIGN=TOP>
					<CENTER>
						<INPUT TYPE=SUBMIT NAME=action ID=Score VALUE=Score>
					</CENTER>
				</TD>
			</TR>
		</TABLE>
		</FORM>

<TABLE>
	<TR>
		<TD VALIGN=TOP>
<?
$sub=1;
include("scoreboard.php");
?>
		</TD>
		
		<TD VALIGN=TOP>
<?
				if(count($ICDisp))
				{
					myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=\"100%\">");
						myprint("<TR>");
							myprint("<TD STYLE=\"border: 1px solid black;\" VALIGN=TOP>");
								// combo box
								myprint("<TABLE WIDTH=\"100%\" cellspacing=0 marginheight=0 marginwidth=0 leftmargin=0 topmargin=0>");
									myprint("<TR style=\"background: black;\">");
										myprint("<TD COLSPAN=2 style=\"color: white; background: black;\">");
											myprint("<B>Combination Legend:</B><BR>");
										myprint("</TD>");
									myprint("</TR>");
									
									myprint("<TR style=\"background: black;\">");
										myprint("<TD style=\"color: white;\">");
											myprint("<B>Element</B><BR>");
										myprint("</TD>");

										myprint("<TD ALIGN=RIGHT style=\"color: white;\">");
											myprint("<B>Value</B><BR>");
										myprint("</TD>");
									myprint("</TR>");
									$t=0;
									foreach($ICDisp as $ICID => $Disp)		
									{
										myprint("<TR class=tr$t>");
											myprint("<TD ALIGN=LEFT VALIGN=BOTTOM>");
												myprint($Disp.":");
											myprint("</TD>");

											myprint("<TD ALIGN=RIGHT VALIGN=BOTTOM>");
												myprint($IC[$ICID]["V"]);
											myprint("</TD>");
										myprint("</TR>");
										$t=intval(!$t);
									}
								myprint("</TABLE>");
							myprint("</TD>");
						myprint("</TR>");
					myprint("</TABLE>");
				}
?>
		</TD>
	</TR>
</TABLE>
<?


myprint("<BR>");
include("protestboard.php");
myprint("<BR>");
include("stats1.php");
?>	

		<SCRIPT  LANGUAGE=JavaScript>
			EnableScore();
		</SCRIPT>


	</BODY>
</HTML>
