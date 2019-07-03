<?
//ne rast se nuk eshte uploduar skedari ----------------------------------------------------------------------
  IF (!IS_UPLOADED_FILE($_FILES["file"]["tmp_name"]))
     {
      HEADER('Location: ' . APP_URL);
      EXIT;
     }
//------------------------------------------------------------------------------------------------------------

set_time_limit(0);
INI_SET("memory_limit", "256M");

//parametrat -------------------------------------------------------------------------------------------------
  $id_sel = $_GET["id_sel"];
//parametrat -------------------------------------------------------------------------------------------------

//validohet post_id ------------------------------------------------------------------------------------------
  IF (ISSET($id_sel) AND ($id_sel != ""))
     {
      $id_sel_nem_id = f_app_decrypt($id_sel, DESK_KEY, DESK_IV);
     }
  ELSE
     {
      HEADER('Location: ' . APP_URL);
      EXIT;
     }

  $id_sel_nem_id_arr = EXPLODE("|", $id_sel_nem_id);
  $post_id           = $id_sel_nem_id_arr[0];
  $post_id_nem       = $id_sel_nem_id_arr[1];

  //validojme nese kjo id i perket ketij nemi ----------------------------------------------------------------
    //IF ($post_id_nem != $NEM_ID_SEL)
    //   {
    //    $post_id = ""; //RETURN;
    //   }

  //validohet post_id ----------------------------------------------------------------------------------------

//inkludohet faili i mesazheve -------------------------------------------------------------------------------
  IF ($session->Vars["ln"] != "") 
     {
      $lang_name = "LNG".$session->Vars["ln"]."_Name";
      $name_lang = constant($lang_name);
      IF ($name_lang != "")
         {
          $message_file = APP_PATH."templates/".$name_lang.".mesg";
         }
      ELSE
         {
          $message_file = APP_PATH."templates/Shqip.mesg";
         }

     $faili_mesazheve = new WebBox("id_mesg_mesg");
     $faili_mesazheve->parse_msg_file($message_file);
     EXTRACT($GLOBALS["tplVars"]->Vars[0]);	
    }
//inkludohet faili i mesazheve -------------------------------------------------------------------------------
    
//------------------------------------------------------------------------------------------------------------
//KUSH NA THERET ---------------------------------------------------------------------------------------------
  //VALIDOHET WEBBOX_SEL -------------------------------------------------------------------------------------
    IF (!ISSET($post_id_nem) OR ($post_id_nem == ""))
       {
        HEADER('Location: ' . APP_URL);
        EXIT;
       }
  //VALIDOHET WEBBOX_SEL -------------------------------------------------------------------------------------
  
  //WEBOXI I NEMIT EKZISTON, VALIDOJME NESE USERI KA TE DREJTE TE EKZEKUTOJE KETE NEM ------------------------
    $NEM_ID_SEL = $post_id_nem;
    $nem_rights = NemsManager::getFeRightsToNem($NEM_ID_SEL);

    IF (ISSET($nem_rights[$NEM_ID_SEL]["101"]) AND ($nem_rights[$NEM_ID_SEL]["101"] != ""))
       {
        //KA TE DREJTE TE SHTOJE INFORMACION TEK NEMI I POSTUAR
       }
    ELSE
       {
        HEADER('Location: ' . APP_URL);
        EXIT;
       }
    
    //KAPIM ID E NEMIT ---------------------------------------------------------------------------------------
      /*
      $idstemp_arr    = EXPLODE("-", $G_APP_VARS["idstemp"]); //CI-459-101-1-123
      $content_id     = $idstemp_arr[1];

      $content_title  = "";
      
      //VALIDOJME NESE USERI ME KETE UNI KA TE DREJTE TE PERDORI KETE NEM ------------------------------------
        //NDOSHTA DUHET TE THARES DICKA GJENERALE NGA JONIDA
        $sql = "SELECT IF(content.titleLng".$session->Vars["ln"]." IS NULL, '', content.titleLng".$session->Vars["ln"].") as content_title 
                  FROM content INNER JOIN ci_nems ON
                               content.content_id = ci_nems.content_id
                               
                               INNER JOIN profil_rights ON
                                     content.id_zeroNivel    = profil_rights.id_zeroNivel   AND
                                     content.id_firstNivel   = profil_rights.id_firstNivel  AND
                                     content.id_secondNivel  = profil_rights.id_secondNivel AND
                                     content.id_thirdNivel   = profil_rights.id_thirdNivel  AND
                                     content.id_fourthNivel  = profil_rights.id_fourthNivel 
                 WHERE content.content_id       = '".ValidateVarFun::f_real_escape_string($content_id)."' AND
                       ci_nems.nem_id           = '".ValidateVarFun::f_real_escape_string($NEM_ID_SEL)."' AND
                       profil_rights.profil_id IN (".$session->Vars["tip"].")
               ";

        $rs = WebApp::execQuery($sql);
        IF (!$rs->EOF())
           {
            $content_title = $rs->Field("content_title");
           }
        ELSE
           {
            HEADER('Location: ' . APP_URL);
            EXIT;
           }
      //VALIDOJME NESE USERI ME KETE UNI KA TE DREJTE TE PERDORI KETE NEM ------------------------------------
      */
    //KAPIM ID E NEMIT ---------------------------------------------------------------------------------------
  //WEBOXI I NEMIT EKZISTON, VALIDOJME NESE USERI KA TE DREJTE TE EKZEKUTOJE KETE NEM ------------------------

  //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ------------------------------------
    $user_dega_qendra = f_app_user_dega_qendra ($session->Vars["ses_userid"]);
  //KAPIM DEGET OSE QENDRAT E RAPORTIMIT ME TE CILAT ESHTE I LIDHUR USERI ------------------------------------
//KUSH NA THERET ---------------------------------------------------------------------------------------------

//VALIDOJME DEGEN E USERIT -----------------------------------------------------------------------------------
  $post_id_arr    = UNSERIALIZE($post_id);
  $post_id_doc    = 0;
  $post_id_branch = 0;
  
  IF (ISSET($post_id_arr["id_doc"]))
     {
      $post_id_doc = $post_id_arr["id_doc"];
     }
     
  IF (ISSET($post_id_arr["id_branch"]))
     {
      $post_id_branch = $post_id_arr["id_branch"];
     }

  IF ($post_id_branch > 0)
     {
      IF (ISSET($user_dega_qendra["edit_branch_id"][$post_id_branch]) AND ($user_dega_qendra["edit_branch_id"][$post_id_branch] == $post_id_branch))
         {
          //jemi ok
         }
      ELSE
         {
          HEADER('Location: ' . APP_URL);
          EXIT;
         }
     }
//VALIDOJME DEGEN E USERIT -----------------------------------------------------------------------------------

//id e dokumentit per tu kthyer ------------------------------------------------------------------------------
  $doc_attrib = $post_id_arr;
//id e dokumentit per tu kthyer ------------------------------------------------------------------------------
    
//SHKRUAJME SKEDARIN -----------------------------------------------------------------------------------------
  IF (IS_UPLOADED_FILE($_FILES["file"]["tmp_name"]))
     {
      IF (ISSET($post_id_arr["description"]) AND ($post_id_arr["description"] != ""))
         {
          $description = $post_id_arr["description"]; 
         }
      ELSE
         {
          $description = $_FILES['file']['name']; 
         }

      $file_name = $_FILES['file']['name'];
      $file_type = $_FILES['file']['type'];
      $file_size = $_FILES['file']['size'];

      //EKSTRAKTOJME TEKSIN E DOKUMENTIT ---------------------------------------------------------------------
        //$info_return["Author"]            = "";
        //$info_return["CreationDate"]      = "";
        //$info_return["Creator"]           = "";
        //$info_return["ModDate"]           = "";
        //$info_return["Producer"]          = "";
        //$info_return["Title"]             = "";
        //$info_return["Pages"]             = "";
        //$info_return["Info"]              = "";
        //$info_return["mime_content_type"] = "";

        $file_info = Extract_text_from_file::parse_file($_FILES["file"]["tmp_name"], $file_type, '');

        $file_text = "";
        IF ($file_info["Info"] != "")
           {
            $file_text = $file_info["Info"];
           }
      //------------------------------------------------------------------------------------------------------

      $file_name = STR_REPLACE(array("'","\"",":"," ","(",")"), array("","","","_","-","-"), $file_name); 
      
      IF ($file_size > 0)
         {
          //RUAJME ATRIBUTET E SKEDARIT ----------------------------------------------------------------------
            IF ($post_id_doc == 0)
               {
                //gjenerojme id e dokumentit -----------------------------------------------------------------
                  $max_id = 0;
                  $sql = "SELECT IF(MAX(id_doc) IS NULL, 0, MAX(id_doc)) as max_id 
                            FROM phi_docs 
                         ";
                  $rs = WebApp::execQuery($sql);
                  IF (!$rs->EOF())
                     {
                      $max_id = $rs->Field("max_id");
                     }
                 
                  $post_id_doc = $max_id + 1;
                  
                  $doc_attrib["id_doc"] = $post_id_doc; //kthejme mbrapsht id e re te dokumentit
                //gjenerojme id e dokumentit -----------------------------------------------------------------

                $sql_kol     = null; 
                $sql_val     = null;
            
                $sql_kol[]   = "id_doc"; 
                $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($post_id_doc)."'"; 

                $sql_kol[]   = "id_branch"; 
                $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($post_id_branch)."'"; 

                $file_path   =  ValidateVarFun::f_only_all_natural_numbers($post_id_branch)."/";
            
                IF (ISSET($post_id_arr["id_reporting_entity"]))
                   {
                    $sql_kol[] = "id_reporting_entity"; 
                    $sql_val[] = "'".ValidateVarFun::f_real_escape_string($post_id_arr["id_reporting_entity"])."'"; 

                    $file_path .=  ValidateVarFun::f_only_numbers($post_id_arr["id_reporting_entity"])."/";
                   }
            
                IF (ISSET($post_id_arr["id_doc_category"]))
                   {
                    $sql_kol[] = "id_doc_category"; 
                    $sql_val[] = "'".ValidateVarFun::f_real_escape_string($post_id_arr["id_doc_category"])."'"; 

                    $file_path .=  ValidateVarFun::f_only_numbers($post_id_arr["id_doc_category"])."/";
                   }

                IF (ISSET($post_id_arr["id_event"]))
                   {
                    $sql_kol[] = "id_event"; 
                    $sql_val[] = "'".ValidateVarFun::f_real_escape_string($post_id_arr["id_event"])."'"; 
                   }

                IF (ISSET($post_id_arr["share"]))
                   {
                    $sql_kol[] = "share"; 
                    $sql_val[] = "'".ValidateVarFun::f_real_escape_string($post_id_arr["share"])."'"; 
                   }

                $sql_kol[] = "description"; 
                $sql_val[] = "'".ValidateVarFun::f_real_escape_string($description)."'"; 

                $sql_kol[]   = "file_name"; 
                $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($file_name)."'"; 

                $sql_kol[]   = "file_type"; 
                $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($file_type)."'"; 

                $sql_kol[]   = "file_size"; 
                $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($file_size)."'"; 
            
                $file_path  .= $post_id_doc.'_'.$file_name;
                $sql_kol[]   = "file_path"; 
                $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($file_path)."'"; 
            
                $sql_kol[]   = "record_user"; 
                $sql_val[]   = "'".$session->Vars["ses_userid"]."'"; 
            
                $sql_kol[]   = "record_timestamp"; 
                $sql_val[]   = "'".DATE("Y-m-d H:i:s")."'"; 
            
                $sql_kol_ins = IMPLODE(",", $sql_kol);
                $sql_val_ins = IMPLODE(",", $sql_val);
                
                $sql_ins     = "REPLACE INTO phi_docs (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                WebApp::execQuery($sql_ins);
            
                //phi_docs_fulltext_search -------------------------------------------------------------------
                  $sql_kol     = null; 
                  $sql_val     = null;
            
                  $sql_kol[]   = "id_doc"; 
                  $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($post_id_doc)."'"; 

                  $sql_kol[]   = "description"; 
                  $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($description)."'"; 

                  $sql_kol[]   = "file_text"; 
                  $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($file_text)."'"; 

                  $sql_kol[]   = "record_user"; 
                  $sql_val[]   = "'".ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"])."'"; 
            
                  $sql_kol[]   = "record_timestamp"; 
                  $sql_val[]   = "'".DATE("Y-m-d H:i:s")."'"; 
            
                  $sql_kol_ins = IMPLODE(",", $sql_kol);
                  $sql_val_ins = IMPLODE(",", $sql_val);
                
                  $sql_ins     = "REPLACE INTO phi_docs_fulltext_search (".$sql_kol_ins.") VALUES(".$sql_val_ins.")";
                  WebApp::execQuery($sql_ins);
                //phi_docs_fulltext_search -------------------------------------------------------------------
               }
            ELSE
               {
                //RASTI KUR PO UPDEJTOHET VETEM SKEDARI ------------------------------------------------------
                  //FSHIME SKEDARIN E VJETER NGA FILE SYSTEM -------------------------------------------------
                    $sql = "SELECT file_path AS file_path_old
                              FROM phi_docs 
                             WHERE id_doc = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
                           ";
                    
                    $rs = WebApp::execQuery($sql);
                    IF (!$rs->EOF())
                       {
                        $file_path_old = $rs->Field("file_path_old");

                        //FSHIHET SKEDARI NGA FILE SYSTEM ----------------------------------------------------
                          $file_name_disk    = PATH_ROOT_DOCS.$file_path_old;
                          
                          $cmd_delete_file   = USER_SUDO.PATH_SCRIPT_DELETE_FILE." ".$file_name_disk;
                          //$doc_delete_sukses = passthru($cmd_delete_file);
                          exec($cmd_delete_file, $doc_delete_sukses);
                        //FSHIHET SKEDARI --------------------------------------------------------------------
                       }
                  //FSHIME SKEDARIN E VJETER NGA FILE SYSTEM -------------------------------------------------
                  
                  //UPDEJTOJME ATRIBUTET E SKEDARIT TE RI ----------------------------------------------------
                    $file_path =  ValidateVarFun::f_only_all_natural_numbers($post_id_branch)."/";

                    IF (ISSET($post_id_arr["id_reporting_entity"]))
                       {
                        $file_path .=  ValidateVarFun::f_only_numbers($post_id_arr["id_reporting_entity"])."/";
                       }
            
                    IF (ISSET($post_id_arr["id_doc_category"]))
                       {
                        $file_path .=  ValidateVarFun::f_only_numbers($post_id_arr["id_doc_category"])."/";
                       }
                    
                    $file_path  .= $post_id_doc.'_'.$file_name;
                
                    $sql_upd     = "UPDATE phi_docs
                                       SET file_name        = '".ValidateVarFun::f_real_escape_string($file_name)."',
                                           file_type        = '".ValidateVarFun::f_real_escape_string($file_type)."',
                                           file_size        = '".ValidateVarFun::f_real_escape_string($file_size)."',
                                           file_path        = '".ValidateVarFun::f_real_escape_string($file_path)."',
                                           record_user      = '".ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"])."',
                                           record_timestamp = '".DATE("Y-m-d H:i:s")."'
                                     WHERE id_doc           = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
                                   ";
                    WebApp::execQuery($sql_upd);

                    $sql_upd     = "UPDATE phi_docs_fulltext_search
                                       SET file_text = '".ValidateVarFun::f_real_escape_string($file_text)."'
                                     WHERE id_doc    = '".ValidateVarFun::f_real_escape_string($post_id_doc)."'
                                   ";
                    WebApp::execQuery($sql_upd);
                  //UPDEJTOJME ATRIBUTET E SKEDARIT TE RI ----------------------------------------------------
                //RASTI KUR PO UPDEJTOHET VETEM SKEDARI ------------------------------------------------------
               }
          //RUAJME ATRIBUTET E SKEDARIT ----------------------------------------------------------------------


          IF (($file_path != "") AND ($post_id_doc > 0))
             {
              //ATRIBUTET E DOKUMENTIT JANE RUAJTUR NE REGULL ... KESHTU QE E RUAJME ATE FIZIKISHT NE PATHIN QE NA KA PERCAKTUAR PROCEDURA
                $file_name_disk = PATH_ROOT_DOCS.$file_path;

                //SHKRUHET SKEDARI SI NJE I TERE ---------------------------------------------------------
                  /*
                  //lexohet permbajtja e skedarit --------------------------------------------------------
                    $path_file_read = $_FILES["file"]["tmp_name"];
                    $fd             = fopen($path_file_read, 'rb');
                    IF ($fd === false) return false;
                    $file_data = fread($fd, FILESIZE($path_file_read));
                    fclose($fd);
                  //lexohet permbajtja e skedarit --------------------------------------------------------

                  //shkruhet skedari ---------------------------------------------------------------------
                    $fp = fopen($file_name_disk, 'w');
                    IF ($fp)
                       {
                        fwrite($fp, $file_data);
                        fclose($fp);
                        $html_body = "Dokumenti u ngarkua me sukses";
                       }
                    ELSE
                       {
                        $html_body = "Deshtoi shkrimi i skedarit!";
                       }
                  //shkruhet skedari ---------------------------------------------------------------------
                  */
                //SHKRUHET SKEDARI SI NJE I TERE ---------------------------------------------------------

                //SHKRUHET SKEDARI ME PORCIONE, E TESTOVA DHE ECEN OK ------------------------------------
                  /* 
                  $doc_write_sukses = "0";
                  $path_file_read   = $_FILES["file"]["tmp_name"];

                  $fp_read  = FOPEN($path_file_read, 'rb');
                  $fp_write = FOPEN($file_name_disk, 'w');

                  IF (($fp_read) AND ($fp_write))
                     {
                      WHILE (!feof($fp_read)) 
                            {
                             $buffer = FREAD($fp_read, 4096);
                             FWRITE($fp_write, $buffer);
                             
                             $doc_write_sukses = "1";
                            }

                      FCLOSE($fp_read);
                      FCLOSE($fp_write);               
                     }
                  */
                //SHKRUHET SKEDARI ME PORCIONE -----------------------------------------------------------

                //KOPJOHET SKEDARI -----------------------------------------------------------------------
                  CHMOD($_FILES["file"]["tmp_name"], 0644); //ndryshojme te drejtat e skedarit qe ta lexoje user aspcache
                  $cmd_copy_file    = USER_SUDO.PATH_SCRIPT_COPY_FILE." ".$_FILES["file"]["tmp_name"]." ".$file_name_disk." N";
                 
                  //$doc_write_sukses = passthru($cmd_copy_file);
                  //IF ($doc_write_sukses == "1")
                  //   {
                  //     //$html_body = "Dokumenti u ngarkua me sukses";
                  //   }
                  //ELSE
                  //   {
                  //    //$html_body = "Deshtoi shkrimi i skedarit!"; 
                  //   }

                  exec($cmd_copy_file, $doc_write_sukses);
                  IF ($doc_write_sukses[0] == "1")
                     {
                      IF ($file_size > 1048576)
                         {
                          $file_size_print = ROUND($file_size / 1048576, 2)." MB";
                         }
                      ELSE
                         {
                          $file_size_print = ROUND($file_size / 1024,    2)." KB";
                         }
                      
                      //PRINT $post_id_doc;
                      $id_doc_sel = SERIALIZE($doc_attrib);
					  $id_sel     = f_app_encrypt($id_doc_sel.'|'.$NEM_ID_SEL, DESK_KEY, DESK_IV);
                      PRINT $id_sel.'|'.$file_name.' ('.$file_size_print.')';
                      EXIT;
                     }
                  ELSE
                     {
                      PRINT "-1";
                      EXIT;
                     }
                //KOPJOHET SKEDARI -----------------------------------------------------------------------

              //ATRIBUTET E DOKUMENTIT JANE RUAJTUR NE REGULL ... KESHTU QE E RUAJME ATE FIZIKISHT NE PATHIN QE NA KA PERCAKTUAR PROCEDURA
             }
         }
     }
//SHKRUAJME SKEDARIN -----------------------------------------------------------------------------------------
?>