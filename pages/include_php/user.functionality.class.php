<?php

INCLUDE_ONCE ASP_FRONT_PATH."php/FE/user.base.functionality.class.php";
class UserFullFunctionality extends UserBase
{
	function UserFullFunctionality() {
		parent::UserBase();
	}	
    function initUserPartecipationInfo()
    {
    }    
    function getUserFullInfo($user_id="", $prepareForEditMode = "no")
    {
		global $session;
        $UsrInfo["data"] = array();
        $userInfoToReturn = array();

		if ($user_id=="" || $user_id<=0) {
			$user_id = $session->Vars["ses_userid"];
		}
        if($user_id>0 && $user_id!=2){

            $getUserInfo = "SELECT users.UserId, 
                                coalesce(UserName,'')             as UserName,
                                coalesce(FirstName,'')            as FirstName,
                                coalesce(SecondName,'')           as SecondName,
                                coalesce(usr_title,'')            as usr_title,
                                coalesce(usr_email,'')            as usr_email,
                                coalesce(usr_phone,'')            as usr_phone,
                                coalesce(usr_fax,'')              as usr_fax,
                                coalesce(usr_birthdate,'')        as usr_birthdate,
                                coalesce(usr_short,'')            as usr_short,
                                coalesce(Shenime,'')              as Shenime,
                                coalesce(usr_mobile,'')           as usr_mobile,
                                coalesce(usr_diploma,'')          as usr_diploma,
                                coalesce(usr_gender,'')           as usr_gender,
                                coalesce(usr_street,'')           as usr_street,
                                coalesce(usr_city,'')             as usr_city,
                                coalesce(usr_postcode,'')         as usr_postcode,
                                coalesce(newsletter_register,'')  as newsletter_register,

                                IF(user_photo_data.doc_id is NULL,'',user_photo_data.doc_id) as doc_id              
                               FROM users
                               LEFT JOIN user_photo_data ON users.UserId = user_photo_data.id_user
                              WHERE users.UserId ='" .ValidateVarFun::f_full_escape_text($user_id). "'";

            $rs = WebApp::execQuery($getUserInfo);
            $ind = 0;
            while (!$rs->EOF()) {
                $UsrInfo["data"][$ind]["user_id"] = $rs->Field("UserId");
                $UsrInfo["data"][$ind]["UserId"] = $rs->Field("UserId");
                
                $UsrInfo["data"][$ind]["UserName"] = $rs->Field("UserName");
                $UsrInfo["data"][$ind]["FirstName"] = $rs->Field("FirstName");
                $UsrInfo["data"][$ind]["SecondName"] = $rs->Field("SecondName");
                
                $UsrInfo["data"][$ind]["UserFirstName"] = $UsrInfo["data"][$ind]["FirstName"];
                $UsrInfo["data"][$ind]["UserSecondName"] = $UsrInfo["data"][$ind]["SecondName"];
                

                $UsrInfo["data"][$ind]["UserSalutation"] = $rs->Field("usr_title");
                $UsrInfo["data"][$ind]["usr_diploma"] = $rs->Field("usr_diploma");
                $UsrInfo["data"][$ind]["UserTitle"] = $UsrInfo["data"][$ind]["usr_diploma"];
                
                $UsrInfo["data"][$ind]["usr_email"] = $rs->Field("usr_email");
                $UsrInfo["data"][$ind]["usr_phone"] = $rs->Field("usr_phone");
                $UsrInfo["data"][$ind]["UserEmail"] = $UsrInfo["data"][$ind]["usr_email"];
                $UsrInfo["data"][$ind]["UserPhone"] = $UsrInfo["data"][$ind]["usr_phone"];

                $UsrInfo["data"][$ind]["usr_title"]     = $rs->Field("usr_title");
                $UsrInfo["data"][$ind]["usr_fax"]       = $rs->Field("usr_fax");
                $UsrInfo["data"][$ind]["usr_mobile"]    = $rs->Field("usr_mobile");
                $UsrInfo["data"][$ind]["usr_short"]     = $rs->Field("usr_short");
                $UsrInfo["data"][$ind]["Shenime"]       = $rs->Field("Shenime");
                $UsrInfo["data"][$ind]["usr_gender"]    = $rs->Field("usr_gender");
                $UsrInfo["data"][$ind]["usr_street"]    = $rs->Field("usr_street");
                $UsrInfo["data"][$ind]["usr_city"]      = $rs->Field("usr_city");
                $UsrInfo["data"][$ind]["usr_postcode"]  = $rs->Field("usr_postcode");
                $UsrInfo["data"][$ind]["newsletter_register"]   = $rs->Field("newsletter_register");

                $UsrInfo["data"][$ind]["usr_birthdate"]     = "";
                if($rs->Field("usr_birthdate") != "" && $rs->Field("usr_birthdate") != "0000-00-00"){
                    $UsrInfo["data"][$ind]["usr_birthdate"]     = date("d.m.Y", strtotime($rs->Field("usr_birthdate")));
                }

                $UsrInfo["data"][$ind]["doc_id"] = $rs->Field("doc_id");
                $UsrInfo["data"][$ind]["docId"] = $UsrInfo["data"][$ind]["doc_id"];
                

                UserBase::getUserPhotoCached($UsrInfo["data"][$ind]["doc_id"]);
                $userInfoToReturn[$UsrInfo["data"][$ind]["UserId"]] = $UsrInfo["data"][$ind];



                $ind++;
                $rs->MoveNext();
            }

            if($prepareForEditMode == "yes" && count($userInfoToReturn[$user_id])>0){
                foreach($userInfoToReturn[$user_id] as $key => $value){
                    if(!is_array($value))
                        $userInfoToReturn[$user_id]["esc_$key"]= GeneralBase::escapeStringToUseInForm($value);
                }
            }
        }
            return $userInfoToReturn;
    }	
    function getUserInfo($user_id="")
    {
        global $session;

        $UsrInfo["data"] = array();
        $userInfoToReturn = array();
		

		$getUserInfo = "SELECT users.UserId, 
							coalesce(UserName,'')             as UserName,
                            coalesce(FirstName,'')            as FirstName,
                            coalesce(SecondName,'')           as SecondName,
                            coalesce(usr_title,'')            as usr_title,
                            coalesce(usr_email,'')            as usr_email,
                            coalesce(usr_phone,'')            as usr_phone,
                            coalesce(usr_fax,'')           	  as usr_fax,
                            coalesce(usr_birthdate,'')        as usr_birthdate,
                            coalesce(usr_short,'')        	  as usr_short,
                            coalesce(Shenime,'')        	  as Shenime,
                            coalesce(usr_mobile,'')           as usr_mobile,
                            coalesce(usr_diploma,'')          as usr_diploma,
                            coalesce(usr_gender,'')           as usr_gender,
                            coalesce(usr_street,'')           as usr_street,
                            coalesce(usr_city,'')          	  as usr_city,
                            coalesce(usr_postcode,'')         as usr_postcode,
                            coalesce(newsletter_register,'')  as newsletter_register,

                            IF(user_photo_data.doc_id is NULL,'',user_photo_data.doc_id) as doc_id  			
						   FROM users
						   LEFT JOIN user_photo_data ON users.UserId = user_photo_data.id_user
						  WHERE users.UserId ='" .$session->Vars["ses_userid"]. "'";

		$rs = WebApp::execQuery($getUserInfo);
		$ind = 0;
		while (!$rs->EOF()) {
			$UsrInfo["data"][$ind]["user_id"] = $rs->Field("UserId");
			$UsrInfo["data"][$ind]["UserId"] = $rs->Field("UserId");
			
			$UsrInfo["data"][$ind]["UserName"] = $rs->Field("UserName");
			$UsrInfo["data"][$ind]["FirstName"] = $rs->Field("FirstName");
			$UsrInfo["data"][$ind]["SecondName"] = $rs->Field("SecondName");
			
			$UsrInfo["data"][$ind]["UserFirstName"] = $UsrInfo["data"][$ind]["FirstName"];
			$UsrInfo["data"][$ind]["UserSecondName"] = $UsrInfo["data"][$ind]["SecondName"];
			

			$UsrInfo["data"][$ind]["UserSalutation"] = $rs->Field("usr_title");
			$UsrInfo["data"][$ind]["usr_diploma"] = $rs->Field("usr_diploma");
			$UsrInfo["data"][$ind]["UserTitle"] = $UsrInfo["data"][$ind]["usr_diploma"];
			
			$UsrInfo["data"][$ind]["usr_email"] = $rs->Field("usr_email");
			$UsrInfo["data"][$ind]["usr_phone"] = $rs->Field("usr_phone");
			$UsrInfo["data"][$ind]["UserEmail"] = $UsrInfo["data"][$ind]["usr_email"];
			$UsrInfo["data"][$ind]["UserPhone"] = $UsrInfo["data"][$ind]["usr_phone"];

			$UsrInfo["data"][$ind]["usr_title"] 	= $rs->Field("usr_title");
			$UsrInfo["data"][$ind]["usr_fax"] 	 	= $rs->Field("usr_fax");
			$UsrInfo["data"][$ind]["usr_mobile"] 	= $rs->Field("usr_mobile");
			$UsrInfo["data"][$ind]["usr_short"]	 	= $rs->Field("usr_short");
			$UsrInfo["data"][$ind]["Shenime"]	 	= $rs->Field("Shenime");
			$UsrInfo["data"][$ind]["usr_gender"]	= $rs->Field("usr_gender");
			$UsrInfo["data"][$ind]["usr_street"]	= $rs->Field("usr_street");
			$UsrInfo["data"][$ind]["usr_city"]		= $rs->Field("usr_city");
			$UsrInfo["data"][$ind]["usr_postcode"]	= $rs->Field("usr_postcode");
			$UsrInfo["data"][$ind]["newsletter_register"]	= $rs->Field("newsletter_register");

			$UsrInfo["data"][$ind]["usr_birthdate"]	 	= "";
			if($rs->Field("usr_birthdate") != "" && $rs->Field("usr_birthdate") != "0000-00-00"){
				$UsrInfo["data"][$ind]["usr_birthdate"]	 	= date("d.m.Y", strtotime($rs->Field("usr_birthdate")));
			}

			$UsrInfo["data"][$ind]["doc_id"] = $rs->Field("doc_id");
			$UsrInfo["data"][$ind]["docId"] = $UsrInfo["data"][$ind]["doc_id"];
			

			UserBase::getUserPhotoCached($UsrInfo["data"][$ind]["doc_id"]);
			$userInfoToReturn[$UsrInfo["data"][$ind]["UserId"]] = $UsrInfo["data"][$ind];



			$ind++;
			$rs->MoveNext();
		}
		$UsrInfo["AllRecs"] = count($UsrInfo["data"]);
		WebApp::addVar("getUserInfo", $UsrInfo);


        return $userInfoToReturn;
    }


    function eUserSaveChangeData($paramsToControlByModule=array())
    {
        global $session;
        
        $ret = array();
        $ret["ErrorCode"] = 0;
        $ret["ErrorMessage"] = "";
        $updateFieldsGener = array();
        $source = ($paramsToControlByModule["tool_source"] == '1' ? '6' : '5');

        if (isset($paramsToControlByModule["user_name_regis"])) {
            $updateFieldsGener["UserName"] = " UserName = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["user_name_regis"]) . "' ";
        }
        if (isset($paramsToControlByModule["email_regis"])) {

            $res = $this->checkAvaiabilityForEmail($paramsToControlByModule);
            if($res == "true"){

                require_once(TOOLSET_PATH."organogram/organogram.class.php");
                $orbObj = new organogram();

                $orbObj->logUserUpdates($paramsToControlByModule, $source);

                $update_content_general = "UPDATE users SET  usr_email =   '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["email_regis"]) . "' 
                	WHERE UserId = ".$session->Vars["ses_userid"]. "";
                WebApp::execQuery($update_content_general);
            }else{
                $ret["ErrorCode"] = 1;
                $ret["ErrorMessage"] = "The email is already registered!";
                return $ret;
            }
        }

        if (isset($paramsToControlByModule["first_name"])) {
            $updateFieldsGener["FirstName"] = " FirstName = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["first_name"]) . "' ";
        }
        if (isset($paramsToControlByModule["last_name"])) {
            $updateFieldsGener["SecondName"] = " SecondName = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["last_name"]) . "' ";
        }
        if ($paramsToControlByModule["birthdate_regis"] != "") {
            $birth_date_arr = explode(".", $paramsToControlByModule["birthdate_regis"]);
            $birthdate = $birth_date_arr[2] . "-" . $birth_date_arr[1] . "-" . $birth_date_arr[0];
        } else {
            $birthdate = "";
        }

        $updateFieldsGener["usr_birthdate"] = " usr_birthdate = '" . ValidateVarFun::f_full_escape_text($birthdate) . "' ";

        if (isset($paramsToControlByModule["usr_phone"])) {
            $updateFieldsGener["usr_phone"] = " usr_phone = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_phone"]) . "' ";
        }
        if (isset($paramsToControlByModule["city_regis"])) {
            $updateFieldsGener["usr_city"] = " usr_city = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["city_regis"]) . "' ";
        }
        if (isset($paramsToControlByModule["usr_postcode"])) {
            $updateFieldsGener["usr_postcode"] = " usr_postcode = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_postcode"]) . "' ";
        }
        if (isset($paramsToControlByModule["usr_street"])) {
            $updateFieldsGener["usr_street"] = " usr_street = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_street"]) . "' ";
        }
        if (isset($paramsToControlByModule["usr_diploma"])) {
            $updateFieldsGener["usr_diploma"] = " usr_diploma = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_diploma"]) . "' ";
        }
        if (isset($paramsToControlByModule["phone_regis"])) {
            $updateFieldsGener["usr_phone"] = " usr_phone = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["phone_regis"]) . "' ";
        }
	  	if (isset($paramsToControlByModule["usr_title"])) {
	            $updateFieldsGener["usr_title"] = " usr_title = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_title"]) . "' ";
	    }

	    if (isset($paramsToControlByModule["usr_short"])) {
	            $updateFieldsGener["usr_short"] = " usr_short = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_short"]) . "' ";
	    }

	    if (isset($paramsToControlByModule["usr_mobile"])) {
	            $updateFieldsGener["usr_mobile"] = " usr_mobile = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_mobile"]) . "' ";
	    }

	    if (isset($paramsToControlByModule["usr_fax"])) {
	            $updateFieldsGener["usr_fax"] = " usr_fax = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_fax"]) . "' ";
	    }

        if(isset($paramsToControlByModule["usr_gender"])){
            $updateFieldsGener["usr_gender"] = " usr_gender = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["usr_gender"]). "' ";
        }  
        if(isset($paramsToControlByModule["Shenime"])){
            $updateFieldsGener["Shenime"] = " Shenime = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["Shenime"]). "' ";
        }  


        if(isset($paramsToControlByModule["newsletter_register"])){
            $updateFieldsGener["newsletter_register"] = " newsletter_register = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["newsletter_register"]). "' ";
        }

       /* if (isset($paramsToControlByModule["country"])) {
            $updateFieldsGener["user_country"] = " user_country = '" . ValidateVarFun::f_full_escape_text($paramsToControlByModule["country"]) . "' ";
        }*/


        if (count($updateFieldsGener) > 0) {
            $update_content_general = "UPDATE users SET modified_by='".$session->Vars['ses_userid']."', last_modification = NOW(), modified_from='".$source."' , " . implode(",", $updateFieldsGener) . " WHERE UserId = " .$session->Vars["ses_userid"]. "";
            WebApp::execQuery($update_content_general);
        }



        return $ret;
        
    }
    
}