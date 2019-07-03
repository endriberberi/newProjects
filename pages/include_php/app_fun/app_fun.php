<?
   //FUNKSIONET PER ENKRPT DEKRYP --------------------------------------------------------------
     function f_app_encrypt($text, $key, $iv) 
       {
        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($cipher, $key, $iv);
        $decrypted = mcrypt_generic($cipher, $text);
        mcrypt_generic_deinit($cipher);

        //$inp_var = bin2hex($decrypted);
        $inp_var = base64_encode($decrypted);
        $inp_var = strtr($inp_var, "+/=", "-_,");
     
        RETURN RTRIM($inp_var);
       }

     function f_app_decrypt($encrypted_text, $key, $iv)
       {
        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
        mcrypt_generic_init($cipher, $key, $iv);
        
        //$decrypted = mdecrypt_generic($cipher,pack("H*" , $encrypted_text));
        $decrypted = mdecrypt_generic($cipher, base64_decode(strtr($encrypted_text, "-_,", "+/=")));
        
        mcrypt_generic_deinit($cipher);
        
        RETURN RTRIM($decrypted);
       }

      function f_app_difference_in_seconds($data_ora)
         {
          IF (strlen($data_ora) != 14)
             {
              RETURN -1;
             }
          
          IF (!is_numeric($data_ora))
             {
              RETURN -1;
             }

          $timeFirst    = STRTOTIME($data_ora);
          $data_ora_now = DATE("YmdHis");
          $timeSecond   = STRTOTIME($data_ora_now);

          $differenceInSeconds = $timeSecond - $timeFirst;
          
          RETURN $differenceInSeconds;
         }
   //FUNKSIONET PER ENKRPT DEKRYP --------------------------------------------------------------

//VARS_PAGE -------------------------------------------------------------------------------------------------------------------
  function f_app_vars_page ($arg_vars_page) 
    {
     $vars_page_arr = array();

     IF ($arg_vars_page != "")
        {
         $vars_page         = f_app_decrypt($arg_vars_page, DESK_KEY, DESK_IV);
         
         $kol_val1          = STR_REPLACE(array("<pikp>", "<_and_>"), array(";", "&"), $vars_page);
         $kol_val_array1    = EXPLODE("<->", $kol_val1);
         $kol_array1        = EXPLODE("<_>", $kol_val_array1[0]);
         $val_array1        = EXPLODE("<_>", $kol_val_array1[1]);
    
         $nr_var_ses        = COUNT($kol_array1) - 1;
      
         IF (($kol_array1[0] == "oOo") AND ($val_array1[0] == "oOo") AND ($kol_array1[$nr_var_ses] == "cCc") AND ($val_array1[$nr_var_ses] == "cCc"))
            {
             FOR ($i=1; $i < $nr_var_ses; $i++)
                 {
                  $et = $kol_array1[$i];
                  $vl = TRIM($val_array1[$i]);
                  
                  IF ($et == "form_id_token")
                     {
                      $token_ekziston     = f_app_form_id_token_check($vl);
                      $vars_page_arr[$et] = $token_ekziston;
                     }
                  ELSE
                     {
                      $vars_page_arr[$et] = $vl;
                     }
                 }
            }
         ELSE
            {
             header('Location: ' . APP_URL);
             EXIT;
            }
        }
    
     RETURN $vars_page_arr;
    }
//VARS_PAGE -------------------------------------------------------------------------------------------------------------------

//VARS_PAGE ENCRYPT -----------------------------------------------------------------------------------------------------------
  function f_app_vars_page_encrypt ($arg_kol, $arg_val) 
    {
     $kolonat   = implode("<_>", $arg_kol);
     $vlerat    = implode("<_>", $arg_val);
	  
     $vars_page = "oOo<_>".$kolonat."<_>cCc<->oOo<_>".$vlerat."<_>cCc";
     $vars_page = STR_REPLACE(array("'","\"",";","&"), array("","","<pikp>","<_and_>"), $vars_page);
     $vars_page = f_app_encrypt($vars_page, DESK_KEY, DESK_IV);
	  
     RETURN $vars_page;
    }
//VARS_PAGE ENCRYPT -----------------------------------------------------------------------------------------------------------

//form id_token ---------------------------------------------------------------------------------------------------------------
  function f_app_form_id_token () 
    {
     GLOBAL $session;
     
     $form_id_token = "";
     
     //gjenerojme token dhe e ruajme tek logu ---------------------------------------------------------------------------------
       $ip_add   = ValidateVarFun::f_only_numbers($_SERVER['REMOTE_ADDR']);
       $dt_ora   = DATE("YmdHis");
       $id_token = $dt_ora.$ip_add.rand(1000000,9999999);
			
       IF (STRLEN($id_token) < 32)
          {
           $id_token = $id_token.rand(1000000,9999999);
          }
			
       $form_id_token = SUBSTR($id_token, 0, 32);

       $sql_ins = "INSERT INTO phi_form_token (ID_S,                                                              form_id_token)
                                       VALUES ('".ValidateVarFun::f_real_escape_string($session->Vars["uni"])."', '".ValidateVarFun::f_real_escape_string($form_id_token)."')
                  ";
       WebApp::execQuery($sql_ins);
     //gjenerojme token dhe e ruajme tek logu ---------------------------------------------------------------------------------
     
     RETURN $form_id_token;
    }
//form id_token ---------------------------------------------------------------------------------------------------------------

//form id_token_check ---------------------------------------------------------------------------------------------------------
  function f_app_form_id_token_check($arg_form_id_token) 
    {
     GLOBAL $session;
     
     //cekojme nese tokeni ekziston -------------------------------------------------------------------------------------------
       $token_count = 0;
       
       $sql = "SELECT count(*) as token_count 
                 FROM phi_form_token 
                WHERE ID_S          = '".ValidateVarFun::f_real_escape_string($session->Vars["uni"])."' AND
                      form_id_token = '".ValidateVarFun::f_real_escape_string($arg_form_id_token)."'
              ";
       $rs = WebApp::execQuery($sql);
       IF (!$rs->EOF())
          {
           $token_count = $rs->Field("token_count");
          }
     //cekojme nese tokeni ekziston -------------------------------------------------------------------------------------------

     IF ($token_count == 1)
        {
         $token_ekziston = "Y";

         //FSHIME TOKENIN -----------------------------------------------------------------------------------------------------
           $sql_del = "DELETE 
                         FROM phi_form_token 
                        WHERE ID_S          = '".ValidateVarFun::f_real_escape_string($session->Vars["uni"])."' AND
                              form_id_token = '".ValidateVarFun::f_real_escape_string($arg_form_id_token)."'
                      ";
           WebApp::execQuery($sql_del);
         //FSHIME TOKENIN -----------------------------------------------------------------------------------------------------
        }
     ELSE
        {
         $token_ekziston = "N";
        }

     RETURN $token_ekziston;
    }
//form id_token_check ---------------------------------------------------------------------------------------------------------

//f_app_kush_nem_jam_une -------------------------------------------------------------------------------------------------
  function f_app_kush_nem_jam_une ($arg_path_nem) 
    {
     $webbox_nem     = STR_REPLACE(APP_PATH."templates/NEModules/", "", $arg_path_nem);
     $webbox_nem     = SUBSTR($webbox_nem, 0, -4);

     $webbox_nem_arr = EXPLODE("/", $webbox_nem);
     $last_value     = COUNT($webbox_nem_arr) - 1;
     $webbox_sel     = $webbox_nem_arr[$last_value];

     $nem_id = 0;
     
     $sql = "SELECT nem_id 
               FROM nems 
              WHERE nem_box = '".ValidateVarFun::f_real_escape_string($webbox_nem).".html'
            ";
     
     $rs = WebApp::execQuery($sql);
     IF (!$rs->EOF())
        {
         $nem_id = $rs->Field("nem_id");
        }

     $kush_jam_une["nem_id"] = $nem_id;
     $kush_jam_une["webbox"] = $webbox_sel;

     RETURN $kush_jam_une;
    }
//f_app_kush_nem_jam_une -------------------------------------------------------------------------------------------------

//f_app_record_user ------------------------------------------------------------------------------------------------------
  function f_app_record_user ($arg_record_user) 
    {
     $user_name = "";
     
     $sql = "SELECT FirstName, SecondName 
               FROM users 
              WHERE UserId = '".ValidateVarFun::f_real_escape_string($arg_record_user)."'
            ";
     
     $rs = WebApp::execQuery($sql);
     IF (!$rs->EOF())
        {
         $user_name = $rs->Field("FirstName")." ".$rs->Field("SecondName");
        }

     RETURN $user_name;
    }
//f_app_record_user ------------------------------------------------------------------------------------------------------

//f_app_js_arr -----------------------------------------------------------------------------------------------------------
  //per filtrimin e listboxeve me js 
  function f_app_js_arr ($arg_arr) 
    {
     $js_arr_txt = "";
     
     IF (IS_ARRAY($arg_arr))
        {
         WHILE (LIST($key, $val) = EACH($arg_arr)) 
               {
                $name_js = STR_REPLACE(array("\"","'"), array("",""), $val);
                //$key_arr = EXPLODE("_X_", $key);
                //$js_arr_txt .= "_Y_".$key_arr[0]."_X_".$key_arr[1]."_X_".$name_js;

                $js_arr_txt .= "_Y_".$key."_X_".$name_js; //id tek key i kemi konkatenuar me _X_
               }
        }

     RETURN $js_arr_txt;
    }
//f_app_js_arr -----------------------------------------------------------------------------------------------------------

//f_app_reporting_entity_users -------------------------------------------------------------------------------------------
  function f_app_user_dega_qendra ($arg_UserId="") 
    {
     GLOBAL $session;
     
     IF ($arg_UserId == "")
        {
         $arg_UserId = $session->Vars["ses_userid"];
        }
     
     $tipi_userit = "Q"; //Q= qender raportimi; D = dege
     
     //shohim nese useri eshte i lidhur me qender raportimi --------------------------------------------------------------
       $nr_id_branch        = 0;
       $nr_id_branch_edit   = 0;
       $nr_qender_raportimi = 0;
       
       $sql = "SELECT id_reporting_entity 
                 FROM phi_reporting_entity_users 
                WHERE UserId = '".ValidateVarFun::f_real_escape_string($arg_UserId)."'
              ";

       $rs  = WebApp::execQuery($sql);
       $rs->MoveFirst();
       WHILE (!$rs->EOF())
             {
              $id_reporting_entity = $rs->Field("id_reporting_entity");

              $nr_qender_raportimi = $nr_qender_raportimi + 1;
              
              $user_reporting_entity_arr[$id_reporting_entity] = $id_reporting_entity;
              
              //merim degen e qendres ------------------------------------------------------------------------------------
                $sql1 = "SELECT id_branch 
                           FROM phi_reporting_entity 
                          WHERE id_reporting_entity = '".ValidateVarFun::f_real_escape_string($id_reporting_entity)."'
                        ";

                $rs1 = WebApp::execQuery($sql1);
                IF (!$rs1->EOF())
                   {
                    $id_branch         = $rs1->Field("id_branch");
                    
                    $nr_id_branch      = $nr_id_branch      + 1;
                    $nr_id_branch_edit = $nr_id_branch_edit + 1;
                    
                    $user_branch_arr[$id_branch]      = $id_branch;
                    $user_branch_edit_arr[$id_branch] = $id_branch; //per userin e qenderse i japim te drejte edit ne cdo rekord te saj
                   }
              //merim degen e qendres ------------------------------------------------------------------------------------

              $tipi_userit = "Q";
              
              $rs->MoveNext();
             }
     //shohim nese useri eshte i lidhur me qender raportimi --------------------------------------------------------------

     //nese useri nuk eshte i lidhur me nje qender raportimi shohim nese eshte i lidhur me nje dege ----------------------
       IF (($nr_id_branch == 0) AND ($nr_qender_raportimi == 0))
          {
           $sql = "SELECT id_branch,
                          view_edit
                     FROM phi_branch_users 
                    WHERE UserId = '".ValidateVarFun::f_real_escape_string($arg_UserId)."'
                  ";

           $rs  = WebApp::execQuery($sql);
           $rs->MoveFirst();
           WHILE (!$rs->EOF())
                 {
                  $id_branch = $rs->Field("id_branch");
                  $view_edit = $rs->Field("view_edit");

                  $nr_id_branch = $nr_id_branch + 1;
                    
                  $user_branch_arr[$id_branch] = $id_branch;

                  IF ($view_edit == "E")
                     {
                      $nr_id_branch_edit                = $nr_id_branch_edit + 1;
                      $user_branch_edit_arr[$id_branch] = $id_branch; //per userin e deges i japim te drejte edit vetem kur view_edit == "E"
                     }
                     
                  $tipi_userit = "D";
                  
                  $rs->MoveNext();
                 }
          }
     //nese useri nuk eshte i lidhur me nje qender raportimi shohim nese eshte i lidhur me nje dege ----------------------

     IF ($nr_id_branch == 0)     
        {
         $user_branch_arr[-1] = -1; //qe mos te nxjerim asgje tek listboxet
        }

     $user_attrib["tipi_userit"]             = $tipi_userit;
     
     $user_attrib["view_branch_id"]          = $user_branch_arr;
     $user_attrib["view_branch_nr"]          = $nr_id_branch;
     $user_attrib["view_branch_ids"]         = IMPLODE(",", $user_branch_arr);
 
     IF ($nr_id_branch_edit == 0)
        {
         $user_attrib["edit_branch_id"]      = "";
         $user_attrib["edit_branch_nr"]      = $nr_id_branch_edit;
         $user_attrib["edit_branch_ids"]     = "-1";
        }
     ELSE
        {
         $user_attrib["edit_branch_id"]      = $user_branch_edit_arr;
         $user_attrib["edit_branch_nr"]      = $nr_id_branch_edit;
         $user_attrib["edit_branch_ids"]     = IMPLODE(",", $user_branch_edit_arr);
        }        
     
     IF ($nr_qender_raportimi == 0)
        {
         $user_attrib["reporting_entity_id"]  = "";
         $user_attrib["reporting_entity_nr"]  = "";
         $user_attrib["reporting_entity_ids"] = "";
        }
     ELSE
        {
         $user_attrib["reporting_entity_id"]  = $user_reporting_entity_arr;
         $user_attrib["reporting_entity_nr"]  = $nr_qender_raportimi;
         $user_attrib["reporting_entity_ids"] = IMPLODE(",", $user_reporting_entity_arr);
        }

     RETURN $user_attrib;
    }
//f_app_reporting_entity_users --------------------------------------------------------------------------------------

//NOTIFIKIMI ME EMAIL ---------------------------------------------------------------------------------------------------------------------
  function f_app_send_email ($array_info_email) 
    {
     IF (defined('SYSTEM_EMAIL_SEND') AND (SYSTEM_EMAIL_SEND == "Y") AND ($array_info_email["email_to"] != ""))
        {
         $email_subjekti = SYSTEM_EMAIL_SUBJECT;
         
         IF (ISSET($array_info_email["email_subject"]) AND ($array_info_email["email_subject"] != ""))
            {
             $email_subjekti .= " - ".$array_info_email["email_subject"];
            }
         
         $mesazhi = '<html>
                      <head>
                      <title>'.EMAIL_SUBJECT.'</title>
                      </head>
                      <body>
                      '.$array_info_email["email_message"].'
                      </body>
                     </html>
                    ';
  

         $emailConfiguration = array();

         //per ta nisur emailin me klasen me SMTP --------------------------------------------------
           //seet the mail subject -----------------------------------------------------------------
             $emailConfiguration["subject"] = $email_subjekti;
           //---------------------------------------------------------------------------------------
       
           //mesazhi -------------------------------------------------------------------------------
             $emailConfiguration["html"]    = $mesazhi;
           //---------------------------------------------------------------------------------------

           //adresa from ---------------------------------------------------------------------------
             IF (ISSET($array_info_email["email_from"]) AND ($array_info_email["email_from"] != "")) 
                {
                 $email_from = $array_info_email["email_from"];
                }
             ELSE
                {
                 $email_from = SYSTEM_EMAIL_FROM;
                }

             IF (ISSET($array_info_email["email_from_label"]) AND ($array_info_email["email_from_label"] != "")) 
                {
                 $email_from_label = $array_info_email["email_from_label"];
                }
             ELSE
                {
                 $email_from_label = SYSTEM_EMAIL_FROM_LABEL;
                }

             $emailConfiguration["from"]      = $email_from;
             $emailConfiguration["fromlabel"] = $email_from_label;
           //adresa from ---------------------------------------------------------------------------

           //adresa to ose te ndara me , ose ne array kur duam tia dergojme vec e vec --------------
             $emailConfiguration["to"] = $array_info_email["email_to"];
           //---------------------------------------------------------------------------------------

           //adresa cc -----------------------------------------------------------------------------
             IF (ISSET($array_info_email["email_cc"]) AND ($array_info_email["email_cc"] != "")) 
                {
                 $emailConfiguration["cc"] = $array_info_email["email_cc"];
                }
           //adresa cc -----------------------------------------------------------------------------

           //adresa bcc ----------------------------------------------------------------------------
             IF (ISSET($array_info_email["email_bcc"]) AND ($array_info_email["email_bcc"] != "")) 
                {
                 $emailConfiguration["bcc"] = $array_info_email["email_bcc"];
                }
           //adresa bcc ----------------------------------------------------------------------------

           //send email ----------------------------------------------------------------------------
             $mailResult = GeneralBase::sendEmailWraper($emailConfiguration);
           //---------------------------------------------------------------------------------------
         //per ta nisur emailin me klasen me SMTP --------------------------------------------------
        }
    }
//NOTIFIKIMI ME EMAIL ------------------------------------------------------------------------------

//PO E FORMOJE KETU KETE FUNKSION PER TE PARE NESE USERAT QE DO NOTIFIKOJE E KANE NJE AKSION -------
  function f_app_user_RightsToNem ($arg_UserId, $arg_id_nem, $arg_id_action=102) 
    {
     $RightsToNem = 0;

     $sql = "SELECT COUNT(1) AS RightsToNem
               FROM nems_func
                              INNER JOIN profil_nems_func ON 
                                         nems_func.nem_id      = profil_nems_func.nem_id AND 
                                         nems_func.function_id = profil_nems_func.function_id
                              
                              INNER JOIN user_profile ON
                                         profil_nems_func.profil_id = user_profile.profil_id
                              
              WHERE nems_func.nem_id      = '".$arg_id_nem."'     AND
                    nems_func.function_id IN (".$arg_id_action.") AND
                    user_profile.UserId   = '".$arg_UserId."'
            ";		

     $rs = WebApp::execQuery($sql);
     IF (!$rs->EOF())
        {
         $RightsToNem = $rs->Field("RightsToNem");
        }

     RETURN $RightsToNem;
    }
//PO E FORMOJE KETU KETE FUNKSION PER TE PARE NESE USERAT QE DO NOTIFIKOJE E KANE NJE AKSION ----------------------

//PO E FORMOJE KETU KETE FUNKSION PER TE PARE NESE USERAT QE DO NOTIFIKOJE E KANE TE DREJTE TE SHOHIN KETE NEM ----
  function f_app_user_RightsToCI ($arg_UserId, $arg_id_nem) 
    {
     $RightsToCI = 0;

     $sql = "SELECT COUNT(1) AS RightsToCI
               FROM content INNER JOIN ci_nems ON
                            content.content_id = ci_nems.content_id
                            
                            INNER JOIN profil_rights ON
                                  content.id_zeroNivel    = profil_rights.id_zeroNivel   AND
                                  content.id_firstNivel   = profil_rights.id_firstNivel  AND
                                  content.id_secondNivel  = profil_rights.id_secondNivel AND
                                  content.id_thirdNivel   = profil_rights.id_thirdNivel  AND
                                  content.id_fourthNivel  = profil_rights.id_fourthNivel 
                            
                            INNER JOIN user_profile ON
                                  profil_rights.profil_id = user_profile.profil_id
                            
                      WHERE ci_nems.nem_id      = '".ValidateVarFun::f_real_escape_string($arg_id_nem)."' AND
                            user_profile.UserId = '".ValidateVarFun::f_real_escape_string($arg_UserId)."'
            ";
     //PRINT $sql;
     
     $rs = WebApp::execQuery($sql);
     IF (!$rs->EOF())
        {
         $RightsToCI = $rs->Field("RightsToCI");
        }

     RETURN $RightsToCI;
    }
//PO E FORMOJE KETU KETE FUNKSION PER TE PARE NESE USERAT QE DO NOTIFIKOJE E KANE NJE AKSION ----------------------

//f_app_dega_user_email -------------------------------------------------------------------------------------------
  function f_app_dega_user_emails ($arg_id_branch, $arg_id_nem, $arg_id_action=102, $arg_view_edit="E") 
    {
     GLOBAL $session;
     $user_emails_arr = array();

     $sql = "SELECT DISTINCT 
                    users.UserId                                      AS UserId_sel,
                    IF (users.usr_email IS NULL, '', users.usr_email) AS usr_email_sel
               FROM phi_branch_users INNER JOIN users ON
                                     phi_branch_users.UserId = users.UserId
              WHERE phi_branch_users.id_branch IN (".$arg_id_branch.") AND 
                    phi_branch_users.view_edit = '".$arg_view_edit."'  AND
                    users.UserId              != '".$session->Vars["ses_userid"]."'
                  ";
     //print $sql;
     $rs  = WebApp::execQuery($sql);
     $rs->MoveFirst();
     WHILE (!$rs->EOF())
           {
            $UserId_sel    = $rs->Field("UserId_sel");
            $usr_email_sel = $rs->Field("usr_email_sel");
                  
            IF ($usr_email_sel != "")
               {
                $RightsToNemCI = 0;
                
                IF ($arg_view_edit == "E")
                   {
                    //shohim nese useri ka te drejte per aksionin e zgjdhur ---------------------------------------------
                      $RightsToNemCI = f_app_user_RightsToNem ($UserId_sel, $arg_id_nem, $arg_id_action);
                    //shohim nese useri ka te drejte per aksionin e zgjdhur ---------------------------------------------
                   }
                ELSE
                   {
                    //shohim nese useri ka te drejte TE AKSESOJE CI QE MBAN NEMIN ---------------------------------------
                      $RightsToNemCI = f_app_user_RightsToCI ($UserId_sel, $arg_id_nem);
                    //shohim nese useri ka te drejte TE AKSESOJE CI QE MBAN NEMIN ---------------------------------------
                   }

                IF ($RightsToNemCI > 0)
                   {
                    $user_emails_arr[] = trim($usr_email_sel);
                   }
                //---------------------------------------------------------------------------------------------------
               }
                  
            $rs->MoveNext();
           }
     //nese useri nuk eshte i lidhur me nje qender raportimi shohim nese eshte i lidhur me nje dege -----------------
    
     RETURN $user_emails_arr;
    }
//f_app_reporting_entity_users --------------------------------------------------------------------------------------

//f_app_url_nem -----------------------------------------------------------------------------------------------------
  function f_app_url_nem ($arg_id_nem, $arg_id_action=101) 
    {
     GLOBAL $session;

     $NEM_ID_SEL    = $arg_id_nem;
     $id_action_sel = $arg_id_action; //101 = Add
     
     $url_nem       = "";
     
     $nem_rights    = NemsManager::getFeRightsToNem($NEM_ID_SEL);
     
     IF (ISSET($nem_rights[$NEM_ID_SEL][$id_action_sel]) AND ($nem_rights[$NEM_ID_SEL][$id_action_sel] != ""))
        {
         //useri ka te drejte per aksionin e zgjedhur tek nemi ... jemi ok
        }
     ELSE
        {
         RETURN $url_nem;
        }

     //USERI KA TE DREJTE TE PERDOR AKSIONIN TEK NEMI ... KAPIM KORDINATAT E CI QE MBAN NEMIN ----------------------
        $sql = "SELECT content.content_id AS content_id_nem
                  FROM content INNER JOIN ci_nems ON
                               content.content_id = ci_nems.content_id
                               
                               INNER JOIN profil_rights ON
                                     content.id_zeroNivel    = profil_rights.id_zeroNivel   AND
                                     content.id_firstNivel   = profil_rights.id_firstNivel  AND
                                     content.id_secondNivel  = profil_rights.id_secondNivel AND
                                     content.id_thirdNivel   = profil_rights.id_thirdNivel  AND
                                     content.id_fourthNivel  = profil_rights.id_fourthNivel 
                 WHERE content.id_zeroNivel     = '".$session->Vars["level_0"]."'                         AND
                       ci_nems.nem_id           = '".ValidateVarFun::f_real_escape_string($NEM_ID_SEL)."' AND
                       profil_rights.profil_id IN (".$session->Vars["tip"].")
               ";
       //print $sql;
       
       $rs = WebApp::execQuery($sql);
       IF (!$rs->EOF())
          {
           $content_id_nem = $rs->Field("content_id_nem");
           $url_nem        = APP_URL.'?uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&mode=alone&crd='.$content_id_nem;
          }
     //USERI KA TE DREJTE TE PERDOR AKSIONIN TEK NEMI ... KAPIM KORDINATAT E CI QE MBAN NEMIN ----------------------

     RETURN $url_nem;
    }
//f_app_url_nem -----------------------------------------------------------------------------------------------------

//f_app_users_management_tools -------------------------------------------------------------------------------------------
  function f_app_user_management_tool ($arg_tools_id=14) 
    {
     GLOBAL $session;

     $user_tool_management = 0;
     
     $sql = "SELECT COUNT(*)  AS user_tool_management
               FROM profil_tools
              WHERE tools_id = '".$arg_tools_id."' AND
                    profil_id IN (".$session->Vars["tip"].")
            ";
     
     $rs = WebApp::execQuery($sql);
     IF (!$rs->EOF())
        {
         $user_tool_management = $rs->Field("user_tool_management");
        }

     IF ($user_tool_management > 0)
        {
         RETURN "Y";
        }
     ELSE
        {
         RETURN "N";
        }
    }
//f_app_users_management_tools -------------------------------------------------------------------------------------------

//f_app_id_branch_municipality_village -----------------------------------------------------------------------------------
  function f_app_id_branch_municipality_village ($arg_id_municipality, $arg_id_village) 
    {
     $id_branch_sel = "";
     
     IF (($arg_id_municipality != "") OR ($arg_id_village != ""))
        {
         $kushti_where = "";
     
         IF ($arg_id_municipality != "")
            {
             $kushti_where .= " AND phi_village.id_municipality = '".ValidateVarFun::f_real_escape_string($arg_id_municipality)."'";
            }

         IF ($arg_id_village != "")
            {
             $kushti_where .= " AND phi_village.id_village = '".ValidateVarFun::f_real_escape_string($arg_id_village)."'";
            }

         $kushti_where = SUBSTR($kushti_where, 5);
         
         $sql = "SELECT DISTINCT phi_reporting_entity.id_branch AS id_branch_sel
                   FROM phi_reporting_entity INNER JOIN phi_reporting_entity_village ON
                                             phi_reporting_entity.id_reporting_entity = phi_reporting_entity_village.id_reporting_entity
                                             INNER JOIN phi_village ON
                                             phi_reporting_entity_village.id_village = phi_village.id_village
                  WHERE ".$kushti_where."
                ";
         //print $sql;
         $rs = WebApp::execQuery($sql);
         IF (!$rs->EOF())
            {
             $id_branch_sel = $rs->Field("id_branch_sel");
            }
        }

     RETURN $id_branch_sel;
    }
//f_app_id_branch_municipality_village -----------------------------------------------------------------------------------

INCLUDE(dirname(__FILE__)."/form_function.php");

?>