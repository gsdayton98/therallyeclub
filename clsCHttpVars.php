<?PHP
// static variables for CHttpVars
$CHttpVars_allVars = array();

class CHttpVars
{
	
	function /*boolean*/ IsEmpty(/*String*/ $paramName)
	{
		if (isset($_REQUEST[$paramName])) 
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	
	function /*int or String*/ Get(/*String*/ $paramName , $ignoreError=true)
	{
		$val = @$_REQUEST[$paramName];
		return $val;
	}
	
	function /*int or String*/ GetValue(/*String*/ $paramName , $ignoreError=true)
	{
		return CHttpVars::Get($paramName,$ignoreError);
	}

	/*
		GetInt() returns the value of $paramName if it is an int or calls ShowError()
	
	*/
	function /*int*/ GetInt(/*String*/ $name)
	{
		$val = intval(CHttpVars::Get($name));

		return $val;
	}
	
	function /*int*/ GetArray(/*String*/ $name)
	{
		$val = CHttpVars::Get($name);
		if(is_array($val))
			return $val;
      
		if($val!='')
        		return array($val);
        
		return array();
	}

	function /*array*/ GetRequestArray()
	{
		return $_REQUEST;
	}

	function GetQueryString()
	{
		$query='';
		foreach($_REQUEST as $k=>$v)
		{
			if($k!='PHPSESSID')
			{
				if(is_array($v))
				{
					foreach($v as $_k=>$_v)
					{
						if($v!='') $query.=$k.'['.urlencode($_k).']='.$_v.'&'; 
					}
				}
				else
				{
					if($v!='') $query.="$k=".urlencode($v)."&"; 
				}
			}
		}
		
		return substr($query,0,strlen($query)-1);
	} 
}

?>