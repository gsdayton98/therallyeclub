<?PHP
// Makes an http redirect
function  redirect($url='')
{
  global $_APPENV;

  header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');    // Date in the past
  header('Last-Modified: '.gmdate("D, d M Y H:i:s").' GMT'); 
                                                     // always modified
  header('Cache-Control: no-store, no-cache, must-revalidate');  // HTTP/1.1
  header('Cache-Control: post-check=0, pre-check=0', false);
  header('Pragma: no-cache');      
  
  header("Location: $url");
  exit;
}

function Z($id,$len)
{
	// This function changes the ID from an integer to a zero padded string
	// return is the string

	$i=0;
	$retVal = "";
	$strLen = strlen($id);
	$addChr = $len - $strLen;
	while ($i<$addChr) {
		$retVal=$retVal."0";
		$i++;
	}
	$retVal=$retVal.$id;
	
	return $retVal;
}

// NJS 2004-03-04
// truncates or right pads str with spaces to len characters
function S($str,$len)
{
	$ret=substr($str, 0, $len);
	while(strlen($ret) < $len)
	{
		$ret .= " ";
	}
	return($ret);
}

function DBEscape($text,$nostrip=false) {
  //
  // Do the built in add slashes to \ ' and "
  if(!$nostrip)
  	$text = strip_tags($text, '<b><i><u><p><ul>'); 
  $ret = addslashes($text);

  return $ret; 
}

function HTMLEscape($text) {
  $ret = @htmlspecialchars($text);
   
  return $ret; 
}

function URLEscape($text) {
  $ret = @urlencode($text);
   
  return $ret; 
}

function URLUnEscape($text) {
  $ret = @urldecode($text);
   
  return $ret; 
}


// when using this function you must also include incDateJavaScript.php
function DateSelect($ScatterName, $DateName, $CurrentValue, $style="s0")
{
	
	$thisyear = Date("Y");
	$arrMonth=array("01","02","03","04","05","06","07","08","09","10","11","12");
	$arrDay=array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");

	list($date)=explode(" ",$CurrentValue);
	list($year,$month,$day)=explode("-",$date);
		
	if($year == "" || $year == NULL)
		$year = 0;
	if($month == "" || $month == NULL)
		$month = 0;
	if($day == "" || $day == NULL)
		$day = 0;
	
	print("<INPUT TYPE=HIDDEN NAME=$DateName VALUE=$CurrentValue>");
	
	print("<SELECT class=$style NAME=".$ScatterName."_year onChange={SetDate('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day')}>");
	print("<OPTION VALUE=0000>Unkn</OPTION>");
	for($value=($thisyear-5);$value <= ($thisyear+5); $value++)
	{
		print("<OPTION ");
		if((int)$value == (int)$year)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
	print("-");
	print("<SELECT class=$style NAME=".$ScatterName."_month onChange={SetDate('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day')}>");
	print("<OPTION VALUE=00>Uk</OPTION>");
	foreach($arrMonth as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$month)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
	print("-");
	print("<SELECT class=$style NAME=".$ScatterName."_day onChange={SetDate('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day')}>");
	print("<OPTION VALUE=00>Uk</OPTION>");
	foreach($arrDay as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$day)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
}
// when using this function you must also include incDateJavaScript.php
function DateTimeSelect($ScatterName, $DateName, $CurrentValue, $style="s0")
{
	
	$thisyear = Date("Y");
	$arrMonth=array("01","02","03","04","05","06","07","08","09","10","11","12");
	$arrDay=array("01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30","31");
	$arrHour=array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
	$arrMinute=array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30",
	              "31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59");
	
	list($date,$time)=explode(" ",$CurrentValue);
	list($year,$month,$day)=explode("-",$date);
	list($hour,$minute,$second)=explode(":",$time);
		
	if($year == "" || $year == NULL)
		$year = 0;
	if($month == "" || $month == NULL)
		$month = 0;
	if($day == "" || $day == NULL)
		$day = 0;
	if($hour == "" || $hour == NULL)
		$hour = 0;
	if($minute == "" || $minute == NULL)
		$minute = 0;
	if($second == "" || $second == NULL)
		$second = 0;
	
	print("<INPUT TYPE=HIDDEN NAME=$DateName VALUE=\"$CurrentValue\">");

	print("<SELECT class=$style NAME=".$ScatterName."_year onChange={SetDateTime('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day','".$ScatterName."_hour','".$ScatterName."_minute')}>");
	for($value=($thisyear-5);$value <= ($thisyear+5); $value++)
	{
		print("<OPTION ");
		if((int)$value == (int)$year)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
	print("<B>-</B>");
	print("<SELECT class=$style NAME=".$ScatterName."_month onChange={SetDateTime('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day','".$ScatterName."_hour','".$ScatterName."_minute')}>");
	foreach($arrMonth as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$month)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
	print("<B>-</B>");
	print("<SELECT class=$style NAME=".$ScatterName."_day onChange={SetDateTime('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day','".$ScatterName."_hour','".$ScatterName."_minute')}>");
	foreach($arrDay as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$day)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
	print("&nbsp;&nbsp;");
	print("<SELECT class=$style NAME=".$ScatterName."_hour onChange={SetDateTime('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day','".$ScatterName."_hour','".$ScatterName."_minute')}>");
	foreach($arrHour as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$hour)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
	print("<B>:</B>");
	print("<SELECT class=$style NAME=".$ScatterName."_minute onChange={SetDateTime('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day','".$ScatterName."_hour','".$ScatterName."_minute')}>");
	foreach($arrMinute as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$minute)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
}

function DurationSelect($ScatterName, $DateName, $CurrentValue, $style="s0")
{
	
	$arrMonth=array("00","01","02","03","04","05","06","07","08","09","10","11");
	$arrDay=array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30");
	$arrHour=array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23");
	$arrMinute=array("00","01","02","03","04","05","06","07","08","09","10","11","12","13","14","15","16","17","18","19","20","21","22","23","24","25","26","27","28","29","30",
	              "31","32","33","34","35","36","37","38","39","40","41","42","43","44","45","46","47","48","49","50","51","52","53","54","55","56","57","58","59");
	
	list($date,$time)=explode(" ",$CurrentValue);
	list($year,$month,$day)=explode("-",$date);
	list($hour,$minute,$second)=explode(":",$time);
		
	if($year == "" || $year == NULL)
		$year = 0;
	if($month == "" || $month == NULL)
		$month = 0;
	if($day == "" || $day == NULL)
		$day = 0;
	if($hour == "" || $hour == NULL)
		$hour = 0;
	if($minute == "" || $minute == NULL)
		$minute = 0;
	if($second == "" || $second == NULL)
		$second = 0;
	
	print("<INPUT TYPE=HIDDEN NAME=$DateName VALUE=\"$CurrentValue\">");
	print("<INPUT TYPE=HIDDEN NAME=".$ScatterName."_year VALUE=$year>");
	print("<INPUT TYPE=HIDDEN NAME=".$ScatterName."_month VALUE=$month>");
	print("<INPUT TYPE=HIDDEN NAME=".$ScatterName."_day VALUE=$day>");
	print("<SELECT class=$style NAME=".$ScatterName."_hour onChange={SetDateTime('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day','".$ScatterName."_hour','".$ScatterName."_minute')}>");
	foreach($arrHour as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$hour)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
	print("<B>:</B>");
	print("<SELECT class=$style NAME=".$ScatterName."_minute onChange={SetDateTime('$DateName','".$ScatterName."_year','".$ScatterName."_month','".$ScatterName."_day','".$ScatterName."_hour','".$ScatterName."_minute')}>");
	foreach($arrMinute as $value)
	{
		print("<OPTION ");
		if((int)$value == (int)$minute)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");
}

function AnswerSelect(&$arr, $SelectName, $CurrentValue, $style="s0", $Script="")
{
	//var_dump($arr);
	print("<SELECT class=$style NAME=$SelectName $Script>");
	foreach($arr as $value => $name)
	{
		print("<OPTION ");
		if($value == $CurrentValue)
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$name</OPTION>");
	}
	print("</SELECT>");
}				

function YearMonthSelect($SelectName, $style="s0")
{

//Define month array
$currentMonth=Date("F");
$arrMonth=array();
$arrMonth["01"]="January";
$arrMonth["02"]="February";
$arrMonth["03"]="March";
$arrMonth["04"]="April";
$arrMonth["05"]="May";
$arrMonth["06"]="June";
$arrMonth["07"]="July";
$arrMonth["08"]="August";
$arrMonth["09"]="September";
$arrMonth["10"]="October";
$arrMonth["11"]="November";
$arrMonth["12"]="December";

//Define year array
$arrYear=array();
$arrYear[0]=Date("Y");
$arrYear[1]=Date("Y",strtotime("-1 year"));
$arrYear[2]=Date("Y",strtotime("-2 year"));
$arrYear[3]=Date("Y",strtotime("-3 year"));
$arrYear[4]=Date("Y",strtotime("-4 year"));
$arrYear[5]=Date("Y",strtotime("-5 year"));


	
	
	
	print("<SELECT class=$style NAME=".$SelectName."_month>");
	foreach($arrMonth as $name => $value)
	{
		print("<OPTION ");
		if($value == $currentMonth)
		{
			print("SELECTED ");
		}
		print("VALUE=$name>$value</OPTION>");
	}
	print("</SELECT>");
	
	print("&nbsp;&nbsp;");
	
	print("<SELECT class=$style NAME=".$SelectName."_year>");
	foreach($arrYear as $value)
	{
		print("<OPTION ");
		if($value == Date("Y"))
		{
			print("SELECTED ");
		}
		print("VALUE=$value>$value</OPTION>");
	}
	print("</SELECT>");

}


function CurlPost($url, $post="", $timeout=120, $iWantHeader=0, $iDontWantBody = 0,$cookie="",$referer="") // cookie may need to be an array I don't know if you can have more than one set-cookie: lines in the header or not
{
	if(!($b=function_exists("curl_init")))
	{
		dl("php_curl.dll");
	}
		
	$optHeader = array("Accept-Language: en-us","Connection: Keep-Alive");


	$iDoPost=1;
	if($post === "")
	{
		$iDoPost=0;
	}
	
	$ch = curl_init();
	
	curl_setopt ($ch, CURLOPT_URL, $url);
	curl_setopt ($ch, CURLOPT_HEADER, $iWantHeader);
	curl_setopt ($ch, CURLOPT_NOBODY, $iDontWantBody);

	if(@count($optHeader))
	{
		curl_setopt ($ch, CURLOPT_HTTPHEADER, $optHeader);
	}
	
	curl_setopt ($ch, CURLOPT_POST, $iDoPost);
	if($iDoPost)
		curl_setopt ($ch, CURLOPT_POSTFIELDS, $post);
		
	if($cookie != "")
	{
		curl_setopt ($ch, CURLOPT_COOKIE, $cookie);
	}
	
	if($referer != "")
	{
		curl_setopt ($ch, CURLOPT_REFERER, $referer);
	}
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_NOPROGRESS, 1);
	curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt ($ch, CURLOPT_TIMEOUT, $timeout);
	curl_setopt ($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.1.4322)");
	
	$ret = curl_exec ($ch);
	curl_close ($ch);
	
	return($ret);
}

function SelectOption($name,$arrOptions,$selected="",$multi=false,$size=6,$style="",$optionstyles=false)
{
	if(!is_array($selected))
	{
		$selected=array($selected);
	}
	
	$MULTIPLE = "";
	if($multi === true)
		$MULTIPLE = "MULTIPLE SIZE=$size";
	else if($multi === false)
	{
	}
	else
	{
		$MULTIPLE = $multi; // this allows us to use the $multi to stuff in a style
	}

	$CLASS = "";
	if($style != "")
	{
		$CLASS="CLASS=$style";
	}

	print("<SELECT $CLASS $MULTIPLE NAME=\"$name\" ID=".preg_replace('/[\[\]]/','',$name).">");
	foreach($arrOptions as $value => $label) // note the array Key is actually the value and the array value is label
	{
		$SELECTED = "";
		if(in_array($value, $selected))
			$SELECTED = "SELECTED";

		$CLASS="";
		if($optionstyles!==false)
		{
			if($optionstyles[$value] != "")
			{
				$CLASS="CLASS=".$optionstyles[$value];
			}
		}

		print("<OPTION $CLASS $SELECTED VALUE=\"$value\">$label</OPTION>");
	}
	print("</SELECT>");
		
}

function CheckBoxArray($name,$arrOptions,$arrSelected=false,$eoln="")
{
	if($arrSelected == false)
		$arrSelected = array();
		
			
	foreach($arrOptions as $value => $label)
	{
		$CHECKED = "";
		if(in_array($value, $arrSelected))
			$CHECKED = "CHECKED";	
		print("<INPUT TYPE=CHECKBOX $CHECKED NAME=".$name."[] VALUE=$value>$label$eoln");
	}
}

function PasswordCheck($RallyeID, $Password)
{
	global $oDB;
	
	$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID AND Password = PASSWORD('$Password')";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray(false);
	
	return(count($arr));
}

function ScorePasswordCheck($RallyeID, $Password)
{
	global $oDB;
	
	$strSQL="SELECT * FROM ars_rallye_base WHERE RallyeID = $RallyeID AND (Password = PASSWORD('$Password') OR ScoreOnlyPassword = PASSWORD('$Password'))";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray(false);
	
	return(count($arr));
}

function Ordinal($i)
{
	$i = intval($i);
	
	if($i == 1) $th = "<sup><U>st</U></sup>";
	else if($i == 2) $th = "<sup><U>nd</U></sup>";
	else if($i == 3) $th = "<sup><U>rd</U></sup>";
	else $th = "<sup><U>th</U></sup>"; // hense the name
	
	return($i.$th);
}

function SetStep($Step,$RallyeID)
{
	//print("SetStep($Step,$RallyeID)<BR>");

	global $oDB;
	
	$RallyeID = intval($RallyeID);
	$Step = intval($Step);
	
	$strSQL = "SELECT Steps FROM ars_rallye_base WHERE RallyeID = $RallyeID";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray();
	if(count($arr))
	{
		$Steps = intval($arr[0][0]);
		$Steps |= $Step;
		
		$strSQL = "UPDATE ars_rallye_base SET Steps = $Steps WHERE RallyeID = $RallyeID";
		//print("$strSQL<BR>");
		$oDB->Query($strSQL);
		return(1);
	}
	
	return(false);
}

function IsSetStep($Step,$RallyeID)
{
	global $oDB;
	
	$RallyeID = intval($RallyeID);
	$Step = intval($Step);
	
	$strSQL = "SELECT Steps FROM ars_rallye_base WHERE RallyeID = $RallyeID";
	$oDB->Query($strSQL);
	$arr = $oDB->GetRecordArray();
	if(count($arr))
	{
		$Steps = intval($arr[0][0]);
		
		if($Steps >= 0 && $Step == 0)
			return true;
		
		if($Steps &= $Step)
			return(true);
		else
			return(false);
	}
}
?>
