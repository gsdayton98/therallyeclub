<?
include "config.php";

$ThisStep = 8;
$RallyeID = CHTTPVars::GetValue("RallyeID");
if(!PasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");

function myprint($str)
{
	print($str."\n");
}

// Since in HTML rows come first, we have a funky order

if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "recall":
			$RecallICID = CHTTPVars::GetValue("RecallICID");
			$RecalledICID = $RecallICID;
			
			$strSQL="SELECT * FROM ars_rallye_impcombo_elements WHERE RallyeID = $RallyeID AND ICID = $RecallICID";
			$oDB->Query($strSQL);
			$arr2 = $oDB->GetRecordArray("ICID, F, X, Y, O");
			//var_dump($arr2);
			foreach($arr2 as $data)
			{	
				list($ICID, $F, $X, $Y, $O) = $data; // the value is stored with each element of the IC just so I don't need to tables because of one lousy column
				
				$IC[$F][$X][$Y][$O] = 1;	
			}
			break;

		case "store":
			if(!CHTTPVars::IsEmpty("RecalledICID"))
			{
				$RecalledICID = CHTTPVars::GetValue("RecalledICID");
				$strSQL="DELETE FROM ars_rallye_impcombo WHERE ICID = $RecalledICID";
				$oDB->Query($strSQL);

				$strSQL="DELETE FROM ars_rallye_impcombo_elements WHERE ICID = $RecalledICID";
				$oDB->Query($strSQL);
			}

		case "register":
			$IC = CHTTPVars::GetValue("IC");
			if($IC == "") $IC = array();
			
			if(!count($IC))
				break; // nothing to see here, move along.
			
			
			$Points = intval(CHTTPVars::GetValue("Points"));
			$Trigger = intval(CHTTPVars::GetValue("Trigger"));

			$strSQL="INSERT INTO ars_rallye_impcombo (RallyeID, Points, `Trigger`) VALUES($RallyeID, $Points, $Trigger)";
			$oDB->Query($strSQL);

			// get insert id
			$ICID = $oDB->GetLastInsertID();
			
			// delete existing elemets for this ICID
			$strSQL="DELETE FROM ars_rallye_impcombo_elements WHERE RallyeID = $RallyeID AND ICID = $ICID";
			$oDB->Query($strSQL);
			
			foreach($IC as $Section => $arrSection)
			{
				foreach($arrSection as $x => $arr1)
				{
					foreach($arr1 as $y => $arr2)
					{
						foreach($arr2 as $o => $data)
						{
							// find out what the old value was
							$value = $Values[$Section][$x][$y][$o];
							if(trim($value) === "") $value = 'NULL';
							
							$strSQL="INSERT ars_rallye_impcombo_elements (RallyeID, ICID, F, X, Y, O) VALUES($RallyeID, $ICID, '$Section', $x, $y, $o)";
							$oDB->Query($strSQL);
						}
					}	
				}
			}

			redirect("impcombo.php?RallyeID=$RallyeID");
			break;
		
		case "done":
			SetStep($ThisStep,$RallyeID);

			redirect("configdone.php?RallyeID=$RallyeID");
			break;

			
		default:
			redirect("error.php");
	}
}


$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeID, RallyeName, RallyeDate, RallyeMaster, TopX, TopY, CMX, CMY, CPX, CPY, BotX, BotY, CPLoc");
if(count($arr))
{
	list($RallyeID, $RallyeName, $RallyeDate, $RallyeMaster, $TopX, $TopY, $CMX, $CMY, $CPX, $CPY, $BotX, $BotY, $CPLoc) = $arr[0];
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

$strSQL="SELECT * FROM ars_rallye_impcombo WHERE RallyeID = $RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray("RallyeID, ICID, Points, Trigger");
foreach($arr as $data)
{
	list($RallyeID, $ICID, $Points, $Trigger) = $data;
	$ICs[$ICID]["V"] = intval($Points);
	$ICs[$ICID]["T"] = intval($Trigger);
	$ICs[$ICID]["G"] = array();

	$strSQL="SELECT * FROM ars_rallye_impcombo_elements WHERE RallyeID = $RallyeID AND ICID = $ICID ORDER BY F, X, Y, O";
	$oDB->Query($strSQL);
	$arr2 = $oDB->GetRecordArray("ICID, F, X, Y, O");
	//var_dump($arr2);
	foreach($arr2 as $data)
	{	
		list($ICID, $F, $X, $Y, $O) = $data; // the value is stored with each element of the IC just so I don't need to tables because of one lousy column
		
		$ICs[$ICID]["G"][]=array($F,$X,$Y,$O);
		
	}
	//var_dump($ICs);
	
	$strDisp = "";
	$sep="";
	
	foreach($ICs[$ICID]["G"] as $data)
	{
		list($F,$X,$Y,$O) = $data;

		$strDisp .= $sep.$Cells[$F][$X][$Y][$O];
		$sep = ", ";		
	}
	
	$strDisp .= " :(On ".$ICs[$ICID]["T"]." ".$ICs[$ICID]["V"]."pts.)";
	$ICDisp[$ICID] = $strDisp;
}

?>
<HTML>
	<HEAD>
		<TITLE>ARS - Combos</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>
<?
		include("configmenu.php");
?>
		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
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
			</TR>
		</TABLE>
		<BR>

		<fieldset>
		<legend><B>Instructions</B></legend>
		The table below should closely aproximate your scoresheet's layout with all of its cells labled and all of the points assigned. 
		You must now Register your impossible combinations.<BR>
		Select the CMs, CPs, or other gimmicks that are involved in a single Impossible Combination, then press the 'Register' button at the bottom of the page.
		It is customary for impossible combinations to only contain two elements, however this program allows you to place as many elements as you like into a single impossible combo. When two or more elements of an impossible combo are marked for scoring,
		the value of the markers together are scored as the value registerd, normally 0. However, being that the point value for an impossible combination can be set to a value other than 0, this section can also be used for bonus or alternate minium points scoring.
		For example A is worth 10pts, B is worth 10pts, if you have both A and B you get 25pts because it was hard, or 15pts because it is more correct than having A or B alone, and you really should have had A and C for 20pts. This is a difficult scoring concept, and is generally
		too difficult to score by hand because, as humans, we tend to forget the rules. The scoring program does not have this problem so it can be consistant in its scoring. I'm sure that if you ever find a need for this strange scoring you will find it useful.
		You may also recall an impossible combination for edit by selecting it from the list at the bottom of the page and then pressing 'Recall.' When you are done entering all of your impossible combinations, press the 'Done' button to continue on to the next step.
		</fieldset>
		<BR>
		<BR>
		
		
		<TABLE BORDER=0 WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP COLSPAN=2>
					<DIV ID=topdiv>
<?
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=100%>");
						$i=0;
						for($y = 0; $y < $TopY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $TopX; $x++)
							{
								myprint("<TD STYLE=\"border: 1px solid black;\" VALIGN=TOP>");
									myprint("<TABLE WIDTH=100%>");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=Top_".$x."_".$y.">");
												for($i = 0; $i < count($Cells['Top'][$x][$y]); $i++)
												{	
													if(trim($Values['Top'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if($IC['Top'][$x][$y][$i])
															$CHECKED="CHECKED";
																		
														myprint("<INPUT NAME=IC[Top][".$x."][".$y."][".$i."] VALUE=1 $CHECKED TYPE=CHECKBOX>");
													}
													else
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}
													
													myprint(str_replace(" ","&nbsp;",$Cells['Top'][$x][$y][$i])); 
													
													if(trim($Values['Top'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['Top'][$x][$y][$i]."pts."); 
													}
													print("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
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
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=100%>");
						$i=0;
						for($y = 0; $y < $CMY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $CMX; $x++)
							{
								myprint("<TD STYLE=\"border: 1px solid black;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=100%>");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CM_".$x."_".$y.">");
												for($i = 0; $i < count($Cells['CM'][$x][$y]); $i++)
												{	
													if(trim($Values['CM'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$IC['CM'][$x][$y][$i])
															$CHECKED="CHECKED";
															
														myprint("<INPUT NAME=IC[CM][".$x."][".$y."][".$i."] VALUE=1 $CHECKED TYPE=CHECKBOX>");
													}
													else
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}
													
													myprint(str_replace(" ","&nbsp;",$Cells['CM'][$x][$y][$i]));
													
													if(trim($Values['CM'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['CM'][$x][$y][$i]."pts."); 
													}
													print("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
								myprint("</TD>");
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
				<TD VALIGN=TOP WIDTH=<?=(15*$CPX)?>%>
					<DIV ID=cprdiv>
<?
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=100%>");
						$i=0;
						for($y = 0; $y < $CPY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $CPX; $x++)
							{
								myprint("<TD STYLE=\"border: 1px solid black;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=100%>");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CP_".$x."_".$y.">");
												for($i = 0; $i < count($Cells['CP'][$x][$y]); $i++)
												{	
													if(trim($Values['CP'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$IC['CP'][$x][$y][$i])
															$CHECKED="CHECKED";
															
														myprint("<INPUT NAME=IC[CP][".$x."][".$y."][".$i."] VALUE=1 $CHECKED TYPE=CHECKBOX>");
													}
													else
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}

													myprint(str_replace(" ","&nbsp;",$Cells['CP'][$x][$y][$i])); 

													if(trim($Values['CP'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['CP'][$x][$y][$i]."pts."); 
													}
													print("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
								myprint("</TD>");
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
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=100%>");
						$i=0;
						for($y = 0; $y < $CPY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $CPX; $x++)
							{
								myprint("<TD STYLE=\"border: 1px solid black;\" VALIGN=TOP>");
									myprint("<DIV ID=CP_".$x."_".$y.">");
									for($i = 0; $i < count($Cells['CP'][$x][$y]); $i++)
									{	
										if(trim($Values['CP'][$x][$y][$i]) !== "")
										{
											$CHECKED="";
											if($IC['CP'][$x][$y][$i])
												$CHECKED="CHECKED";
												
											myprint("<INPUT NAME=IC[CP][".$x."][".$y."][".$i."] VALUE=1 $CHECKED TYPE=CHECKBOX>");
										}
										else
										{
											myprint("&nbsp;&nbsp;&nbsp;");
										}

										myprint(str_replace(" ","&nbsp;",$Cells['CP'][$x][$y][$i])); 
										
										if(trim($Values['CP'][$x][$y][$i]) !== "")
										{
											myprint(": ".$Values['CP'][$x][$y][$i]."pts."); 
										}
										print("<BR>");
									}
									myprint("</DIV>");
								myprint("</TD>");
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
						myprint("<TABLE STYLE=\"border: 1px solid black;\" WIDTH=100%>");
						$i=0;
						for($y = 0; $y < $BotY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $BotX; $x++)
							{
								myprint("<TD STYLE=\"border: 1px solid black;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=100%>");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=Bot_".$x."_".$y.">");
												for($i = 0; $i < count($Cells['Bot'][$x][$y]); $i++)
												{	
													if(trim($Values['Bot'][$x][$y][$i]) !== "")
													{
														$CHECKED="";
														if(@$IC['Bot'][$x][$y][$i])
															$CHECKED="CHECKED";
															
														myprint("<INPUT NAME=IC[Bot][".$x."][".$y."][".$i."] VALUE=1 $CHECKED TYPE=CHECKBOX>");
													}
													else
													{
														myprint("&nbsp;&nbsp;&nbsp;");
													}

													myprint(str_replace(" ","&nbsp;",$Cells['Bot'][$x][$y][$i])); 
													
													if(trim($Values['Bot'][$x][$y][$i]) !== "")
													{
														myprint(": ".$Values['Bot'][$x][$y][$i]."pts."); 
													}
													print("<BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
										myprint("</TR>");
									myprint("</TABLE>");
								myprint("</TD>");
							}
							myprint("</TR>");
						}
						myprint("</TABLE>");
?>						
					</DIV>
				</TD>
			</TR>
		</TABLE>
		
		
		<TABLE WIDTH=100% border=0>
<?
			if(strtolower(@$action) == "recall")
			{
?>
				<TR>
					<TD COLSPAN=3>
						<FONT COLOR=red>
						<CENTER>
						Warning: You are editing an existing Impossible Combo<BR>
						</CENTER>
						</FONT>	
					</TD>
				</TR>
<?
			}
?>



			<TR>
				<TD VALIGN=TOP ALIGN=CENTER WIDTH=30%>
<?


	// TODO: if I recalled a IC then I should not be able to register a new one until I save the one I have
	//       There should also be a notice at the top of the page that says I am editing a recalled item
					if(count($ICDisp)) // if count  then display the recall
					{
						SelectOption("RecallICID",@$ICDisp,@$RecalledICID);
?>
					<INPUT TYPE=SUBMIT NAME=action VALUE=Recall>
<?
						if(strtolower(@$action) == "recall")
						{
?>					
					<INPUT TYPE=HIDDEN NAME=RecalledICID VALUE=<?=$RecalledICID?>>
<?
						}
					} // end the if count
?>
				</TD>

				<TD VALIGN=TOP ALIGN=CENTER WIDTH=40%>
<?					
					$Trigger = @$ICs[$RecalledICID]['T'];
					if(!isset($RecalledICID))
					{
						$Trigger = 2;
					}
?>						
					Trigger On <INPUT TYPE=TEXT SIZE=4 NAME=Trigger VALUE=<?=$Trigger?>>
					
					
<?					
					$Points = @$ICs[$RecalledICID]['V'];
					if(!isset($RecalledICID))
					{
						$Points = 0;
					}
?>						
					<INPUT TYPE=TEXT SIZE=4 NAME=Points VALUE=<?=$Points?>>pts.
<?
						if(strtolower(@$action) == "recall")
						{
?>
							<INPUT TYPE=SUBMIT NAME=action VALUE=Store>
<?
						}
						else
						{
?>
							<INPUT TYPE=SUBMIT NAME=action VALUE=Register>
<?
						}
?>
				</TD>

				<TD VALIGN=TOP ALIGN=CENTER WIDTH=30%>
					<INPUT TYPE=SUBMIT NAME=action VALUE=Done>
				</TD>
			</TR>
		</TABLE>
		</FORM>
	</BODY>
</HTML>
