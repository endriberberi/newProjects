<?
function InternalSearchModule_onRender() 
{
	global $session,$event;
	extract($event->args);
	$NEM_TEMPLATE = "";
	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
		require_once(INC_PATH.'SearchModule.Ext.class.php');
		$LP = new InternalSearchExt();
		$LP->InitClass();
		if (isset($LP->generalMessages["NEM_FILENAME"]) && $LP->generalMessages["NEM_FILENAME"]!='') 
			 $NEM_FILENAME = $LP->generalMessages["NEM_FILENAME"];
		if (is_file(NEMODULES_PATH."SearchModule/".$NEM_FILENAME)) 
			$NEM_TEMPLATE = "<Include SRC=\"".NEMODULES_PATH."InternalSearchModule/".$NEM_FILENAME."\"/>";
	}
	WebApp::addVar("SearchNEM_TEMPLATE", $NEM_TEMPLATE);
}