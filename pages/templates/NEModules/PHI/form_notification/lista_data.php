<?
//kushti i kerkimit ------------------------------------------------------------------------------------------------------
  $kushti_kerkim  = '';

 //APLIKOJME LIDHJEN E USERIT ME DEGE/QENDER -----------------------------------------------------------------------------
   //$kushti_kerkim .= ' AND phi_form_notification.id_branch IN ('.$user_dega_qendra["view_branch_ids"].') ';

   //IF ($user_dega_qendra["reporting_entity_ids"] != "")
   //   {
   //    $kushti_kerkim .= ' AND phi_form_notification.id_reporting_entity IN ('.$user_dega_qendra["reporting_entity_ids"].') ';
   //   }

   IF ($user_dega_qendra["tipi_userit"] == "D")
      {
       $tab_users_branch_or_reporting_entity   = "phi_branch_users";
       $field_users_branch_or_reporting_entity = "id_branch";
      }
   ELSE
      {
       $tab_users_branch_or_reporting_entity   = "phi_reporting_entity_users";
       $field_users_branch_or_reporting_entity = "id_reporting_entity";
      }
 //APLIKOJME LIDHJEN E USERIT ME DEGE/QENDER -----------------------------------------------------------------------------

  IF (ISSET($G_APP_VARS['s_id_branch']) AND ($G_APP_VARS['s_id_branch'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.id_branch = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_branch']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_id_reporting_entity']) AND ($G_APP_VARS['s_id_reporting_entity'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_reporting_entity']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_id_reporting_entity_kind']) AND ($G_APP_VARS['s_id_reporting_entity_kind'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.id_reporting_entity_kind = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_reporting_entity_kind']).'" ';
     }

  
  IF (ISSET($G_APP_VARS['s_dt1']) AND ($G_APP_VARS['s_dt1'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.date_of_completion >= "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_dt1']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_dt2']) AND ($G_APP_VARS['s_dt2'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.date_of_completion <= "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_dt2']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_form_number']) AND ($G_APP_VARS['s_form_number'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.form_number = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_form_number']).'" ';
     }


  IF (ISSET($G_APP_VARS['s_id_disease']) AND ($G_APP_VARS['s_id_disease'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.id_disease = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_disease']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_person_first_name']) AND ($G_APP_VARS['s_person_first_name'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.person_first_name LIKE "%'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_person_first_name']).'%" ';
     }

  IF (ISSET($G_APP_VARS['s_person_last_name']) AND ($G_APP_VARS['s_person_last_name'] != ''))
     {
      $kushti_kerkim .= ' AND phi_form_notification.person_last_name LIKE "%'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_person_last_name']).'%" ';
     }
//kushti i kerkimit ------------------------------------------------------------------------------------------------------

//numri i rekordeve total ------------------------------------------------------------------------------------------------
  //IF ($kushti_kerkim != '')
  //   {
  //    $kushti_kerkim = ' WHERE '.SUBSTR($kushti_kerkim, 5);
  //   }
     
  $sql = 'SELECT count(1) as nr_rec_total
            FROM phi_form_notification 
                     
                     INNER JOIN phi_branch ON
                           phi_form_notification.id_branch           = phi_branch.id_branch 

                     INNER JOIN phi_reporting_entity ON
                           phi_form_notification.id_reporting_entity = phi_reporting_entity.id_reporting_entity 

                     INNER JOIN phi_reporting_entity_kind ON
                           phi_reporting_entity.id_reporting_entity_kind = phi_reporting_entity_kind.id_reporting_entity_kind 

                     INNER JOIN '.$tab_users_branch_or_reporting_entity.' ON
                           phi_form_notification.'.$field_users_branch_or_reporting_entity.' = '.$tab_users_branch_or_reporting_entity.'.'.$field_users_branch_or_reporting_entity.' 

           WHERE phi_form_notification.record_status              = 1 AND
                 '.$tab_users_branch_or_reporting_entity.'.UserId = "'.ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"]).'"
                 '.$kushti_kerkim.'
         ';

  $rs = WebApp::execQuery($sql);
  IF (!$rs->EOF())
     {
      $nr_rec_total = $rs->Field('nr_rec_total');
     }
  ELSE
     {
      $nr_rec_total = 0;
     }
//------------------------------------------------------------------------------------------------------------------------

//LISTA DATA -------------------------------------------------------------------------------------------------------------
  IF ($nr_rec_total > 0)
     {
      $nr_rec_page_exp = 100000;

      IF ($exp_data == "Y")
         {
          $nr_rec_start = 0;
          $nr_rec_page  = $nr_rec_page_exp;

          unset($row_sel);
          unset($cel_sel);
          $cel_sel['exp_file_name']    = ''; //ne rast se kjo lihet bosh atehere tek exp.php kemi kapur titullin e CI qe inkudon nemin
          //$cel_sel['exp_sheet_name'] = "sheet_name";
          $cel_sel['exp_titull']       = ''; //ne rast se kjo lihet bosh atehere tek exp.php kemi kapur titullin e CI qe inkudon nemin

          $row_sel["properties"]     = $cel_sel;
          $data_arr[]                = $row_sel;
        }

      $kolonat_fusha[] = "phi_form_notification.id_form_notification"; $kolonat_etiketa[] = WebApp::getVar("id_mesg");
      $kolonat_fusha[] = "phi_branch.name";                            $kolonat_etiketa[] = WebApp::getVar("id_branch_sh_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.name";                  $kolonat_etiketa[] = WebApp::getVar("id_reporting_entity_sh_mesg");
      $kolonat_fusha[] = "phi_reporting_entity_kind.name";             $kolonat_etiketa[] = WebApp::getVar("id_reporting_entity_kind_mesg");

      $kolonat_fusha[] = "phi_form_notification.id_disease";           $kolonat_etiketa[] = WebApp::getVar("id_disease_mesg");
      $kolonat_fusha[] = "phi_form_notification.form_number";          $kolonat_etiketa[] = WebApp::getVar("form_number_sh_mesg");
      $kolonat_fusha[] = "phi_form_notification.date_of_completion";   $kolonat_etiketa[] = WebApp::getVar("date_of_completion_sh_mesg");
      
      $kolonat_fusha[] = "phi_form_notification.person_first_name";    $kolonat_etiketa[] = WebApp::getVar("person_first_name_mesg");
      $kolonat_fusha[] = "phi_form_notification.person_last_name";     $kolonat_etiketa[] = WebApp::getVar("person_last_name_mesg");

      IF ($exp_data == "Y")
         {
          //$kolonat_fusha[] = "phi_form_notification.date_receipt_from_reporting_entity";    $kolonat_etiketa[] = WebApp::getVar("date_receipt_from_reporting_entity_mesg");
         }

      IF ($exp_data != "Y")
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " "; //ikona preview
         }

      IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != ""))
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " ";  //ikona edit
         }
      
      unset($row_sel);
      FOR ($i=0; $i < count($kolonat_fusha); $i++)
          {
           unset($cel_sel);
           $cel_sel['tag']              = 'th';
           $cel_sel['tag_att']          = '';
           $cel_sel['link']             = 'N'; //Y/N  //e trajtoje me vone ne skriptin e centralizuar php_script\lista_info.php
           $cel_sel['link_att']         = '';
           $cel_sel['data_type']        = 'label'; //label/icon
           
           $cel_sel['vl']               = $kolonat_etiketa[$i];
           $cel_sel['vl_db']            = $kolonat_fusha[$i];
           $cel_sel['vl_db_indx']       = $i;
           $cel_sel['vlf']              = '';
           $cel_sel['bold']             = 'Y';
           $cel_sel['align']            = '';
           $cel_sel['style']            = '';
           $cel_sel['colspan']          = '';
           $cel_sel['format_number']    = ''; //per xlsx

           $row_sel[]                   = $cel_sel;
          }
      
      $data_arr[] = $row_sel;
     }
//------------------------------------------------------------------------------------------------------------------------

//LISTA DATA -------------------------------------------------------------------------------------------------------------
  IF ($nr_rec_total > 0)
     {
      //id_disease_arr ---------------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_disease";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $id_disease_arr        = f_app_lov_default_values($lov);
      //id_disease_arr ---------------------------------------------------------------------------------------------------

      //exp_data ---------------------------------------------------------------------------------------------------------
        IF ($exp_data == "Y")
           {
            //
           }
      //exp_data ---------------------------------------------------------------------------------------------------------

      //order_by_name ----------------------------------------------------------------------------------------------------
        IF (!ISSET($G_APP_VARS["order_by"]) OR ($G_APP_VARS["order_by"] == ""))
           {
            $G_APP_VARS["order_by"] = '2';
           }
        
        $order_by      = 'ASC';
        $order_by_name = $kolonat_fusha[0];

        IF ($G_APP_VARS["order_by"] == 2)
           {
            $order_by = 'DESC';
           }
        
        IF (ISSET($kolonat_fusha[$G_APP_VARS["order_by_indx"]]) AND ($kolonat_fusha[$G_APP_VARS["order_by_indx"]] != ""))
           {
            $order_by_name = $kolonat_fusha[$G_APP_VARS["order_by_indx"]];
           }
      //------------------------------------------------------------------------------------------------------------------

      //futemi ne kursorin e te dhenave ----------------------------------------------------------------------------------
        $sql = 'SELECT phi_form_notification.id_form_notification as id_sel,
                       phi_form_notification.id_branch            as id_branch_sel,
                       phi_form_notification.id_reporting_entity  as id_reporting_entity_sel,
                       phi_branch.name                            as dega_name,
                       phi_reporting_entity.name                  as qendra_name,
                       phi_reporting_entity_kind.name             as qendra_lloji,

                       phi_form_notification.id_disease                                  as id_disease,
                       IF(phi_form_notification.form_number IS NULL, "", form_number)    as form_number_f,
                       DATE_FORMAT(phi_form_notification.date_of_completion, "%d.%m.%Y") as date_of_completion_f,
                       
                       COALESCE (phi_form_notification.person_first_name, "") as person_first_name,
                       COALESCE (phi_form_notification.person_last_name,  "") as person_last_name

                  FROM phi_form_notification 
                       
                           INNER JOIN phi_branch ON
                                 phi_form_notification.id_branch = phi_branch.id_branch 

                           INNER JOIN phi_reporting_entity ON
                                 phi_form_notification.id_reporting_entity = phi_reporting_entity.id_reporting_entity 

                           INNER JOIN phi_reporting_entity_kind ON
                                 phi_reporting_entity.id_reporting_entity_kind = phi_reporting_entity_kind.id_reporting_entity_kind 

                          INNER JOIN '.$tab_users_branch_or_reporting_entity.' ON
                                 phi_form_notification.'.$field_users_branch_or_reporting_entity.' = '.$tab_users_branch_or_reporting_entity.'.'.$field_users_branch_or_reporting_entity.' 
                     
                 WHERE phi_form_notification.record_status              = 1 AND
                       '.$tab_users_branch_or_reporting_entity.'.UserId = "'.ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"]).'"
                       '.$kushti_kerkim.'
              ORDER BY '.$order_by_name.' '.$order_by.'
                       Limit '.$nr_rec_start.','.$nr_rec_page.'
	            ';

        $rs_list = WebApp::execQuery($sql);
        $rs_list->MoveFirst();
        WHILE (!$rs_list->EOF())
	          {
               $id_sel                       = $rs_list->Field('id_sel');
               $id_branch_sel                = $rs_list->Field('id_branch_sel');
               $id_reporting_entity_sel      = $rs_list->Field('id_reporting_entity_sel');
               $dega_name                    = $rs_list->Field('dega_name');
               $qendra_name                  = $rs_list->Field('qendra_name');
               $qendra_lloji                 = $rs_list->Field('qendra_lloji');
               
               $id_disease                   = $rs_list->Field('id_disease');
               $form_number_f                = $rs_list->Field('form_number_f');
               $date_of_completion_f         = $rs_list->Field('date_of_completion_f');
               $person_first_name            = $rs_list->Field('person_first_name');
               $person_last_name             = $rs_list->Field('person_last_name');
               
               //exp_data ------------------------------------------------------------------------------------------------
                 IF ($exp_data == "Y")
                    {
                     //
                    }
               //exp_data ------------------------------------------------------------------------------------------------
               
               //array me datat ------------------------------------------------------------------------------------------
                 unset($row_sel);

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $id_sel;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $dega_name;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $qendra_name;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $qendra_lloji;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $id_disease_arr[$id_disease].'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $form_number_f.'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;
                 
                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $date_of_completion_f.'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $person_first_name.'';
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $person_last_name;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 IF ($exp_data == "Y")
				    {
                     //
                    }

                 IF ($exp_data != "Y")
                    {
                     $post_id_sel = f_app_encrypt($id_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                    }

                 IF ($exp_data != "Y")
                    {
                     //variabli per preview ---------------------------------------------------------
                       $vars_post_kol    = null;
                       $vars_post_val    = null;
                     
                       $vars_post_kol[]  = 'gjendje';
                       $vars_post_val[]  = 'record_detaje';

                       $vars_post_kol[]  = 'editim_konsultim';
                       $vars_post_val[]  = 'konsultim';

                       $vars_post_kol[]  = 'post_id';
                       $vars_post_val[]  = $post_id_sel;

                       $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
                       //$data_url_sel   = $data_url_preview.'&vars_post='.$vars_post; //preview e shpejte kur nuk ka shume info
                       $data_url_sel     = $data_url_this_nem.'&vars_post='.$vars_post;  //previev me iframe kur kemi scroll
                     //variabli per preview ---------------------------------------------------------
                     
                     unset($cel_sel);
                     $cel_sel['tag']               = 'td';
                     $cel_sel['tag_att']           = '';

                     $cel_sel['link']              = 'Y'; //Y/N
                     $cel_sel['link_att']          = 'href="javascript:void(0);"';
                     $cel_sel['link_data_modal']   = 'Y';
                     $cel_sel['link_data_title']   = $content_title." - ".WebApp::getVar("preview_mesg");
                     $cel_sel['link_data_url']     = $data_url_sel;
                     $cel_sel['link_modal_iframe'] = 'true'; //previev me iframe kur kemi scroll            
                     $cel_sel['link_modal_width']  = $link_modal_width_default;
                     $cel_sel['link_modal_height'] = $link_modal_height_default;

                     $cel_sel['data_type']         = 'icon'; //label/icon
                     $cel_sel['vl']                = 'icon_preview';
                     $cel_sel['vl_db']             = '';
                     $cel_sel['vlf']               = '';
                     $cel_sel['bold']              = '';
                     $cel_sel['align']             = '';
                     $cel_sel['style']             = '';
                     $cel_sel['colspan']           = '';
                     $cel_sel['format_number']     = ''; //per xlsx
                     $row_sel[]                    = $cel_sel;
                    }

                 IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != ""))
                    {
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';
                     $cel_sel['link']             = 'Y'; //Y/N
                     
                     IF (ISSET($user_dega_qendra["edit_branch_id"][$id_branch_sel]) AND ($user_dega_qendra["edit_branch_id"][$id_branch_sel] == $id_branch_sel))
                        {
                         //USERI KA TE DREJTE TE EDITOJE NE KETE DEGE 
                         $cel_sel['link_att']     = 'href="javascript:f_app_add_edit(\''.$arg_webbox.'\', \'add_edit\', \''.$arg_id_form.'\', \''.$post_id_sel.'\')"';
                         $cel_sel['data_type']    = 'icon'; //label/icon
                         $cel_sel['vl']           = 'icon_edit';
                        }
                     ELSE
                        {
                         //USERI NUK KA TE DREJTE TE EDITOJE NE KETE DEGE 
                         $cel_sel['link_att']     = '';
                         $cel_sel['data_type']    = 'label'; //label/icon
                         $cel_sel['vl']           = '';
                        }

                     $cel_sel['vl_db']            = '';
                     $cel_sel['vlf']              = '';
                     $cel_sel['bold']             = '';
                     $cel_sel['align']            = '';
                     $cel_sel['style']            = '';
                     $cel_sel['colspan']          = '';
                     $cel_sel['format_number']    = ''; //per xlsx
                     $row_sel[]                   = $cel_sel;
                    }

                 $data_arr[] = $row_sel;
               //array me datat ------------------------------------------------------------------------------------------

	          $rs_list->MoveNext();
	         }
      //futemi ne kursorin e akteve --------------------------------------------------------------------------------------
     
      //per exportin -----------------------------------------------------------------------------------------------------
        IF (ISSET($nem_rights[$NEM_ID_SEL]["104"]) AND ($nem_rights[$NEM_ID_SEL]["104"] != ""))
           {
            //shtojme ne vars_page variabla shtese ---------------------------------------------------------------------------
              $webbox_list_data = STR_REPLACE(APP_PATH, "", __FILE__);

              $vars_page_kol[]  = "webbox";
              $vars_page_val[]  = $webbox_list_data;

              $vars_page_kol[]  = "idstemp";
              $vars_page_val[]  = $session->Vars["idstemp"];

              $vars_page_exp    = f_app_vars_page_encrypt($vars_page_kol, $vars_page_val);
            //shtojme ne vars_page variabla shtese ---------------------------------------------------------------------------

            $exp_params["vars_page"]  = $vars_page_exp;
            $exp_params["nr_rec_exp"] = $nr_rec_page_exp;
            $exp_params["xls"]        = "Y";
            $exp_params["cvs"]        = "Y";
            $exp_params["html"]       = "Y";
            $exp_params["pdf"]        = "Y";
            $exp_params["doc"]        = "N";
           }
      //per exportin -----------------------------------------------------------------------------------------------------
     }
//LISTA DATA -------------------------------------------------------------------------------------------------------------
?>