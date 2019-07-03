<?


$start_time = microtime(true);
/*********************   page caching ****************************************************/
// define how long we want to keep the file in seconds.
/*echo "<textarea>";
print_r($_SERVER);
print_r($_POST);
echo "</textarea>";*/

$cachetime = 60*60;

// define the path and name of cached file\
$cachefile='';
//if($_SERVER['REQUEST_METHOD'] != 'POST')	//
/*if($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_URI'] != '/') // && count($_SERVER['argv']) == 0
{

	$cachefile = 'cache/'.md5($_SERVER['HTTP_HOST'].'-'.$_SERVER['REQUEST_URI']).'.php';

	// Check if the cached file is still fresh. If it is, serve it up and exit.
	if (file_exists($cachefile) && time() - $cachetime < filemtime($cachefile)) {
			//trigger_error('cached file:'.$cachefile.' uri:'.$_SERVER['REQUEST_URI']);
			readfile($cachefile);
			$page_load_time = microtime(true) - $start_time;
			echo "<div style=\"z-index:9999;clear:both\">";
			echo "<pre>";
			printf("Page Loading: %f seconds | memory usage: ". round(memory_get_usage(true)/1024,2)." kb", $page_load_time);
			echo "</pre>";
			echo "</div>";
			exit;
	}
	// if there is either no file OR the file to too old, render the page and capture the HTML.
	ob_start();

} else {
	//kjo ketu fshin filen e cachuar nese ka post. rregullonte rastin leximit te filave te cachuar pas logout
	$cachefiletoRmv = 'cache/'.md5($_SERVER['HTTP_HOST'].'-'.$_SERVER['REQUEST_URI']).'.php';
	if (file_exists($cachefiletoRmv)) {

		unlink($cachefiletoRmv);
		//echo $cachefiletoRmv."FSHIHET FILE I CACHUAR";
	}
}*/
/*****************************************************************************************/

define("BO_ENVIRONMENT", "APPLICATION");

INCLUDE_ONCE dirname(__FILE__)."/application.php";

GLOBAL $VS,$global_cache_dynamic,$cacheDyn;
require_once(ASP_FRONT_PATH."php/ValidateState.class.php");


$VS = new ValidateState();



if ($event->target=="none") { //pasi validatori ka mbaruar pune me validimet kontrollohet nese ka ndonje free event per tu ekzekutuar
	WebApp::callFreeEvent($event);
}

$head_file 	= TPL_PATH."head.html";
$tpl_file 	= TPL_PATH."main.html";
//$zoneMasterTemplate = WebApp::getZoneMasterTemplate();


IF (isset($_GET["mode"]) AND ($_GET["mode"] == "alone")) {
	$session->Vars["mode"] = "alone";
}

IF (isset($session->Vars["mode"]) AND ($session->Vars["mode"] == "alone")) {
	$session->Vars["tplCase"] = "";
	$head_file 	= "/head.html";
	$tpl_file 	= "/mainAlone.html";
} else {
	$session->Vars["tplCase"] = "";
	$head_file 	= "/head.html";
	$tpl_file 	= "/main.html";
}


if (isset($session->Vars["lang"])) {
    $lang_sel=strtoupper($session->Vars["lang"])."_Name";
    $name_lang=constant($lang_sel);
    if ($name_lang!="")
       $messg_file= TPL_PATH.$name_lang.".mesg";
    else
       $messg_file= TPL_PATH."messages.mesg";
}

//global $event,$session,$global_cache_dynamic,$cacheDyn;

if ($global_cache_dynamic=="Y") {
	WebApp::collectHtmlPage();
	WebApp::constructMasterTemplateHtmlPage($tpl_file,$head_file,$messg_file);
	echo $contentForm = WebApp::getHtmlPage();
	//echo $cacheDyn->collectAndReplaceFeLinks($contentForm);
} else {
	WebApp::constructMasterTemplateHtmlPage($tpl_file,$head_file,$messg_file);
}


  /*********************   page caching ****************************************************/
        IF($cachefile!='' && $global_cache_dynamic == "Y")
        {
                //trigger_error('generating cached page:'.$cachefile.' uri:'.$_SERVER['REQUEST_URI']);
                // We're done! Save the cached content to a file
                $fp = fopen($cachefile, 'w');
                fwrite($fp, ob_get_contents());
                fclose($fp);
                // finally send browser output
                ob_end_flush();
				//$page_load_time = microtime(true) - $start_time;
				//printf("Page Loading: %f seconds | memory usage: ". round(memory_get_usage(true)/1024,2)." kb", $page_load_time);
                
        }

/*echo "<textarea>$cachefile:cachefile";
PRINT_r($_SERVER['argv']);
PRINT_r($_GET);
echo "</textarea>";*/

  /*****************************************************************************************/

?>