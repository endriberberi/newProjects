<?
	switch ($event->name)
	       {
            case "next_prev":
                  //ngreme ne variabla global variablat ekzistuese te faqes -------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                  //---------------------------------------------------------------
                  
                  $G_APP_VARS["gjendje"] = "lista";
                  
                  IF (ISSET($var_post))
                     {
                      $var_post     = ValidateVarFun::f_only_numbers_minus($var_post);
                      $var_post_arr = EXPLODE("-", $var_post);
                      
                      IF (IS_NUMERIC($var_post_arr[0]) AND IS_NUMERIC($var_post_arr[1]))
                         {
                          $G_APP_VARS["nr_rec_start"] = $var_post_arr[0];
                          $G_APP_VARS["nr_rec_page"]  = $var_post_arr[1];
                         }
                     }
            break;

		    case "change_page":
                  //ngreme ne variabla sesioni variablat ekzistuese te faqes ------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                  //---------------------------------------------------------------

                  $G_APP_VARS["gjendje"] = "lista";
		          
                  IF (ISSET($var_post))
                     {
                      $var_post     = ValidateVarFun::f_only_numbers_minus($var_post);
                      $var_post_arr = EXPLODE("-", $var_post);

                      IF (IS_NUMERIC($var_post_arr[0]) AND IS_NUMERIC($var_post_arr[1]))
                         {
                          $change_page               = $var_post_arr[0];
                          $G_APP_VARS["nr_rec_page"] = $var_post_arr[1];

                          $nr_rec = $G_APP_VARS["nr_rec_page"]*($change_page - 1);
	                      IF ($nr_rec > 0)
	                         {
	                          $G_APP_VARS["nr_rec_start"] = $nr_rec;
		                     }
		                  ELSE
		                     {
		                      $G_APP_VARS["nr_rec_start"] = 0;
		                     }
		                 }
		             }
		          break;

		    case "change_nr_rec":
                  //ngreme ne variabla sesioni variablat ekzistuese te faqes ------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                  //---------------------------------------------------------------

                  $G_APP_VARS["gjendje"]      = "lista";
                  $G_APP_VARS["nr_rec_start"] = 0;

                  IF (ISSET($var_post))
                     {
                      $var_post = ValidateVarFun::f_only_numbers($var_post);

                      IF (IS_NUMERIC($var_post))
                         {
                          $G_APP_VARS["nr_rec_page"] = $var_post;
		                 }
		             }
		          break;

		    case "col_order":
                  //ngreme ne variabla sesioni variablat ekzistuese te faqes ------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                  //---------------------------------------------------------------

                  $G_APP_VARS["gjendje"] = "lista";
		          
		          $order_by_indx = ValidateVarFun::f_pozitive_numbers($order_by_indx);
		          
		          IF ($order_by_indx >= 0)
		             {
		              $G_APP_VARS["order_by_indx"] = $order_by_indx;
		             }
		          
		          IF (ISSET($order_by) AND ($order_by != ""))
		             {
		              IF ($order_by == "2")
		                 {
		                  $order_by = "1";
		                 }
		              ELSE
		                 {
		                  $order_by = "2";
		                 }
		             }
		          ELSE
		             {
		              $order_by = "1";
		             }
		          
		          $G_APP_VARS["order_by"] = $order_by;
		          break;

            case "search":
                  //ngreme ne variabla sesioni variablat ekzistuese te faqes ------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                  //---------------------------------------------------------------
                  $G_APP_VARS["gjendje"]      = "lista";
		          $G_APP_VARS["nr_rec_start"] = 0;

                  $kol_val       = STR_REPLACE(array("<pikp>","<_and_>"), array(";","&"), $var_post);
                  $kol_val_array = EXPLODE("<->", $kol_val);
                  $kol_array     = EXPLODE("<_>", $kol_val_array[0]);
                  $val_array     = EXPLODE("<_>", $kol_val_array[1]);

                  FOR ($i=0; $i < count($kol_array); $i++)
                      {
                       $G_APP_VARS[$kol_array[$i]] = TRIM($val_array[$i]);
                      }
                 break;

            case "back":
                  //shperthejme variablat e faqes ne rast se jemi ne popup dhe po bejme nje shtim te ri ------------------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                  //shperthejme variablat e faqes ne rast se jemi ne popup dhe po bejme nje shtim te ri ------------------

                  $G_APP_VARS["gjendje"] = "lista";
            break;

            case "add_edit":
                  //shperthejme variablat e faqes ne rast se jemi ne popup dhe po bejme nje shtim te ri ------------------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                  //shperthejme variablat e faqes ne rast se jemi ne popup dhe po bejme nje shtim te ri ------------------

                  $G_APP_VARS["post_id"]          = $post_id;
                  $G_APP_VARS["gjendje"]          = "record_detaje";
                  $G_APP_VARS["editim_konsultim"] = "editim";
                  break;

		    case "save":
                  //VALIDIMET E PERGJITHSHME PER NGJARJEN SAVE -----------------------------------------------------------
                  //shperthejme variablat e faqes dhe validojme te drejtat -----------------------------------------------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                    
                    IF (!ISSET($G_APP_VARS["form_id_token"]) OR ($G_APP_VARS["form_id_token"] != "Y"))
                       {
                        UNSET($G_APP_VARS);
                        //NUK PO NXJERIM MESAZH SE MUND TE JETE RASTI REFRESH 
                        //$G_APP_VARS["kodi"]  = "error";
                        //$G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}}";
                        //$G_APP_VARS["time"]  = "10";
                        RETURN;
                       }
                    
                    //VALIDOJME QE KERKESA KA ARDHUR PO NGA KY NEM -------------------------------------------------------
                      IF (!ISSET($G_APP_VARS["nem_id"]) OR ($G_APP_VARS["nem_id"] != $NEM_ID_SEL) OR ($NEM_ID_SEL == 0))
                         {
                          UNSET($G_APP_VARS);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}}";
                          $G_APP_VARS["time"]  = "10";
                          RETURN;
                         }
                    //----------------------------------------------------------------------------------------------------

                    //validojme te drejtat tek nemi ----------------------------------------------------------------------
                      $id_pk      = $G_APP_VARS["id_pk"];
                      $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);
                      
                      IF ($id_pk == "")
                         {
                          $id_action_sel = 101; //Add
                         }
                      ELSE
                         {
                          $id_action_sel = 102; //Modify
                         }

                      IF (ISSET($nem_rights[$NEM_ID_SEL][$id_action_sel]) AND ($nem_rights[$NEM_ID_SEL][$id_action_sel] != ""))
                         {
                          //useri ka te drejte te Add/Modify ... jemi ok
                         }
                      ELSE
                         {
                          UNSET($G_APP_VARS);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{ruajtja_deshtoi_mesg}} {{nuk_keni_te_drejte_mesg}}";
                          $G_APP_VARS["time"]  = "15";
                          RETURN;
                         }
                    //----------------------------------------------------------------------------------------------------
                  //------------------------------------------------------------------------------------------------------
                  break;

		    case "del":
                  //VALIDIMET E PERGJITHSHME PER NGJARJEN DELETE ---------------------------------------------------------
                  //shperthejme variablat e faqes dhe validojme te drejtat -----------------------------------------------
                    IF ($vars_page != "")
                       {
                        $G_APP_VARS = f_app_vars_page($vars_page);
                       }
                    
                    IF (!ISSET($G_APP_VARS["form_id_token"]) OR ($G_APP_VARS["form_id_token"] != "Y"))
                       {
                        UNSET($G_APP_VARS);
                        RETURN;
                       }
                    
                    //VALIDOJME QE KERKESA KA ARDHUR PO NGA KY NEM -------------------------------------------------------
                      IF (!ISSET($G_APP_VARS["nem_id"]) OR ($G_APP_VARS["nem_id"] != $NEM_ID_SEL) OR ($NEM_ID_SEL == 0))
                         {
                          UNSET($G_APP_VARS);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{fshirja_deshtoi_mesg}}";
                          $G_APP_VARS["time"]  = "10";
                          RETURN;
                         }
                    //----------------------------------------------------------------------------------------------------

                    //validojme te drejtat tek nemi ----------------------------------------------------------------------
                      $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);
                      
                      IF (ISSET($nem_rights[$NEM_ID_SEL][103]) AND ($nem_rights[$NEM_ID_SEL][103] != ""))
                         {
                          //useri ka te drejte te Delete ... jemi ok
                         }
                      ELSE
                         {
                          UNSET($G_APP_VARS);
                          $G_APP_VARS["kodi"]  = "error";
                          $G_APP_VARS["mesg"]  = "{{fshirja_deshtoi_mesg}} {{nuk_keni_te_drejte_mesg}}";
                          $G_APP_VARS["time"]  = "15";
                          RETURN;
                         }
                    //----------------------------------------------------------------------------------------------------
                  //------------------------------------------------------------------------------------------------------

                  $id_pk = $G_APP_VARS["id_pk"];
                  //VALIDIMET E PERGJITHSHME PER NGJARJEN DELETE ---------------------------------------------------------
                  break;
           }
?>