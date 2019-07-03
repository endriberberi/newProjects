<?
  define("APPLICATION_STATE", "BO");	//BO;FE

//  set_time_limit(0);
/*$valid_var_request  = "";
$valid_var_request .= "mainWin,Y;";

  $valid_var_request .= "vars_page,Y;"; //TANI: duhet per kete aplikim ISHP
  $valid_var_request .= "vars_post,Y;";
  $valid_var_request .= "tipi_exp,Y;";  
  $valid_var_request .= "tipi_exp,f_only_numbers;";*/


  $app_path = dirname(__FILE__);
  DEFINE("APP_PATH",		$app_path."/");

  DEFINE("CONFIG_PATH",		APP_PATH."config/");
  DEFINE("DEBUG",			"0");

  INCLUDE CONFIG_PATH."app_valid_var_request.php";

  INCLUDE CONFIG_PATH."const.Paths.php";
  INCLUDE CONFIG_PATH."const.Nems.php";

  //include configuration features and modules
  INCLUDE CONFIG_PATH."const.Config.php";

  //include configuration constants
  INCLUDE CONFIG_PATH."const.DB_ad.php";
  INCLUDE CONFIG_PATH."const.Settings1.php";

  //include the WebApp framework
  INCLUDE WEBAPP_PATH."WebApp.php";

  INCLUDE EASY_PATH."inc/php/php_session_start.php";

  //add some template variables that are commonly used
  INCLUDE INCLUDE_PATH."add_app_vars.php";
  INCLUDE INCLUDE_PATH."htmlMimeMail.php";

  INCLUDE APP_PATH."include_php/app_fun/app_fun.php";
  REQUIRE_ONCE(INC_PHP_AJAX."NemsManager.class.php");