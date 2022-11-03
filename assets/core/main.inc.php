<?php

function showFileRow($query_result)
{
	global $db;
	$html='';
	foreach($query_result as $id => $row)
	{
		$row_key=$row['id'];
		$row_filename=$row['filename'];
		$array = array("FILE_NAME" => $row_filename,
		"DELETE_ID" => $row_key."_delete");
		$html .=  process_template("metadata_row_header",$array);

		foreach($row as $key => $value )
		{
			if ($key == "id" ) {
				$html .= "<input type='hidden' value='".$row_key."' name='".$row_key."_".$key."'>";
				continue;
			}
			if ($key == "filename" ) {
				continue;
			}
			
			$default = "placeholder=\"".$value."\"";

			if ($value == "" ){
				$default = "placeholder";
			}
			
			
			 $array = array(
				"FIELD_KEY" => $key,
				"FIELD_NAME" =>$row_key."_".$key,
				"VALUE" =>  $default);

		$html .=  process_template("metadata_row",$array);
			
		
		}
		
	}
	echo $html;
}


function doRequest($request, $callback, $return=0, $redirect=false){
	global $_REQUEST;
	
	#if (array_key_exists($request, $_REQUEST)) {
	#	echo "The 'first' element is in the array";
	#	return $return;
	#}

	$arr=array_keys($_REQUEST, $request, true);
	
	if(count($arr) > 0 ) {
		$request = $arr[0];
	}
	
	if (isset($_REQUEST[$request]) ) 
	{
		return $callback($_REQUEST, $redirect );
		
	} else {
		return 0;
	}
}



function missingArtist($key, $row) {
	global $studio_pattern;
	global $__namesArray;
	global $artistNameFixArray;

	global $studio_ignore;
	
	$alt_studio='';
	$value_array = array();
	if ($row['studio_a'] != "" )
	{				
		$match_studio=$row['studio_a'];
		$alt_studio=strtolower(str_replace(" ","_",$row['studio']));
	} else {
		$match_studio=$row['studio'];
	}

	$studio_match=strtolower(str_replace(" ","_",$match_studio));

	
	unset($__match);
	if(key_exists($studio_match,$studio_pattern) )
	{

		$__match = $studio_match;
		print_r2($__match);

	} else if(key_exists($alt_studio,$studio_pattern) ){
		$__match = $alt_studio;
	
	}

//print_r2($studio_ignore);
//print_r2(str_replace(" ","_",strtolower($row['studio_a'])));
//echo in_array(str_replace(" ","_",strtolower($row['studio_a'])), $studio_ignore );
	if( in_array(str_replace(" ","_",strtolower($row['studio_a'])), $studio_ignore ) == true) {
			unset($__match);
		}
		
	if(isset($__match)){
	
		$pattern=$studio_pattern[$__match]['artist']['pattern'];
		$delimeter=$studio_pattern[$__match]['artist']['delimeter'];
		$group=$studio_pattern[$__match]['artist']['group'];

		preg_match($pattern,$row['filename'],$matches);
	

		if(count($matches) > 0) {
			$names_array = explode($delimeter,$matches[$group]);
			$name_list="";
			$full_name_array=array();
			
			foreach ($names_array as $name)
			{
				$pieces = preg_split('/(?=[A-Z_])/',$name);
				$full_name="";
				foreach($pieces as $part)
				{									

					$part=str_replace(" ","",$part);

					if($part == "") { continue; }
					if($part == "_") { continue; }
					$full_name .=" ".$part;

				}
				$full_name=trim($full_name);
				if( array_search(str_replace(" ","",strtolower($full_name)), $__namesArray) == false) {
					
					if (array_key_exists($full_name,$artistNameFixArray))
					{
						$full_name = $artistNameFixArray[$full_name];
					}
					$full_name_array[] = ucfirst($full_name);
				}
				
			
			}
			$name_list = implode(", ",$full_name_array);					
			$value_array=array($key => array($name_list));
		}
	}
	return $value_array;
}

function missingTitle($key, $row)
{
	global $studio_pattern;
	global $__namesArray;

	
	$value_array = array();
	if ($row['studio_a'] != "" )
	{
		$match_studio=$row['studio_a'];
	} else {
		$match_studio=$row['studio'];
	}

	$studio_match=strtolower(str_replace(" ","_",$match_studio));
	$alt_studio=strtolower(str_replace(" ","_",$row['studio']));
	unset($__match);
	if(key_exists($studio_match,$studio_pattern) )
	{
		$__match = $studio_match;
	} else if(key_exists($alt_studio,$studio_pattern) ){
		$__match = $alt_studio;
	
	}
		
	if(isset($__match))
	{
		if(key_exists("title",$studio_pattern[$__match]) ) 
		{
			$pattern=$studio_pattern[$__match]['title']['pattern'];
			$group=$studio_pattern[$__match]['title']['group'];

			preg_match($pattern,$row['filename'],$matches);
				
			if(count($matches) > 0) {
				$title = $matches[$group];
				$title=strtolower(str_replace("_"," ",$title));
				$title=ucwords($title) ;
				$value_array=array($key => array($title));
			}
		}
	}
	return $value_array;
}

function deleteEntry($data_array, $redirect=false, $timeout=4)
{
	global $db;
	
	foreach ($data_array as $key => $value )
	{	
		if(str_contains($key, "_") == true ) 
		{
			$value=trim($value);
			
			if($value != "") {
				$pcs= explode("_",$key);
				$id=$pcs[0];
				$field=$pcs[1];
				if ($field == "delete" ) {					
					$db->where ('id', $id);
					$db->delete (Db_TABLE_FILEDB);
				}
			}
		}
	}
	if ($redirect != false ) {
		return JavaRefresh($redirect,$timeout);
	}
}

function saveData($data_array, $redirect=false, $timeout=4)
{
	global $db;
	
	foreach ($data_array as $key => $value )
	{		
		if(str_contains($key, "_") == true ) 
		{
			$value=trim($value);
			
			if($value != "") {
				$pcs= explode("_",$key);

				$id=$pcs[0];
				$field=$pcs[1];
				
				if ($field == "id" ) {
					continue;
				}
				
				if(isset($pcs[2])) {
					$field.="_".$pcs[2];
				}
		
				if ($value == "NULL") {
					$sql = "UPDATE ".Db_TABLE_FILEDB."  SET `".$field."` = NULL WHERE id = ".$id;
					$db->query($sql);
				} else {
					
					if($field == "artist") {
						if(str_contains($value, ",") == true ) 
						{
							$value=str_replace(" ,",",",$value);
							$value=str_replace(", ",",",$value);
						}
						
						$names_arr = explode(",",$value);
						$names_list="";
						
						foreach( $names_arr as $str_name )
						{
							$str_name=ucwords(strtolower($str_name));
							$names_list = $str_name.",".$names_list;
						}
						
						$value=rtrim($names_list, ',');
					}
					
					$value=trim($value);
					
					logger("Field Name",$field);
					logger("Field Value",$value);

					$data = array($field => $value );
					$db->where ('id', $id);
					$db->update (Db_TABLE_FILEDB, $data);
				}
			}
		}
	}
	if ($redirect != false ) {
		return JavaRefresh($redirect,$timeout);
	}
	
}

function myHeader($redirect = __URL_PATH__."/home.php")
{
    
    
    
    header( "refresh:0;url=".$redirect);
    
}


function getBaseUrl($pathOnly=false) 
{
	// output: /myproject/index.php
	$currentPath = $_SERVER['PHP_SELF']; 
	
	// output: Array ( [dirname] => /myproject [basename] => index.php [extension] => php [filename] => index ) 
	$pathInfo = pathinfo($currentPath); 
	
	// output: localhost
	$hostName = $_SERVER['HTTP_HOST']; 
	
	// output: http://
	$protocol = strtolower(substr($_SERVER["SERVER_PROTOCOL"],0,5))=='https://'?'https://':'http://';
	
    if($pathOnly == true ) return $protocol.$hostName.$pathInfo['dirname']."/";
	// return: http://localhost/myproject/
	return $protocol.$hostName.$pathInfo['dirname']."/";
}


function print_r2($val){
        echo '<pre>';
        print_r($val);
        echo  '</pre>';
}




function toint($string)
{
    
    $string_ret = str_replace(",","",$string);
    return $string_ret;
}
