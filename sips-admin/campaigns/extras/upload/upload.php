<?php foreach ($_POST as $key => $value) { 
	${$key} = $value;
}
foreach ($_GET as $key => $value) {
	${$key} = $value;
}
ini_set("display_errors", "1");

$Timestamp = date("dmY-His");
$UploadedFile = $Timestamp."_".preg_replace("/[^-\.\_0-9a-zA-Z]/" , "_" , $_FILES['input-db-wizard-upload']['name']);
$ConvertedFile = preg_replace("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", '.txt', $UploadedFile);
move_uploaded_file($_FILES['input-db-wizard-upload']['tmp_name'], "/tmp/".$UploadedFile);
$ConvertCommand = $_SERVER["DOCUMENT_ROOT"]."/sips-admin/campaigns/extras/upload/sheet2tab.pl /tmp/$UploadedFile /tmp/$ConvertedFile";
passthru("$ConvertCommand");
echo $ConvertedFile;

/*


// ACTIONS


if($action=="submit_campaign_fields")
{
	// REGEX
	$field_regx = "/['\"`\\;]/";
	
	// INIS
	$entry_date = date("Y-m-d H:i:s");
	$last_local_call_time = "2008-01-01 00:00:00";
	$gmt_offset = '0';
	$called_since_last_reset = 'N';
	
	$file = fopen("upload/$sent_file_name", "r");
	$headers = explode("\t", rtrim(fgets($file, 4096)));
	
	
       for ($i=0;$i<count($sent_match_ids);$i++)
        {
            if (count($sent_match_ids) == 1) 
                {
                $field_ids_sql = $sent_match_ids[$i]; 
                }
            elseif (count($sent_match_ids) - 1 == $i)
                {
                $field_ids_sql .= $sent_match_ids[$i];    
                }    
            else
                {
                $field_ids_sql .= $sent_match_ids[$i].", ";
                }    
        }
	
	//echo $field_ids_sql;
	while (!feof($file)) 
	{
		$buffer = rtrim(fgets($file, 4096));
		if(strlen($buffer)>0) {
		
		$buffer = stripslashes($buffer);
		$buffer = explode("\t", $buffer);
		$headers_ids_sql = "";
		
				for ($i=0;$i<count($sent_match_headers);$i++)
				{
					if($sent_match_ids[$i]=="PHONE_NUMBER" || $sent_match_ids[$i]=="ALT_PHONE" || $sent_match_ids[$i]=="ADDRESS3" ){
						
						$buffer[$sent_match_headers[$i]] = preg_replace("/[^0-9]/", "", $buffer[$sent_match_headers[$i]]);
						
						}
					
					if (count($sent_match_headers) == 1){
		
							$headers_ids_sql = "'".preg_replace($field_regx, "", $buffer[$sent_match_headers[$i]])."'";
		
					}
					elseif (count($sent_match_headers) - 1 == $i)
						{
		
							$headers_ids_sql .= "'".preg_replace($field_regx, "", $buffer[$sent_match_headers[$i]])."'";
		
					} else {
						$headers_ids_sql .= "'".preg_replace($field_regx, "", $buffer[$sent_match_headers[$i]])."', ";
						}    
				}
				
				//echo $headers_ids_sql."<br><br>";
				mysql_query("INSERT INTO vicidial_list (".$field_ids_sql.", entry_date, called_since_last_reset, gmt_offset_now, last_local_call_time, list_id, status ) VALUES (".$headers_ids_sql.", '$entry_date', '$called_since_last_reset', '$gmt_offset', '$last_local_call_time', '$sent_list_id', 'NEW')", $link) or die(mysql_error);
		}
		
	} 
	
	
	
	//$buffer = rtrim(fgets($file, 4096));
	//$buffer = explode("\t", $buffer);
	fclose($file);
	//echo json_encode( $final );
exit();
}

//print_r($_FILES);

/*


$timestamp = date("dmY-His");
$uploaded_file = preg_replace("/[^-\.\_0-9a-zA-Z]/","_",$_FILES['fileToUpload']['name']);;
$filename = $timestamp."-original-".$uploaded_file;
$copyfilename = $timestamp."-copy-".$uploaded_file;
$convertedfilename = $timestamp."-converted-".preg_replace("/\.csv$|\.xls$|\.xlsx$|\.ods$|\.sxc$/i", '.txt', $uploaded_file);

move_uploaded_file($_FILES['fileToUpload']['tmp_name'], "upload/$filename");
copy("upload/$filename", "upload/$copyfilename");
$convert = "/srv/www/htdocs/sips-admin/campaign_manager/wizard_intra_justicia/sheet2tab.pl /srv/www/htdocs/sips-admin/campaign_manager/wizard_intra_justicia/upload/$copyfilename /srv/www/htdocs/sips-admin/campaign_manager/wizard_intra_justicia/upload/$convertedfilename";
passthru("$convert");

echo $convertedfilename;

*/