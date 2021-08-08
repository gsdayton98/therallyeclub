<?
include "config.php";

$ThisStep = 4;
$RallyeID = CHTTPVars::GetValue("RallyeID");
if(!PasswordCheck($RallyeID,@$_SESSION["Password"])) redirect("index.php?RallyeID=$RallyeID");

if(!IsSetStep(2,$RallyeID)) // have I completed the previous step?
	redirect("name.php?RallyeID=$RallyeID");


function myprint($str)
{
	print($str."\n");
}

// Since in HTML rows come first, we have a funky order
$DefaultCM = array("A","N","AA","NN","B","O","BB","OO","C","P","CC","PP","D","Q","DD","QQ","E","R","EE","RR","F","S","FF","SS","G","Y","GG","YY","H","U","HH","UU","I","V","II","VV","J","W","JJ","WW","K","X","KK","XX","L","Y","LL","YY","M","Z","MM","ZZ");

$DefaultCP = array("CP1","CP2","CP3","CP4","CP5","CP6","CP7","CP8","CP9","CP10","CP11","CP12","CP13");


if(!CHTTPVars::IsEmpty("action"))
{
	$action = CHTTPVars::GetValue("action");
	
	switch(strtolower($action))
	{
		case "done":
			$Values = CHTTPVars::GetValue("Values");

			foreach($Values as $Section => $arrSection)
			{
				foreach($arrSection as $x => $arr1)
				{
					foreach($arr1 as $y => $arr2)
					{
						foreach($arr2 as $o => $data)
						{
							// find out what the old value was
							$value = $Values[$Section][$x][$y][$o];
							if(trim($value) === "")  // if empty then make it null
								$value = 'NULL';
							else 
								$value = floatval($value); // otherwise force to a number
							
							$strSQL="UPDATE ars_rallye_cells SET Value=$value WHERE RallyeID = $RallyeID AND F='$Section' AND X=$x AND Y=$y AND O=$o"; // hopefully this means leave value alone
							$oDB->Query($strSQL);
						}
					}	
				}
			}

			SetStep($ThisStep,$RallyeID);

			redirect("impcombo.php?RallyeID=$RallyeID");
			break;

			
		default:
			redirect("error.php");
	}
}


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

//var_dump($Cells['Top']);
//var_dump($Values['Top']);

?>
<HTML>
	<HEAD>
		<TITLE>ARS - Values</TITLE>
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
		The table below should closely aproximate your scoresheet's layout with all of its cells labled. 
		You must now assign values to your CM's, CP's and other gimmicks. Leaving the value blank will
		cause the check box in the scoring template to disappear, this is useful in discovering errors
		both at configuration and scoring time. 
		Since we assume
		you are using the bottom section for Tie Breakers, the points here are set to 0.1 by default. The score board shows 
		whole points only, and if a car's whole points are not the same as its total points; an asterisk is placed next to the
		car's score. For this reason, it is recommended that tie breaker spaces be checked off only if a Tie needs to be broken.
		</fieldset>
		<BR>
		<BR>
		
		
		<TABLE WIDTH=100% border=0>
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
													myprint($Cells['Top'][$x][$y][$i].":"); 
													myprint("<INPUT STYLE=\"font-family: courier;\" SIZE=4 NAME=Values[Top][".$x."][".$y."][".$i."] VALUE=\"".$Values['Top'][$x][$y][$i]."\" TYPE=TEXT>pts.<BR>");
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
													myprint($Cells['CM'][$x][$y][$i].":"); 
													myprint("<INPUT STYLE=\"font-family: courier;\" SIZE=4 NAME=Values[CM][".$x."][".$y."][".$i."] VALUE=\"".$Values['CM'][$x][$y][$i]."\" TYPE=TEXT>pts.<BR>");
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
													myprint($Cells['CP'][$x][$y][$i].":"); 
													myprint("<INPUT STYLE=\"font-family: courier;\" SIZE=4 NAME=Values[CP][".$x."][".$y."][".$i."] ID=CP_".$x."_".$y."_".($i+1)." VALUE=\"".$Values['CP'][$x][$y][$i]."\" TYPE=TEXT>pts.<BR>");
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
									myprint("<TABLE BORDER=0 WIDTH=100%>");
										myprint("<TR>");
											myprint("<TD VALIGN=MIDDLE>");
												myprint("<DIV ID=CP_".$x."_".$y.">");
												for($i = 0; $i < count($Cells['CP'][$x][$y]); $i++)
												{	
													myprint($Cells['CP'][$x][$y][$i].":"); 
													myprint("<INPUT STYLE=\"font-family: courier;\" SIZE=4 NAME=Values[CP][".$x."][".$y."][".$i."] ID=CP_".$x."_".$y."_".($i+1)." VALUE=\"".$Values['CP'][$x][$y][$i]."\" TYPE=TEXT>pts.<BR>");
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
													myprint($Cells['Bot'][$x][$y][$i].":"); 
													myprint("<INPUT STYLE=\"font-family: courier;\" SIZE=4 NAME=Values[Bot][".$x."][".$y."][".$i."] ID=Bot_".$x."_".$y."_".($i+1)." VALUE=\"".$Values['Bot'][$x][$y][$i]."\" TYPE=TEXT>pts.<BR>");
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

		<TABLE STYLE="border: 1px solid black;">
			<TR>
				<TD STYLE="border: 1px solid black;">
					Finish: <INPUT STYLE="font-family: courier;" SIZE=4 NAME=Values[Fin][0][0][0] VALUE="<?=@$Values['Fin'][0][0][0]?>" TYPE=TEXT>pts.
				</TD>
			</TR>
		</TABLE>
		
		
		
		<TABLE WIDTH=100% border=0>
			<TR>
				<TD VALIGN=TOP>
					<CENTER>
					<INPUT TYPE=SUBMIT NAME=action VALUE=Done>
					</CENTER>
				</TD>
			</TR>
		</TABLE>
		</FORM>
	</BODY>
</HTML>
