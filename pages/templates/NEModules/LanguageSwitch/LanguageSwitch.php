 <?

function LanguageSwitch_onRender() 
{
    global $session,$event;
    extract($event->args);
	//LanguageSwitch -----------------------------------------------------------------------------------------------------
	define("LANGUAGESWITCH_LNG_ID",           "1,2");		//ID e gjuheve te aplikimit te ndara me presje te renditura sipas radhes se shfaqej: '1,2' ose '2,1,3' ose '2,1'
	define("LANGUAGESWITCH_SHOW_CURRENT_LNG", "N");		//vlerat Y,N; Ta shfaq apo jo gjuhen korente ?
	define("LANGUAGESWITCH_ONLY_ICO_ENABLE",  "Y");		//vlerat Y,N; Te punoje apo jo vetem me iconat enable ?
	define("LANGUAGESWITCH_SHOW_LNG_ALWAYS",  "N");		//vlerat Y,N; Ti shfaq gjuhet pa link kur ato nuk jane aktive?
	//--------------------------------------------------------------------------------------------------------------------
    
    INCLUDE(ASP_FRONT_PATH."nems/LanguageSwitch/LanguageSwitch.php");
}


?>