<?
WebApp::addVar("max_width", "1200");

//start ------------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_01_start.php");
//start ------------------------------------------------------------------------------------------------------------------

//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------
  $nr_cols  = 3;
  $col_size = 12/$nr_cols;

  $col_1_size = 3; 
  $col_2_size = 2; 
  $col_3_size = 4; 
  $col_4_size = 3; 

  $col_size_tab_1 = 6; 
  $col_size_tab_2 = 6; 

  
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

  //per kolonen e dyte
  $width_lab2  = 8;
  $width_obj2  = 4;
//overright size per etiketat dhe objektet e formes ----------------------------------------------------------------------

IF (ISSET($post_id) AND ($post_id != ""))
   {
    unset($tab);
    $kushti_where        = 'WHERE id_event = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
    $tab['tab_name']     = 'phi_event';            //*emri i tabeles ku do behet select
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
    
    //kapim tabelen phi_event_branch ------------------------------------------------------------------------------
      unset($tab);
      $kushti_where        = 'WHERE id_event = "'.ValidateVarFun::f_real_escape_string($post_id).'"';
      $tab['tab_name']     = 'phi_event_branch';            //*emri i tabeles ku do behet select
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
      $val_rec_event_branch = f_app_select_form_table($tab);
      
      FOR ($i=0; $i < $val_rec_event_branch['nr_rec']; $i++)
          {
           $ids_branch_arr[] = $val_rec_event_branch['id_branch'][$i]['vl'];
          }
      
      IF (IS_ARRAY($ids_branch_arr))
         {
          $ids_branch = implode(",", $ids_branch_arr);
         }
      ELSE
         {
          $ids_branch = "";
         }
    //kapim tabelen phi_event_branch ------------------------------------------------------------------------------
    
    //kapim tabelen phi_docs --------------------------------------------------------------------------------------
      $kushti_where_docs = "";
      IF ($editim_konsultim == 'konsultim')
         {
          $show_all_docs = 'N';
          
          //SHOHIM NESE USERI KA TE DREJTE TE EDITOJE NE NJE NGA QENDRAT E EVENTIT --------------------------------
            $id_branch_event_sel = $val_rec['id_branch'][0]['vl'];
            IF (ISSET($user_dega_qendra["view_branch_id"][$id_branch_event_sel]) AND ($user_dega_qendra["view_branch_id"][$id_branch_event_sel] == $id_branch_event_sel))
               {
                $show_all_docs = 'Y';
               }
            ELSE
               {
                IF (IS_ARRAY($ids_branch_arr))
                   {
                    FOR ($i=0; $i < COUNT($ids_branch_arr); $i++)
                        {
                         $id_branch_event_sel = $ids_branch_arr[$i];
                         IF (ISSET($user_dega_qendra["view_branch_id"][$id_branch_event_sel]) AND ($user_dega_qendra["view_branch_id"][$id_branch_event_sel] == $id_branch_event_sel))
                            {
                             $show_all_docs = 'Y';
                            }
                        }
                   }
               }
          //SHOHIM NESE USERI KA TE DREJTE TE EDITOJE NE NJE NGA QENDRAT E EVENTIT --------------------------------
          
          IF ($show_all_docs == 'N')
             {
              $kushti_where_docs = " AND share = 'Y' ";
             }
         }
         
      unset($tab);
      $kushti_where        = 'WHERE id_event = "'.ValidateVarFun::f_real_escape_string($post_id).'" AND record_status = 1'.$kushti_where_docs;
      $tab['tab_name']     = 'phi_docs';            //*emri i tabeles ku do behet select
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
      $val_rec_event_docs  = f_app_select_form_table($tab);
    //kapim tabelen phi_docs --------------------------------------------------------------------------------------

    //per lehtesi krijojme arrayt ---------------------------------------------------------------------------------
      //id_branch -----------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "id_branch";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $id_branch_arr         = f_app_lov_default_values($lov);
      //id_branch -----------------------------------------------------------------------------------------------

      //event_status --------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "event_status";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $event_status_arr      = f_app_lov_default_values($lov);
      //event_status --------------------------------------------------------------------------------------------

      //share ---------------------------------------------------------------------------------------------------
        unset($lov);
        $lov["name"]           = "share";
        $lov["obj_or_label"]   = "label";
        $lov["all_data_array"] = "Y";
        $share_arr      = f_app_lov_default_values($lov);
      //share ---------------------------------------------------------------------------------------------------
    //per lehtesi krijojme arrayt ---------------------------------------------------------------------------------
   }
ELSE
   {
    //PO SHTOHET REKORD I RI ---------------------------------------------------------------------------------------------
    $val_rec['channel_registration'][0]['vl'] = 3; //1 = sms; 2 = web; 3 = Personeli Institucionit
    $val_rec['event_status'][0]['vl']         = 2; //1 =I Ri; 2 = Ne proces; 3 = I perfunduar; 4 = Nuk eshte evenet

    $ids_branch = $user_dega_qendra["edit_branch_ids"];
   }
   
//DEGA REGJISTRUESE E EVENTIT --------------------------------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
     {
      IF (($user_dega_qendra["edit_branch_nr"] == "1") AND !ISSET($val_rec['id_branch'][0]['vl']))
         {
          $val_rec['id_branch'][0]['vl'] = $user_dega_qendra["edit_branch_ids"];
         }
     }
//DEGA REGJISTRUESE E EVENTIT --------------------------------------------------------------------------------------------

//MANIPULOJME editim_konsultim PER FORMEN --------------------------------------------------------------------------------
  $editim_konsultim_temp = $editim_konsultim;
  
  IF (ISSET($post_id) AND ($post_id != "") AND ($editim_konsultim == 'editim'))
     {
      $id_branch_event = $val_rec['id_branch'][0]['vl'];
      IF (ISSET($user_dega_qendra["edit_branch_id"][$id_branch_event]) AND ($user_dega_qendra["edit_branch_id"][$id_branch_event] == $id_branch_event))
         {
          //USERI KA TE DREJTE TE EDITOJE KETE DEGE TE EVENTIT
         }
      ELSE
         {
          //USERI NUK KA TE DREJTE TE EDITOJE KETE DEGE TE EVENTIT PRANDAJ E HAPIM NE KONSULTIM
          $editim_konsultim = "konsultim";
         }
     }
//------------------------------------------------------------------------------------------------------------------------

//rights -----------------------------------------------------------------------------------------------------------------
  INCLUDE(dirname(__FILE__)."/../php_script/add_edit_02_rights.php");
//rights -----------------------------------------------------------------------------------------------------------------

//ndryshojme funksionin e savit meqe kemi validime ekstra per kete forme -------------------------------------------------
  $but_regj_action = STR_REPLACE("f_app_save", "f_app_save_local", $but_regj_action); 
//------------------------------------------------------------------------------------------------------------------------

//LOV --------------------------------------------------------------------------------------------------------------------
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
    
    IF ($editim_konsultim == 'editim')
       {
        $lov["filter"]    = "WHERE id_branch IN (".$user_dega_qendra["edit_branch_ids"].") ";
       }
       
    $lov_id_branch        = f_app_lov_default_values($lov);
  //id_branch ------------------------------------------------------------------------------------------------------------

  //id_event_source ------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "id_event_source";
    IF ($val_rec['id_event_source'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['id_event_source'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "id_event_source";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_id_event_source = f_app_lov_default_values($lov);
  //id_event_source --------------------------------------------------------------------------------------------------

  //channel_registration --------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "channel_registration";
    IF ($val_rec['channel_registration'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['channel_registration'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "channel_registration";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_channel_registration = f_app_lov_default_values($lov);
  //channel_registration --------------------------------------------------------------------------------------------------

  //event_status --------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "event_status";
    IF ($val_rec['event_status'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['event_status'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    
    IF ($editim_konsultim == 'editim')
       {
        IF (ISSET($post_id) AND ($post_id != ""))
           {
	        $lov["id"]         = "2,3";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	        $lov["field_name"] = WebApp::getVar("event_status_2_mesg").",".WebApp::getVar("event_status_3_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;
           }
       }
    
    $lov["object_name"]   = "event_status";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_event_status     = f_app_lov_default_values($lov);
  //id_event_source --------------------------------------------------------------------------------------------------

  //share --------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"] = "share";
    IF ($val_rec['share'][0]['vl'] != "")
       {
        $lov["id_select"] = $val_rec['share'][0]['vl'];
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "share";
    $lov["valid"]         = "1,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
  
    $lov_share = f_app_lov_default_values($lov);
  //share --------------------------------------------------------------------------------------------------

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

  //ids_branch -----------------------------------------------------------------------------------------------------------
    unset($lov);
    $lov["name"]          = "id_branch";
    IF ($ids_branch != "")
       {
        $lov["id_select"] = $ids_branch;
       }
    ELSE
       {
        $lov["id_select"] = "-1";
       }
    $lov["object_name"]   = "ids_branch";
    $lov["valid"]         = "0,0,0,0,0,0";
    $lov["only_options"]  = "Y";
    $lov["obj_or_label"]  = $obj_or_label;
    $lov["null_print"]    = "F";

    IF ($obj_or_label == 'label')
       {
        $lov["layout_forma"] = " | ";
       }
    ELSE
       {
        $lov["filter"]    = "WHERE id_branch IN (".$user_dega_qendra["view_branch_ids"].") ";
       }
  
    $lov_ids_branch       = f_app_lov_default_values($lov);

    IF ($obj_or_label == 'label')
       {
        $lov_ids_branch = SUBSTR($lov_ids_branch, 3);
       }
  //ids_branch -----------------------------------------------------------------------------------------------------------

//LOV --------------------------------------------------------------------------------------------------------------------

//TITULLI E MBIVENDOSIM  -------------------------------------------------------------------------------------------------
  //$titull_print  = WebApp::getVar("form_alert_titull_mesg");
   
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
  $Grid_form['data'][$nr]['width']            = $col_1_size;
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_branch_sh_mesg}}:';
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
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_branch_mesg}}"';
  $Grid_form['data'][$nr]['filter']           = 'Y';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_branch ------------------------------------------------------------------------------------------

  //date_receipt_from_reporting_entity ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{date_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_date';
  $Grid_form['data'][$nr]['id']               = 'id_date_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_date;
  $Grid_form['data'][$nr]['name']             = 'date';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['date'][0]['vlf']).'';
  $Grid_form['data'][$nr]['id']               = 'id_date';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '10';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,1,0,0" etiketa="{{date_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //date_receipt_from_reporting_entity -------------------------------------------------------------------------------------------

  //share ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{share_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_share';
  $Grid_form['data'][$nr]['id']               = 'id_share_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'share';
  $Grid_form['data'][$nr]['value']            = $lov_share;
  $Grid_form['data'][$nr]['id']               = 'id_share';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{share_mesg}}"';
  $Grid_form['data'][$nr]['filter']           = 'N';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //share ----------------------------------------------------------------------------------

  //event_status ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{event_status_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_event_status';
  $Grid_form['data'][$nr]['id']               = 'id_event_status_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'event_status';
  $Grid_form['data'][$nr]['value']            = $lov_event_status;
  $Grid_form['data'][$nr]['id']               = 'id_event_status';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{event_status_mesg}}"';
  $Grid_form['data'][$nr]['filter']           = 'N';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //event_status ---------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_2_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //channel_registration ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{channel_registration_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_channel_registration';
  $Grid_form['data'][$nr]['id']               = 'id_channel_registration_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'channel_registration';
  $Grid_form['data'][$nr]['value']            = $lov_channel_registration;
  $Grid_form['data'][$nr]['id']               = 'id_channel_registration';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{channel_registration_mesg}}" disabled';
  $Grid_form['data'][$nr]['filter']           = 'N';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //channel_registration ---------------------------------------------------------------------------

  //id_event_source ---------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{id_event_source_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_event_source';
  $Grid_form['data'][$nr]['id']               = 'id_event_source_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'id_event_source';
  $Grid_form['data'][$nr]['value']            = $lov_id_event_source;
  $Grid_form['data'][$nr]['id']               = 'id_id_event_source';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{id_event_source_mesg}}"';
  $Grid_form['data'][$nr]['filter']           = 'N';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //id_event_source ---------------------------------------------------------------------------

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
  $Grid_form['data'][$nr]['value']            = '{{reporter_name_mesg}}:';
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
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{reporter_name_mesg}}"';
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
  $Grid_form['data'][$nr]['value']            = '{{reporter_tel_mesg}}:';
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
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{reporter_tel_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //reporter_tel --------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //mbyllet col 1 ----------------------------------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_3_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //subject ----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{subject_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_subject';
  $Grid_form['data'][$nr]['id']               = 'id_subject_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = $width_lab;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_text;
  $Grid_form['data'][$nr]['name']             = 'subject';
  $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec['subject'][0]['vl']).'';
  $Grid_form['data'][$nr]['id']               = 'id_subject';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['maxlength']        = '250';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{subject_mesg}}"';
  $Grid_form['data'][$nr]['width']            = $width_obj;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //subject --------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //place_id_municipality ---------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
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
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'place_id_municipality';
  $Grid_form['data'][$nr]['value']            = $lov_place_id_municipality;
  $Grid_form['data'][$nr]['id']               = 'id_place_id_municipality';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{vendi_event_mesg}} {{id_municipality_mesg}}" onchange="f_app_filter_listbox(this.id)" js_data_array="place_id_municipality" id_obj_child="id_place_id_village"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //place_id_municipality ----------------------------------------------------------------------------------------

  //place_id_village ---------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = 6;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{vendi_mesg}} {{id_village_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_place_id_village';
  $Grid_form['data'][$nr]['id']               = 'id_place_id_village_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select;
  $Grid_form['data'][$nr]['name']             = 'place_id_village';
  $Grid_form['data'][$nr]['value']            = $lov_place_id_village;
  $Grid_form['data'][$nr]['id']               = 'id_place_id_village';
  $Grid_form['data'][$nr]['placeholder']      = '';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="0,0,0,0,0,0" etiketa="{{vendi_event_mesg}} {{id_village_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //place_id_village -----------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

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
  $Grid_form['data'][$nr]['value']            = '{{description_event_mesg}}:';
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
  $Grid_form['data'][$nr]['other_attributes'] = ' rows="4" valid="0,0,0,0,0,0" etiketa="{{description_event_mesg}}" style="height:100px;"';
  $Grid_form['data'][$nr]['width']            = $width_obj;
  $Grid_form['data'][$nr]['filter']           = 'N';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //description ----------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_4_size;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //lov_ids_branch ---------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = $yll.'{{dega_ndjekese_mesg}}:';
  $Grid_form['data'][$nr]['for']              = 'id_ids_branch';
  $Grid_form['data'][$nr]['id']               = 'id_ids_branch_label';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = $obj_type_select_multiple;
  $Grid_form['data'][$nr]['name']             = 'ids_branch';
  $Grid_form['data'][$nr]['value']            = $lov_ids_branch;
  $Grid_form['data'][$nr]['id']               = 'id_ids_branch';
  $Grid_form['data'][$nr]['placeholder']      = '{{type_to_select_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = 'valid="1,0,0,0,0,0" etiketa="{{dega_ndjekese_mesg}}"';
  $Grid_form['data'][$nr]['width']            = 12;

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //lov_ids_branch ---------------------------------------------------------------------

  //notes -----------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['other_attributes'] = ' rows="2" valid="0,0,0,0,0,0" etiketa="{{notes_mesg}}" style="height:100px;"';
  $Grid_form['data'][$nr]['width']            = 12;
  $Grid_form['data'][$nr]['filter']           = 'Y';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
  //notes ----------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //mbyllet col 2 --------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_body_end';

  //buttons --------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_03_buttons.php");
  //buttons --------------------------------------------------------------------------------------------------------------

  IF ($editim_konsultim_temp != $editim_konsultim)
     {
      //buttons vetem back -----------------------------------------------------------------------------------------------
        INCLUDE(dirname(__FILE__)."/../php_script/add_edit_03_buttons_back.php");
      //buttons vetem back -----------------------------------------------------------------------------------------------
     }
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'form_closed';

//RIVENDOSIM VLEREN ------------------------------------------------------------------------------------------------------  
  $editim_konsultim = $editim_konsultim_temp;
//RIVENDOSIM VLEREN ------------------------------------------------------------------------------------------------------  


//ROW INFO + DOC ---------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //INFO DEGA ------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_size_tab_1;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //PHI_EVENT_BRANCH INFORMACION RRETH NGJARJES --------------------------------------------------------------------------
  IF ($ids_branch != "")
  {
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //tr koka1 -------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = 'colspan="4"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{event_dega_info_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr koka1 ------------------------------------------------------------------------------------------------------------------

  //tr koka2 ------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{id_branch_sh_mesg}}';
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
  $Grid_form['data'][$nr]['value']            = '{{statusi_mesg}}';
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
  $Grid_form['data'][$nr]['value']            = '{{description_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------

  IF ($editim_konsultim == 'editim')
     {
      //th ---------------------------------------------------------------------------------------------------------------
        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'th_start';
        $Grid_form['data'][$nr]['other_attributes'] = '';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'label';
        $Grid_form['data'][$nr]['value']            = '';
        $Grid_form['data'][$nr]['other_attributes'] = '';
        $Grid_form['data'][$nr]['width']            = '0';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'th_end';
      //th ---------------------------------------------------------------------------------------------------------------
     }
     
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr koka2 -------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  
  //TRUPI TABELE ---------------------------------------------------------------------------------------------------------
  //print_r($val_rec_event_branch);
  FOR ($i=0; $i < $val_rec_event_branch['nr_rec']; $i++)
      {
       $Grid_form['data'][$nr]['type']             = 'tr_start';
       $Grid_form['data'][$nr]['other_attributes'] = '';

         //td ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($id_branch_arr[$val_rec_event_branch["id_branch"][$i]['vl']]).'';
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($event_status_arr[$val_rec_event_branch["event_status"][$i]['vl']]).'';
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec_event_branch["description"][$i]['vl']).'';
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         IF ($editim_konsultim == 'editim')
            {
             $nr = $nr + 1;
             $Grid_form['data'][$nr]['type']             = 'td_start';
             $Grid_form['data'][$nr]['other_attributes'] = '';

             $id_branch_info = $val_rec_event_branch["id_branch"][$i]['vl'];
             
             IF (ISSET($user_dega_qendra["edit_branch_id"][$id_branch_info]) AND ($user_dega_qendra["edit_branch_id"][$id_branch_info] == $id_branch_info))
                {
                 //hapim po kete nem per te edituar informacionin e deges --------------------------
                 $id_post_sel = $post_id.','.$id_branch_info;
                 $post_id_sel = f_app_encrypt($id_post_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                 
                 $vars_post_kol    = null;
                 $vars_post_val    = null;
                 
                 $vars_post_kol[]  = 'gjendje';
                 $vars_post_val[]  = 'record_detaje_info';

                 $vars_post_kol[]  = 'editim_konsultim';
                 $vars_post_val[]  = 'editim';

                 $vars_post_kol[]  = 'post_id';
                 $vars_post_val[]  = $post_id_sel;

                 $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
                 $data_url_sel     = $data_url_this_nem.'&vars_post='.$vars_post;
                 //variabli per preview ------------------------------------------------------------

                 $nr = $nr + 1;
                 $Grid_form['data'][$nr]['type']             = 'label';
                 $Grid_form['data'][$nr]['value']            = '';
                 $Grid_form['data'][$nr]['other_attributes'] = '';
                 $Grid_form['data'][$nr]['width']            = '0';


                 $Grid_form['data'][$nr]['link']              = 'Y';
                 $Grid_form['data'][$nr]['link_att']          = '';
                 $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
                 $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("event_dega_info_mesg")." - ".WebApp::getVar("perditesim_mesg");
                 $Grid_form['data'][$nr]['link_data_url']     = $data_url_sel;
                 $Grid_form['data'][$nr]['has_icon']          = 'Y';
                 $Grid_form['data'][$nr]['icon_type']         = 'icon_edit';
                 $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
                 $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
                 $Grid_form['data'][$nr]['link_modal_width']  = '800';
                 $Grid_form['data'][$nr]['link_modal_height'] = '850';
                }

             $nr = $nr + 1;
             $Grid_form['data'][$nr]['type']         = 'td_end';
            }

       $nr = $nr + 1;
       $Grid_form['data'][$nr]['type']             = 'tr_end';
      }
  //TRUPI TABELE ---------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_end';
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_end';
  }
  //PHI_EVENT_BRANCH INFORMACION RRETH NGJARJES --------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //INFO DEGA ------------------------------------------------------------------------------------------------------------

  //DOC DEGA -------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_start';
  $Grid_form['data'][$nr]['width']            = $col_size_tab_2;
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //PHI_EVENT_BRANCH_DOC INFORMACION RRETH NGJARJES ----------------------------------------------------------------------
  IF ($ids_branch != "")
  {
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //tr koka1 -------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = 'colspan="6"';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{event_dega_docs_mesg}}';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '0';

  //label event_dega_docs_mesg -------------------------------------------------------------------------------------------
    IF ($editim_konsultim == 'editim')
       {
        //hapim po kete nem per te uploduar doc --------------------------
        $id_post_sel      = $post_id.',0,0'; //id_event,id_branch,id_doc
        $post_id_sel      = f_app_encrypt($id_post_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
        
        $vars_post_kol    = null;
        $vars_post_val    = null;
        
        $vars_post_kol[]  = 'gjendje';
        $vars_post_val[]  = 'record_detaje_doc';

        $vars_post_kol[]  = 'editim_konsultim';
        $vars_post_val[]  = 'editim';

        $vars_post_kol[]  = 'post_id';
        $vars_post_val[]  = $post_id_sel;

        $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
        $data_url_sel     = $data_url_this_nem.'&vars_post='.$vars_post;
        //hapim po kete nem per te uploduar doc --------------------------
        
        //$nr = $nr + 1;
        //$Grid_form['data'][$nr]['type']             = 'label';
        //$Grid_form['data'][$nr]['value']            = '';
        //$Grid_form['data'][$nr]['for']              = '';
        //$Grid_form['data'][$nr]['id']               = '';
        //$Grid_form['data'][$nr]['other_attributes'] = '';
        //$Grid_form['data'][$nr]['width']            = 0;
        
        $Grid_form['data'][$nr]['link']              = 'Y';
        $Grid_form['data'][$nr]['link_att']          = 'href="javascript:void(0);"';
        $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
        $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("event_dega_docs_mesg")." - ".WebApp::getVar("regjistrim_i_ri_mesg");
        $Grid_form['data'][$nr]['link_data_url']     = $data_url_sel;
        $Grid_form['data'][$nr]['has_icon']          = 'Y';
        $Grid_form['data'][$nr]['icon_type']         = 'icon_add';
        $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
        $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
        $Grid_form['data'][$nr]['link_modal_width']  = '800';
        $Grid_form['data'][$nr]['link_modal_height'] = '850';
       }
  //label event_dega_docs_mesg -------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr koka1 ------------------------------------------------------------------------------------------------------------------

  //tr koka2 ------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  //th -------------------------------------------------------------------------------------------------------------------
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'th_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'label';
  $Grid_form['data'][$nr]['value']            = '{{id_branch_sh_mesg}}';
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
  $Grid_form['data'][$nr]['value']            = '{{share_mesg}}';
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
  $Grid_form['data'][$nr]['value']            = '{{description_mesg}}';
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
    $Grid_form['data'][$nr]['value']            = '';
    $Grid_form['data'][$nr]['other_attributes'] = '';
    $Grid_form['data'][$nr]['width']            = '0';

    $nr = $nr + 1;
    $Grid_form['data'][$nr]['type']             = 'th_end';
  //th -------------------------------------------------------------------------------------------------------------------
     
  //th preview -----------------------------------------------------------------------------------------------------------
    $nr = $nr + 1;
    $Grid_form['data'][$nr]['type']             = 'th_start';
    $Grid_form['data'][$nr]['other_attributes'] = '';

    $nr = $nr + 1;
    $Grid_form['data'][$nr]['type']             = 'label';
    $Grid_form['data'][$nr]['value']            = '';
    $Grid_form['data'][$nr]['other_attributes'] = '';
    $Grid_form['data'][$nr]['width']            = '0';

    $nr = $nr + 1;
    $Grid_form['data'][$nr]['type']             = 'th_end';
  //th preview -----------------------------------------------------------------------------------------------------------

  IF ($editim_konsultim == 'editim')
     {
      //th ---------------------------------------------------------------------------------------------------------------
        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'th_start';
        $Grid_form['data'][$nr]['other_attributes'] = '';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'label';
        $Grid_form['data'][$nr]['value']            = '';
        $Grid_form['data'][$nr]['other_attributes'] = '';
        $Grid_form['data'][$nr]['width']            = '0';

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'th_end';
      //th ---------------------------------------------------------------------------------------------------------------
     }

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tr_end';
  //tr koka2 -------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'thead_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_start';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  
  //TRUPI TABELE ---------------------------------------------------------------------------------------------------------
  //print_r($val_rec_event_branch);
  FOR ($i=0; $i < $val_rec_event_docs['nr_rec']; $i++)
      {
       $Grid_form['data'][$nr]['type']             = 'tr_start';
       $Grid_form['data'][$nr]['other_attributes'] = '';

         $id_doc_info    = $val_rec_event_docs["id_doc"][$i]['vl'];
         $id_branch_info = $val_rec_event_docs["id_branch"][$i]['vl'];

         //td ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($id_branch_arr[$val_rec_event_docs["id_branch"][$i]['vl']]).'';
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($share_arr[$val_rec_event_docs["share"][$i]['vl']]).'';
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'label';
           $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($val_rec_event_docs["description"][$i]['vl']).'';
           $Grid_form['data'][$nr]['other_attributes'] = '';
           $Grid_form['data'][$nr]['width']            = '0';

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         //td preview ----------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';

           //preview doc ------------------------------------------------------------------------------------------------
             $file_name_print = '';
             IF ($val_rec_event_docs['file_size'][$i]['vl'] > 1048576)
                {
                 $file_size_print = ROUND($val_rec_event_docs['file_size'][$i]['vl'] / 1048576, 2)." MB";
                }
             ELSE
                {
                 $file_size_print = ROUND($val_rec_event_docs['file_size'][$i]['vl'] / 1024,    2)." KB";
                }

             $file_name_print = $val_rec_event_docs['file_name'][$i]['vl'].' ('.$file_size_print.')';

             $file_name = $val_rec_event_docs['file_name'][$i]['vl'];

             IF (
                 (STRTOUPPER(SUBSTR($file_name, -4)) == '.GIF') OR 
                 (STRTOUPPER(SUBSTR($file_name, -4)) == '.PNG') OR 
                 (STRTOUPPER(SUBSTR($file_name, -4)) == '.JPG') OR 
                 (STRTOUPPER(SUBSTR($file_name, -4)) == '.BMP') OR
                 (STRTOUPPER(SUBSTR($file_name, -4)) == '.MP4') OR
                 (STRTOUPPER(SUBSTR($file_name, -4)) == '.MP3') OR
                 (STRTOUPPER(SUBSTR($file_name, -4)) == '.PDF') 
                )
                {
                 $doc_attrib["id_doc"]  = $id_doc_info;
                 $doc_attrib["preview"] = "Y";
                 $doc_attrib_ser        = SERIALIZE($doc_attrib);

                 $id_sel      = f_app_encrypt($doc_attrib_ser.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                 $url_preview = $data_url_download.'&id_sel='.$id_sel;
                 
                 IF (STRTOUPPER(SUBSTR($file_name, -4)) == '.PDF')
                    {
                     $data_url_sel = $data_url_preview_pdf.urlencode($url_preview);
                    }
                 ELSE
                    {
                     $data_url_sel = $url_preview;
                    }

                 $icon_type_sel = "";
                 IF (
                     (STRTOUPPER(SUBSTR($file_name, -4)) == '.GIF') OR 
                     (STRTOUPPER(SUBSTR($file_name, -4)) == '.PNG') OR 
                     (STRTOUPPER(SUBSTR($file_name, -4)) == '.JPG') OR 
                     (STRTOUPPER(SUBSTR($file_name, -4)) == '.BMP')
                    )
                    {
                     $icon_type_sel = "icon_preview_img";
                    }
                 ELSEIF (STRTOUPPER(SUBSTR($file_name, -4)) == '.MP4')
                    {
                     $icon_type_sel = "icon_preview_video";
                    }
                 ELSEIF (STRTOUPPER(SUBSTR($file_name, -4)) == '.MP3')
                    {
                     $icon_type_sel = "icon_preview_audio";
                    }
                 ELSEIF (STRTOUPPER(SUBSTR($file_name, -4)) == '.PDF')
                    {
                     $icon_type_sel = "icon_preview_pdf";
                    }
                 ELSE
                    {
                     $icon_type_sel = "icon_preview";
                    }
                 
                 $nr = $nr + 1;
                 $Grid_form['data'][$nr]['type']             = 'label';
                 $Grid_form['data'][$nr]['value']            = '';
                 $Grid_form['data'][$nr]['other_attributes'] = '';
                 $Grid_form['data'][$nr]['width']            = '0';

                 $Grid_form['data'][$nr]['link']              = 'Y';
                 $Grid_form['data'][$nr]['link_att']          = '';
                 $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
                 $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("shiko_mesg").': '.$file_name_print;
                 $Grid_form['data'][$nr]['link_data_url']     = $data_url_sel;
                 $Grid_form['data'][$nr]['has_icon']          = 'Y';
                 $Grid_form['data'][$nr]['icon_type']         = $icon_type_sel;
                 $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
                 $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
                 $Grid_form['data'][$nr]['link_modal_width']  = '1200';
                 $Grid_form['data'][$nr]['link_modal_height'] = '850';
                }
           //preview doc ------------------------------------------------------------------------------------------------

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']         = 'td_end';
         //td preview ----------------------------------------------------------------------------------------------------

         //td ------------------------------------------------------------------------------------------------------------
           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']             = 'td_start';
           $Grid_form['data'][$nr]['other_attributes'] = '';
           
           //download doc ------------------------------------------------------------------------------------------------
             $doc_attrib["id_doc"]  = $id_doc_info;
             $doc_attrib["preview"] = "N";
             $doc_attrib_ser        = SERIALIZE($doc_attrib);

             $id_sel       = f_app_encrypt($doc_attrib_ser.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
             $url_download = 'location.href="'.$data_url_download.'&id_sel='.$id_sel.'"';
             
             $nr = $nr + 1;
             $Grid_form['data'][$nr]['type']              = 'label';
             $Grid_form['data'][$nr]['value']             = '';
             $Grid_form['data'][$nr]['other_attributes']  = '';
             $Grid_form['data'][$nr]['width']             = '0';
             
             $Grid_form['data'][$nr]['link']              = 'Y';
             $Grid_form['data'][$nr]['link_att']          = 'href="javascript:void(0);" onClick=\''.$url_download.'\''; // target="_blank"
             $Grid_form['data'][$nr]['link_data_modal']   = 'N';
             $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("download_mesg").': '.$file_name_print;
             $Grid_form['data'][$nr]['link_data_url']     = $data_url_sel;
             $Grid_form['data'][$nr]['has_icon']          = 'Y';
             $Grid_form['data'][$nr]['icon_type']         = 'icon_download';
             //$Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
             //$Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
             //$Grid_form['data'][$nr]['link_modal_width']  = '800';
             //$Grid_form['data'][$nr]['link_modal_height'] = '850';
           //download doc ------------------------------------------------------------------------------------------------

           $nr = $nr + 1;
           $Grid_form['data'][$nr]['type']         = 'td_end';
         //td ------------------------------------------------------------------------------------------------------------

         IF ($editim_konsultim == 'editim')
            {
             $nr = $nr + 1;
             $Grid_form['data'][$nr]['type']             = 'td_start';
             $Grid_form['data'][$nr]['other_attributes'] = '';

             IF (ISSET($user_dega_qendra["edit_branch_id"][$id_branch_info]) AND ($user_dega_qendra["edit_branch_id"][$id_branch_info] == $id_branch_info))
                {
                 //hapim po kete nem per te uploduar doc --------------------------
                 $id_post_sel      = $post_id.','.$id_branch_info.','.$id_doc_info;  //id_event,id_branch,id_doc
                 $post_id_sel      = f_app_encrypt($id_post_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                 
                 $vars_post_kol    = null;
                 $vars_post_val    = null;
                 
                 $vars_post_kol[]  = 'gjendje';
                 $vars_post_val[]  = 'record_detaje_doc';

                 $vars_post_kol[]  = 'editim_konsultim';
                 $vars_post_val[]  = 'editim';

                 $vars_post_kol[]  = 'post_id';
                 $vars_post_val[]  = $post_id_sel;

                 $vars_post        = f_app_vars_page_encrypt($vars_post_kol, $vars_post_val);
                 $data_url_sel     = $data_url_this_nem.'&vars_post='.$vars_post;
                 //hapim po kete nem per te uploduar doc --------------------------
         
                 $nr = $nr + 1;
                 $Grid_form['data'][$nr]['type']             = 'label';
                 $Grid_form['data'][$nr]['value']            = '';
                 $Grid_form['data'][$nr]['other_attributes'] = '';
                 $Grid_form['data'][$nr]['width']            = '0';


                 $Grid_form['data'][$nr]['link']              = 'Y';
                 $Grid_form['data'][$nr]['link_att']          = '';
                 $Grid_form['data'][$nr]['link_data_modal']   = 'Y';
                 $Grid_form['data'][$nr]['link_data_title']   = WebApp::getVar("event_dega_docs_mesg")." - ".WebApp::getVar("perditesim_mesg");
                 $Grid_form['data'][$nr]['link_data_url']     = $data_url_sel;
                 $Grid_form['data'][$nr]['has_icon']          = 'Y';
                 $Grid_form['data'][$nr]['icon_type']         = 'icon_edit';
                 $Grid_form['data'][$nr]['link_modal_iframe'] = 'true';
                 $Grid_form['data'][$nr]['link_modal_size']   = 'modal-lg'; //link_modal_width mer perparesi
                 $Grid_form['data'][$nr]['link_modal_width']  = '800';
                 $Grid_form['data'][$nr]['link_modal_height'] = '850';
                }

             $nr = $nr + 1;
             $Grid_form['data'][$nr]['type']         = 'td_end';
            }
         //td ------------------------------------------------------------------------------------------------------------

       $nr = $nr + 1;
       $Grid_form['data'][$nr]['type']           = 'tr_end';
      }
  //TRUPI TABELE ---------------------------------------------------------------------------------------------------------
  
  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'tbody_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'table_end';
  }
  //PHI_EVENT_BRANCH_DOC INFORMACION RRETH NGJARJES ----------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';
  //DOC DEGA -------------------------------------------------------------------------------------------------------------

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
//ROW INFO + DOC ---------------------------------------------------------------------------------------------------------

//RROW BOSH --------------------------------------------------------------------------------------------------------------
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
  $Grid_form['data'][$nr]['value']            = ' ';
  $Grid_form['data'][$nr]['for']              = '';
  $Grid_form['data'][$nr]['id']               = '';
  $Grid_form['data'][$nr]['other_attributes'] = '';
  $Grid_form['data'][$nr]['width']            = '12';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'section_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'col_end';

  $nr = $nr + 1;
  $Grid_form['data'][$nr]['type']             = 'row_end';
//RROW BOSH --------------------------------------------------------------------------------------------------------------

  //audit trail ----------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_04_audit_trail.php");
  //audit trail ----------------------------------------------------------------------------------------------------------

  //end ------------------------------------------------------------------------------------------------------------------
    INCLUDE(dirname(__FILE__)."/../php_script/add_edit_05_end.php");
  //end ------------------------------------------------------------------------------------------------------------------
//Grid_form --------------------------------------------------------------------------------------------------------------
?>