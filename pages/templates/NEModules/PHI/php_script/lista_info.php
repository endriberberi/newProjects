<?
  $nr  = -1;
  $Grid_nav_from_to      = array('data' => array(), 'AllRecs' => '0');	
  $Grid_nav_rec_page_att = array('data' => array(), 'AllRecs' => '0');	
  $Grid_nav_rec_page_val = array('data' => array(), 'AllRecs' => '0');	
  $Grid_nav_browse_link  = array('data' => array(), 'AllRecs' => '0');	
  $Grid_nav_browse_page  = array('data' => array(), 'AllRecs' => '0');	
  $Grid_tab_data         = array('data' => array(), 'AllRecs' => '0');	
  $Grid_add_button       = array('data' => array(), 'AllRecs' => '0');	
  $Grid_tab_exp          = array('data' => array(), 'AllRecs' => '0');	

  IF ($nr_rec_total > 0)
     {
      //browse -----------------------------------------------------------------------------------------------------------
        IF ($nr_rec_end < $nr_rec_total)
           {
            $nr_rec_end_print = $nr_rec_end;

            $Grid_nav_browse_link['data'][0]['next_trigger_function']        = 'f_app_var_post';
            $Grid_nav_browse_link['data'][0]['next_trigger_function_params'] = "'".$arg_webbox."', '".$arg_event_next_prev."', '".$arg_id_form."', '".$nr_rec_end."-".$nr_rec_page."'";
            $Grid_nav_browse_link['data'][0]['next_disabled']                = '';
           }
        ELSE
           {
            $nr_rec_end_print = $nr_rec_total;

            $Grid_nav_browse_link['data'][0]['next_trigger_function']        = 'f_app_var_post';
            $Grid_nav_browse_link['data'][0]['next_trigger_function_params'] = "'".$arg_webbox."', '".$arg_event_next_prev."', '".$arg_id_form."', '0-".$nr_rec_page."'";
            $Grid_nav_browse_link['data'][0]['next_disabled']                = 'disabled';
           }
  
        IF ($nr_rec_start > 0)
           {
            $nr_rec_start_prev = $nr_rec_start - $nr_rec_page;

            $Grid_nav_browse_link['data'][0]['prev_trigger_function']        = 'f_app_var_post';
            $Grid_nav_browse_link['data'][0]['prev_trigger_function_params'] = "'".$arg_webbox."', '".$arg_event_next_prev."', '".$arg_id_form."', '".$nr_rec_start_prev."-".$nr_rec_page."'";
            $Grid_nav_browse_link['data'][0]['prev_disabled']                = '';
           }
        ELSE
           { 
            $Grid_nav_browse_link['data'][0]['prev_trigger_function']        = 'f_app_var_post';
            $Grid_nav_browse_link['data'][0]['prev_trigger_function_params'] = "'".$arg_webbox."', '".$arg_event_next_prev."', '".$arg_id_form."', '0-".$nr_rec_page."'";
            $Grid_nav_browse_link['data'][0]['prev_disabled']                = 'disabled';
           }
  
        $faqja_korente = ceil($nr_rec_start_print/$nr_rec_page);
        $faqja_total   = ceil($nr_rec_total/$nr_rec_page);

        $nr_faqe_min = $faqja_korente - 4;
        IF ($nr_faqe_min < 1)
           {
            $nr_faqe_min = 1;
           }

        $nr_faqe_max = $faqja_korente + 4;
        IF ($nr_faqe_max > $faqja_total)
           {
            $nr_faqe_max = $faqja_total;
           }
        
        $nr = -1;
        FOR ($i=$nr_faqe_min; $i <= $nr_faqe_max; $i++)
            {
             $current_sel = '';
             
             IF ($i == $faqja_korente)
                {
                 $current_sel = 'current';
                }
             
             $nr = $nr + 1;
             $Grid_nav_browse_page['data'][$nr]['label']                   = $i;
             $Grid_nav_browse_page['data'][$nr]['trigger_function']        = 'f_app_var_post';
             $Grid_nav_browse_page['data'][$nr]['trigger_function_params'] = "'".$arg_webbox."', '".$arg_event_change_page."', '".$arg_id_form."', '".$i."-".$nr_rec_page."'";
             $Grid_nav_browse_page['data'][$nr]['current']                 = $current_sel;
            }
      //browse -----------------------------------------------------------------------------------------------------------
    
      //navigimi print ---------------------------------------------------------------------------------------------------
        $Grid_nav_from_to['data'][0]['rec_start'] = $nr_rec_start_print;
        $Grid_nav_from_to['data'][0]['rec_end']   = $nr_rec_end_print;
        $Grid_nav_from_to['data'][0]['rec_total'] = $nr_rec_total;
        $Grid_nav_from_to['data'][0]['rec_label'] = "{{rec_mesg}}";
      //navigimi print ---------------------------------------------------------------------------------------------------
      
      //html_nr_rec_faqe -------------------------------------------------------------------------------------------------
        $a1 = ''; $a2 = ''; $a3 = ''; $a4 = '';
        IF ($nr_rec_page == 10)
           {
            $a1 = ' selected';
           }
        
        IF ($nr_rec_page == 25)
           {
            $a2 = ' selected';
           }
           
        IF ($nr_rec_page == 50)
           {
            $a3 = ' selected';
           }
        
        IF ($nr_rec_page == 100)
           {
            $a4 = ' selected';
           }

        $Grid_nav_rec_page_att['data'][0]['name']                    = "nr_rec_page";
        $Grid_nav_rec_page_att['data'][0]['label']                   = "{{rec_ne_faqe_mesg}}";
        $Grid_nav_rec_page_att['data'][0]['trigger_function']        = 'f_app_var_post';
        $Grid_nav_rec_page_att['data'][0]['trigger_function_params'] = "'".$arg_webbox."', '".$arg_event_change_nr_rec."', '".$arg_id_form."'";
        
        $Grid_nav_rec_page_val['data'][0]['value']                   = "10";
        $Grid_nav_rec_page_val['data'][0]['selected']                = $a1;

        $Grid_nav_rec_page_val['data'][1]['value']                   = "25";
        $Grid_nav_rec_page_val['data'][1]['selected']                = $a2;

        $Grid_nav_rec_page_val['data'][2]['value']                   = "50";
        $Grid_nav_rec_page_val['data'][2]['selected']                = $a3;

        $Grid_nav_rec_page_val['data'][3]['value']                   = "100";
        $Grid_nav_rec_page_val['data'][3]['selected']                = $a4;
      //html_nr_rec_faqe -------------------------------------------------------------------------------------------------

      //grid tab data ----------------------------------------------------------------------------------------------------

        $nr = -1;
        
        $row_start = 0;
        IF (ISSET($data_arr[0]["properties"]) AND IS_ARRAY($data_arr[0]["properties"]))
           {
            $row_start = 1;
           }

        FOR ($i=$row_start; $i < count($data_arr); $i++)
            {
             $row_arr = $data_arr[$i];
             
             $nr = $nr + 1;
             $Grid_tab_data['data'][$nr]['tag'] = 'tr';

             FOR ($j=0; $j < count($row_arr); $j++)
                 {
                  $cel_sel = $row_arr[$j];

                  //per linkun tek koka ---------------------------------------------------------------------------------- 
                    $class_order = '';
                    IF ($cel_sel['vl_db'] != "")
                       {
                        IF ($G_APP_VARS["order_by_indx"] == $cel_sel['vl_db_indx'])
                           {
                            IF ($G_APP_VARS["order_by"] == "2")
                               {
                                $class_order  = ' class="sorting_desc"';
                               }
                            ELSE
                               {
                                $class_order  = ' class="sorting_asc"';
                               }
 
                            $link_koka   = 'href="JavaScript:f_app_col_order(\''.$arg_webbox.'\', \''.$arg_event_col_order.'\', \''.$arg_id_form.'\', \''.$cel_sel['vl_db_indx'].'\',\''.$G_APP_VARS["order_by"].'\')"';
                           }
                        ELSE
                           {
                            $class_order = ' class="sorting"';
                            $link_koka   = 'href="JavaScript:f_app_col_order(\''.$arg_webbox.'\', \''.$arg_event_col_order.'\', \''.$arg_id_form.'\', \''.$cel_sel['vl_db_indx'].'\',\'\')"';
                           }
                       
                        $cel_sel['link']     = 'Y';
                        $cel_sel['link_att'] = $link_koka;
                       }
                  //per linkun tek koka ---------------------------------------------------------------------------------- 
                  
                  IF ($cel_sel['vlf'] != '')
                     {
                      $cel_sel['vl'] = $cel_sel['vlf'];
                     }
                  
                  $cel_style_plus = "";
                  IF ($cel_sel['bold'] == 'Y')
                     {
                      $cel_style_plus .= 'font-weight: bold;';
                     }

                  IF ($cel_sel['align'] != "")
                     {
                      $cel_style_plus .= ' text-align: '.$cel_sel['align'].';';
                     }

                  $cel_style_sel = "";
                  IF (($cel_sel['style'] != "") OR ($cel_style_plus != ""))
                     {
                      $cel_style_sel = ' style="'.$cel_style_plus.$cel_sel['style'].'"';
                     }

                  $cel_colspan_sel = "";
                  IF ($cel_sel['colspan'] != "")
                     {
                      $cel_colspan_sel = ' colspan="'.$cel_sel['colspan'].'"';
                     }

                  $nr = $nr + 1;
                  $Grid_tab_data['data'][$nr]['tag']               = $cel_sel['tag'];
                  $Grid_tab_data['data'][$nr]['tag_att']           = $cel_sel['tag_att'].$cel_style_sel.$cel_colspan_sel.$class_order;

                  $Grid_tab_data['data'][$nr]['link']              = $cel_sel['link'];
                  $Grid_tab_data['data'][$nr]['link_att']          = $cel_sel['link_att'];
                  $Grid_tab_data['data'][$nr]['link_data_modal']   = $cel_sel['link_data_modal'];
                  $Grid_tab_data['data'][$nr]['link_data_title']   = $cel_sel['link_data_title'];
                  $Grid_tab_data['data'][$nr]['link_data_url']     = $cel_sel['link_data_url'];
                  $Grid_tab_data['data'][$nr]['link_modal_width']  = $cel_sel['link_modal_width'].'';
                  $Grid_tab_data['data'][$nr]['link_modal_height'] = $cel_sel['link_modal_height'].'';
                  $Grid_tab_data['data'][$nr]['link_modal_iframe'] = $cel_sel['link_modal_iframe'].'';
                  $Grid_tab_data['data'][$nr]['data_type']         = $cel_sel['data_type'];
                  $Grid_tab_data['data'][$nr]['label']             = $cel_sel['vl'];
                 }
             
             $nr = $nr + 1;
             $Grid_tab_data['data'][$nr]['tag'] = 'tr_end';
            }
      //grid tab data ----------------------------------------------------------------------------------------------------
      
      //Grid_tab_exp -----------------------------------------------------------------------------------------------------
        IF (ISSET($exp_params["vars_page"]) AND ($exp_params["vars_page"] != "") AND ($G_APP_VARS["nem_mode"] != "select_record"))
           {
            $indx_exp = -1;
            IF ($exp_params["xls"] == "Y")
               {
                $indx_exp = $indx_exp + 1;
                $Grid_tab_exp['data'][$indx_exp]['label']    = '{{exporto_listen_mesg}} XLS';
                $Grid_tab_exp['data'][$indx_exp]['title']    = '{{formati_mesg}}: XLS ({{maksimumi_mesg}} '.number_format($exp_params["nr_rec_exp"], 0, ".", ",").' {{rec_mesg}})';
                $Grid_tab_exp['data'][$indx_exp]['exp_type'] = 'xlsx';
                $Grid_tab_exp['data'][$indx_exp]['exp_url']  = 'location.href="{{APP_URL}}ajxDt.php?apprcss=export&tipi_exp=1&uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&vars_page='.$exp_params["vars_page"].'"';
               }

            IF ($exp_params["cvs"] == "Y")
               {
                $indx_exp = $indx_exp + 1;
                $Grid_tab_exp['data'][$indx_exp]['label']    = '{{exporto_listen_mesg}} CVS';
                $Grid_tab_exp['data'][$indx_exp]['title']    = '{{formati_mesg}}: CVS ({{maksimumi_mesg}} '.number_format($exp_params["nr_rec_exp"], 0, ".", ",").' {{rec_mesg}})';
                $Grid_tab_exp['data'][$indx_exp]['exp_type'] = 'cvs';
                $Grid_tab_exp['data'][$indx_exp]['exp_url']  = 'location.href="{{APP_URL}}ajxDt.php?apprcss=export&tipi_exp=2&uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&vars_page='.$exp_params["vars_page"].'"';
               }
               
            IF ($exp_params["html"] == "Y")
               {
                $indx_exp = $indx_exp + 1;
                $Grid_tab_exp['data'][$indx_exp]['label']    = '{{exporto_listen_mesg}} HTML';
                $Grid_tab_exp['data'][$indx_exp]['title']    = '{{formati_mesg}}: HTML ({{maksimumi_mesg}} '.number_format($exp_params["nr_rec_exp"], 0, ".", ",").' {{rec_mesg}})';
                $Grid_tab_exp['data'][$indx_exp]['exp_type'] = 'html';
                $Grid_tab_exp['data'][$indx_exp]['exp_url']  = 'location.href="{{APP_URL}}ajxDt.php?apprcss=export&tipi_exp=3&uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&vars_page='.$exp_params["vars_page"].'"';
               }

            IF ($exp_params["pdf"] == "Y")
               {
                $indx_exp = $indx_exp + 1;
                $Grid_tab_exp['data'][$indx_exp]['label']    = '{{exporto_listen_mesg}} PDF';
                $Grid_tab_exp['data'][$indx_exp]['title']    = '{{formati_mesg}}: PDF ({{maksimumi_mesg}} '.number_format($exp_params["nr_rec_exp"], 0, ".", ",").' {{rec_mesg}})';
                $Grid_tab_exp['data'][$indx_exp]['exp_type'] = 'pdf';
                $Grid_tab_exp['data'][$indx_exp]['exp_url']  = 'location.href="{{APP_URL}}ajxDt.php?apprcss=export&tipi_exp=4&uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&vars_page='.$exp_params["vars_page"].'"';
               }

            IF ($exp_params["doc"] == "Y")
               {
                $indx_exp = $indx_exp + 1;
                $Grid_tab_exp['data'][$indx_exp]['label']    = '{{exporto_listen_mesg}} DOC';
                $Grid_tab_exp['data'][$indx_exp]['title']    = '{{formati_mesg}}: DOC ({{maksimumi_mesg}} '.number_format($exp_params["nr_rec_exp"], 0, ".", ",").' {{rec_mesg}})';
                $Grid_tab_exp['data'][$indx_exp]['exp_type'] = 'doc';
                $Grid_tab_exp['data'][$indx_exp]['exp_url']  = 'location.href="{{APP_URL}}ajxDt.php?apprcss=export&tipi_exp=5&uni='.$session->Vars["uni"].'&ln='.$session->Vars["ln"].'&vars_page='.$exp_params["vars_page"].'"';
               }
           } 
      //Grid_tab_exp -----------------------------------------------------------------------------------------------------
     }


   IF (COUNT($Grid_nav_from_to['data']) > 0) 
      {
       $Grid_nav_from_to['AllRecs'] = COUNT($Grid_nav_from_to['data']);
      }
   WebApp::addVar('Grid_nav_from_to', $Grid_nav_from_to);

   IF (COUNT($Grid_nav_rec_page_att['data']) > 0) 
      {
       $Grid_nav_rec_page_att['AllRecs'] = COUNT($Grid_nav_rec_page_att['data']);
      }
   WebApp::addVar('Grid_nav_rec_page_att', $Grid_nav_rec_page_att);
   
   IF (COUNT($Grid_nav_rec_page_val['data']) > 0) 
      {
       $Grid_nav_rec_page_val['AllRecs'] = COUNT($Grid_nav_rec_page_val['data']);
      }
   WebApp::addVar('Grid_nav_rec_page_val', $Grid_nav_rec_page_val);

   IF (COUNT($Grid_nav_browse_link['data']) > 0) 
      {
       $Grid_nav_browse_link['AllRecs'] = COUNT($Grid_nav_browse_link['data']);
      }
   WebApp::addVar('Grid_nav_browse_link', $Grid_nav_browse_link);

   IF (COUNT($Grid_nav_browse_page['data']) > 0) 
      {
       $Grid_nav_browse_page['AllRecs'] = COUNT($Grid_nav_browse_page['data']);
      }
   WebApp::addVar('Grid_nav_browse_page', $Grid_nav_browse_page);

   IF (COUNT($Grid_tab_data['data']) > 0) 
      {
       $Grid_tab_data['AllRecs'] = COUNT($Grid_tab_data['data']);
      }
   WebApp::addVar('Grid_tab_data', $Grid_tab_data);


   IF (COUNT($Grid_tab_exp['data']) > 0) 
      {
       $Grid_tab_exp['AllRecs'] = COUNT($Grid_tab_exp['data']);
      }
   WebApp::addVar('Grid_tab_exp', $Grid_tab_exp);

   //buton_add -----------------------------------------------------------------------------------------------------------
     IF (ISSET($nem_rights[$NEM_ID_SEL]["101"]) AND ($nem_rights[$NEM_ID_SEL]["101"] != ""))
        {
         IF (ISSET($buton_add_params["fun"]) AND ($buton_add_params["fun"] != ""))
            {
             $buton_add_action = $buton_add_params["fun"];
            }
         ELSE
            {
             $id_add           = f_app_encrypt('|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
             $buton_add_action = 'javascript:f_app_add_edit(\''.$arg_webbox.'\', \'add_edit\', \''.$arg_id_form.'\', \''.$id_add.'\');"'; //aksioni add/edit
            }

         $buton_add_label_sel = "{{shto_rekord_te_ri_mesg}}";
         IF (ISSET($buton_add_params["label"]) AND ($buton_add_params["label"] != ""))
            {
             $buton_add_label_sel = $buton_add_params["label"];
            }
         
         $title_sel = "";
         IF (ISSET($buton_add_params["title"]) AND ($buton_add_params["title"] != ""))
            {
             $title_sel = $buton_add_params["title"];
            }

         $Grid_add_button['data'][0]['label']            = $buton_add_label_sel;
         $Grid_add_button['data'][0]['title']            = $title_sel;
         $Grid_add_button['data'][0]['trigger_function'] = $buton_add_action;
        }

     IF (COUNT($Grid_add_button['data']) > 0) 
        {
         $Grid_add_button['AllRecs'] = COUNT($Grid_add_button['data']);
        }
     WebApp::addVar('Grid_add_button', $Grid_add_button);
   //buton_add -----------------------------------------------------------------------------------------------------------

?>