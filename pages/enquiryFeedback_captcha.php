<?php
IF (ISSET($_GET["uni"]) AND ($_GET["uni"] != "") AND ISSET($_GET["idstemp"]) AND ($_GET["idstemp"] != ""))
   {
    $reload_sel = "N";
    IF (ISSET($_GET["reload"]) AND ($_GET["reload"] == "R"))
       {
        $reload_sel = "R";
       }
    
    INCLUDE_ONCE dirname(__FILE__)."/application.php";
    INCLUDE ASP_FRONT_PATH."php/enquiryFeedback_captcha.php";
   }
?>