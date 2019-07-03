<?
function eUserDataChange_onRender() {
	global $session,$event;
    
	if (isset($session->Vars["runningCache"]) && $session->Vars["runningCache"]=="yes")
		return;
    	INCLUDE_ONCE INC_PATH."user.functionality.class.php";
	
        extract($event->args);
        $Lng = $session->Vars["lang"];
        
        $dataProfileDefaultHtml =array ();
        
        if (isset($session->Vars["ses_userid"])){
                $ses_userid = $session->Vars["ses_userid"];
                $userSystemID=$session->Vars["ses_userid"];
                }
            else{
                $ses_userid = "2";
                $userSystemID= "2";
        
        }
            
         WebApp::addVar("ln", $session->Vars["lang"]);
         WebApp::addVar("uniqueid", $session->Vars["uni"]);
         WebApp::addVar("userId", $session->Vars["ses_userid"]);
        





	  $NEM_TEMPLATE = "";
      if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {

			$objIdList =  $session->Vars["idstemp"];
			$objectPropList = unserialize(base64_decode(WebApp::findNemProp($objIdList)));


			require_once(INC_PHP_AJAX."NemsManager.class.php");
			$grn = NemsManager::getFrontEndGeneralProperties($objectPropList);

			if (isset($grn["NEM_FILENAME"]) && $grn["NEM_FILENAME"]!='') {
				$NEM_FILENAME = $grn["NEM_FILENAME"];
			}		
			
			/*echo "<textarea>";
			print_r($objectPropList);
			print_r($grn);
			echo "</textarea>";*/
			$NEM_TEMPLATE = "<Include SRC=\"".NEMODULES_PATH."eUserFunction/eUserDataChange/".$NEM_FILENAME."\"/>";
			//echo NEMODULES_PATH."eUserFunction/eUserDataChange/".$NEM_FILENAME;
			
			
			
			//E:\projects_114\ASP4v\ASP4_NOBUG_APP\mpesa\templates\NEModules\eUserFunction\eUserDataChange\eUserDataChange.php
			$dataProfileDefaultHtml["1"] = "account_information.html";		//Account Information
			$dataProfileDefaultHtml["2"] = "contact_details.html";			//Contact Details
			$dataProfileDefaultHtml["3"] = "public_profile.html";			//Public Profile	
			$dataProfileDefaultHtml["4"] = "proffesional_data.html";		//Professional Data
			$dataProfileDefaultHtml["5"] = "biometrical_data.html";  		//Authentication Data      

			while (list($confKey,$tettt)=each($dataProfileDefaultHtml)) {
				WebApp::addVar("show_step_$confKey", "no"); 
				WebApp::addVar("htmlTemplateInclude_step_$confKey", ""); 
			}

			if (isset($objectPropList["profile_step"]) && count($objectPropList["profile_step"])>0)  {
				while (list($key,$confKey)=each($objectPropList["profile_step"])) {
					if (isset($dataProfileDefaultHtml[$confKey])) {
						$html_template_to_include = "<Include SRC=\"".NEMODULES_PATH."eUserFunction/eUserDataChange/temp/".$dataProfileDefaultHtml[$confKey]."\" />";  
						WebApp::addVar("htmlTemplateInclude_step_$confKey", $html_template_to_include); 
						WebApp::addVar("show_step_$confKey", "yes"); 

						//echo "<br><br>htmlTemplateInclude_step_$confKey<br>";
						//echo NEMODULES_PATH."eUserFunction/eUserDataChange/temp/".$dataProfileDefaultHtml[$confKey]."<br>";
						//echo $dataProfileDefaultHtml[$confKey];
					}
				}
			}
		      
			 if (isset($objectPropList["enter_email_address"]) && $objectPropList["enter_email_address"]!="")
                $enter_email_address = $objectPropList["enter_email_address"];
	        else    $enter_email_address = "{{_enter_email_address}}";
	        WebApp::addVar("enter_email_address","$enter_email_address");

	        if (isset($objectPropList["provide_valid_email_address"]) && $objectPropList["provide_valid_email_address"]!="")
	                $provide_valid_email_address = $objectPropList["provide_valid_email_address"];
	        else    $provide_valid_email_address = "{{_provide_valid_email_address}}";
	        WebApp::addVar("provide_valid_email_address","$provide_valid_email_address");

	        if (isset($objectPropList["email_address_already_registered"]) && $objectPropList["email_address_already_registered"]!="")
	                $email_address_already_registered = $objectPropList["email_address_already_registered"];
	        else    $email_address_already_registered = "{{_email_address_already_registered}}";
	        WebApp::addVar("email_address_already_registered","$email_address_already_registered");

	        if (isset($objectPropList["enter_first_name"]) && $objectPropList["enter_first_name"]!="")
	                $enter_first_name = $objectPropList["enter_first_name"];
	        else    $enter_first_name = "{{_enter_first_name}}";
	        WebApp::addVar("enter_first_name","$enter_first_name");

	        if (isset($objectPropList["enter_last_name"]) && $objectPropList["enter_last_name"]!="")
	                $enter_last_name = $objectPropList["enter_last_name"];
	        else    $enter_last_name = "{{_enter_last_name}}";
	        WebApp::addVar("enter_last_name","$enter_last_name");






		        
              
            $PropList["username"] 				= "username";
            $PropList["email"] 				    = "email";          
            $PropList["firstname"] 				= "firstname";
            $PropList["lastname"] 				= "lastname";
            
            $PropList["date_of_birth"] 		    = "date_of_birth";
            $PropList["address"] 				= "address";
            $PropList["city"] 				    = "city";
            $PropList["zip"] 				    = "zip";
            $PropList["country"] 			    = "country";
            $PropList["phone_number"] 			= "phone_number";  
            
            $PropList["academic_title"] 		= "academic_title";
            $PropList["affiliation"] 		    = "affiliation";
            $PropList["hospital_name"] 			= "hospital_name";
            $PropList["hospital_name_inst"] 	= "hospital_name_inst";
            $PropList["position"] 			    = "position";    
            $PropList["use_camera_picture"]     = "use_camera_picture";
            $PropList["user_public_nickname"]   = "user_public_nickname";
            $PropList["user_public_location"]   = "user_public_location";
            $PropList["user_occupation"]        = "user_occupation";
            $PropList["user_about"]             = "user_about";
            $PropList["user_interes"]           = "user_interes";
            
            $PropList["snap_photo_pr"]          = "snap_photo_pr";
            $PropList["new_photo_pr"]           = "new_photo_pr";
            
            $PropList["yes"]                    = "yes";
            $PropList["no"]                     = "no";
            
            $PropList["user_photo"]             = "user_photo";
            $PropList["snap_photo"]             = "snap_photo";
            $PropList["uplod_photo"]            = "uplod_photo";
                                   
            $PropList["new_photo"]            = "new_photo"; 
		  

                      
			while (list($key,$value)=each($PropList)) {           
				if (isset($objectPropList[$value]) && $objectPropList[$value]!='')  {          
				   WebApp::addVar("_".$value."_mesg", $objectPropList[$value]);
				}			
			} 
     
        $ObjUsr = new UserFullFunctionality($session->Vars["ses_userid"],"","","");		
    //    		    $ObjUsr->getUserFullInfo($session->Vars["ses_userid"]);   
		//$userInfo = $ObjUsr->getUserFullInfo($session->Vars["ses_userid"]);
		$userInfo = $ObjUsr->getUserFullInfo($session->Vars["ses_userid"],"yes");

		if(count($userInfo[$session->Vars["ses_userid"]])>0){
			$userDataGrid["data"][0] = $userInfo[$session->Vars["ses_userid"]];
		}

		if(count($userDataGrid["data"])>0)
			$userDataGrid["AllRecs"] = count($userDataGrid["data"]);
		WebApp::addVar("userDataGrid",$userDataGrid);


	}
    WebApp::addVar("idstemp","");
    if (isset($session->Vars["idstemp"]) && $session->Vars["idstemp"]!="") {
        WebApp::addVar("idstempNl",$session->Vars["idstemp"]);
    }
		

	WebApp::addVar("EDTCHANGE_NEM_TEMPLATE", $NEM_TEMPLATE);

  	
 }
 ?>