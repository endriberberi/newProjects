<?
//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols        = 2;
  $col_size       = 12/$nr_cols;
  $col_size_other = 12 - $col_size;
  
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

IF (ISSET($post_id) AND ($post_id != ""))
   {
    unset($tab);
    $kushti_where        = 'WHERE id_branch = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
    $tab['tab_name']     = 'phi_branch';            //*emri i tabeles ku do behet select
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
    
    $upd_record_user      = f_app_record_user ($val_rec['record_user'][0]['vl']);
    $upd_record_timestamp = $val_rec['record_timestamp'][0]['vlf_dt'];

    //kapim id userave view me te cilat eshte e lidhur kjo dege ----------------------------------------------------------
      unset($lov);
      $lov["name"]              = "ids_user_branch";
      $lov["obj_or_label"]      = "label";
      $lov["only_ids"]          = "Y";
      $lov["filter"]            = 'WHERE id_branch = "'.ValidateVarFun::f_real_escape_string($post_id).'" AND view_edit = "V"';
      $ids_user_branch_view_arr = f_app_lov_default_values($lov);
        
      IF (IS_ARRAY($ids_user_branch_view_arr))
         {
          $ids_user_branch_view = implode(",", $ids_user_branch_view_arr);
         }
      ELSE
         {
          $ids_user_branch_view = "";
         }
    //kapim id userave view me te cilat eshte e lidhur kjo dege ----------------------------------------------------------

    //kapim id userave edit me te cilat eshte e lidhur kjo dege ----------------------------------------------------------
      unset($lov);
      $lov["name"]               = "ids_user_branch";
      $lov["obj_or_label"]       = "label";
      $lov["only_ids"]           = "Y";
      $lov["filter"]             = 'WHERE id_branch = "'.ValidateVarFun::f_real_escape_string($post_id).'" AND view_edit = "E"';
      $ids_user_branch_edit_arr = f_app_lov_default_values($lov);
        
      IF (IS_ARRAY($ids_user_branch_edit_arr))
         {
          $ids_user_branch_edit = implode(",", $ids_user_branch_edit_arr);
         }
      ELSE
         {
          $ids_user_branch_edit = "";
         }
    //kapim id userave edit me te cilat eshte e lidhur kjo dege ----------------------------------------------------------
   }

//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//LOV --------------------------------------------------------------------------------------------------------------------
  //id_municipality ------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_municipality";
    IF ($val_rec['address_id_municipality'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['address_id_municipality'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "address_id_municipality";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_address_id_municipality = f_app_lov_default_values($lov);
  //id_municipality ------------------------------------------------------------------------------------------------------

  //id_village ----------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_village";
    IF ($val_rec['address_id_village'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['address_id_village'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }

    IF ($val_rec['address_id_municipality'][0]['vl'] != "")
       {
        $lov["filter"] = "WHERE id_municipality = '".ValidateVarFun::f_real_escape_string($val_rec['address_id_municipality'][0]['vl'])."'";
       }
    ELSE
       {
        $lov["filter"] = "WHERE id_municipality = '-1'";
       }

    $lov["object_name"]   = "address_id_village";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_address_id_village = f_app_lov_default_values($lov);
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
                                f_app_array_js["address_id_municipality"] = "'.$arr_js.'";
                              ';
       }
  //PER FILTRIMIN E FSHATRAVE --------------------------------------------------------------------------------------------

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

  //ids_user_branch_view ------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "UserId";
    IF ($ids_user_branch_view != "")
       {
        $lov["id_select"] = $ids_user_branch_view;
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "ids_user_branch_view";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["null_print"]    = "F";

    IF ($editim_konsultim == 'editim')
       {
        $lov["filter_plus"] = " AND Status_user = 'y'";
       }
  
    $lov_ids_user_branch_view = f_app_lov_default_values($lov);
  //ids_user_branch_view ------------------------------------------------------------------------------------------------

  //ids_user_branch_edit ------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "UserId";
    IF ($ids_user_branch_edit != "")
       {
        $lov["id_select"] = $ids_user_branch_edit;
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "ids_user_branch_edit";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["null_print"]    = "F";
  
    IF ($editim_konsultim == 'editim')
       {
        $lov["filter_plus"] = " AND Status_user = 'y'";
       }

    $lov_ids_user_branch_edit = f_app_lov_default_values($lov);
  //ids_user_branch_view ------------------------------------------------------------------------------------------------
//LOV --------------------------------------------------------------------------------------------------------------------
   
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
  $Grid_form['data'][$nr]['width']            = $col_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //name -----------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{emertimi_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_name';
  $Grid_form['data'][$nr]['id']               = 'id_name_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'name';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['name'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_name';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '100';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{emertimi_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //name -----------------------------------------------------------------------------------------------

  //address_id_municipality ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{address_id_municipality_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'address_id_municipality';
  $Grid_form['data'][$nr]['id']               = 'address_id_municipality_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'address_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_address_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_address_id_municipality';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{address_id_municipality_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="address_id_municipality" id_obj_child="id_address_id_village"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //address_id_municipality ----------------------------------------------------------------------------------------

  //address_id_village ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{address_id_village_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_address_id_village';
  $Grid_form['data'][$nr]['id']               = 'id_address_id_village_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'address_id_village';
  $Grid_form['data'][$nr]['value']            = $lov_address_id_village;
  $Grid_form['data'][$nr]['id']               = 'id_address_id_village';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{address_id_village_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //address_id_village ----------------------------------------------------------------------------------------
  
  //address ---------------------------------------------------------------------------------
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
  //address ---------------------------------------------------------------------------------
  
  //code -----------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = '12';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{code_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_code';
  $Grid_form['data'][$nr]['id']               = 'id_code_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '4';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'code';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['code'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_code';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '30';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{code_mesg}}"';
  $Grid_form['data'][$nr]['width']            = '8';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //code -----------------------------------------------------------------------------------------------

  //tel -----------------------------------------------------------------------------------------
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

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_size_other;
  $Grid_form['data'][$nr]['other_attributes'] = '';
  
  //lov_ids_user_branch_view ---------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = '12';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{perdoruesit_view_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'ids_user_branch_view';
  $Grid_form['data'][$nr]['id']               = 'id_ids_user_branch_view_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select_multiple;
  $Grid_form['data'][$nr]['name']             = 'ids_user_branch_view';
  $Grid_form['data'][$nr]['value']            = $lov_ids_user_branch_view;
  $Grid_form['data'][$nr]['id']               = 'id_ids_user_branch_view';
  $Grid_form['data'][$nr]['placeholder']      = '{{type_to_select_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{perdoruesit_view_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //lov_ids_user_branch_view ---------------------------------------------------------------------

  //lov_ids_user_branch_edit ---------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = '12';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{perdoruesit_edit_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'ids_user_branch_edit';
  $Grid_form['data'][$nr]['id']               = 'id_ids_user_branch_edit_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select_multiple;
  $Grid_form['data'][$nr]['name']             = 'ids_user_branch_edit';
  $Grid_form['data'][$nr]['value']            = $lov_ids_user_branch_edit;
  $Grid_form['data'][$nr]['id']               = 'id_ids_user_branch_edit';
  $Grid_form['data'][$nr]['placeholder']      = '{{type_to_select_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{perdoruesit_edit_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //lov_ids_user_branch_edit ---------------------------------------------------------------------

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
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_textarea;
  $Grid_form['data'][$nr]['name']             = 'notes';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['notes'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_notes';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = ' rows="3" valid="0,0,0,0,0,0" etiketa="{{notes_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

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
  $Grid_form['data'][$nr]['width']            = $col_size;
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