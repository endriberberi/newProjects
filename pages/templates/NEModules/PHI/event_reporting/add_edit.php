<?
WebApp::addVar("max_width", "800");

//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols  = 3;
  $col_size = 12/$nr_cols;

  $col_1_size = 6; 
  $col_2_size = 2; 
  $col_3_size = 4; 
  $col_4_size = 3; 

  $col_size_tab_1 = 6; 
  $col_size_tab_2 = 6; 

  
  $width_form = 12; //brenda kolones sa madhesi do zeme
  $width_lab  = 4;

  IF ($width_lab == 12)
     {
      $width_obj = 12; 
     }
  ELSE
     {
      $width_obj = 12 - $width_lab; 
     }
//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------

//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//ndryshojme funksionin e savit meqe kemi validime ekstra per kete forme -------------------------------------------------
  $but_regj_action = ' onclick="javascript:f_app_save_local(\''.$arg_webbox.'\', \'save\', \''.$arg_id_form.'\');"'; //ka te drejte te modifikoje
//------------------------------------------------------------------------------------------------------------------------

//LOV --------------------------------------------------------------------------------------------------------------------
  //place_id_municipality ---------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_municipality";
    IF ($val_rec['place_id_municipality'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['place_id_municipality'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "place_id_municipality";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_place_id_municipality = f_app_lov_default_values($lov);
  //person_address_id_municipality ---------------------------------------------------------------------------------------

  //place_id_village --------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_village";
    IF ($val_rec['place_id_village'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['place_id_village'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    IF ($val_rec['place_id_municipality'][0]['vl'] != "")
       {
        $lov["filter"] = "WHERE id_municipality = '".ValidateVarFun::f_real_escape_string($val_rec['place_id_municipality'][0]['vl'])."'";
       }
    ELSE
       {
        $lov["filter"] = "WHERE id_municipality = '-1'";
       }

    $lov["object_name"]   = "place_id_village";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_place_id_village = f_app_lov_default_values($lov);
  //id_village ----------------------------------------------------------------------------------------------------------

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
                                f_app_array_js["place_id_municipality"] = "'.$arr_js.'";
                              ';
       }
  //PER FILTRIMIN E FSHATRAVE --------------------------------------------------------------------------------------------
//LOV --------------------------------------------------------------------------------------------------------------------

//TITULLI E MBIVENDOSIM  -------------------------------------------------------------------------------------------------
  $titull_print  = WebApp::getVar("raporto_ngjarjen_mesg");
   
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

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //reporter_name ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{emri_juaj_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_reporter_name';
  $Grid_form['data'][$nr]['id']               = 'id_reporter_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'reporter_name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['reporter_name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_reporter_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '50';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{emri_juaj_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //reporter_name --------------------------------------------------------------------------------------------------------------

  //reporter_tel ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{reporter_telefon_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_reporter_tel';
  $Grid_form['data'][$nr]['id']               = 'id_reporter_tel_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'reporter_tel';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['reporter_tel'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_reporter_tel';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '50';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{reporter_telefon_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //reporter_tel ---------------------------------------------------------------------------------------------------------

  //description ----------------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{description_event_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_description';
  $Grid_form['data'][$nr]['id']               = 'id_description_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_textarea;
  $Grid_form['data'][$nr]['name']             = 'description';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['description'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_notes';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = ' rows="4" valid="1,0,0,0,0,0" etiketa="{{description_event_mesg}}" style="height:100px;"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //description ----------------------------------------------------------------------------------------------------------

  //place_id_municipality ---------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{vendi_event_mesg}} {{id_municipality_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_place_id_municipality';
  $Grid_form['data'][$nr]['id']               = 'id_place_id_municipality_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'place_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_place_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_place_id_municipality';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{vendi_event_mesg}} {{id_municipality_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="place_id_municipality" id_obj_child="id_place_id_village"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //place_id_municipality ----------------------------------------------------------------------------------------

  //place_id_village ---------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = '{{vendi_event_mesg}} {{id_village_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_place_id_village';
  $Grid_form['data'][$nr]['id']               = 'id_place_id_village_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'place_id_village';
  $Grid_form['data'][$nr]['value']            = $lov_place_id_village;
  $Grid_form['data'][$nr]['id']               = 'id_place_id_village';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{vendi_event_mesg}} {{id_village_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //place_id_village -----------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //buttons --------------------------------------------------------------------------------------------------------------
      $Grid_form['data'][$nr]['type']             = 'form_footer_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_start';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_start';
      $Grid_form['data'][$nr]['width']            = '12';
      $Grid_form['data'][$nr]['other_attributes'] = '';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'button';
      $Grid_form['data'][$nr]['button_type']      = 'submit';
      $Grid_form['data'][$nr]['name']             = 'but_regj';
      $Grid_form['data'][$nr]['value']            = '{{regjistro_ngjarjen_mesg}}';
      $Grid_form['data'][$nr]['id']               = 'id_but_regj';
      $Grid_form['data'][$nr]['other_attributes'] = $but_regj_action;
      $Grid_form['data'][$nr]['primary']          = 'primary';
      $Grid_form['data'][$nr]['action_type']      = 'save';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'form_footer_end';
  //buttons --------------------------------------------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_closed';

  //audit trail ----------------------------------------------------------------------------------------------------------
    //INCLUDE(dirname(__FILE__)."/../php_script/add_edit_04_audit_trail.php");
  //audit trail ----------------------------------------------------------------------------------------------------------

  //end ------------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_05_end.php");
  //end ------------------------------------------------------------------------------------------------------------------
//Grid_form --------------------------------------------------------------------------------------------------------------
?>