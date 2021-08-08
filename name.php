<?
include "config.php";

$ThisStep = 2;
$RallyeID = CHTTPVars::GetValue("RallyeID");
$ABChoices = $RallyeID = CHTTPVars::GetValue("ABChoices");
if(!PasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");

if(!IsSetStep(1,$RallyeID)) // have I completed the previous step?
	redirect("configure.php?RallyeID=$RallyeID");

function myprint($str)
{
	print($str."\n");
}

// Since in HTML rows come first, we have a funky order
$DefaultTop = array("Gimick1","Gimick2","Gimick3","Gimick4","Gimick5","Gimick6","Gimick7","Gimick8","Gimick9","Gimick10");
$DefaultCM = array("A","N","AA","NN","B","O","BB","OO","C","P","CC","PP","D","Q","DD","QQ","E","R","EE","RR","F","S","FF","SS","G","T","GG","TT","H","U","HH","UU","I","V","II","VV","J","W","JJ","WW","K","X","KK","XX","L","Y","LL","YY","M","Z","MM","ZZ");
$DefaultCP = array("CP1","CP2","CP3","CP4","CP5","CP6","CP7","CP8","CP9","CP10","CP11","CP12","CP13");
$DefaultBot = array("TB1","TB2","TB3","TB4","TB5","TB6","TB7","TB8","TB9","TB10");

// Flatten 2D columns of RIs into Row Order for AB Rallyes
for($i=1, $c=0; $c<$CMX; $c++) {
	for($r=1; $r<=$CMY; $r++, $i++) {
		$temp = ($c * $CMY) + $r;
		$DefaultAB[$i] = "${temp}";
	}
}

if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "done":
			// delete this rallye
			$strSQL="DELETE FROM ars_rallye_cells WHERE RallyeID = $RallyeID";
			$oDB->Query($strSQL);
			
			$Cells = CHTTPVars::GetValue("Cells");
			$Values = CHTTPVars::GetValue("Values");

			foreach($Cells as $Section => $arrSection)
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
							
							$strSQL="INSERT INTO ars_rallye_cells (RallyeID, F, X, Y, O, Name, Value) VALUES($RallyeID, '".$Section."', $x, $y, $o, '".DBEscape($data)."', $value)"; // hopefully this means leave value alone
							$oDB->Query($strSQL);
						}
					}	
				}
			}

			SetStep($ThisStep,$RallyeID);

			redirect("value.php?RallyeID=$RallyeID");
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


?>
<HTML>
	<HEAD>
		<TITLE>ARS - Names</TITLE>
		<link rel="stylesheet" href="stylesheet.css">

		<SCRIPT LANGUAGE=JavaScript> 
			function Schizo(section,x,y,opp)
			{
				store = new Array;
				vstore = new Array;
				//alert(section+","+x+","+y+","+opp);
				thediv = document.getElementById(section+"_"+x+"_"+y);
				//alert(section+"_"+x+"_"+y+" is "+thediv);
				thecount = document.getElementById("Count"+section+"_"+x+"_"+y);
				//alert(thecount.value);
				
				// store the existing data in an array
				for(i=1; i <= parseInt(thecount.value); i++)
				{
					//alert(i);
					theelement = document.getElementById(section+"_"+x+"_"+y+"_"+i);
					//alert(section+"_"+x+"_"+y+"_"+i);
					store[i] = theelement.value;
					//alert(i);

					theelement = document.getElementById("v"+section+"_"+x+"_"+y+"_"+i);
					//alert(section+"_"+x+"_"+y+"_"+i);
					vstore[i] = theelement.value;
				}
				
				// inc/dec the counter
				if(opp == "+")
				{
					thecount.value = parseInt(thecount.value) + 1;
				}
				else
				{
					thecount.value = parseInt(thecount.value) - 1;
					if(thecount.value == 0)
						thecount.value = 1;
				}

				
				// alter the innerHTML
				thediv.innerHTML = "";
				kmax = parseInt(thecount.value);
				for(i=1; i <= kmax; i++)
				{
					//alert(i);
					thediv.innerHTML += '<INPUT STYLE="font-family: courier; width: 100%;" NAME=Cells['+section+']['+x+']['+y+'][] ID='+section+'_'+x+'_'+y+'_'+i+' TYPE=TEXT><BR>';
					thediv.innerHTML += '<INPUT NAME=Values['+section+']['+x+']['+y+'][] ID=v'+section+'_'+x+'_'+y+'_'+i+' TYPE=HIDDEN>';
					thediv.innerHTML += (i == kmax) ? '<BR>' : '';
				}
				// restore the existing data
				j=store.length - 1;
				
				for(i=1; i <= j; i++)
				{
					//alert(i);
					theelement = document.getElementById(section+"_"+x+"_"+y+"_"+i);
					theelement.value = store[i];

					theelement = document.getElementById("v"+section+"_"+x+"_"+y+"_"+i);
					theelement.value = vstore[i];
				}
				
			}
		</SCRIPT>
	</HEAD>
	
	<BODY>
<?
		include("configmenu.php");
?>
		<FORM METHOD=POST>
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
		The table below should closely aproximate your scoresheet's layout. However, it
		is normal and appropriate for the cells to have names. For example: in the case of a CM, H1 or in the
		case of a header gimmick, passengers, or in the case of a tie breaker, how far off from perfect. I think you get the idea.<BR>
		Enter the names of the elements for each cell. If you need to split a cell into more than one option
		because of a CM numbering, AB Rallye or similar gimmick, click the '+' button in that cell. If you click too many,
		just click the '-' button to remove the last split.<BR>
		For your convenience a CM scoresheet has been prepopulated with the standard A-Z and AA-ZZ CMs and an A/B scoresheet is prepopulated with RI numbers and horizontal A/B/C choices. Your CPs have also be pre numbered.
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
												myprint("<INPUT TYPE=HIDDEN ID=CountTop_".$x."_".$y." VALUE=".(count($Cells['Top'][$x][$y])?count($Cells['Top'][$x][$y]):1).">");
												myprint("<DIV ID=Top_".$x."_".$y.">");
												if(count($Cells['Top'][$x][$y]) == 0 && !isset($Cells['Top'][$x][$y][0]))
												{
													$Cells['Top'][$x][$y][0] = @$DefaultTop[$i++];
													$Values['Top'][$x][$y][0] = "";

												}
												for($j = 0; $j < count($Cells['Top'][$x][$y]); $j++)
												{	
													myprint("<INPUT STYLE=\"font-family: courier; width: 100%;\" NAME=Cells[Top][".$x."][".$y."][".$j."] ID=Top_".$x."_".$y."_".($j+1)." VALUE=\"".$Cells['Top'][$x][$y][$j]."\" TYPE=TEXT><BR>");
													myprint("<INPUT NAME=Values[Top][".$x."][".$y."][".$j."] ID=vTop_".$x."_".$y."_".($j+1)." VALUE=\"".$Values['Top'][$x][$y][$j]."\" TYPE=HIDDEN><BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
											myprint("<TD VALIGN=TOP ALIGN=RIGHT WIDTH=20>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"+\" OnClick=\"Schizo('Top',$x,$y,'+')\"><BR>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"-\" OnClick=\"Schizo('Top',$x,$y,'-');\">");
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
						myprint("<br>WDxyZ<br><TABLE STYLE=\"border: 1px solid black;\" WIDTH=100%>");
						$i=1;
						for($y = 0; $y < $CMY; $y++) // in HTML Rows come first
						{
							myprint("<TR>");
							for($x = 0; $x < $CMX; $x++)
							{
								myprint($ABChoices);
								myprint("<TD STYLE=\"border: 1px solid black;\" VALIGN=TOP>");
									myprint("<TABLE BORDER=0 WIDTH=100%>");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<INPUT TYPE=HIDDEN ID=CountCM_".$x."_".$y." VALUE=".(count($Cells['CM'][$x][$y])?count($Cells['CM'][$x][$y]):1).">");
												myprint("<DIV ID=CM_".$x."_".$y.">");
												if(count($Cells['CM'][$x][$y]) == 0 && !isset($Cells['CM'][$x][$y][0]))
													if($ABChoices > 0 || $RallyeID == 169)
													{	// Assuming an A/B type rallye
														$Cells['CM'][$x][$y][0] = "Z"; // $DefaultAB[$i];	// RI number
														$Cells['CM'][$x][$y][1] = "A";	// choice
														$Cells['CM'][$x][$y][2] = "B";	// choice
														$Cells['CM'][$x][$y][3] = "C";	// choice
														myprint(".");

														for($j = 0; $j < 4; $j++)
														{	
															$Values['CM'][$x][$y][$j] = "";
															myprint("<INPUT STYLE=\"font-family: courier; width: 100%;\" NAME=Cells[CM][".$x."][".$y."][".$j."] ID=CM_".$x."_".$y."_".($j+1)." VALUE=\"".$Cells['CM'][$x][$y][$j]."\" TYPE=TEXT>");
															myprint("<INPUT NAME=Values[CM][".$x."][".$y."][".$j."] ID=vCM_".$x."_".$y."_".($j+1)." VALUE=\"".$Values['CM'][$x][$y][$j]."\" TYPE=HIDDEN>");
														}
														myprint("<BR>");
													}
													else
													{
														$Cells['CM'][$x][$y][0] = @$DefaultAB[$i++];	// Should be CM
														$Values['CM'][$x][$y][0] = "";
														for($j = 0; $j < count($Cells['CM'][$x][$y]); $j++)
														{	
															myprint("<INPUT STYLE=\"font-family: courier; width: 100%;\" NAME=Cells[CM][".$x."][".$y."][".$j."] ID=CM_".$x."_".$y."_".($j+1)." VALUE=\"".$Cells['CM'][$x][$y][$j]."\" TYPE=TEXT><BR>");
															myprint("<INPUT NAME=Values[CM][".$x."][".$y."][".$j."] ID=vCM_".$x."_".$y."_".($j+1)." VALUE=\"".$Values['CM'][$x][$y][$j]."\" TYPE=HIDDEN><BR>");
														}	
													}
												myprint("</DIV>");
											myprint("</TD>");
											myprint("<TD VALIGN=TOP ALIGN=RIGHT WIDTH=20>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"+\" OnClick=\"Schizo('CM',$x,$y,'+');\"><BR>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"-\" OnClick=\"Schizo('CM',$x,$y,'-');\">");
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
												myprint("<INPUT TYPE=HIDDEN ID=CountCP_".$x."_".$y." VALUE=".(count($Cells['CP'][$x][$y])?count($Cells['CP'][$x][$y]):1).">");
												myprint("<DIV ID=CP_".$x."_".$y.">");
												if(count($Cells['CP'][$x][$y]) == 0 && !isset($Cells['CP'][$x][$y][0]))
												{
													$Cells['CP'][$x][$y][0] = @$DefaultCP[$i++];
													$Values['CP'][$x][$y][0] = "";
												}
												for($j = 0; $j < count($Cells['CP'][$x][$y]); $j++)
												{	
													myprint("<INPUT STYLE=\"font-family: courier; width: 100%;\" NAME=Cells[CP][".$x."][".$y."][".$j."] ID=CP_".$x."_".$y."_".($j+1)." VALUE=\"".$Cells['CP'][$x][$y][$j]."\" TYPE=TEXT><BR>");
													myprint("<INPUT NAME=Values[CP][".$x."][".$y."][".$j."] ID=vCP_".$x."_".$y."_".($j+1)." VALUE=\"".$Values['CP'][$x][$y][$j]."\" TYPE=HIDDEN><BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
											myprint("<TD VALIGN=TOP ALIGN=RIGHT WIDTH=20>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"+\" OnClick=\"Schizo('CP',$x,$y,'+');\"><BR>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"-\" OnClick=\"Schizo('CP',$x,$y,'-');\">");
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
									myprint("<TABLE BORDER=0 WIDTH=100%>");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<INPUT TYPE=HIDDEN ID=CountCP_".$x."_".$y." VALUE=".(count($Cells['CP'][$x][$y])?count($Cells['CP'][$x][$y]):1).">");
												myprint("<DIV ID=CP_".$x."_".$y.">");
												if(count($Cells['CP'][$x][$y]) == 0 && !isset($Cells['CP'][$x][$y][0]))
												{
													$Cells['CP'][$x][$y][0] = @$DefaultCP[$i++];
													$Values['CP'][$x][$y][0] = "";
												}
												for($j = 0; $j < count($Cells['CP'][$x][$y]); $j++)
												{	
													myprint("<INPUT STYLE=\"font-family: courier; width: 100%;\" NAME=Cells[CP][".$x."][".$y."][".$j."] ID=CP_".$x."_".$y."_".($j+1)." VALUE=\"".$Cells['CP'][$x][$y][$j]."\" TYPE=TEXT><BR>");
													myprint("<INPUT NAME=Values[CP][".$x."][".$y."][".$j."] ID=vCP_".$x."_".$y."_".($j+1)." VALUE=\"".$Values['CP'][$x][$y][$j]."\" TYPE=HIDDEN><BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
											myprint("<TD VALIGN=TOP ALIGN=RIGHT WIDTH=20>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"+\" OnClick=\"Schizo('CP',$x,$y,'+');\"><BR>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"-\" OnClick=\"Schizo('CP',$x,$y,'-');\">");
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
												myprint("<INPUT TYPE=HIDDEN ID=CountBot_".$x."_".$y." VALUE=".(count(@$Cells['Bot'][$x][$y])?count(@$Cells['CP'][$x][$y]):1).">");
												myprint("<DIV ID=Bot_".$x."_".$y.">");
												if(count($Cells['Bot'][$x][$y]) == 0 && !isset($Cells['Bot'][$x][$y][0]))
												{
													$Cells['Bot'][$x][$y][0] = @$DefaultBot[$i++];
													$Values['Bot'][$x][$y][0] = 0.1;
												}
												for($j = 0; $j < count($Cells['Bot'][$x][$y]); $j++)
												{	
													myprint("<INPUT STYLE=\"font-family: courier; width: 100%;\" NAME=Cells[Bot][".$x."][".$y."][".$j."] ID=Bot_".$x."_".$y."_".($j+1)." VALUE=\"".$Cells['Bot'][$x][$y][$j]."\" TYPE=TEXT><BR>");
													myprint("<INPUT NAME=Values[Bot][".$x."][".$y."][".$j."] ID=vBot_".$x."_".$y."_".($j+1)." VALUE=\"".$Values['Bot'][$x][$y][$j]."\" TYPE=HIDDEN><BR>");
												}
												myprint("</DIV>");
											myprint("</TD>");
											myprint("<TD VALIGN=TOP ALIGN=RIGHT WIDTH=20>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"+\" OnClick=\"Schizo('Bot',$x,$y,'+');\"><BR>");
												myprint("<INPUT STYLE=\"width=18px; font-size:11px;\" TYPE=BUTTON VALUE=\"-\" OnClick=\"Schizo('Bot',$x,$y,'-');\">");
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
		
		<INPUT NAME=Cells[Fin][0][0][0] VALUE="<?=@$Cells['Fin'][0][0][0]?>" TYPE=HIDDEN>
		<INPUT NAME=Values[Fin][0][0][0] VALUE="<?=@$Values['Fin'][0][0][0]?>" TYPE=HIDDEN>
		
		<TABLE WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP>
					<CENTER>
					<INPUT TYPE=SUBMIT NAME=action VALUE=Done>
					<CENTER>
				</TD>
			</TR>
		</TABLE>
		</FORM>
	</BODY>
</HTML>
