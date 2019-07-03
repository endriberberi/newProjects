<?
//admini duhet te kete te drejte te editoje te gjithe rekordet sepse mund te percaktoje userat per institucion -----------
  $user_tool_users_management = f_app_user_management_tool(14); //14 = toolsi i userave
//------------------------------------------------------------------------------------------------------------------------

//kushti i kerkimit ------------------------------------------------------------------------------------------------------
  $kushti_kerkim  = '';
  
  //kur kemi ardhur nga formulari i njoftimit per te zgjdhur institucionin jo laburatorin --------------------------------
    IF ($G_APP_VARS["nem_mode_filter"] == "institucioni")
       {
        $kushti_kerkim .= " AND phi_reporting_entity.id_branch IN (".$user_dega_qendra["edit_branch_ids"].") ";
       }
  //----------------------------------------------------------------------------------------------------------------------
  
  IF (ISSET($G_APP_VARS['s_id_branch']) AND ($G_APP_VARS['s_id_branch'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.id_branch = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_branch']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_address_id_municipality']) AND ($G_APP_VARS['s_address_id_municipality'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.address_id_municipality = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_address_id_municipality']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_name']) AND ($G_APP_VARS['s_name'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.name LIKE "%'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_name']).'%" ';
     }

  IF (ISSET($G_APP_VARS['s_id_reporting_entity_kind']) AND ($G_APP_VARS['s_id_reporting_entity_kind'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.id_reporting_entity_kind = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_reporting_entity_kind']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_state_or_private']) AND ($G_APP_VARS['s_state_or_private'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.state_or_private = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_state_or_private']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_fills_alert_form']) AND ($G_APP_VARS['s_fills_alert_form'] != ''))
     {
      $kushti_kerkim .= ' AND phi_reporting_entity.fills_alert_form = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_fills_alert_form']).'" ';
     }

//kushti i kerkimit ------------------------------------------------------------------------------------------------------
    
//numri i rekordeve total ------------------------------------------------------------------------------------------------
  //IF ($kushti_kerkim != '')
  //   {
  //    $kushti_kerkim = ' WHERE '.SUBSTR($kushti_kerkim, 5);
  //   }
     
  $sql = 'SELECT count(1) as nr_rec_total
            FROM phi_reporting_entity 
                     INNER JOIN phi_branch ON
                           phi_reporting_entity.id_branch = phi_branch.id_branch 
                     INNER JOIN phi_branch_users ON
                           phi_branch.id_branch = phi_branch_users.id_branch 
           WHERE phi_branch_users.UserId = "'.ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"]).'"
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

      $kolonat_fusha[] = "phi_reporting_entity.id_reporting_entity";      $kolonat_etiketa[] = WebApp::getVar("id_mesg");
      $kolonat_fusha[] = "phi_branch.name";                               $kolonat_etiketa[] = WebApp::getVar("id_branch_sh_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.name";                     $kolonat_etiketa[] = WebApp::getVar("emertimi_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.id_reporting_entity_kind"; $kolonat_etiketa[] = WebApp::getVar("id_reporting_entity_kind_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.address_id_municipality";  $kolonat_etiketa[] = WebApp::getVar("address_id_municipality_mesg");
      
      IF ($exp_data == "Y")
         {
          $kolonat_fusha[] = "phi_reporting_entity.address_id_village";   $kolonat_etiketa[] = WebApp::getVar("address_id_village_mesg");
          $kolonat_fusha[] = "phi_reporting_entity.address";              $kolonat_etiketa[] = WebApp::getVar("address_mesg");

          $kolonat_fusha[] = "phi_reporting_entity.state_or_private";     $kolonat_etiketa[] = WebApp::getVar("state_or_private_mesg");
          $kolonat_fusha[] = "phi_reporting_entity.fills_alert_form";     $kolonat_etiketa[] = WebApp::getVar("fills_alert_form_mesg");
          $kolonat_fusha[] = "phi_reporting_entity.code";                 $kolonat_etiketa[] = WebApp::getVar("code_mesg");
         }

      $kolonat_fusha[] = "phi_reporting_entity.tel";                      $kolonat_etiketa[] = WebApp::getVar("tel_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.mobile";                   $kolonat_etiketa[] = WebApp::getVar("mobile_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.email";                    $kolonat_etiketa[] = WebApp::getVar("email_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.record_status";            $kolonat_etiketa[] = WebApp::getVar("statusi_mesg");

      IF ($exp_data == "Y")
         {
          $kolonat_fusha[] = "";                                          $kolonat_etiketa[] = WebApp::getVar("fshatrat_qe_mbulohen_nga_qsh_mesg");
          $kolonat_fusha[] = "";                                          $kolonat_etiketa[] = WebApp::getVar("perdoruesit_qender_raportimi_mesg");
         }
      ELSE
         {
          $kolonat_fusha[] = "";                                          $kolonat_etiketa[] = WebApp::getVar("fshatrat_qe_mbulohen_nga_qsh_sh_mesg");
         }
         
         
         
      IF ($exp_data != "Y")
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " "; //ikona preview
         }

      IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != "") AND ($G_APP_VARS["nem_mode"] != "select_record"))
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " "; //ikon edit
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
      //record_status_data -----------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "record_status";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $record_status_arr     = f_app_lov_default_values($lov);
      //record_status_data -----------------------------------------------------------------------------------------------

      //id_reporting_entity_kind_data ------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_reporting_entity_kind";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $id_reporting_entity_kind_arr = f_app_lov_default_values($lov);
      //id_reporting_entity_kind_data ------------------------------------------------------------------------------------

      //id_municipality_data ---------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_municipality";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $id_municipality_arr   = f_app_lov_default_values($lov);
      //id_municipality_data ---------------------------------------------------------------------------------------------

      //id_village_data --------------------------------------------------------------------------------------------------
        IF ($exp_data == "Y")
           {
            unset($lov);
            $lov["name"]           = "id_village";
            $lov["obj_or_label"]   = "label";
            $lov["all_data_array"] = "Y";
            $id_village_arr        = f_app_lov_default_values($lov);
           
            unset($lov);
            $lov["name"]           = "state_or_private";
            $lov["obj_or_label"]   = "label";
            $lov["all_data_array"] = "Y";
            $state_or_private_arr  = f_app_lov_default_values($lov);
           
            unset($lov);
            $lov["name"]           = "fills_alert_form";
            $lov["obj_or_label"]   = "label";
            $lov["all_data_array"] = "Y";
            $fills_alert_form_arr  = f_app_lov_default_values($lov);
           }
      //id_village_data --------------------------------------------------------------------------------------------------

      //order_by_name ----------------------------------------------------------------------------------------------------
        IF (!ISSET($G_APP_VARS["order_by"]) OR ($G_APP_VARS["order_by"] == ""))
           {
            $G_APP_VARS["order_by"] = '1';
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
        $sql = 'SELECT phi_reporting_entity.id_reporting_entity                                                                      as id_sel,
                       phi_reporting_entity.id_branch                                                                                as id_branch_sel,
                       IF (phi_reporting_entity.name                     IS NULL, "", phi_reporting_entity.name)                     as name_sel,
                       IF (phi_branch.name                               IS NULL, "", phi_branch.name)                               as dega_name,
                       
                       IF (phi_reporting_entity.address_id_municipality  IS NULL, "", phi_reporting_entity.address_id_municipality)  as address_id_municipality_sel,
                       IF (phi_reporting_entity.address_id_village       IS NULL, "", phi_reporting_entity.address_id_village)       as address_id_village_sel,
                       IF (phi_reporting_entity.address                  IS NULL, "", phi_reporting_entity.address)                  as address_sel,

                       IF (phi_reporting_entity.id_reporting_entity_kind IS NULL, "", phi_reporting_entity.id_reporting_entity_kind) as id_reporting_entity_kind_sel,

                       IF (phi_reporting_entity.tel                      IS NULL, "", phi_reporting_entity.tel)    as tel_sel,
                       IF (phi_reporting_entity.mobile                   IS NULL, "", phi_reporting_entity.mobile) as mobile_sel,
                       IF (phi_reporting_entity.email                    IS NULL, "", phi_reporting_entity.email)  as email_sel,

                       IF (phi_reporting_entity.state_or_private         IS NULL, "", phi_reporting_entity.state_or_private)  as state_or_private,
                       IF (phi_reporting_entity.fills_alert_form         IS NULL, "", phi_reporting_entity.fills_alert_form)  as fills_alert_form,
                       IF (phi_reporting_entity.code                     IS NULL, "", phi_reporting_entity.code)              as code,

                       IF (phi_reporting_entity.record_status            IS NULL, "", phi_reporting_entity.record_status) as record_status
                  FROM phi_reporting_entity 
                           INNER JOIN phi_branch ON
                               phi_reporting_entity.id_branch = phi_branch.id_branch 
                           INNER JOIN phi_branch_users ON
                               phi_branch.id_branch = phi_branch_users.id_branch 
                 WHERE phi_branch_users.UserId = "'.ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"]).'"
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
               $name_sel                     = $rs_list->Field('name_sel');
               $dega_name                    = $rs_list->Field('dega_name');
               
               $address_id_municipality_sel  = $rs_list->Field('address_id_municipality_sel');
               $address_id_village_sel       = $rs_list->Field('address_id_village_sel');
               $address_sel                  = $rs_list->Field('address_sel');

               $id_reporting_entity_kind_sel = $rs_list->Field('id_reporting_entity_kind_sel');

               $tel_sel                      = $rs_list->Field('tel_sel');
               $mobile_sel                   = $rs_list->Field('mobile_sel');
               $email_sel                    = $rs_list->Field('email_sel');

               $state_or_private             = $rs_list->Field('state_or_private');
               $fills_alert_form             = $rs_list->Field('fills_alert_form');
               $code                         = $rs_list->Field('code');

               $record_status                = $rs_list->Field('record_status');

               //id_reporting_entity_kind ----------------------------------------------------------------------------------------
                 $lov_id_reporting_entity_kind = $id_reporting_entity_kind_arr[$id_reporting_entity_kind_sel];
               //id_municipality ----------------------------------------------------------------------------------------

               //id_municipality ----------------------------------------------------------------------------------------
                 $lov_address_id_municipality = $id_municipality_arr[$address_id_municipality_sel];
               //id_municipality ----------------------------------------------------------------------------------------

               //id_village ---------------------------------------------------------------------------------------------
                 IF ($exp_data == "Y")
                    {
                     $lov_address_id_village = $id_village_arr[$address_id_village_sel];
                    }
               //id_village ---------------------------------------------------------------------------------------------
               
               //record_status -------------------------------------------------------------------------------------------
                 $lov_record_status = $record_status_arr[$record_status];
               //record_status -------------------------------------------------------------------------------------------

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
                 $cel_sel['vl']               = $name_sel;
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
                 $cel_sel['vl']               = $lov_id_reporting_entity_kind.'';
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
                 $cel_sel['vl']               = $lov_address_id_municipality.'';
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
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';
                     $cel_sel['link']             = 'N'; //Y/N
                     $cel_sel['link_att']         = '';
                     $cel_sel['data_type']        = 'label'; //label/icon
                     $cel_sel['vl']               = $lov_address_id_village.'';
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
                     $cel_sel['vl']               = $address_sel.'';
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
                     $cel_sel['vl']               = $state_or_private_arr[$state_or_private].'';
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
                     $cel_sel['vl']               = $fills_alert_form_arr[$fills_alert_form].'';
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
                     $cel_sel['vl']               = $code.'';
                     $cel_sel['vl_db']            = '';
                     $cel_sel['vlf']              = '';
                     $cel_sel['bold']             = '';
                     $cel_sel['align']            = '';
                     $cel_sel['style']            = '';
                     $cel_sel['colspan']          = '';
                     $cel_sel['format_number']    = ''; //per xlsx
                     $row_sel[]                   = $cel_sel;
                    }


                 unset($cel_sel);
                 $cel_sel['tag']              = 'td';
                 $cel_sel['tag_att']          = '';
                 $cel_sel['link']             = 'N'; //Y/N
                 $cel_sel['link_att']         = '';
                 $cel_sel['data_type']        = 'label'; //label/icon
                 $cel_sel['vl']               = $tel_sel;
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
                 $cel_sel['vl']               = $mobile_sel;
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
                 $cel_sel['vl']               = $email_sel;
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
                 $cel_sel['vl']               = $lov_record_status;
                 $cel_sel['vl_db']            = '';
                 $cel_sel['vlf']              = '';
                 $cel_sel['bold']             = '';
                 $cel_sel['align']            = '';
                 $cel_sel['style']            = '';
                 $cel_sel['colspan']          = '';
                 $cel_sel['format_number']    = ''; //per xlsx
                 $row_sel[]                   = $cel_sel;

                 //kapim fshatrat qe mbullon kjo qender ---------------------------------------------------------------------------
                   $lov_reporting_entity_village       = "";
                   $lov_reporting_entity_village_title = "";
                   IF ($exp_data == "Y")
                      {
                       unset($lov);
                       $lov["name"]         = "district_commune_village";
                       $lov["obj_or_label"] = "label";

                       $lov["tab_name"]     = "phi_village INNER JOIN phi_reporting_entity_village ON 
                                                       phi_village.id_village = phi_reporting_entity_village.id_village
                                                       INNER JOIN phi_district ON 
                                                       phi_village.id_district = phi_district.id_district 
                                                       INNER JOIN phi_commune ON 
                                                       phi_village.id_commune = phi_commune.id_commune 
                                          ";
                       $lov["filter"]       = 'WHERE id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($id_sel).'"';
                       $lov["layout_forma"] = " | ";
                       $lov_reporting_entity_village = f_app_lov_default_values($lov);
                       $lov_reporting_entity_village = SUBSTR($lov_reporting_entity_village, 3);
                      }
                   ELSE
                      {
                       unset($lov);
                       $lov["name"]         = "id_village";
                       $lov["obj_or_label"] = "label";
                       $lov["id"]           = "phi_village.id_village";

                       $lov["tab_name"]     = "phi_village INNER JOIN phi_reporting_entity_village ON 
                                                       phi_village.id_village = phi_reporting_entity_village.id_village
                                              ";
                       $lov["filter"]       = 'WHERE id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($id_sel).'"';
                       $lov["layout_forma"] = " | ";
                       $lov_reporting_entity_village = f_app_lov_default_values($lov);
                       $lov_reporting_entity_village = SUBSTR($lov_reporting_entity_village, 3);
                       
                       IF ($lov_reporting_entity_village != "")
                          {
                           $lov_reporting_entity_village_title = ' title="'.$lov_reporting_entity_village.'"';
                           
                           IF (strlen($lov_reporting_entity_village) > 25)
                              {
                               $lov_reporting_entity_village       = mb_substr($lov_reporting_entity_village, 0, 23)."...";
                              }
                          }
                      }
                   unset($cel_sel);
                   $cel_sel['tag']              = 'td';
                   $cel_sel['tag_att']          = ''.$lov_reporting_entity_village_title;
                   $cel_sel['link']             = 'N'; //Y/N
                   $cel_sel['link_att']         = '';
                   $cel_sel['data_type']        = 'label'; //label/icon
                   $cel_sel['vl']               = $lov_reporting_entity_village;
                   $cel_sel['vl_db']            = '';
                   $cel_sel['vlf']              = '';
                   $cel_sel['bold']             = '';
                   $cel_sel['align']            = '';
                   $cel_sel['style']            = '';
                   $cel_sel['colspan']          = '';
                   $cel_sel['format_number']    = ''; //per xlsx
                   $row_sel[]                   = $cel_sel;
                 //kapim fshatrat qe mbullon kjo qender ---------------------------------------------------------------------------

                 IF ($exp_data == "Y")
				    {
                     //kapim userat me te cilat eshte e lidhur kjo qender -------------------------------------------------------------
                       unset($lov);
                       $lov["name"]         = "UserId";
                       $lov["obj_or_label"] = "label";
                       $lov["tab_name"]     = "users INNER JOIN phi_reporting_entity_users ON 
                                                 users.UserId = phi_reporting_entity_users.UserId
                                              ";
                       $lov["filter"]       = 'WHERE id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($id_sel).'"';
                       $lov["layout_forma"] = " | ";
                       $lov_reporting_entity_users = f_app_lov_default_values($lov);

                       unset($cel_sel);
                       $cel_sel['tag']              = 'td';
                       $cel_sel['tag_att']          = '';
                       $cel_sel['link']             = 'N'; //Y/N
                       $cel_sel['link_att']         = '';
                       $cel_sel['data_type']        = 'label'; //label/icon
                       $cel_sel['vl']               = SUBSTR($lov_reporting_entity_users, 3);
                       $cel_sel['vl_db']            = '';
                       $cel_sel['vlf']              = '';
                       $cel_sel['bold']             = '';
                       $cel_sel['align']            = '';
                       $cel_sel['style']            = '';
                       $cel_sel['colspan']          = '';
                       $cel_sel['format_number']    = ''; //per xlsx
                       $row_sel[]                   = $cel_sel;
                     //kapim id userave me te cilat eshte e lidhur kjo qender -------------------------------------------------------------
                    }

                 IF ($exp_data != "Y")
                    {
                     $post_id_sel = f_app_encrypt($id_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                    }

                 IF ($exp_data != "Y")
                    {
                     IF ($G_APP_VARS["nem_mode"] == "select_record")
                        {
                         //variabli per preview ---------------------------------------------------------
                           //$name_sel_js      = $name_sel.' ('.$lov_id_reporting_entity_kind.')';
                           $name_sel_js      = $name_sel;
                           $name_sel_js      = STR_REPLACE(array("'", "\""), "", $name_sel_js);
                           
                           $data_url_sel     = '';

                           $link_att         = 'href="javascript:parent.'.$G_APP_VARS["return_fun_name"].'(\''.$G_APP_VARS["return_elm_id"].'\', \''.$id_sel.'\', \''.$name_sel_js.'\', \''.$id_branch_sel.'\');parent.EW.modal.close();"';
                           $link_data_modal  = 'N';
                           $link_data_title  = WebApp::getVar("select_mesg");
                           $link_vl          = 'icon_select';
                         //variabli per preview ---------------------------------------------------------
                        }
                     ELSE
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
                           $data_url_sel     = $data_url_preview.'&vars_post='.$vars_post;
                           
                           $link_att         = 'href="javascript:void(0);"';
                           $link_data_modal  = 'Y';
                           $link_data_title  = $content_title." - ".WebApp::getVar("preview_mesg");
                           $link_vl          = 'icon_preview';
                         //variabli per preview ---------------------------------------------------------
                        }
                     
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';

                     $cel_sel['link']             = 'Y'; //Y/N
                     $cel_sel['link_att']         = $link_att;
                     $cel_sel['link_data_modal']  = $link_data_modal;
                     $cel_sel['link_data_title']  = $link_data_title;
                     $cel_sel['link_data_url']    = $data_url_sel;
                     $cel_sel['data_type']        = 'icon'; //label/icon
                     $cel_sel['vl']               = $link_vl;
                     
                     $cel_sel['vl_db']            = '';
                     $cel_sel['vlf']              = '';
                     $cel_sel['bold']             = '';
                     $cel_sel['align']            = '';
                     $cel_sel['style']            = '';
                     $cel_sel['colspan']          = '';
                     $cel_sel['format_number']    = ''; //per xlsx
                     $row_sel[]                   = $cel_sel;
                    }

                 IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != "") AND ($G_APP_VARS["nem_mode"] != "select_record"))
                    {
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';
                     $cel_sel['link']             = 'Y'; //Y/N
                     
                     IF (
                         (ISSET($user_dega_qendra["edit_branch_id"][$id_branch_sel]) AND ($user_dega_qendra["edit_branch_id"][$id_branch_sel] == $id_branch_sel))
                         OR
                         ($user_tool_users_management == "Y")
                        )
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
            //shtojme ne vars_page variabla shtese -----------------------------------------------------------------------
              $webbox_list_data = STR_REPLACE(APP_PATH, "", __FILE__);

              $vars_page_kol[]  = "webbox";
              $vars_page_val[]  = $webbox_list_data;

              $vars_page_kol[]  = "idstemp";
              $vars_page_val[]  = $session->Vars["idstemp"];

              $vars_page_exp    = f_app_vars_page_encrypt($vars_page_kol, $vars_page_val);
            //shtojme ne vars_page variabla shtese -----------------------------------------------------------------------

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