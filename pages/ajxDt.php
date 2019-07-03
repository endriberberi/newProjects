<?php
global $postParamsOri;
if (isset($_POST))
	$postParamsOri = $_POST;


define("BO_ENVIRONMENT", "AJAX_RESP_FE");
INCLUDE_ONCE "application.php";
header("Content-Type: text/html; charset=" . APP_ENCODING);

INCLUDE_ONCE "application.php";
require_once INC_PATH."ajax.fe.app.class.php";

$ajaxAppFe = new ajaxAppFe();
$ajaxAppFe->ajaxEventHandler();