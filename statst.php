<?
// Who got what
include "config.php";

	$arrShortClasses[1] = "FT";
	$arrShortClasses[2] = "Beg";
	$arrShortClasses[3] = "Nov";
	$arrShortClasses[4] = "Sen";
	$arrShortClasses[5] = "Exp";
	$arrShortClasses[6] = "Mstr";
	
if(!@$sub)
{
		
	function myprint($str)
	{
		print($str."\n");
	}

	
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

$strSQL="SELECT CarClass, COUNT(CarClass) as c FROM ars_scoresheet as s WHERE RallyeID = $RallyeID AND s.CarNumber % 2 = 1 GROUP BY CarClass";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
foreach($arr as $data)
{
	list($CarClass, $CarCount) = $data;
	
	$CarClassCountAB[1][$CarClass] = intval($CarCount);
	$CarClassCountAB[1][0] = @$CarClassCountAB[1][0] + intval($CarCount);
}

$strSQL="SELECT CarClass, COUNT(CarClass) as c FROM ars_scoresheet as s WHERE RallyeID = $RallyeID AND s.CarNumber % 2 = 0 GROUP BY CarClass";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
foreach($arr as $data)
{
	list($CarClass, $CarCount) = $data;
	
	$CarClassCountAB[0][$CarClass] = intval($CarCount);
	$CarClassCountAB[0][0] = @$CarClassCountAB[0][0] + intval($CarCount);
}

// Who got what
$strSQL="SELECT s.CarClass, F, X, Y, O, Name, s.CarNumber FROM ars_scoresheet as s, ars_scoresheet_elements as e WHERE s.RallyeID = $RallyeID AND s.CarNumber=e.CarNumber AND s.RallyeID = e.RallyeID";
$oDB->Query($strSQL);
$arr = $oDB->GetRecordArray(false);
foreach($arr as $data)
{
	list($CarClass, $F, $X, $Y, $O, $Name, $CarNumber) = $data;
	
	if(@$WhoGot[$CarClass][$F][$X][$Y][$O] == 0) 
	{
		$WhoGot[$CarClass][$F][$X][$Y][$O] = 0;
		$WhoGotAB[$CarNumber%2][$CarClass][$F][$X][$Y][$O] = 0;
	}
		
	@$WhoGot[$CarClass][$F][$X][$Y][$O]++;
	@$WhoGot[0][$F][$X][$Y][$O]++;
	@$WhoGotAB[$CarNumber%2][$CarClass][$F][$X][$Y][$O]++;
	@$WhoGotAB[$CarNumber%2][0][$F][$X][$Y][$O]++;
	
	
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
	$MaxLen = 4;
	foreach(array('Top','CM','CP','Bot','Combo') as $Section)
		if(isset($ScorePoints[$Section])) foreach($ScorePoints[$Section] as $X => $arr2)
			foreach($arr2 as $Y => $arr3)
				foreach($arr3 as $O => $Name)
				{
					$MaxLen = Max($MaxLen,strlen($Name));
				}
				//if($MaxLen > 9) $MaxLen = 9;

			printf("%".$MaxLen."s ","Item");

			foreach($arrShortClasses as $CarClass => $CarClassName)
			{
					printf("%-4s",$CarClassName);
					printf("%4s ","(".intval($CarClassCount[$CarClass]).")");
					if(isset($_REQUEST["AB"]))
						printf("        ");
			}
			printf("%-4s%4s\n","All","(".intval($CarClassCount[0]).")");
			for($i=0;$i<$MaxLen;$i++)
				print("-");
			if(!isset($_REQUEST["AB"]))
				print("---------------------------------------------------------------\n");
			else
				print("---------------------------------------------------------------------------------------------------------------------\n");
	
	

	foreach(array('Top','CM','CP','Bot','Combo') as $Section)
		if(isset($ScorePoints[$Section])) foreach($ScorePoints[$Section] as $X => $arr2)
			foreach($arr2 as $Y => $arr3)
				foreach($arr3 as $O => $Name)
				{
					printf("%".$MaxLen."s",substr($Name,0,$MaxLen));
					//printf("%".$MaxLen."s",wordwrap($Name, 9, "\n"));

					foreach($arrShortClasses + array(0) as $CarClass => $CarClassName)
					{
						if(intval($CarClassCount[$CarClass]) > 0)
						{
							$percent=intval(floatval(@$WhoGot[$CarClass][$Section][$X][$Y][$O])/ floatval(@$CarClassCount[$CarClass]) * 100);
							$percentA="--";
							if(intval($CarClassCountAB[1][$CarClass]) > 0)
							$percentA=intval(floatval(@$WhoGotAB[1][$CarClass][$Section][$X][$Y][$O])/ floatval(@$CarClassCountAB[1][$CarClass]) * 100);
							$percentB="--";
							if(intval($CarClassCountAB[0][$CarClass]) > 0)
							$percentB=intval(floatval(@$WhoGotAB[0][$CarClass][$Section][$X][$Y][$O])/ floatval(@$CarClassCountAB[0][$CarClass]) * 100);
							if(!isset($_REQUEST["AB"]))
							{
								printf(" %4s%4s",$percent."%","(".intval(@$WhoGot[$CarClass][$Section][$X][$Y][$O]).")");
							}
							else
							{
								printf(" %4s%4s/%4s%4s",$percentA."%","(".intval(@$WhoGotAB[1][$CarClass][$Section][$X][$Y][$O]).")",$percentB."%","(".intval(@$WhoGotAB[0][$CarClass][$Section][$X][$Y][$O]).")");
							}
						}
						else
						{
								printf(" %4s%4s"," -- "," ");
						}
					}
					printf("\n");

				}
?>
</PRE>

