<?
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

	//ky variable per cakton parametrat specifike qe duhen transportuar me session

	global $extra_var_to_export_with_session;
	$extra_var_to_export_with_session=array();
	$extra_var_to_export_with_session["mode"]	= "f_enum_mode";

	$valid_var_request  = "";
	$valid_var_request .= "idElC,Y;";
	$valid_var_request .= "idElC,f_pozitive_numbers;";
	
	$valid_var_request .= "aid,Y;"; 	
	$valid_var_request .= "aid,f_pozitive_numbers;";
	
	/* USER RELATED MODULES login,register,changepassowrd,forgot password, changeuserdata*/
	$valid_var_request .= "user_name_regis,Y;"; 	
	$valid_var_request .= "user_name_regis,f_full_escape_text;";		

	$valid_var_request .= "password_regis,Y;"; 	
	$valid_var_request .= "password_regis,f_full_escape_text;";		
	
	$valid_var_request .= "password_regis_confirm,Y;"; 	
	$valid_var_request .= "password_regis_confirm,f_full_escape_text;";		
	

	$valid_var_request .= "usr_postcode,Y;"; 	
	$valid_var_request .= "usr_postcode,f_full_escape_text;";	
	
	$valid_var_request .= "usr_street,Y;"; 	
	$valid_var_request .= "usr_street,f_full_escape_text;";		

	$valid_var_request .= "usr_gender,Y;"; 	
	$valid_var_request .= "usr_gender,f_full_escape_text;";		

	$valid_var_request .= "usr_position,Y;"; 	
	$valid_var_request .= "usr_position,f_full_escape_text;";		


	$valid_var_request .= "usr_position,Y;"; 	
	$valid_var_request .= "usr_position,f_full_escape_text;";	
	
	$valid_var_request .= "email,Y;";
	$valid_var_request .= "email,f_full_escape_text;";	//f_real_escape_string
	$valid_var_request .= "email_regis,Y;";	
	$valid_var_request .= "email_regis,f_full_escape_text;";	//f_real_escape_string
	

	$valid_var_request .= "user_country,Y;"; 	
	$valid_var_request .= "user_country,f_full_escape_text;";		


	$valid_var_request .= "user_public_nickname,Y;"; 	
	$valid_var_request .= "user_public_nickname,f_full_escape_text;";		

	$valid_var_request .= "user_public_location,Y;"; 	
	$valid_var_request .= "user_public_location,f_full_escape_text;";		

	$valid_var_request .= "user_occupation,Y;"; 	
	$valid_var_request .= "user_occupation,f_full_escape_text;";		

	$valid_var_request .= "user_about,Y;"; 	
	$valid_var_request .= "user_about,f_full_escape_text;";		

	$valid_var_request .= "user_about,Y;"; 	
	$valid_var_request .= "user_about,f_full_escape_text;";		

	$valid_var_request .= "user_interes,Y;"; 	
	$valid_var_request .= "user_interes,f_full_escape_text;";		

	$valid_var_request .= "address_country,Y;"; 	
	$valid_var_request .= "address_country,f_full_escape_text;";		

	$valid_var_request .= "address_name,Y;"; 	
	$valid_var_request .= "address_name,f_full_escape_text;";		

	$valid_var_request .= "address_email,Y;"; 	
	$valid_var_request .= "address_email,f_full_escape_text;";		

	$valid_var_request .= "address_phone,Y;"; 	
	$valid_var_request .= "address_phone,f_full_escape_text;";		

	$valid_var_request .= "address_street,Y;"; 	
	$valid_var_request .= "address_street,f_full_escape_text;";		

	$valid_var_request .= "address_street_ext,Y;"; 	
	$valid_var_request .= "address_street_ext,f_full_escape_text;";		

	$valid_var_request .= "firs_name,Y;"; 	
	$valid_var_request .= "firs_name,f_full_escape_text;";		

	$valid_var_request .= "last_name_regis,Y;"; 	
	$valid_var_request .= "last_name_regis,f_full_escape_text;";		

	$valid_var_request .= "city_regis,Y;"; 	
	$valid_var_request .= "city_regis,f_full_escape_text;";		

	$valid_var_request .= "telephone,Y;"; 	
	$valid_var_request .= "telephone,f_full_escape_text;";		

	$valid_var_request .= "diploma_regis,Y;"; 	
	$valid_var_request .= "diploma_regis,f_full_escape_text;";		

	$valid_var_request .= "country,Y;"; 	
	$valid_var_request .= "country,f_full_escape_text;";	

	$valid_var_request .= "phone_regis,Y;"; 	
	$valid_var_request .= "phone_regis,f_full_escape_text;";		

	$valid_var_request .= "birthdate_regis,Y;"; 	
	$valid_var_request .= "birthdate_regis,f_full_escape_text;";		

	$valid_var_request .= "mail_preferences,Y;"; 	
	$valid_var_request .= "mail_preferences,f_full_escape_text;";	
	
	$valid_var_request .= "hospital_address,Y;"; 	
	$valid_var_request .= "hospital_address,f_full_escape_text;";	
	
	$valid_var_request .= "hospital_name,Y;"; 	
	$valid_var_request .= "hospital_name,f_full_escape_text;";	
	
	$valid_var_request .= "contactEmail,Y;";
	$valid_var_request .= "contactEmail,f_full_escape_text;";		
	$valid_var_request .= "curr_email,Y;";
	$valid_var_request .= "curr_email,f_full_escape_text;";		

	$valid_var_request .= "usr_state,Y;"; 							//user country
	$valid_var_request .= "usr_state,f_full_escape_text;";		

	$valid_var_request .= "termsConditions,Y;"; 	
	$valid_var_request .= "termsConditions,f_full_escape_text;";	
	
	$valid_var_request .= "account_type,Y;"; 	
	$valid_var_request .= "account_type,f_full_escape_text;";	

	/**Enquiry Feedback	*/
	$valid_var_request .= "EnquirerType,Y;";
	$valid_var_request .= "EnquirerType,f_only_numbers;";
	$valid_var_request .= "EnquiryQuestion,Y;";
	$valid_var_request .= "EnquiryQuestion,f_only_numbers;";
	$valid_var_request .= "UserSalutation,Y;";
	$valid_var_request .= "UserSalutation,f_full_escape_text;";
	$valid_var_request .= "UserFirstName,Y;";
	$valid_var_request .= "UserFirstName,f_full_escape_text;";
	$valid_var_request .= "UserSecondName,Y;";
	$valid_var_request .= "UserSecondName,f_full_escape_text;";
	$valid_var_request .= "UserEmail,Y;";
	$valid_var_request .= "UserEmail,f_full_escape_text;";
	$valid_var_request .= "phone,Y;";
	$valid_var_request .= "phone,f_full_escape_text;";
	$valid_var_request .= "institution,Y;";
	$valid_var_request .= "institution,f_full_escape_text;";
	$valid_var_request .= "country,Y;";
	$valid_var_request .= "country,f_full_escape_text;";
	$valid_var_request .= "enquiry_details,Y;";
	$valid_var_request .= "enquiry_details,f_full_escape_text;";
	$valid_var_request .= "EF_id,Y;";
	$valid_var_request .= "EF_id,f_only_numbers;";
	$valid_var_request .= "doc_id,Y;";
	$valid_var_request .= "doc_id,f_only_numbers;";

	$valid_var_request .= "typeEF,Y;";
	$valid_var_request .= "typeEF,f_only_numbers;";
	
	
	$valid_var_request .= "UsrPssw,Y;";
	$valid_var_request .= "UsrPssw,f_full_escape_text;";
	$valid_var_request .= "NwUsrPssw,Y;";
	$valid_var_request .= "NwUsrPssw,f_full_escape_text;";

	$valid_var_request .= "usregflg,Y;";
	$valid_var_request .= "usregflg,only_chars;";
		
	//cookies
	$valid_var_request .= "analytic_attr,Y;"; 	
	$valid_var_request .= "analytic_attr,only_chars;";		

	$valid_var_request .= "cookieconsent_status,Y;"; 	
	$valid_var_request .= "cookieconsent_status,only_chars;";		

	//assesmentAndSurveys

	$valid_var_request .= "CID,Y;"; 	
	$valid_var_request .= "CID,control_validity_of_koordinate;";		

	$valid_var_request .= "oid,Y;"; 	
	$valid_var_request .= "oid,control_validity_of_koordinate;";		

	$valid_var_request .= "qid,Y;"; 	
	$valid_var_request .= "qid,control_validity_of_koordinate;";		


	$valid_var_request .= "usdTm,Y;"; 	
	$valid_var_request .= "usdTm,f_full_escape_text;";		

	$valid_var_request .= "all_bck,Y;"; 					//TO BE CONTROLLED AND REMOVED	
	$valid_var_request .= "all_bck,f_full_escape_text;";		

	$valid_var_request .= "sID,Y;"; 					//TO BE CONTROLLED if is used by something else	me tjeter tip
	//$valid_var_request .= "sID,control_array;";		

	$valid_var_request .= "apprcss,Y;";
	$valid_var_request .= "apprcss,f_full_escape_text;";
	
	
	$valid_var_request .= "itemId,Y;";
	$valid_var_request .= "itemId,f_full_escape_text;";

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
	//$valid_var_request .= "vars_page,f_real_escape_string;"; //TANI: duhet per kete aplikim ISHP
	
	$valid_var_request .= "vars_post,Y;";
	$valid_var_request .= "id_sel,Y;";
	$valid_var_request .= "tipi_exp,Y;";  
	$valid_var_request .= "tipi_exp,f_only_numbers;";

	

    define("APP_VALID_VAR_REQUEST", $valid_var_request);	