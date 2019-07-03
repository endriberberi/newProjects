<?
function guessByAjax_onRender() 
{
	global $session,$event;
	extract($event->args);

	if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

		require_once(INC_PATH.'SearchModule.Ext.class.php');
		$LP = new InternalSearchExt();
		$LP->InitClass();		
		$LP->configureShowNodesAndSubnodes();
		$LP->searchInternalResults("guessByAjax");
		/*echo "<textarea>";
		print_r($LP);
		echo "</textarea>";*/
	}

}