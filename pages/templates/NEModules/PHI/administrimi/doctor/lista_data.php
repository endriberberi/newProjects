<?
//kushti i kerkimit ------------------------------------------------------------------------------------------------------
  $kushti_kerkim  = '';

 //APLIKOJME LIDHJEN E USERIT ME DEGE/QENDER -----------------------------------------------------------------------------
   //$kushti_kerkim .= ' AND phi_doctor.id_branch IN ('.$user_dega_qendra["view_branch_ids"].') ';

   //IF ($user_dega_qendra["reporting_entity_ids"] != "")
   //   {
   //    $kushti_kerkim .= ' AND phi_doctor.id_reporting_entity IN ('.$user_dega_qendra["reporting_entity_ids"].') ';
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
      $kushti_kerkim .= ' AND phi_doctor.id_branch = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_branch']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_id_reporting_entity']) AND ($G_APP_VARS['s_id_reporting_entity'] != ''))
     {
      $kushti_kerkim .= ' AND phi_doctor.id_reporting_entity = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_reporting_entity']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_id_work_position']) AND ($G_APP_VARS['s_id_work_position'] != ''))
     {
      $kushti_kerkim .= ' AND phi_doctor.id_work_position = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_id_work_position']).'" ';
     }

  IF (ISSET($G_APP_VARS['s_first_name']) AND ($G_APP_VARS['s_first_name'] != ''))
     {
      $kushti_kerkim .= ' AND phi_doctor.first_name LIKE "%'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_first_name']).'%" ';
     }

  IF (ISSET($G_APP_VARS['s_last_name']) AND ($G_APP_VARS['s_last_name'] != ''))
     {
      $kushti_kerkim .= ' AND phi_doctor.last_name LIKE "%'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_last_name']).'%" ';
     }

  IF (ISSET($G_APP_VARS['s_doctor_no']) AND ($G_APP_VARS['s_doctor_no'] != ''))
     {
      $kushti_kerkim .= ' AND phi_doctor.doctor_no = "'.ValidateVarFun::f_real_escape_string($G_APP_VARS['s_doctor_no']).'" ';
     }
//kushti i kerkimit ------------------------------------------------------------------------------------------------------
    
//numri i rekordeve total ------------------------------------------------------------------------------------------------
  //IF ($kushti_kerkim != '')
  //   {
  //    $kushti_kerkim = ' WHERE '.SUBSTR($kushti_kerkim, 5);
  //   }
     
  $sql = 'SELECT count(1) as nr_rec_total
            FROM phi_doctor 
                     
                     INNER JOIN phi_branch ON
                           phi_doctor.id_branch = phi_branch.id_branch 

                     INNER JOIN phi_reporting_entity ON
                           phi_doctor.id_reporting_entity = phi_reporting_entity.id_reporting_entity 

                     INNER JOIN '.$tab_users_branch_or_reporting_entity.' ON
                           phi_doctor.'.$field_users_branch_or_reporting_entity.' = '.$tab_users_branch_or_reporting_entity.'.'.$field_users_branch_or_reporting_entity.' 

           WHERE '.$tab_users_branch_or_reporting_entity.'.UserId = "'.ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"]).'"
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

      $kolonat_fusha[] = "phi_doctor.id_doctor";                $kolonat_etiketa[] = WebApp::getVar("id_mesg");

      $kolonat_fusha[] = "phi_doctor.first_name";               $kolonat_etiketa[] = WebApp::getVar("first_name_mesg");
      $kolonat_fusha[] = "phi_doctor.father_name";              $kolonat_etiketa[] = WebApp::getVar("father_name_mesg");
      $kolonat_fusha[] = "phi_doctor.last_name";                $kolonat_etiketa[] = WebApp::getVar("last_name_mesg");

      $kolonat_fusha[] = "phi_branch.name";                     $kolonat_etiketa[] = WebApp::getVar("id_branch_sh_mesg");
      $kolonat_fusha[] = "phi_reporting_entity.name";           $kolonat_etiketa[] = WebApp::getVar("id_reporting_entity_sh_mesg");
      $kolonat_fusha[] = "phi_doctor.id_work_position";         $kolonat_etiketa[] = WebApp::getVar("id_work_position_mesg");
      
      IF ($exp_data == "Y")
         {
          $kolonat_fusha[] = "phi_doctor.gender";               $kolonat_etiketa[] = WebApp::getVar("gender_mesg");
          $kolonat_fusha[] = "phi_doctor.birthday";             $kolonat_etiketa[] = WebApp::getVar("birthday_mesg");

          $kolonat_fusha[] = "phi_doctor.doctor_no";            $kolonat_etiketa[] = WebApp::getVar("doctor_no_mesg");
          $kolonat_fusha[] = "phi_doctor.address";              $kolonat_etiketa[] = WebApp::getVar("address_mesg");
         }

      $kolonat_fusha[] = "phi_doctor.tel";                      $kolonat_etiketa[] = WebApp::getVar("tel_mesg");
      $kolonat_fusha[] = "phi_doctor.mobile";                   $kolonat_etiketa[] = WebApp::getVar("mobile_mesg");
      $kolonat_fusha[] = "phi_doctor.email";                    $kolonat_etiketa[] = WebApp::getVar("email_mesg");
      $kolonat_fusha[] = "phi_doctor.record_status";            $kolonat_etiketa[] = WebApp::getVar("statusi_mesg");

      IF ($exp_data == "Y")
         {
          $kolonat_fusha[] = "phi_doctor.notes";                $kolonat_etiketa[] = WebApp::getVar("notes_mesg");
         }

      IF ($exp_data != "Y")
         {
          $kolonat_fusha[] = "";          $kolonat_etiketa[] = " "; //ikona preview
         }

      IF (($exp_data != "Y") AND ISSET($nem_rights[$NEM_ID_SEL]["102"]) AND ($nem_rights[$NEM_ID_SEL]["102"] != "") AND ($G_APP_VARS["nem_mode"] != "select_record"))
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
      //record_status_data -----------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "record_status";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $record_status_arr     = f_app_lov_default_values($lov);
      //record_status_data -----------------------------------------------------------------------------------------------

      //id_work_position_data --------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_work_position";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $id_work_position_arr  = f_app_lov_default_values($lov);
      //id_work_position_data --------------------------------------------------------------------------------------------

      //exp_data ---------------------------------------------------------------------------------------------------------
        IF ($exp_data == "Y")
           {
            //gender_data ------------------------------------------------------------------------------------------------
              unset($lov);
              $lov["name"]           = "gender";
              $lov["obj_or_label"]   = "label";
              $lov["all_data_array"] = "Y";
              $gender_arr   = f_app_lov_default_values($lov);
            //gender_data ------------------------------------------------------------------------------------------------
           }
      //exp_data ---------------------------------------------------------------------------------------------------------

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
        $sql = 'SELECT phi_doctor.id_doctor                                                              as id_sel,

                       phi_doctor.id_branch                                                              as id_branch_sel,
                       phi_doctor.id_reporting_entity                                                    as id_reporting_entity_sel,

                       IF (phi_branch.name                     IS NULL, "", phi_branch.name)             as dega_name,
                       IF (phi_reporting_entity.name           IS NULL, "", phi_reporting_entity.name)   as qendra_name,

                       IF (phi_doctor.first_name               IS NULL, "", phi_doctor.first_name)       as first_name,
                       IF (phi_doctor.father_name              IS NULL, "", phi_doctor.father_name)      as father_name,
                       IF (phi_doctor.last_name                IS NULL, "", phi_doctor.last_name)        as last_name,
                       
                       IF (phi_doctor.gender                   IS NULL, "", phi_doctor.gender)                            as gender,
                       IF (phi_doctor.birthday                 IS NULL, "", DATE_FORMAT(phi_doctor.birthday, "%d.%m.%Y")) as birthday_f,
                       IF (phi_doctor.doctor_no                IS NULL, "", phi_doctor.doctor_no)        as doctor_no,
                       IF (phi_doctor.address                  IS NULL, "", phi_doctor.address)          as address,

                       IF (phi_doctor.tel                      IS NULL, "", phi_doctor.tel)              as tel_sel,
                       IF (phi_doctor.mobile                   IS NULL, "", phi_doctor.mobile)           as mobile_sel,
                       IF (phi_doctor.email                    IS NULL, "", phi_doctor.email)            as email_sel,

                       IF (phi_doctor.id_work_position         IS NULL, "", phi_doctor.id_work_position) as id_work_position_sel,
                       IF (phi_doctor.notes                    IS NULL, "", phi_doctor.notes)            as notes,

                       IF (phi_doctor.record_status            IS NULL, "", phi_doctor.record_status) as record_status

                  FROM phi_doctor 
                       
                           INNER JOIN phi_branch ON
                                 phi_doctor.id_branch = phi_branch.id_branch 

                           INNER JOIN phi_reporting_entity ON
                                 phi_doctor.id_reporting_entity = phi_reporting_entity.id_reporting_entity 

                           INNER JOIN '.$tab_users_branch_or_reporting_entity.' ON
                                 phi_doctor.'.$field_users_branch_or_reporting_entity.' = '.$tab_users_branch_or_reporting_entity.'.'.$field_users_branch_or_reporting_entity.' 

                     
                 WHERE '.$tab_users_branch_or_reporting_entity.'.UserId = "'.ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"]).'"
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
               
               $first_name                   = $rs_list->Field('first_name');
               $father_name                  = $rs_list->Field('father_name');
               $last_name                    = $rs_list->Field('last_name');

               $gender                       = $rs_list->Field('gender');
               $birthday                     = $rs_list->Field('birthday_f');
               $doctor_no                    = $rs_list->Field('doctor_no');
               $address                      = $rs_list->Field('address');

               $tel_sel                      = $rs_list->Field('tel_sel');
               $mobile_sel                   = $rs_list->Field('mobile_sel');
               $email_sel                    = $rs_list->Field('email_sel');
               
               $id_work_position_sel         = $rs_list->Field('id_work_position_sel');
               $notes                        = $rs_list->Field('notes');

               $record_status                = $rs_list->Field('record_status');

               //id_work_position ----------------------------------------------------------------------------------------
                 $lov_id_work_position = $id_work_position_arr[$id_work_position_sel];
               //id_work_position ----------------------------------------------------------------------------------------

               //exp_data ------------------------------------------------------------------------------------------------
                 IF ($exp_data == "Y")
                    {
                     //gender --------------------------------------------------------------------------------------------
                       $lov_gender = $gender_arr[$gender];
                     //gender --------------------------------------------------------------------------------------------
                    }
               //exp_data ------------------------------------------------------------------------------------------------
               
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
                 $cel_sel['vl']               = $first_name.'';
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
                 $cel_sel['vl']               = $father_name.'';
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
                 $cel_sel['vl']               = $last_name.'';
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
                 $cel_sel['vl']               = $lov_id_work_position.'';
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
                     $cel_sel['vl']               = $lov_gender.'';
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
                     $cel_sel['vl']               = $birthday.'';
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
                     $cel_sel['vl']               = $doctor_no.'';
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
                     $cel_sel['vl']               = $address.'';
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

                 IF ($exp_data == "Y")
				    {
                     unset($cel_sel);
                     $cel_sel['tag']              = 'td';
                     $cel_sel['tag_att']          = '';
                     $cel_sel['link']             = 'N'; //Y/N
                     $cel_sel['link_att']         = '';
                     $cel_sel['data_type']        = 'label'; //label/icon
                     $cel_sel['vl']               = $notes.'';
                     $cel_sel['vl_db']            = '';
                     $cel_sel['vlf']              = '';
                     $cel_sel['bold']             = '';
                     $cel_sel['align']            = '';
                     $cel_sel['style']            = '';
                     $cel_sel['colspan']          = '';
                     $cel_sel['format_number']    = ''; //per xlsx
                     $row_sel[]                   = $cel_sel;
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
                           $name_sel_js      = $first_name.' '.$father_name.' '.$last_name;
                           $name_sel_js      = STR_REPLACE(array("'", "\""), "", $name_sel_js);
                           
                           $data_url_sel     = '';

                           $link_att         = 'href="javascript:parent.'.$G_APP_VARS["return_fun_name"].'(\''.$G_APP_VARS["return_elm_id"].'\', \''.$id_sel.'\', \''.$name_sel_js.'\', \'\');parent.EW.modal.close();"';
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