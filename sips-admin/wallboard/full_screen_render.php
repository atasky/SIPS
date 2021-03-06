<?php
require("../../ini/dbconnect.php");
require("../../ini/user.php");
$user = new user;
if (!$user->id) {
    header("WWW-Authenticate: Basic realm=\"Go Contact Center\"");
    header('HTTP/1.0 401 Unauthorized');
    exit;
}
?>

<!DOCTYPE HTML>
<html>

    <head>
        <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=utf-8">
        <title>Fullscreen</title> 
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/jquery.jgrowl.css">
        <link type="text/css" rel="stylesheet" href="/jquery/themes/flick/bootstrap.css">
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/style.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap-responsive.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/css/bootstrap.css" />
        <link type="text/css" rel="stylesheet" href="/bootstrap/icon/font-awesome.css" />
        <script type="text/javascript" src="/jquery/jquery-1.8.3.js"></script>
        <script type="text/javascript" src="/jquery/jqueryUI/jquery-ui-1.10.2.custom.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/chosen.jquery.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/warnings-api.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.flot.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.flot.pie.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.flot.resize.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.flot.time.min.js"></script>
        <script type="text/javascript" src="/bootstrap/js/jquery.jgrowl.js"></script>
        <script  type="text/javascript" src="/bootstrap/js/jquery.cookie.js"></script>
                <script type="text/javascript" src="/bootstrap/js/moment.min.js"></script>
        <style>
            .PanelWB{position:absolute;
                     -webkit-border-radius: 3px;
                     -moz-border-radius: 3px;
                     border-radius: 4px;
                     box-shadow: 5px 5px 13px -6px rgba(0, 0, 0, 0.3) ;
            }





            .inbound_grid_title{
                border-radius: 10px;
                border-bottom:3px solid #FFFFFF;
                background-color: #050430;
                height:1.5em;
                font-size:0.85em;
                color: #FFFFFF;
                padding-top:2%;
                padding-bottom: 0.3%;
                text-align: center; 
            }

            .inbound_grid_content{

                text-align: center;
                color: #000000;
                font-size: 2em;
                padding-top:0.25em;


            }
            .inbound_grid_div{
                width:9.5em;
                height:4.5em;
                border-radius: 15px;
                border:3px solid rgb(43, 80, 38);
                box-shadow: 5px 5px 13px -6px rgba(0, 0, 0, 0.3) ;
                background-color: rgb(220, 220, 220);
            }

            .inbound_grid_div_large{
                width:11em;
                height:4.5em;
                border-radius: 15px;
                border:3px solid rgb(43, 80, 38);
                box-shadow: 5px 5px 13px -6px rgba(0, 0, 0, 0.3) ;
                background-color: rgb(220, 220, 220);
            }
            .inbound_title{
                border:3px solid rgb(43, 80, 38);
                text-align: center; 
                font-size: 1.9em;
                border-radius: 10px;
                color: #FFFFFF;
                padding-top:0.8%;
                padding-bottom:0.8%;
                background-color:rgb(38, 52, 109);
            }
            .legend {
                display:block!important;

            }


            .legend div {
                background-color: transparent!important;
            }

            .legend_inbound .legend div {
                background-color: #FFFFFF!important;
            }




            .label{
                border-radius: 0px;
            }

            #closebutton {
                position: relative;
                top: 5%; right: 10%;
                width: 28px; height: 28px
            }
            body
            {
                overflow: hidden;  
            }
            .popover{width:auto;}



            .pull-right-letter_button
            {
                position: absolute;
                margin-top: 7px;
                display: none;
                right:45%;

            }

        </style>




    </head>    

    <body>

        <div style="width:100%;height:100%;">

            <div id="MainLayout"></div>
            <div id="jGrowl" class="top-right jGrowl" ><div class="jGrowl-notification" ></div></div>
        </div>
        <script type="text/javascript" src="full_screen_render.js"></script>
    </body>


</html>




