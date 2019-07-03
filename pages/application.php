<?
  define("APPLICATION_STATE", "FE");	//BO;FE
/*
  //KETU DEKLAROHEN VARIABLAT E SESIONIT TE PHP SPECIFIKE PER CDO APLIKIM QE NUK DUHEN FSHIRE NGA WEBAPP --
  //FORMATI : EMER_VARIABLI,Y; -> PRA E DEKLAROJME NE FILLIM
  //ME PAS MUND TE DEKLAROJME FUNKSIONET E VALIDIMIT PER KETE VARIABEL NESE KA NEVOJE
  //define("PHP_VALID_VAR_SESSION", "unic,Y;unic,f_pozitive_numbers");	TO_HTML_ENTITIES
  //------------------------------------------------------------------------------------------------  

  //KETU DEKLAROHEN VARIABLAT E SESIONIT SPECIFIKE PER CDO APLIKIM QE NUK DUHEN FSHIRE NGA WEBAPP --
  //FORMATI : EMER_VARIABLI,Y; -> PRA E DEKLAROJME NE FILLIM
  //ME PAS MUND TE DEKLAROJME FUNKSIONET E VALIDIMIT PER KETE VARIABEL NESE KA NEVOJE
    //define("APP_VALID_VAR_SESSION", "nr_rec_page,Y;nr_rec_page,ONLY_NUMBERS;xx,Y;xx,ONLY_NUMBERS");	
  //------------------------------------------------------------------------------------------------  

  //KETU DEKLAROHEN VARIABLAT REQUEST SPECIFIKE PER CDO APLIKIM QE NUK DUHEN FSHIRE NGA WEBAPP -----
  //KETO JANE VARIABLAT QE VIJNE ME _GET OSE _POST
  //FORMATI : EMER_VARIABLI,Y; -> PRA E DEKLAROJME NE FILLIM
  //ME PAS MUND TE DEKLAROJME FUNKSIONET E VALIDIMIT PER KETE VARIABEL NESE KA NEVOJE
    //define("APP_VALID_VAR_REQUEST", "nr_rec_page,Y;nr_rec_page,ONLY_NUMBERS;xx,Y;xx,ONLY_NUMBERS");	
  //------------------------------------------------------------------------------------------------ 
      //APP_VALID_VAR_REQUEST

define("PHP_VALID_VAR_SESSION", "");
define("APP_VALID_VAR_SESSION", "simpleModePreview,Y;simpleEditAuthoring,Y");	



$valid_var_request  = "";
 
  //variabla per rss feed


  $valid_var_request .= "idb,Y;";
  $valid_var_request .= "nrpage,Y;";
  $valid_var_request .= "nrpage,f_only_numbers;";
  
  $valid_var_request .= "idkd,Y;";
  $valid_var_request .= "idkd,f_only_numbers_minus;";
  
  $valid_var_request .= "prm,Y;";
  $valid_var_request .= "prm,f_safe_unserialize;";
  
  $valid_var_request .= "search,Y;";
  $valid_var_request .= "search,f_full_escape_text;";

  $valid_var_request .= "email,Y;";
  $valid_var_request .= "email,f_full_escape_text;";
  $valid_var_request .= "firstname,Y;";
  $valid_var_request .= "firstname,f_full_escape_text;";
  $valid_var_request .= "lastname,Y;";
  $valid_var_request .= "lastname,f_full_escape_text;";
  $valid_var_request .= "salutation,Y;";
  $valid_var_request .= "salutation,f_full_escape_text;";
  $valid_var_request .= "cis,Y;";
  $valid_var_request .= "cis,f_only_numbers;";
  $valid_var_request .= "register_newsletter,Y;";
  $valid_var_request .= "register_newsletter,f_full_escape_text;";

  $valid_var_request .= "file_id,Y;";				//parameter i nevojshem te marre te dhenat e SL item
  $valid_var_request .= "file_id,f_only_numbers;";
  $valid_var_request .= "CID,Y;";					//parameter i nevojshem te marre te dhenat e SL item
  $valid_var_request .= "CID,f_only_numbers;";
  $valid_var_request .= "file_name,Y;";				//parameter i nevojshem te marre te dhenat e SL item
  $valid_var_request .= "file_name,f_full_escape_text;";
          	
   //variabla per kalendarin e  eventeve
  
  $valid_var_request .= "apprcss,Y;"; //emri i procesit 
  $valid_var_request .= "act,Y;"; //action
 

  $valid_var_request .= "vars_page,Y;"; //TANI: duhet per kete aplikim ISHP
  $valid_var_request .= "vars_post,Y;";
  $valid_var_request .= "tipi_exp,Y;";  
  $valid_var_request .= "tipi_exp,f_only_numbers;";
 
	*/
  //set_time_limit(0);
  $app_path = dirname(__FILE__);
  DEFINE("APP_PATH",		$app_path."/");

  DEFINE("CONFIG_PATH",	APP_PATH."config/");
  DEFINE("DEBUG",			"0");

  INCLUDE CONFIG_PATH."app_valid_var_request.php";

  INCLUDE CONFIG_PATH."const.Paths.php";
  INCLUDE CONFIG_PATH."const.Nems.php";

  define("APP_VALID_VAR_REQUEST", $valid_var_request);	//tani duhet pas const.Paths

  //include configuration features and modules
  INCLUDE CONFIG_PATH."const.Config.php";

  //include configuration constants
  INCLUDE CONFIG_PATH."const.DB.php";
  INCLUDE CONFIG_PATH."const.Settings.php";

  INCLUDE EASY_PATH."inc/php/php_session_start.php";

  //include the WebApp framework
  INCLUDE WEBAPP_PATH."WebApp.php";

  //add some template variables that are commonly used
  INCLUDE INCLUDE_PATH."add_app_vars.php";
  INCLUDE INCLUDE_PATH."htmlMimeMail.php";

  INCLUDE APP_PATH."include_php/app_fun/app_fun.php";
  REQUIRE_ONCE(INC_PHP_AJAX."NemsManager.class.php");