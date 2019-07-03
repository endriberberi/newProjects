<?
//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols  = 3;
  $col_size = 12/$nr_cols;
  
  $col_1_size = 4; 
  $col_2_size = 4; 
  $col_3_size = 4; 
  
  $width_form = 12; //brenda kolones sa madhesi do zeme
  $width_lab  = 12;
  IF ($width_lab == 12)
     {
      $width_obj = 12; 
     }
  ELSE
     {
      $width_obj = 12 - $width_lab; 
     }
//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------

IF (ISSET($post_id) AND ($post_id != ""))
   {
    unset($tab);
    $kushti_where        = 'WHERE id_doctor = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
    $tab['tab_name']     = 'phi_doctor';            //*emri i tabeles ku do behet select
    $tab['sql_where']    = $kushti_where;           //default = "", kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
    $tab['nr_rec_tot']   = 'F';                     //default = "F", (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
    $tab['kol_filter']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
    $tab['kol_select']   = '';                      //default = ""(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
    $tab['kol_order']    = '';                      //default = "", pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
    $tab['kol_asc_desc'] = '';                      //default = "ASC"; pranon vlerat ASC, DESC; meret parasysh kur $tab['kol_order'] != "";
    $tab['rec_limit']    = '';                      //default = "", pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
    $tab['obj_class']    = '';            		  //default = "txtbox"; emri i klases ne style, vlen kur $tab['is_form'] = "T";
    $tab['distinct']     = 'F';      				  //default = "F" -> pranon vlerat T,F (pra true ose false);
    $tab['is_form']      = 'F';                     //default = "T"; pranon vlerat T,F;
    $val_rec             = f_app_select_form_table($tab);
    
    $ins_record_user      = f_app_record_user ($val_rec['record_user_ins'][0]['vl']);
    $ins_record_timestamp = $val_rec['record_timestamp_ins'][0]['vlf_dt'];

    $upd_record_user      = f_app_record_user ($val_rec['record_user_upd'][0]['vl']);
    $upd_record_timestamp = $val_rec['record_timestamp_upd'][0]['vlf_dt'];
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
  INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//LOV --------------------------------------------------------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      $branch_ids_sel = $user_dega_qendra["edit_branch_ids"];
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

  //gender obj select ----------------------------------------------------------------------------------------------------
    /*
    unset($lov);
    $lov["name"]          = "gender";
    IF ($val_rec['gender'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['gender'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "gender";
    $lov["layout_object"] = "radio";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_gender           = f_app_lov_default_values($lov);
    */
  //gender obj select ----------------------------------------------------------------------------------------------------

  //GENDER OBJ RADIO MARIM VETEM ARRAY ME VLERAT -------------------------------------------------------------------------
    unset($lov);
    $lov["name"]           = "gender";
    IF ($val_rec['gender'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['gender'][0]['vl'];
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
        $lov_gender = f_app_lov_default_values($lov);
       }
    ELSE
       {
        $gender_arr = f_app_lov_default_values($lov);
       }
  //GENDER OBJ RADIO MARIM VETEM ARRAY ME VLERAT -------------------------------------------------------------------------

  //id_work_position ---------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_work_position";
    IF ($val_rec['id_work_position'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_work_position'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_work_position";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_id_work_position = f_app_lov_default_values($lov);
  //id_municipality ------------------------------------------------------------------------------------------------------

  //record_status --------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "record_status";
    IF ($val_rec['record_status'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['record_status'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "record_status";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["alert_etiketa"] = "Statusi";
    $lov["null_print"]    = "F";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_record_status    = f_app_lov_default_values($lov);
  //record_status --------------------------------------------------------------------------------------------------------
//LOV --------------------------------------------------------------------------------------------------------------------
   
//Grid_form -----------------------------------------------------------------------------------------------------------
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

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_1_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //first_name -----------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{first_name_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_first_name';
  $Grid_form['data'][$nr]['id']               = 'id_first_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'first_name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['first_name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_first_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{first_name_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //first_name -----------------------------------------------------------------------------------------------

  //father_name -----------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{father_name_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_father_name';
  $Grid_form['data'][$nr]['id']               = 'id_father_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'father_name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['father_name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_father_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{father_name_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //father_name -----------------------------------------------------------------------------------------------

  //last_name -----------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{last_name_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_last_name';
  $Grid_form['data'][$nr]['id']               = 'id_last_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'last_name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['last_name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_last_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{last_name_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //last_name -----------------------------------------------------------------------------------------------

  //birthday ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{birthday_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_birthday';
  $Grid_form['data'][$nr]['id']               = 'id_birthday_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'birthday';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['birthday'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_birthday';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,1,0,0" etiketa="{{birthday_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //birthday -------------------------------------------------------------------------------------------

  //gender ------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['for']              = 'gender';
  $Grid_form['data'][$nr]['id']               = 'gender_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12; //$width_lab;

  /*
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'gender';
  $Grid_form['data'][$nr]['value']            = $lov_gender;
  $Grid_form['data'][$nr]['id']               = 'id_gender';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{gender_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';
  */
  
  IF ($obj_type_radio == 'radio')
     {
      //edit mode
      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'radio_start';
      $Grid_form['data'][$nr]['width']            = 12; //$width_obj;
      $Grid_form['data'][$nr]['other_attributes'] = '';
      $Grid_form['data'][$nr]['inline']           = 'Y'; //Y/N

      IF (IS_ARRAY($gender_arr))
         {
          WHILE (LIST($key_sel, $val_sel) = EACH($gender_arr)) 
                {
                 $nr = $nr + 1;
                 $Grid_form['data'][$nr]['type']             = $obj_type_radio;
                 $Grid_form['data'][$nr]['name']             = 'gender';
                 $Grid_form['data'][$nr]['value']            = $key_sel;
                 $Grid_form['data'][$nr]['id']               = 'id_gender'.$nr;
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
      $Grid_form['data'][$nr]['name']             = 'gender';
      $Grid_form['data'][$nr]['value']            = $lov_gender;
      $Grid_form['data'][$nr]['id']               = 'id_gender';
      $Grid_form['data'][$nr]['placeholder']      = '';
      $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{gender_mesg}}"';
      $Grid_form['data'][$nr]['width']            = 9; //$width_obj;
      $Grid_form['data'][$nr]['filter']           = 'N';
     }
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //gender ------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_2_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';
  
  //id_branch ------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_branch_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_branch';
  $Grid_form['data'][$nr]['id']               = 'id_branch_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_branch';
  $Grid_form['data'][$nr]['value']            = $lov_id_branch;
  $Grid_form['data'][$nr]['id']               = 'id_id_branch';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_branch_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="id_branch" id_obj_child="id_id_reporting_entity"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_branch ------------------------------------------------------------------------------------------

  //id_reporting_entity --------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_reporting_entity_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_reporting_entity';
  $Grid_form['data'][$nr]['id']               = 'id_reporting_entity_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_reporting_entity';
  $Grid_form['data'][$nr]['value']            = $lov_id_reporting_entity;
  $Grid_form['data'][$nr]['id']               = 'id_id_reporting_entity';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_reporting_entity_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_reporting_entity ----------------------------------------------------------------------------------------

  //id_work_position ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_work_position_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_work_position';
  $Grid_form['data'][$nr]['id']               = 'id_work_position_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_work_position';
  $Grid_form['data'][$nr]['value']            = $lov_id_work_position;
  $Grid_form['data'][$nr]['id']               = 'id_id_work_position';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_work_position_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_work_position ---------------------------------------------------------------------------

  //doctor_no ------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{doctor_no_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_doctor_no';
  $Grid_form['data'][$nr]['id']               = 'id_doctor_no_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'doctor_no';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['doctor_no'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_doctor_no';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{doctor_no_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //doctor_no ------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';


  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_3_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //address ----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{address_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_address';
  $Grid_form['data'][$nr]['id']               = 'id_address_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'address';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['address'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_address';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{address_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //address ----------------------------------------------------------------------------------------

  //tel --------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{tel_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_tel';
  $Grid_form['data'][$nr]['id']               = 'id_tel_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'tel';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['tel'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_tel';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['icon']             = 'tel';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{tel_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //tel -----------------------------------------------------------------------------------------

  //mobile -----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{mobile_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_mobile';
  $Grid_form['data'][$nr]['id']               = 'id_mobile_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'mobile';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['mobile'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_mobile';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['icon']             = 'mobile';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{mobile_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //mobile -----------------------------------------------------------------------------------------

  //email -----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{email_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_email';
  $Grid_form['data'][$nr]['id']               = 'id_email_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'email';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['email'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_email';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['icon']             = 'email';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,1,0" etiketa="{{email_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //email -----------------------------------------------------------------------------------------

  //notes -----------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $width_form;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{notes_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_notes';
  $Grid_form['data'][$nr]['id']               = 'id_notes_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'notes';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['notes'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_notes';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = ' rows="3" valid="0,0,0,0,0,0" etiketa="{{notes_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //notes -----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  //statusi --------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_1_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = $yll.'{{statusi_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'record_status';
  $Grid_form['data'][$nr]['id']               = 'id_record_status_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'record_status';
  $Grid_form['data'][$nr]['value']            = $lov_record_status;
  $Grid_form['data'][$nr]['id']               = 'id_record_status';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{statusi_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //statusi ----------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //buttons --------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_03_buttons.php");
  //buttons --------------------------------------------------------------------------------------------------------------
  
  IF (($G_APP_VARS["nem_mode"] == "select_record") AND ($post_id != ""))
     {
      //shtojme butonin per zgjedhjen e ketij rekordi -------------------------------------------------------------------
      //$nr = $nr + 1;
      //$Grid_form['data'][$nr]['type']             = 'form_footer_start';
      //$Grid_form['data'][$nr]['other_attributes'] = '';

      $name_sel_js       = $val_rec['first_name'][0]['vl'].' '.$val_rec['father_name'][0]['vl'].' '.$val_rec['last_name'][0]['vl'];
      $name_sel_js       = STR_REPLACE(array("'", "\""), "", $name_sel_js);

      $but_select_action = ' onclick="javascript:parent.'.$G_APP_VARS["return_fun_name"].'(\''.$G_APP_VARS["return_elm_id"].'\', \''.$val_rec['id_doctor'][0]['vl'].'\', \''.$name_sel_js.'\', \'\');parent.EW.modal.close();"';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_start';
      $Grid_form['data'][$nr]['width']            = '12';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'button';
      $Grid_form['data'][$nr]['button_type']      = 'button';
      $Grid_form['data'][$nr]['name']             = 'but_select';
      $Grid_form['data'][$nr]['value']            = '{{select_mesg}}';
      $Grid_form['data'][$nr]['id']               = 'id_but_select';
      $Grid_form['data'][$nr]['other_attributes'] = $but_select_action;
      $Grid_form['data'][$nr]['primary']          = 'primary';
      $Grid_form['data'][$nr]['action_type']      = 'select';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';

      //$nr = $nr + 1;
      //$Grid_form['data'][$nr]['type']             = 'form_footer_end';
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_closed';

  //audit trail ----------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_04_audit_trail.php");
  //audit trail ----------------------------------------------------------------------------------------------------------

  //end ------------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_05_end.php");
  //end ------------------------------------------------------------------------------------------------------------------
//Grid_form --------------------------------------------------------------------------------------------------------------
?>