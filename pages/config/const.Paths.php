<?
//shikohet nese jemi nen https --------------------------------------------------------------------------------------------------------------
  $httpss = "";
  IF (ISSET($_SERVER["HTTPS"]) AND ($_SERVER["HTTPS"] == "on"))
     {
      $httpss = "s";
     }
//shikohet nese jemi nen https --------------------------------------------------------------------------------------------------------------


//E:\projects_114\ASP4v\ishp\config\const.Paths.php

if (stristr($_SERVER["HTTP_HOST"], '.arkit'))
{
	define("APP_URL", 				"http".$httpss."://".$_SERVER["HTTP_HOST"]."/");
	define("APP_FRONT_URL",			"/");
	define("APP_URL_DOMAIN_NAME", 	"/");
	define("EASY_URL", "http".$httpss."://".$_SERVER["HTTP_HOST"]."/asp4_nobug/");
	
} else {
	define("APP_URL", 				"http".$httpss."://".$_SERVER["HTTP_HOST"]."/ishp/");
	define("APP_FRONT_URL",			"ishp/");
	define("APP_URL_DOMAIN_NAME", 	"ishp/");

	define("EASY_URL", 				"http".$httpss."://".$_SERVER["HTTP_HOST"]."/asp4_nobug/");
}


define("BO_PATH",					"adm/");
define("BO_PATH_PHP",				"application_window.php");


define("AJAX_BO_PATH", 	APP_URL."adm/ajxrsp.php");
//define("APP_STATE",                    "development");

//adresa e backoffice
define("APP_BACK_URL",				"admin/");
define("APP_ADMIN_PATH",			"admin/");

//case ASP
define("EASY_PATH",		"/var/www/html/asp4_nobug/");

define("APP_PHP_PATH",			APP_URL."include_php/");
define("INCLUDE_PATH",			EASY_PATH."include_php/");


//constants of the paths in the application
define("WEBAPP_PATH",			EASY_PATH."web_app/");

define("STYLE_DEFAULT",				APP_URL."include_css/default.css");
define("STYLE_DEFAULT_PATH",		APP_PATH."include_css/default.css");
define("STYLE_FONTICON_PATH",		APP_PATH."include_css/icons/icomoonultimate/iconmoon.css");
define("STYLE_FONTICON_SPECIFIC",	"yes");


/////rss
define("SYSTEM_NAME_DEFAULT",			"ishp");			// jep emrin e website-it
define("RSS_DESCRIPTION",        		"ishp");			// feed description:

define("SYSTEM_NAME_DEFAULT_EN",		"ishp");			// jep emrin e website-it
define("RSS_DESCRIPTION_EN",        	"by ishp");			// feed description:

define("RSS_LOGO_EXIST",          		"NO");								// in NO will not be displayed
define("RSS_LOGO_PATH",          		APP_URL."show_image.php?file_id=2");	// logo path
define("SPECIFIK_TOOLS",				APP_PATH."modules_back/tools/");

define("RC_FOLDER_NAME", 				"rc/");
define("RC_FOLDER_NAME_VERSION", 		"rc_version/");

define("RC_FOLDER_PATH", 				APP_PATH."rc/");
define("RC_FOLDER_PATH_VERSION", 		APP_PATH."rc_version/");

define("APP_URL_DOMAIN", "http".$httpss."://".$_SERVER["HTTP_HOST"]."/");
define("RC_FOLDER_URL", 				APP_URL_DOMAIN.APP_URL_DOMAIN_NAME."rc/");

//Google API keys---------------------------------------------------
//define("GMAP_JS_API_KEY",			"AIzaSyC4pmI93_AMcfBH4VSLN1LCX1phGC-ONek");
//---------------------------------------------------------------

define("STREAMING_URL",    APP_URL);

//define("EXAMINATION_MESSAGE_FILE",    "learningEvaluation");
//define("EXAMINATION_FOLDER_NAME",     "mpesaElearning/Examination/");


//define("WS_OFFCE_CONVERT_URL",    "http://192.168.1.119/office_doc_processing/");
//define("WS_OFFCE_CONVERT_USER",   "office_convert");
//define("WS_OFFCE_CONVERT_PASSWD", "office2010");
define("ELEARNING_USER_PLATFORM_CLASS", APP_PATH."include_php/eLearning.user.platform.class.php");

//CELESAT PER ENKRPT DEKRYP ME PROGRAMIN E MENAXHIMIT -----------------------------------------------------
  define("DESK_KEY",    "gR12!Y9&s*-p7fpV,WtkjatMO4sFYCBc");
  define("DESK_IV",     "YuWWcqI65XUEj554");
//CELESAT PER ENKRPT DEKRYP ME PROGRAMIN DESKTOP ----------------------------------------------------------

//SEND_EMAIL ----------------------------------------------------------------------------------------------
  DEFINE("SMTP_HOST", "arkit.ch");
  DEFINE("SMTP_PORT", 587);
  DEFINE("SMTP_AUTH", true);
  DEFINE("SMTP_USER", "officeaccount");
  DEFINE("SMTP_PASS", "23@456smtp");
  
  DEFINE("SYSTEM_EMAIL_SEND",       "Y"); //VLERAT Y/N ;
  DEFINE("SYSTEM_EMAIL_DEFAULT",    "info@arkit.ch");
  
  DEFINE("SYSTEM_EMAIL_FROM",       "info@arkit.ch");
  DEFINE("SYSTEM_EMAIL_FROM_LABEL", "SISI");
  //DEFINE("SYSTEM_EMAIL_TO",       "info@arkit.ch");
  //DEFINE("SYSTEM_EMAIL_BCC",      "info@arkit.ch");
  DEFINE("SYSTEM_EMAIL_SUBJECT",    "SISI");
//SEND_EMAIL ----------------------------------------------------------------------------------------------

//VARIABLAT PER UPLODIMIN E EKEDAREVE ---------------------------------------------------------------------
  DEFINE("USER_SUDO",               "sudo -u aspcache ");                       //USERI QE PERDORET PER KOPJIMIN E SKEDAREVE
  DEFINE("PATH_ROOT_DOCS",          "/var/www/app_ishp_docs/docs/");            //direktoria rrot ku do ruhen dokumentat qe uplodohen ... duhet / ne fund
  DEFINE("PATH_SCRIPT_COPY_FILE",   "/var/www/app_ishp_docs/file_copy.php");    //ky skedar DUHET TE RRIJE NE NJE DIREKTORI JASHTE APLIKIMIT DHE TE TE KONFIGUROHEn  TE DREJTAT ME USERIN ASPCHACHE
  DEFINE("PATH_SCRIPT_DELETE_FILE", "/var/www/app_ishp_docs/file_delete.php");  //ky skedar DUHET TE RRIJE NE NJE DIREKTORI JASHTE APLIKIMIT DHE TE TE KONFIGUROHEn  TE DREJTAT ME USERIN ASPCHACHE
//VARIABLAT PER UPLODIMIN E EKEDAREVE ---------------------------------------------------------------------
?>