<?
// Who got what
include "config.php";

if(!@$sub)
{
		
	function myprint($str)
	{
		print($str."\n");
	}

	$arrClasses[1] = "First Timer";
	$arrClasses[2] = "Beginner";
	$arrClasses[3] = "Novice";
	$arrClasses[4] = "Senior";
	$arrClasses[5] = "Expert";
	$arrClasses[6] = "Master";
	
	
	$RallyeID = CHTTPVars::GetValue("RallyeID"); // any one can look at the scoreboard, so there is no need for password here
	
	
	if(!CHTTPVars::IsEmpty("action"))
	{
		$action = CHTTPVars::GetValue("action");
		
		switch(strtolower($action))
		{
			case "done":
				redirect("score.php?RallyeID=$RallyeID");
				break;
				
			default:
				redirect("error.php");
		}
	}
}

// how many cars are in each class
$strSQL="SELECT CarClass, COUNT(CarClass) as c FROM ars_scoresheet as s WHERE RallyeID = $RallyeID GROUP BY CarClass";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
foreach($arr as $data)
{
	list($CarClass, $CarCount) = $data;
	
	$CarClassCount[$CarClass] = intval($CarCount);
	$CarClassCount[0] = @$CarClassCount[0] + intval($CarCount);
}

// Who got what
$strSQL="SELECT s.CarClass, F, X, Y, O, Name FROM ars_scoresheet as s, ars_scoresheet_elements as e WHERE s.RallyeID = $RallyeID AND s.CarNumber=e.CarNumber AND s.RallyeID = e.RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
foreach($arr as $data)
{
	list($CarClass, $F, $X, $Y, $O, $Name) = $data;
	
	if(@$WhoGot[$CarClass][$F][$X][$Y][$O] == 0) 
		$WhoGot[$CarClass][$F][$X][$Y][$O] = 0;
		
	@$WhoGot[$CarClass][$F][$X][$Y][$O]++;
	@$WhoGot[0][$F][$X][$Y][$O]++;
	
	
}

//What are all the Score Points including combos
$strSQL = "SELECT F, X, Y, O, Name FROM ars_rallye_cells WHERE RallyeID = $RallyeID AND Value IS NOT NULL  ORDER BY F, X, Y, O";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);

foreach($arr as $data)
{
	list($F, $X, $Y, $O, $Name) = $data;
	
	$ScorePoints[$F][$X][$Y][$O] = $Name;
}


$strSQL = "SELECT ICID, F, X, Y, O FROM ars_rallye_impcombo_elements WHERE RallyeID = $RallyeID ORDER BY F, X, Y, O"; // name needs to come from the ScorePoints Array
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);

$Sep = array();
foreach($arr as $data)
{
	list($ICID, $F, $X, $Y, $O) = $data;
	
	if(!isset($Sep[$ICID])) $Sep[$ICID] = "";
	@$ScorePoints['Combo'][$ICID][0][0] .= $Sep[$ICID].$ScorePoints[$F][$X][$Y][$O];
	@$Sep[$ICID] = ", ";
}



if(!@$sub)
{
?>

<HTML>
	<HEAD>
		<TITLE>ARS - Statistics</TITLE>
		<link rel="stylesheet" href="stylesheet.css">
	</HEAD>
	
	<BODY>
<?
}
?>
	<TABLE BORDER=1>
		<TR>
			<TD>
				<B>Item</B>
			</TD>
<?
			foreach($arrClasses as $CarClass => $CarClassName)
			{
				myprint("<TD>");
					myprint("<B>$CarClassName (".intval($CarClassCount[$CarClass]).")</B>");
				myprint("</TD>");
			}
?>
			<TD>
				<B>Overall (<?=intval($CarClassCount[0]);?>)</B>
			</TD>
		</TR>
	
<?
	
	foreach(array('Top','CM','CP','Bot','Combo') as $Section)
		if(isset($ScorePoints[$Section])) foreach($ScorePoints[$Section] as $X => $arr2)
			foreach($arr2 as $Y => $arr3)
				foreach($arr3 as $O => $Name)
				{
					myprint("<TR>");
					myprint("<TD>");
					myprint("<B>".HTMLEscape($Name)."</B>");
					myprint("</TD>");

					foreach($arrClasses + array(0) as $CarClass => $CarClassName)
					{
						if(intval($CarClassCount[$CarClass]) > 0)
						{
							$percent=intval(floatval(@$WhoGot[$CarClass][$Section][$X][$Y][$O])/ floatval(@$CarClassCount[$CarClass]) * 100);
							myprint("<TD>");
								myprint($percent."% (".intval(@$WhoGot[$CarClass][$Section][$X][$Y][$O]).")");
							myprint("</TD>");
						}
						else
						{
							myprint("<TD>");
								myprint("--");
								//print("-- $CarClass $Section $X $Y $O");
							myprint("</TD>");
						}
					}
					myprint("</TR>\n");

				}
?>
	</TABLE>


<?
if(!@$sub)
{
?>
		<FORM METHOD=POST>
		<INPUT TYPE=HIDDEN NAME=RallyeID VALUE=<?=$RallyeID?>>
		</FORM>
	</BODY>
</HTML>
<?
}
?>
