<?php
require "../../../ini/dbconnect.php";

if(isset($_POST["user"])){
        
    $user=$_POST["user"];
        $q="SELECT alias_code FROM (
                    (SELECT  `ref_id` ,  `user` ,  `alias_code` ,  `cal_type` 
                    FROM  `sips_sd_agent_ref` a
                    INNER JOIN sips_sd_schedulers b ON a.id_calendar = b.id_scheduler
                    WHERE cal_type ='SCHEDULER')
                UNION ALL (SELECT  `ref_id` ,  `user`,  `alias_code`,  `cal_type`
                    FROM  `sips_sd_agent_ref` a
                    INNER JOIN sips_sd_resources b ON a.id_calendar = b.id_resource
                    WHERE cal_type ='RESOURCE'))a
                    INNER JOIN vicidial_users b ON a.user = b.user
                WHERE b.user='$user' and alias_code like '%/%' limit 1";
        
        $result=mysql_query($q,$link);
        $row= mysql_fetch_assoc($result);
        echo json_encode(array("alias"=>$row["alias_code"]));
}