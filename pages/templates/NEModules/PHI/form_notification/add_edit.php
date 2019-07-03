<?
WebApp::addVar("max_width", "1000");

//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols  = 2;
  $col_size = 12/$nr_cols;
  
  $width_form = 12; //brenda kolones sa madhesi do zeme
  $width_lab  = 6;
  IF ($width_lab == 12)
     {
      $width_obj = 12; 
     }
  ELSE
     {
      $width_obj = 12 - $width_lab; 
     }

  //per kolonen e dyte 
  $width_lab2  = 8;
  $width_obj2  = 4;

  $col_size_lab_1 = 4;
  $col_size_obj_1 = 8;

  $col_size_lab_2 = 4;
  $col_size_obj_2 = 8;
//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------

IF (ISSET($post_id) AND ($post_id != ""))
   {
    unset($tab);
    $kushti_where         = 'WHERE id_form_notification = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
    $tab['tab_name']      = 'phi_form_notification';            //*emri i tabeles ku do behet select
    $tab['sql_where']     = $kushti_where;           //default = "", kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
    $tab['nr_rec_tot']    = 'F';                     //default = "F", (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
    $tab['kol_filter']    = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
    $tab['kol_select']    = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
    $tab['kol_order']     = '';                      //default = "", pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
    $tab['kol_asc_desc']  = '';                      //default = "ASC"; pranon vlerat ASC, DESC; meret parasysh kur $tab['kol_order'] != "";
    $tab['rec_limit']     = '';                      //default = "", pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
    $tab['obj_class']     = '';            		  //default = "txtbox"; emri i klases ne style, vlen kur $tab['is_form'] = "T";
    $tab['distinct']      = 'F';      				  //default = "F" -> pranon vlerat T,F (pra true ose false);
    $tab['is_form']       = 'F';                     //default = "T"; pranon vlerat T,F;
    $val_rec              = f_app_select_form_table($tab);
    
    $ins_record_user      = f_app_record_user ($val_rec['record_user_ins'][0]['vl']);
    $ins_record_timestamp = $val_rec['record_timestamp_ins'][0]['vlf_dt'];

    $upd_record_user      = f_app_record_user ($val_rec['record_user_upd'][0]['vl']);
    $upd_record_timestamp = $val_rec['record_timestamp_upd'][0]['vlf_dt'];

    //kapim tabelen phi_form_notification_laboratory ---------------------------------------------------------------------
      unset($tab);
      $kushti_where        = 'WHERE id_form_notification = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
      $tab['tab_name']     = 'phi_form_notification_laboratory';            //*emri i tabeles ku do behet select
      $tab['sql_where']    = $kushti_where;           //default = "", kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
      $tab['nr_rec_tot']   = 'F';                     //default = "F", (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
      $tab['kol_filter']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
      $tab['kol_select']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
      $tab['kol_order']    = 'id_testi';              //default = "", pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
      $tab['kol_asc_desc'] = '';                      //default = "ASC"; pranon vlerat ASC, DESC; meret parasysh kur $tab['kol_order'] != "";
      $tab['rec_limit']    = '';                      //default = "", pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
      $tab['obj_class']    = '';            		  //default = "txtbox"; emri i klases ne style, vlen kur $tab['is_form'] = "T";
      $tab['distinct']     = 'F';      				  //default = "F" -> pranon vlerat T,F (pra true ose false);
      $tab['is_form']      = 'F';                     //default = "T"; pranon vlerat T,F;
      $val_rec_laboratory  = f_app_select_form_table($tab);
    //kapim tabelen phi_form_notification_laboratory ---------------------------------------------------------------------
   }
ELSE
   {
    //PO SHTOHET REKORD I RI ---------------------------------------------------------------------------------------------
   }
   
//NESE USERI ESHTE I LIDHUR ME NJE QENDER RAPORTIMI ----------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      IF (($user_dega_qendra["edit_branch_nr"] == "1") AND !ISSET($val_rec['id_branch'][0]['vl']))
         {
          $val_rec['id_branch'][0]['vl'] = $user_dega_qendra["edit_branch_ids"];
         }

      IF (($user_dega_qendra["reporting_entity_nr"] == "1") AND !ISSET($val_rec['id_reporting_entity'][0]['vl']))
         {
          $val_rec['id_reporting_entity'][0]['vl'] = $user_dega_qendra["reporting_entity_ids"];
         }
     }
//NESE USERI ESHTE I LIDHUR ME NJE QENDER RAPORTIMI ----------------------------------------------------------------------

//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//ndryshojme funksionin e savit meqe kemi validime ekstra per kete forme -------------------------------------------------
  $but_regj_action = STR_REPLACE("f_app_save", "f_app_save_local", $but_regj_action); 
//------------------------------------------------------------------------------------------------------------------------

//LOV --------------------------------------------------------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      $branch_ids_sel = $user_dega_qendra["edit_branch_ids"];

      //PER OBJEKTET QE SHTOHEN ME JS ------------------------------------------------------------------------------------
        //id_type_of_sample ----------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_type_of_sample";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";

        $lov_app_js            = f_app_lov_default_values($lov);
        $arr_js                = f_app_js_arr($lov_app_js);
        $script_js_in_page .= '
                                f_app_array_js["id_type_of_sample"] = "'.$arr_js.'";
                              ';
        //----------------------------------------------------------------------------------------------------------------

        //id_analysis ----------------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_analysis";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";

        $lov_app_js            = f_app_lov_default_values($lov);
        $arr_js                = f_app_js_arr($lov_app_js);
        $script_js_in_page .= '
                                f_app_array_js["id_analysis"] = "'.$arr_js.'";
                              ';
        //----------------------------------------------------------------------------------------------------------------

        //id_analysis_result ---------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_analysis_result";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $lov["order_by"]       = "id_analysis_result";

        $lov_app_js            = f_app_lov_default_values($lov);
        $arr_js                = f_app_js_arr($lov_app_js);
        $script_js_in_page .= '
                                f_app_array_js["id_analysis_result"] = "'.$arr_js.'";
                              ';
        //----------------------------------------------------------------------------------------------------------------

        //id_reporting_entity_laboratory ---------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_reporting_entity";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $lov["filter"]         = "WHERE id_reporting_entity_kind = 3 "; //laburatoret

        $lov_app_js            = f_app_lov_default_values($lov);
        $arr_js                = f_app_js_arr($lov_app_js);
        $script_js_in_page .= '
                                f_app_array_js["id_reporting_entity_laboratory"] = "'.$arr_js.'";
                              ';
        //----------------------------------------------------------------------------------------------------------------

        //id_disease_code ------------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_disease";
        $lov["obj_or_label"]   = "label";
        $lov["field_name"]     = "code";
        $lov["all_data_array"] = "Y";

        $lov_app_js            = f_app_lov_default_values($lov);
        $arr_js                = f_app_js_arr($lov_app_js);
        $script_js_in_page .= '
                               f_app_array_js["id_disease"] = "'.$arr_js.'";
                              ';
        //----------------------------------------------------------------------------------------------------------------
      //PER FILTRIMIN E QENDRAVE TE RAPORTIMIT ---------------------------------------------------------------------------
     }
  ELSE
     {
      $branch_ids_sel = $user_dega_qendra["view_branch_ids"];
     }

  //id_branch ------------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_branch";
    IF ($val_rec['id_branch'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_branch'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_branch";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["filter"]        = "WHERE id_branch IN (".$branch_ids_sel.") ";
    $lov_id_branch        = f_app_lov_default_values($lov);
  //id_branch ------------------------------------------------------------------------------------------------------------

  //id_reporting_entity --------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_reporting_entity";
    IF ($val_rec['id_reporting_entity'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_reporting_entity'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    $lov["filter"] = "WHERE id_branch IN (".$branch_ids_sel.") ";

    IF ($val_rec['id_branch'][0]['vl'] != "")
       {
        $lov["filter"] .= " AND id_branch = '".ValidateVarFun::f_real_escape_string($val_rec['id_branch'][0]['vl'])."'";

        IF ($user_dega_qendra["reporting_entity_ids"] != "")
           {
            $lov["filter"] .= " AND id_reporting_entity IN (".$user_dega_qendra["reporting_entity_ids"].")";
           }
       }
    ELSE
       {
        $lov["filter"] .= " AND id_branch = '-1'";
       }

    $lov["object_name"]      = "id_reporting_entity";
    $lov["valid"]            = "1,0,0,0,0,0";
    $lov["only_options"]     = "Y";
    $lov["obj_or_label"]     = $obj_or_label;
    $lov_id_reporting_entity = f_app_lov_default_values($lov);
  //id_reporting_entity --------------------------------------------------------------------------------------------------

  //PER FILTRIMIN E QENDRAVE TE RAPORTIMIT -------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]           = "id_branch_id_reporting_entity";
    $lov["obj_or_label"]   = "label";
    $lov["all_data_array"] = "Y";

    $lov["filter"]         = "WHERE id_branch IN (".$branch_ids_sel.") ";

    IF ($user_dega_qendra["reporting_entity_ids"] != "")
       {
        $lov["filter"] .= " AND id_reporting_entity IN (".$user_dega_qendra["reporting_entity_ids"].")";
       }

    $lov_app_js            = f_app_lov_default_values($lov);
    $arr_js                = f_app_js_arr($lov_app_js);
    $script_js_in_page .= '
                           f_app_array_js["id_branch"] = "'.$arr_js.'";
                          ';
  //PER FILTRIMIN E QENDRAVE TE RAPORTIMIT -------------------------------------------------------------------------------

  //id_disease ------------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_disease";
    IF ($val_rec['id_disease'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_disease'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_disease";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov_id_disease       = f_app_lov_default_values($lov);
  //id_disease -----------------------------------------------------------------------------------------------------------

  //GENDER OBJ RADIO MARIM VETEM ARRAY ME VLERAT -------------------------------------------------------------------------
    unset($lov);
    $lov["name"]           = "gender";
    IF ($val_rec['person_gender'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['person_gender'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["obj_or_label"]   = $obj_or_label;
    $lov["only_options"]   = "Y"; //kjo na duhet kur jemi ne editimin
    $lov["layout_object"]  = "radio";
    
    IF ($obj_or_label == 'label')
       {
        $lov_person_gender = f_app_lov_default_values($lov);
       }
    ELSE
       {
        $person_gender_arr = f_app_lov_default_values($lov);
       }
  //GENDER OBJ RADIO MARIM VETEM ARRAY ME VLERAT -------------------------------------------------------------------------

  //person_address_id_municipality ---------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_municipality";
    IF ($val_rec['person_address_id_municipality'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['person_address_id_municipality'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "person_address_id_municipality";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_person_address_id_municipality = f_app_lov_default_values($lov);
  //person_address_id_municipality ---------------------------------------------------------------------------------------

  //person_address_id_village --------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_village";
    IF ($val_rec['person_address_id_village'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['person_address_id_village'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    IF ($val_rec['person_address_id_municipality'][0]['vl'] != "")
       {
        $lov["filter"] = "WHERE id_municipality = '".ValidateVarFun::f_real_escape_string($val_rec['person_address_id_municipality'][0]['vl'])."'";
       }
    ELSE
       {
        $lov["filter"] = "WHERE id_municipality = '-1'";
       }

    $lov["object_name"]   = "person_address_id_village";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_person_address_id_village = f_app_lov_default_values($lov);
  //id_village ----------------------------------------------------------------------------------------------------------

  //person_institution_id_municipality ---------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_municipality";
    IF ($val_rec['person_institution_id_municipality'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['person_institution_id_municipality'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "person_institution_id_municipality";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_person_institution_id_municipality = f_app_lov_default_values($lov);
  //person_institution_id_municipality ---------------------------------------------------------------------------------------

  //person_institution_id_village --------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_village";
    IF ($val_rec['person_institution_id_village'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['person_institution_id_village'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    IF ($val_rec['person_institution_id_municipality'][0]['vl'] != "")
       {
        $lov["filter"] = "WHERE id_municipality = '".ValidateVarFun::f_real_escape_string($val_rec['person_institution_id_municipality'][0]['vl'])."'";
       }
    ELSE
       {
        $lov["filter"] = "WHERE id_municipality = '-1'";
       }

    $lov["object_name"]   = "person_institution_id_village";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_person_institution_id_village = f_app_lov_default_values($lov);
  //person_institution_id_village ----------------------------------------------------------------------------------------------------------

  //disease_place_id_municipality ---------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_municipality";
    IF ($val_rec['disease_place_id_municipality'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['disease_place_id_municipality'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "disease_place_id_municipality";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_disease_place_id_municipality = f_app_lov_default_values($lov);
  //disease_place_id_municipality ---------------------------------------------------------------------------------------

  //disease_place_id_village --------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_village";
    IF ($val_rec['disease_place_id_village'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['disease_place_id_village'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    IF ($val_rec['disease_place_id_municipality'][0]['vl'] != "")
       {
        $lov["filter"] = "WHERE id_municipality = '".ValidateVarFun::f_real_escape_string($val_rec['disease_place_id_municipality'][0]['vl'])."'";
       }
    ELSE
       {
        $lov["filter"] = "WHERE id_municipality = '-1'";
       }

    $lov["object_name"]   = "disease_place_id_village";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_disease_place_id_village = f_app_lov_default_values($lov);
  //disease_place_id_village ----------------------------------------------------------------------------------------------------------

  //PER FILTRIMIN E FSHATRAVE --------------------------------------------------------------------------------------------
    IF ($editim_konsultim == 'editim')
       {
        unset($lov);
        $lov["name"]                        = "id_municipality_id_village";
        $lov["obj_or_label"]                = "label";
        $lov["all_data_array"]              = "Y";
        $lov_id_municipality_id_village_arr = f_app_lov_default_values($lov);
        $arr_js                             = f_app_js_arr($lov_id_municipality_id_village_arr);
        $script_js_in_page .= '
                                f_app_array_js["person_address_id_municipality"] = "'.$arr_js.'";
                              ';
       }
  //PER FILTRIMIN E FSHATRAVE --------------------------------------------------------------------------------------------

  //id_case_classification -----------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_case_classification";
    IF ($val_rec['id_case_classification'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_case_classification'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_case_classification";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov_id_case_classification       = f_app_lov_default_values($lov);
  //id_case_classification -----------------------------------------------------------------------------------------------

  //hospitalization ------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "Y_N";
    IF ($val_rec['hospitalization'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['hospitalization'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "hospitalization";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["alert_etiketa"] = "hospitalization";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_hospitalization    = f_app_lov_default_values($lov);
  //hospitalization ------------------------------------------------------------------------------------------------------

  //dead -----------------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "Y_N";
    IF ($val_rec['dead'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['dead'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "dead";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["alert_etiketa"] = "dead";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_dead             = f_app_lov_default_values($lov);
  //dead -----------------------------------------------------------------------------------------------------------------

  //id_doctor ------------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_doctor";
    IF ($val_rec['id_doctor'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_doctor'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_doctor";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov_id_doctor        = f_app_lov_default_values($lov);
  //id_doctor ------------------------------------------------------------------------------------------------------------
//LOV --------------------------------------------------------------------------------------------------------------------

//TITULLI E MBIVENDOSIM  -------------------------------------------------------------------------------------------------
  $titull_print  = WebApp::getVar("form_notification_titull_mesg");
  //$titull_print .= "<br>".WebApp::getVar("form_notification_nentitull_mesg");
   
//Grid_form --------------------------------------------------------------------------------------------------------------
  $nr  = -1;
  $Grid_form = array('data' => array(), 'AllRecs' => '0');	

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_header';
  $Grid_form['data'][$nr]['label']            = $titull_print;
  $Grid_form['data'][$nr]['data-action']      = '';//collapse

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_open';
  $Grid_form['data'][$nr]['name']             = 'skeda';
  $Grid_form['data'][$nr]['id']               = 'id_skeda';
  $Grid_form['data'][$nr]['method']           = 'post';
  $Grid_form['data'][$nr]['onSubmit']         = 'return false;';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'hidden';
  $Grid_form['data'][$nr]['name']             = 'vars_page';
  $Grid_form['data'][$nr]['value']            = $vars_page;
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //row semundja --------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //col id_disease ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_disease_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_id_disease';
  $Grid_form['data'][$nr]['id']               = 'id_id_disease_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_disease';
  $Grid_form['data'][$nr]['value']            = $lov_id_disease;
  $Grid_form['data'][$nr]['id']               = 'id_id_disease';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_disease_mesg}}" onchange="f_app_set_value(this.id)" js_data_array="id_disease" id_obj_child="id_code"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col id_disease ------------------------------------------------------------------------------------------

  //col 2 ----------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 1;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{kodi_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_code';
  $Grid_form['data'][$nr]['id']               = 'id_code_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'code';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['code'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_code';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '4';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{code_mesg}}" disabled';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col 2 ----------------------------------------------------------------------------

  //date_of_completion ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 3;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{date_of_completion_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_date_of_completion';
  $Grid_form['data'][$nr]['id']               = 'id_date_of_completion_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'date_of_completion';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['date_of_completion'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_date_of_completion';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{date_of_completion_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //date_of_completion -------------------------------------------------------------------------------------------

  //col form_number ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 2;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{form_number_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_form_number';
  $Grid_form['data'][$nr]['id']               = 'id_form_number_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'form_number';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['form_number'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_form_number';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{form_number_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col form_number -----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row semundja ---------------------------------------------------------------------

  //label te_dhenat_socio_mesg --------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_start';
  //$Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'obj_preview';
  $Grid_form['data'][$nr]['value']            = '{{te_dhenat_socio_mesg}}';
  $Grid_form['data'][$nr]['for']              = '';
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = ' style="text-align: center;"';
  $Grid_form['data'][$nr]['width']            = '0';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //label te_dhenat_socio_mesg --------------------------------------------------------------------------------------------------------------

  //row 2 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  
  //person_first_name ----------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{person_first_name_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_first_name';
  $Grid_form['data'][$nr]['id']               = 'id_person_first_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'person_first_name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_first_name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_first_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '20';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{person_first_name_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_first_name ----------------------------------------------------------------------------------------------------

  //person_last_name ----------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{person_last_name_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_last_name';
  $Grid_form['data'][$nr]['id']               = 'id_person_last_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'person_last_name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_last_name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_last_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '20';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{person_last_name_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_last_name ----------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 2 ----------------------------------------------------------------------------------------------------------------
  
  //row 3 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //person_birthday ----------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{person_birthday_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_birthday';
  $Grid_form['data'][$nr]['id']               = 'id_person_birthday_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'person_birthday';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_birthday'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_birthday';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{person_birthday_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_birthday ----------------------------------------------------------------------------------------------------

  //person_personal_no ----------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{person_personal_no_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_personal_no';
  $Grid_form['data'][$nr]['id']               = 'id_person_personal_no_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'person_personal_no';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_personal_no'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_personal_no';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{person_personal_no_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_personal_no ----------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 3 ----------------------------------------------------------------------------------------------------------------

  //row 4 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //person_gender ----------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{gender_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_gender';
  $Grid_form['data'][$nr]['id']               = 'id_person_gender_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;
  
  IF ($obj_type_radio == 'radio')
     {
      //edit mode
      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'radio_start';
      $Grid_form['data'][$nr]['width']            = $col_size_obj_1;
      $Grid_form['data'][$nr]['other_attributes'] = '';
      $Grid_form['data'][$nr]['inline']           = 'Y'; //Y/N

      IF (IS_ARRAY($person_gender_arr))
         {
          WHILE (LIST($key_sel, $val_sel) = EACH($person_gender_arr)) 
                {
                 $nr = $nr + 1;
                 $Grid_form['data'][$nr]['type']             = $obj_type_radio;
                 $Grid_form['data'][$nr]['name']             = 'person_gender';
                 $Grid_form['data'][$nr]['value']            = $key_sel;
                 $Grid_form['data'][$nr]['id']               = 'id_person_gender'.$nr;
                 $Grid_form['data'][$nr]['label']            = $val_sel["name"];
                 $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{gender_mesg}}" '.$val_sel["checked"].'';
                }
         }

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'radio_end';
     }
  ELSE
     {
      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = $obj_type_radio;
      $Grid_form['data'][$nr]['name']             = 'person_gender';
      $Grid_form['data'][$nr]['value']            = $lov_person_gender;
      $Grid_form['data'][$nr]['id']               = 'id_person_gender';
      $Grid_form['data'][$nr]['placeholder']      = '';
      $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{gender_mesg}}"';
      $Grid_form['data'][$nr]['width']            = $col_size_obj_1;
      $Grid_form['data'][$nr]['filter']           = 'N';
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_gender ----------------------------------------------------------------------------------------------------

  //person_contact_tel ----------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{person_contact_tel_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_contact_tel';
  $Grid_form['data'][$nr]['id']               = 'id_person_contact_tel_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'person_contact_tel';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_contact_tel'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_contact_tel';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{person_contact_tel_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_contact_tel ----------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 4 ----------------------------------------------------------------------------------------------------------------

  //row 5 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //person_address_id_municipality ---------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_municipality_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'person_address_id_municipality';
  $Grid_form['data'][$nr]['id']               = 'person_address_id_municipality_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'person_address_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_person_address_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_person_address_id_municipality';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_municipality_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="person_address_id_municipality" id_obj_child="id_person_address_id_village"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_address_id_municipality ----------------------------------------------------------------------------------------

  //person_address_id_village ---------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_village_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_address_id_village';
  $Grid_form['data'][$nr]['id']               = 'id_person_address_id_village_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'person_address_id_village';
  $Grid_form['data'][$nr]['value']            = $lov_person_address_id_village;
  $Grid_form['data'][$nr]['id']               = 'id_person_address_id_village';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_village_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_address_id_village --------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 5 ----------------------------------------------------------------------------------------------------------------

  //row 6 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //person_address_street ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{address_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_address_street';
  $Grid_form['data'][$nr]['id']               = 'id_person_address_street_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'person_address_street';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_address_street'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_address_street';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{address_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 10;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_address_street ----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 6 ----------------------------------------------------------------------------------------------------------------

  //row 7 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //person_institution ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{person_institution_sh_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_institution';
  $Grid_form['data'][$nr]['id']               = 'id_person_institution_label';
  $Grid_form['data'][$nr]['other_attributes'] = 'title="{{person_institution_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'person_institution';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_institution'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_institution';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{person_institution_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 10;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_institution ----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 7 ----------------------------------------------------------------------------------------------------------------

  //row 8 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //person_institution_id_municipality ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{id_municipality_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_institution_id_municipality';
  $Grid_form['data'][$nr]['id']               = 'person_institution_id_municipality_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'person_institution_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_person_institution_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_person_institution_id_municipality';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_municipality_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="person_address_id_municipality" id_obj_child="id_person_institution_id_village"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_institution_id_municipality ----------------------------------------------------------------------------------------

  //person_institution_id_village ---------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{id_village_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_institution_id_village';
  $Grid_form['data'][$nr]['id']               = 'id_person_institution_id_village_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'person_institution_id_village';
  $Grid_form['data'][$nr]['value']            = $lov_person_institution_id_village;
  $Grid_form['data'][$nr]['id']               = 'id_person_institution_id_village';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_village_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_institution_id_village --------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 8 ----------------------------------------------------------------------------------------------------------------

  //row 9 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //person_institution_address ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{address_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_person_institution_address';
  $Grid_form['data'][$nr]['id']               = 'id_person_institution_address_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'person_institution_address';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['person_institution_address'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_person_institution_address';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{address_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 10;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //person_institution_address ----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 9 ----------------------------------------------------------------------------------------------------------------

  //label informacioni_klinik_mesg --------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_start';
  //$Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'obj_preview';
  $Grid_form['data'][$nr]['value']            = '{{informacioni_klinik_mesg}}';
  $Grid_form['data'][$nr]['for']              = '';
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //label informacioni_klinik_mesg --------------------------------------------------------------------------------------------------------------

  //Informacioni klinik
  //row 1 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //col id_case_classification ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 4;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_case_classification_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_case_classification';
  $Grid_form['data'][$nr]['id']               = 'id_case_classification_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_case_classification';
  $Grid_form['data'][$nr]['value']            = $lov_id_case_classification;
  $Grid_form['data'][$nr]['id']               = 'id_id_case_classification';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_case_classification_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col id_case_classification ------------------------------------------------------------------------------------------

  //date_clinical_signs ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 4;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{date_clinical_signs_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_date_clinical_signs';
  $Grid_form['data'][$nr]['id']               = 'id_date_clinical_signs_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'date_clinical_signs';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['date_clinical_signs'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_date_clinical_signs';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,1,0,0" etiketa="{{date_clinical_signs_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //date_clinical_signs -------------------------------------------------------------------------------------------

  //date_of_visit ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 4;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{date_of_visit_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_date_of_visit';
  $Grid_form['data'][$nr]['id']               = 'id_date_of_visit_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'date_of_visit';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['date_of_visit'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_date_of_visit';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,1,0,0" etiketa="{{date_of_visit_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //date_of_visit -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 1 ----------------------------------------------------------------------------------------------------------------

  //label informacioni_klinik_mesg ---------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_start';
  //$Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{vendi_ku_filluan_mesg}}';
  $Grid_form['data'][$nr]['for']              = '';
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //label informacioni_klinik_mesg ---------------------------------------------------------------------------------------

  //row 2 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //disease_place_id_municipality ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{id_municipality_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_disease_place_id_municipality';
  $Grid_form['data'][$nr]['id']               = 'id_disease_place_id_municipality_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'disease_place_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_disease_place_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_disease_place_id_municipality';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_municipality_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="person_address_id_municipality" id_obj_child="id_disease_place_id_village"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //disease_place_id_municipality ----------------------------------------------------------------------------------------

  //disease_place_id_village ---------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{id_village_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_disease_place_id_village';
  $Grid_form['data'][$nr]['id']               = 'id_disease_place_id_village_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'disease_place_id_village';
  $Grid_form['data'][$nr]['value']            = $lov_disease_place_id_village;
  $Grid_form['data'][$nr]['id']               = 'id_disease_place_id_village';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{id_village_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //disease_place_id_village --------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 2 ----------------------------------------------------------------------------------------------------------------

  //row 3 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //disease_place_address ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{address_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_disease_place_address';
  $Grid_form['data'][$nr]['id']               = 'id_disease_place_address_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'disease_place_address';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['disease_place_address'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_disease_place_address';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{address_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 10;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //disease_place_address ------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 3 ----------------------------------------------------------------------------------------------------------------

  //row 4 ----------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //col hospitalization -------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 3;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{hospitalization_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_hospitalization';
  $Grid_form['data'][$nr]['id']               = 'id_hospitalization_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'hospitalization';
  $Grid_form['data'][$nr]['value']            = $lov_hospitalization;
  $Grid_form['data'][$nr]['id']               = 'id_hospitalization';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{hospitalization_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col hospitalization ------------------------------------------------------------------------------------------

  //date_hospitalization ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 3;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{date_hospitalization_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_date_hospitalization';
  $Grid_form['data'][$nr]['id']               = 'id_date_hospitalization_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'date_hospitalization';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['date_hospitalization'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_date_hospitalization';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,1,0,0" etiketa="{{date_hospitalization_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //date_hospitalization -------------------------------------------------------------------------------------------

  //col dead -------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 3;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{dead_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_dead';
  $Grid_form['data'][$nr]['id']               = 'id_dead_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'dead';
  $Grid_form['data'][$nr]['value']            = $lov_dead;
  $Grid_form['data'][$nr]['id']               = 'id_dead';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{dead_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col dead ------------------------------------------------------------------------------------------

  //dead_date ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 3;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{dead_date_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_dead_date';
  $Grid_form['data'][$nr]['id']               = 'id_dead_date_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'dead_date';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['dead_date'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_dead_date';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,1,0,0" etiketa="{{dead_date_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //dead_date -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 4 ----------------------------------------------------------------------------------------------------------------

  //TAB LABORATORI -------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //tr koka --------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  IF ($editim_konsultim == 'editim')
     {
      $colspan_sel = "8";
     }
  ELSE
     {
      $colspan_sel = "7";
     }

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = 'colspan="'.$colspan_sel.'"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{informacioni_laboratorik_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  IF ($editim_konsultim == 'editim')
     {
      $Grid_form['data'][$nr]['link']              = 'Y';
      $Grid_form['data'][$nr]['link_att']          = 'href="javascript:f_add_lab();"';
      $Grid_form['data'][$nr]['link_data_modal']   = 'N';
      $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("add_lab_mesg");
      //$Grid_form['data'][$nr]['link_data_url']     = $url_shto;
      $Grid_form['data'][$nr]['has_icon']          = 'Y';
      $Grid_form['data'][$nr]['icon_type']         = 'icon_add';
      //$Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
      //$Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
      //$Grid_form['data'][$nr]['link_modal_width']  = '1300';
      //$Grid_form['data'][$nr]['link_modal_height'] = '1000';
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr koka ------------------------------------------------------------------------------------------------------------------

  //tr koka1 ------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  IF ($editim_konsultim == 'editim')
     {
      //th -------------------------------------------------------------------------------------------------------------------
      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'th_start';
      $Grid_form['data'][$nr]['other_attributes'] = ' style="width:30px;"';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'label';
      $Grid_form['data'][$nr]['value']            = ' ';
      $Grid_form['data'][$nr]['other_attributes'] = '';
      $Grid_form['data'][$nr]['width']            = '0';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'th_end';
      //th ---------------------------------------------------------------------------------------------------------------
     }

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = ' style="width:50px;"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{nr_regjistrit_sh_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = ' style="width:130px;"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{dt_mostres_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = ' style="width:120px;"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{lloji_mostres_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{testi_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = ' style="width:100px;"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{rezultati_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = ' style="width:120px;"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{data_e_rezultatit_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{laboratori_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  IF ($editim_konsultim == 'editim')
     {
      IF (!ISSET($post_id) OR ($post_id == ""))
         {
          //SHFAQIM IKONEN E HAPJESE SE NEMIT PER TE SHTUAR phi_reporting_entity NE RAST SE KA TE DREJTA 
          //107 = Reporting entity
          $url_shto_reporting_entity = f_app_url_nem (107, 101);

          $vars_post_kol    = null;
          $vars_post_val    = null;
          
          $vars_post_kol[]  = 'editim_konsultim';
          $vars_post_val[]  = 'editim';

          $vars_post_kol[]  = 'nem_mode';
          $vars_post_val[]  = 'select_record';

          $vars_post_kol[]  = 'nem_mode_filter';
          $vars_post_val[]  = 'laboratory';

          $vars_post_kol[]  = 'return_fun_name';
          $vars_post_val[]  = 'f_app_return_laboratory';

          $vars_post_kol[]  = 'return_elm_id';
          $vars_post_val[]  = 'id_lab_id_reporting_entity_laboratory1';

          $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
          $url_shto         = $url_shto_reporting_entity.'&vars_post='.$vars_post;
         
          $Grid_form['data'][$nr]['link']              = 'Y';
          $Grid_form['data'][$nr]['link_att']          = 'href="javascript:void(0);"';
          $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
          $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("shto_laborator_mesg");
          $Grid_form['data'][$nr]['link_data_url']     = $url_shto;
          $Grid_form['data'][$nr]['has_icon']          = 'Y';
          $Grid_form['data'][$nr]['icon_type']         = 'icon_edit';
          $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
          $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
          $Grid_form['data'][$nr]['link_modal_width']  = '1300';
          $Grid_form['data'][$nr]['link_modal_height'] = '1000';
         }
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr koka1 -------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_start';
  $Grid_form['data'][$nr]['other_attributes'] = 'id="id_tab_lab"';

  //tr trupi -------------------------------------------------------------------------------------------------------------
    $tr_nr = 0;
    FOR ($i=0; $i < $val_rec_laboratory['nr_rec']; $i++)
        {
         $tr_nr = $tr_nr + 1;

         $nr = $nr + 1;
         $Grid_form['data'][$nr]['type']             = 'tr_start';
         $Grid_form['data'][$nr]['other_attributes'] = 'id="id_tab_lab_tr_'.$tr_nr.'"';

         IF ($editim_konsultim == 'editim')
            {
             //td --------------------------------------------------------------------------------------------------------
               $nr = $nr + 1;
               $Grid_form['data'][$nr]['type']             = 'td_start';
               $Grid_form['data'][$nr]['other_attributes'] = '';

               $nr = $nr + 1;
               $Grid_form['data'][$nr]['type']             = 'label';
               $Grid_form['data'][$nr]['value']            = '<a href=\'javascript:removetr_id("id_tab_lab_tr_'.$tr_nr.'");\' title="{{fshi_mesg}}"><img src="{{APP_URL}}graphics/del.gif" border="0"></a>';
               $Grid_form['data'][$nr]['other_attributes'] = '';
               $Grid_form['data'][$nr]['width']            = '0';

               $nr = $nr + 1;
               $Grid_form['data'][$nr]['type']             = 'td_end';
             //td --------------------------------------------------------------------------------------------------------
            }
         
         //td ------------------------------------------------------------------------------------------------------------
           $kol_sel  = 'no_registry';
           $kol_name = 'lab_'.$kol_sel;

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_text;
           $Grid_form['data'][$nr]['name']             = $kol_name;
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec_laboratory[$kol_sel][$i]['vl']).'';
           $Grid_form['data'][$nr]['id']               = 'id_'.$kol_name.$tr_nr;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['maxlength']        = '30';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{nr_regjistrit_mesg}}" ';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $kol_sel  = 'date_of_sampling';
           $kol_name = 'lab_'.$kol_sel;

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_date;
           $Grid_form['data'][$nr]['name']             = $kol_name;
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec_laboratory[$kol_sel][$i]['vlf']).'';
           $Grid_form['data'][$nr]['id']               = 'id_'.$kol_name.$tr_nr;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['maxlength']        = '10';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{dt_mostres_mesg}}" ';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $kol_sel  = 'id_type_of_sample';
           $kol_name = 'lab_'.$kol_sel;

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           //id_type_of_sample -------------------------------------------------------------------------------------------
           unset($lov);
           $lov["name"] = "id_type_of_sample";
           IF ($val_rec_laboratory[$kol_sel][$i]['vl'] != "")
              {
               $lov["id_select"] = $val_rec_laboratory[$kol_sel][$i]['vl'];
              }
           ELSE
              {
               $lov["id_select"] = "-1";
              }
           $lov["object_name"]   = $kol_name;
           $lov["valid"]         = "1,0,0,0,0,0";
           $lov["only_options"]  = "Y";
           $lov["obj_or_label"]  = $obj_or_label;
           $lov_id_type_of_sample = f_app_lov_default_values($lov);
           //id_type_of_sample -------------------------------------------------------------------------------------------

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_select;
           $Grid_form['data'][$nr]['name']             = $kol_name;
           $Grid_form['data'][$nr]['value']            = $lov_id_type_of_sample;
           $Grid_form['data'][$nr]['id']               = 'id_'.$kol_name.$tr_nr;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="'.$val_rec_laboratory[$kol_sel]['label'].'"';
           $Grid_form['data'][$nr]['width']            = '0';
           //$Grid_form['data'][$nr]['filter']         = 'Y';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $kol_sel  = 'id_analysis';
           $kol_name = 'lab_'.$kol_sel;

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           //id_type_of_sample -------------------------------------------------------------------------------------------
           unset($lov);
           $lov["name"] = "id_analysis";
           IF ($val_rec_laboratory[$kol_sel][$i]['vl'] != "")
              {
               $lov["id_select"] = $val_rec_laboratory[$kol_sel][$i]['vl'];
              }
           ELSE
              {
               $lov["id_select"] = "-1";
              }
           $lov["object_name"]   = $kol_name;
           $lov["valid"]         = "1,0,0,0,0,0";
           $lov["only_options"]  = "Y";
           $lov["obj_or_label"]  = $obj_or_label;
           $lov_id_analysis      = f_app_lov_default_values($lov);
           //id_type_of_sample -------------------------------------------------------------------------------------------

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_select;
           $Grid_form['data'][$nr]['name']             = $kol_name;
           $Grid_form['data'][$nr]['value']            = $lov_id_analysis;
           $Grid_form['data'][$nr]['id']               = 'id_'.$kol_name.$tr_nr;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="'.$val_rec_laboratory[$kol_sel]['label'].'"';
           $Grid_form['data'][$nr]['width']            = '0';
           //$Grid_form['data'][$nr]['filter']         = 'Y';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $kol_sel  = 'id_analysis_result';
           $kol_name = 'lab_'.$kol_sel;

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           //id_type_of_sample -------------------------------------------------------------------------------------------
           unset($lov);
           $lov["name"] = "id_analysis_result";
           IF ($val_rec_laboratory[$kol_sel][$i]['vl'] != "")
              {
               $lov["id_select"] = $val_rec_laboratory[$kol_sel][$i]['vl'];
              }
           ELSE
              {
               $lov["id_select"] = "-1";
              }
           $lov["object_name"]   = $kol_name;
           $lov["valid"]         = "1,0,0,0,0,0";
           $lov["only_options"]  = "Y";
           $lov["obj_or_label"]  = $obj_or_label;
           $lov["order_by"]      = "id_analysis_result";
           $lov_id_analysis_result = f_app_lov_default_values($lov);
           //id_type_of_sample -------------------------------------------------------------------------------------------

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_select;
           $Grid_form['data'][$nr]['name']             = $kol_name;
           $Grid_form['data'][$nr]['value']            = $lov_id_analysis_result;
           $Grid_form['data'][$nr]['id']               = 'id_'.$kol_name.$tr_nr;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="'.$val_rec_laboratory[$kol_sel]['label'].'"';
           $Grid_form['data'][$nr]['width']            = '0';
           //$Grid_form['data'][$nr]['filter']         = 'Y';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $kol_sel  = 'date_of_result';
           $kol_name = 'lab_'.$kol_sel;

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_date; //duhet bere obj_type_date
           $Grid_form['data'][$nr]['name']             = $kol_name;
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec_laboratory[$kol_sel][$i]['vlf']).'';
           $Grid_form['data'][$nr]['id']               = 'id_'.$kol_name.$tr_nr;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['maxlength']        = '10';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{data_e_rezultatit_mesg}}" ';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $kol_sel  = 'id_reporting_entity_laboratory';
           $kol_name = 'lab_'.$kol_sel;

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           //id_reporting_entity_laboratory ------------------------------------------------------------------------------
           unset($lov);
           $lov["name"] = "id_reporting_entity";
           IF ($val_rec_laboratory[$kol_sel][$i]['vl'] != "")
              {
               $lov["id_select"] = $val_rec_laboratory[$kol_sel][$i]['vl'];
              }
           ELSE
              {
               $lov["id_select"] = "-1";
              }
           $lov["object_name"]   = $kol_name;
           $lov["valid"]         = "1,0,0,0,0,0";
           $lov["only_options"]  = "Y";
           $lov["obj_or_label"]  = $obj_or_label;
           $lov["filter"]        = "WHERE id_reporting_entity_kind = 3 "; //laburatoret
           $lov_id_reporting_entity_laboratory = f_app_lov_default_values($lov);
           //id_reporting_entity_laboratory ------------------------------------------------------------------------------

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = $obj_type_select;
           $Grid_form['data'][$nr]['name']             = $kol_name;
           $Grid_form['data'][$nr]['value']            = $lov_id_reporting_entity_laboratory;
           $Grid_form['data'][$nr]['id']               = 'id_'.$kol_name.$tr_nr;
           $Grid_form['data'][$nr]['placeholder']      = '';
           $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{laboratori_mesg}}"';
           $Grid_form['data'][$nr]['width']            = '0';
           $Grid_form['data'][$nr]['filter']           = 'Y';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         $nr = $nr + 1;
         $Grid_form['data'][$nr]['type']             = 'tr_end';
        }
  //tr trupi -------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //TAB CLOSE ------------------------------------------------------------------------------------------------------------

  //label informacioni_laboratorik_mesg ----------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_start';
  //$Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'obj_preview';
  $Grid_form['data'][$nr]['value']            = '<br>{{personeli_mjeksor_mesg}}';
  $Grid_form['data'][$nr]['for']              = '';
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  //$nr = $nr + 1;
  //$Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //label informacioni_laboratorik_mesg --------------------------------------------------------------------------------------------------------------

  //row 1 --------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //col id_branch ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_branch_sh_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_branch';
  $Grid_form['data'][$nr]['id']               = 'id_branch_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_branch';
  $Grid_form['data'][$nr]['value']            = $lov_id_branch;
  $Grid_form['data'][$nr]['id']               = 'id_id_branch';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_branch_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="id_branch" id_obj_child="id_id_reporting_entity"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col id_branch ------------------------------------------------------------------------------------------

  //id_doctor ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_doctor_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_doctor';
  $Grid_form['data'][$nr]['id']               = 'id_id_doctor_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;
  IF ($editim_konsultim == 'editim')
     {
      IF (!ISSET($post_id) OR ($post_id == ""))
         {
          //SHFAQIM IKONEN E HAPJESE SE NEMIT PER TE SHTUAR phi_reporting_entity NE RAST SE KA TE DREJTA 
          //110 = Doctor
          $url_shto_reporting_entity = f_app_url_nem (110, 101);

          $vars_post_kol    = null;
          $vars_post_val    = null;
          
          $vars_post_kol[]  = 'editim_konsultim';
          $vars_post_val[]  = 'editim';

          $vars_post_kol[]  = 'nem_mode';
          $vars_post_val[]  = 'select_record';

          $vars_post_kol[]  = 'return_fun_name';
          $vars_post_val[]  = 'f_app_return';

          $vars_post_kol[]  = 'return_elm_id';
          $vars_post_val[]  = 'id_id_doctor';

          $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
          $url_shto         = $url_shto_reporting_entity.'&vars_post='.$vars_post;
         
          $Grid_form['data'][$nr]['link']              = 'Y';
          $Grid_form['data'][$nr]['link_att']          = 'href="javascript:void(0);"';
          $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
          $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("shto_doctor_mesg");
          $Grid_form['data'][$nr]['link_data_url']     = $url_shto;
          $Grid_form['data'][$nr]['has_icon']          = 'Y';
          $Grid_form['data'][$nr]['icon_type']         = 'icon_edit';
          $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
          $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
          $Grid_form['data'][$nr]['link_modal_width']  = '1300';
          $Grid_form['data'][$nr]['link_modal_height'] = '1000';
         }
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_doctor';
  $Grid_form['data'][$nr]['value']            = $lov_id_doctor;
  $Grid_form['data'][$nr]['id']               = 'id_id_doctor';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_doctor_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //id_doctor -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 1 ---------------------------------------------------------------------------------------------------------

  //row 2 --------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //col id_branch ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_reporting_entity_sh_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_reporting_entity';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  IF ($editim_konsultim == 'editim')
     {
      IF (!ISSET($post_id) OR ($post_id == ""))
         {
          //SHFAQIM IKONEN E HAPJESE SE NEMIT PER TE SHTUAR phi_reporting_entity NE RAST SE KA TE DREJTA 
          //107 = Reporting entity
          $url_shto_reporting_entity = f_app_url_nem (107, 101);

          $vars_post_kol    = null;
          $vars_post_val    = null;
          
          $vars_post_kol[]  = 'editim_konsultim';
          $vars_post_val[]  = 'editim';

          $vars_post_kol[]  = 'nem_mode';
          $vars_post_val[]  = 'select_record';

          $vars_post_kol[]  = 'nem_mode_filter';
          $vars_post_val[]  = 'institucioni';

          $vars_post_kol[]  = 'return_fun_name';
          $vars_post_val[]  = 'f_app_return';

          $vars_post_kol[]  = 'return_elm_id';
          $vars_post_val[]  = 'id_id_reporting_entity';

          $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
          $url_shto         = $url_shto_reporting_entity.'&vars_post='.$vars_post;
         
          $Grid_form['data'][$nr]['link']              = 'Y';
          $Grid_form['data'][$nr]['link_att']          = 'href="javascript:void(0);"';
          $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
          $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("shto_reporting_entity_mesg");
          $Grid_form['data'][$nr]['link_data_url']     = $url_shto;
          $Grid_form['data'][$nr]['has_icon']          = 'Y';
          $Grid_form['data'][$nr]['icon_type']         = 'icon_edit';
          $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
          $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
          $Grid_form['data'][$nr]['link_modal_width']  = '1300';
          $Grid_form['data'][$nr]['link_modal_height'] = '1000';
         }
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_reporting_entity';
  $Grid_form['data'][$nr]['value']            = $lov_id_reporting_entity;
  $Grid_form['data'][$nr]['id']               = 'id_id_reporting_entity';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_reporting_entity_mesg}}" id_obj_parent="id_id_branch"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col id_branch ------------------------------------------------------------------------------------------

  //reporting_entity_register_number ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{reporting_entity_register_number_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_reporting_entity_register_number';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_register_number_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'reporting_entity_register_number';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['reporting_entity_register_number'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_register_number';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{reporting_entity_register_number_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //reporting_entity_register_number -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 2 ---------------------------------------------------------------------------------------------------------

  //row 3 ---------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //col reporting_entity_tel ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{tel_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_reporting_entity_tel';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_tel_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'reporting_entity_tel';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['reporting_entity_tel'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_tel';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '50';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{tel_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_1;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //col reporting_entity_tel ------------------------------------------------------------------------------------------------

  //reporting_entity_email ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{email_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_reporting_entity_email';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_email_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $col_size_lab_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'reporting_entity_email';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['reporting_entity_email'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_email';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '50';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{email_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $col_size_obj_2;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //reporting_entity_email -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //row 3 ---------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //buttons --------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_03_buttons.php");
  //buttons --------------------------------------------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_closed';

  //audit trail ----------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_04_audit_trail.php");
  //audit trail ----------------------------------------------------------------------------------------------------------

  //end ------------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_05_end.php");
  //end ------------------------------------------------------------------------------------------------------------------
//Grid_form --------------------------------------------------------------------------------------------------------------
?>