<?php
INCLUDE_ONCE ASP_FRONT_PATH."php/FE/ajax.fe.base.class.php";
class ajaxAppFe extends ajaxBaseFe
{
    var $allowSomeParamsToBeTraitedFromNem = "yes";

	function ajaxAppFe() {
		parent::ajaxBaseFe();
	}	
	
	function specificProcesses()
	{
		
		//NESE PROCESI SPECIFIK I APLIKLIMIT DUHET TI LEJOHEN NJE USER WEB te shtohet nje rekord tek this->allowedForFrontWebUser
		//$this->allowedForFrontWebUser["validateUname"] = "validateUname";		
		
		//per cdo proces specifik te shtohet nje rekord tek knownProccesses
		$this->knownProccesses["export"]     = "f_exportData";	
		$this->knownProccesses["upload_doc"] = "f_upload_doc";	
		$this->knownProccesses["download"]   = "f_download";	
		
		
	}	
	function f_exportData()
	{
		global $session;
		include_once (APP_PATH."include_php/app_fun/exp.php");
	
	}

	function f_upload_doc()
	{
		global $session;
		include_once (APP_PATH."include_php/app_fun/upload_doc.php");
	}

	function f_download()
	{
		global $session;
		include_once (APP_PATH."include_php/app_fun/download_doc.php");
	}
}