<?php

function f_app_select_form_table($tab)
{
//############################################ HELP #############################################################################
/*
    unset($tab);
    $tab["tab_name"]     = "project";            	//*emri i tabeles ku do behet select
    $tab["kol_filter"]   = "";                      //default = ''(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur nuk te interesojne disa kolona i vendos emrat e kolonave te ndara me presjeve; zakonisht perdoret per te filtruar fushat e tipit blob;
    $tab["kol_select"]   = "";                      //default = ''(bosh); kur i do gjithe kolonat e tabeles lihet bosh; kur te interesojne vetem disa kolona i vendos emrat e kolonave te ndara me presjeve;
    $tab["kol_order"]    = "";                      //default = '', pra rekordet nuk renditen sipas ndonje kolone; emri i kolones sipas se ciles do renditen rekordet;
    $tab["kol_asc_desc"] = "";                      //default = 'ASC'; pranon vlerat ASC, DESC; meret parasysh kur $tab["kol_order"] != '';
    $tab["sql_where"]    = "";                      //default = '', kthen gjithe rekordet pa filtrim; perndryshe shkruani kushtin e filtrimit, mos haroni fjalen WHERE;
    $tab["nr_rec_tot"]   = "T";                     //default = 'F', (FALSE); pranon vlerat T,F; kur eshte True kthen dhe numrin total te rekordeve qe kthen selekti;
    $tab["rec_limit"]    = "0,1";                   //default = '', pra pa limit; perndryshe kthen ato rekorde qe jane percaktuar ne limit, formati = 0,10;
    $tab["is_form"]      = "T";                     //default = 'T'; pranon vlerat T,F;
    $tab["obj_class"]    = "";            			//default = 'txtbox'; emri i klases ne style, vlen kur $tab["is_form"] = 'T';
    $tab["distinct"]     = "F";      				//default = 'F' -> pranon vlerat T,F (pra true ose false);
*/
//############################################ HELP #############################################################################

    //DATA TYPE -------------------------------------------------------------------------------------------
      $type_is_number   = ",tinyint,smallint,mediumint,int,integer,bigint,real,double,float,decimal,numeric,";
      $type_is_integer  = ",tinyint,smallint,mediumint,int,integer,bigint,";
      $type_is_char     = ",char,varchar,";
      $type_is_text     = "tinyblob,blob,mediumblob,longblob,tinytext,text,mediumtext,longtext,";
      $type_is_date     = ",date,";
      $type_is_datetime = ",timestamp,datetime,"; //,time
    //----------------------------------------------------------------------------------------------------

    IF (!isset($tab["kol_asc_desc"]) OR ($tab["kol_asc_desc"]==""))
       {$tab["kol_asc_desc"] = "ASC";}

    IF (!isset($tab["is_form"]) OR ($tab["is_form"]==""))
       {$tab["is_form"] = "T";}

    IF (!isset($tab["obj_class"]) OR ($tab["obj_class"]==""))
       {$tab["obj_class"] = "form-control input-xs";}

    IF (isset($tab["kol_filter"]) AND ($tab["kol_filter"]!=""))
       {$tab["kol_filter"] = ",".$tab["kol_filter"].",";}
    ELSE
       {$tab["kol_filter"] = "";}

    IF (isset($tab["kol_select"]) AND ($tab["kol_select"]!=""))
       {$tab["kol_select"] = ",".$tab["kol_select"].",";}
    ELSE
       {$tab["kol_select"] = "";}


    $nr_kol = -1;

    $sql_select = "SHOW COLUMNS FROM ".$tab["tab_name"];
    $rs = WebApp::execQuery($sql_select);
    $rs->MoveFirst();
    while (!$rs->EOF())
          {
           $kol_name = $rs->Field("Field");
           $kol_type = $rs->Field("Type");
           $kol_null = $rs->Field("Null");

           $kol_name_eregi = ",".$kol_name.",";

           $kolona_te_selektohet = 1;
           IF (stristr($tab["kol_filter"], $kol_name_eregi))
              {$kolona_te_selektohet = 0;}

           IF (($tab["kol_select"] !="") AND (!$tab["kol_filter"]($tab["kol_select"], $kol_name_eregi)))
              {$kolona_te_selektohet = 0;}

           IF ($kolona_te_selektohet == 1)
              {
               $nr_kol = $nr_kol + 1;

               $kol[$nr_kol]["name"] = $kol_name;
               $kol[$nr_kol]["type"] = $kol_type;
               $kol[$nr_kol]["null"] = $kol_null;

               $etiketa                    = "{{".$kol_name."_mesg}}";
               $val[$kol_name]["label"]    = $etiketa;

               IF ($tab["is_form"] == "T")
                  {
                   $val[$kol_name]["obj_name"]  = " name='".$kol_name."'";
                   $val[$kol_name]["obj_class"] = " class='".$tab["obj_class"]."'";
                   $val[$kol_name]["etiketa"]   = " etiketa='".$etiketa."'";

                   //formohen regullat e validimeve ------------------------------
                     $var_isnull         = 0;
                     $var_isnumber       = 0;
                     $var_isalpha        = 0;
                     $var_isdate         = 0;
                     $var_isemailaddress = 0;
                     $var_isinteger      = 0;

                     $data_type_array = explode(" ", $kol_type);
                     $data_type       = $data_type_array[0];

                     IF (preg_match('/(.*)\((.*)\)/i',  $data_type, $res))
                        {
                         $kol_type_eregi                 = ",".$res[1].",";
                         //$val[$kol_name]["field_type"]   = $res[1];
                         //$val[$kol_name]["field_length"] = $res[2];
                         $val[$kol_name]["obj_maxlength"] = " maxlength='".$res[2]."'";
                        }
                     ELSE
                        {$kol_type_eregi                 = ",".$data_type.",";
                         //$val[$kol_name]["field_type"]   = $data_type;
                         //$val[$kol_name]["field_length"] = 65000;
                         $val[$kol_name]["obj_maxlength"] = " maxlength='65000'";
                         IF (stristr($type_is_datetime, $kol_type_eregi))
                            {
                             //$val[$kol_name]["field_length"] = 19;
                             $val[$kol_name]["obj_maxlength"] = " maxlength='19'";
                            }

                         IF (stristr($type_is_date, $kol_type_eregi))
                            {
                             //$val[$kol_name]["field_length"] = 10;
                             $val[$kol_name]["obj_maxlength"] = " maxlength='10'";
                            }
                        }

                     IF ($kol_null != "YES")
                        {$var_isnull = 1;
                         $val[$kol_name]["label"] = "*".$etiketa;}

                     IF (stristr($type_is_number, $kol_type_eregi))
                        {
                         $var_isnumber = 1;
                         IF (stristr($kol_type, "unsigned"))
                            {$var_isnumber = 2;}
                        }
                     IF (stristr($type_is_integer, $kol_type_eregi))
                        {$var_isinteger = 1;}

                     IF (stristr($type_is_date, $kol_type_eregi))
                        {$var_isdate = 1;}

                     IF (stristr($kol_name, "email"))
                        {$var_isemailaddress = 1;}

                     $val[$kol_name]["valid_js"] = " valid='".$var_isnull.",".$var_isnumber.",".$var_isalpha.",".$var_isdate.",".$var_isemailaddress.",".$var_isinteger."'";
                   //-------------------------------------------------------------
                  }
              }
           $rs->MoveNext();
          }

    //pregatitet sql per te mare vlerat --------------------------------------
      $sql_select = "";
      FOR ($i=0; $i <= $nr_kol; $i++)
          {
           IF ($kol[$i]["null"] == "YES")
              {$sql_select .= ", IF(".$kol[$i]["name"]." IS NULL, '',".$kol[$i]["name"].") AS ".$kol[$i]["name"];}
           ELSE
              {$sql_select .= ",".$kol[$i]["name"];}
          }

      $sql_select       = SUBSTR($sql_select, 1);

      IF ($tab["distinct"] == "T")
         {
          $sql_select       = "SELECT DISTINCT ".$sql_select." FROM ".$tab["tab_name"]." ";
          $sql_select_count = "SELECT COUNT(DISTINCT ".$sql_select.") as nr_rec_tot FROM ".$tab["tab_name"]." ";
         }
      ELSE
         {
          $sql_select       = "SELECT ".$sql_select." FROM ".$tab["tab_name"]." ";
          $sql_select_count = "SELECT COUNT(*) as nr_rec_tot FROM ".$tab["tab_name"]." ";
         }

      IF ($tab["sql_where"] != "")
         {
          $sql_select       .= " ".$tab["sql_where"];
          $sql_select_count .= " ".$tab["sql_where"];
         }
      IF ($tab["kol_order"] != "")
         {$sql_select       .= " ORDER BY ".$tab["kol_order"]." ".$tab["kol_asc_desc"];}

      IF ($tab["rec_limit"] != "")
         {$sql_select       .= " LIMIT ".$tab["rec_limit"];}
    //------------------------------------------------------------------------

    IF ($tab["nr_rec_tot"] == "T")
       {
        $rs = WebApp::execQuery($sql_select_count);
        IF (!$rs->EOF())
           {$val["nr_rec_tot"] = $rs->Field("nr_rec_tot");}
       }

    IF ($nr_kol >= 0)
       {
        //inicializohen vlerat bosh kur jemi ne form ------------------------
          IF ($tab["is_form"] == "T")
             {
              FOR ($i=0; $i <= $nr_kol; $i++)
                  {$val[$kol[$i]["name"]][0]["vl"] = "";}
             }
        //===================================================================

        $nr_val = 0;
        $rs = WebApp::execQuery($sql_select);
        $rs->MoveFirst();
        while (!$rs->EOF())
              {
               FOR ($i=0; $i <= $nr_kol; $i++)
                   {
                    $val[$kol[$i]["name"]][$nr_val]["vl"] = $rs->Field($kol[$i]["name"]);

                    //per fushat e tipit date, datetime, timestamp ------------------------------------------------------------
                      IF (
                          ((stristr($kol[$i]["type"], "date")) AND ($val[$kol[$i]["name"]][$nr_val]["vl"] != ""))
                         )
                         {
                          $vlf  = substr($val[$kol[$i]["name"]][$nr_val]["vl"], 8, 2).".".substr($val[$kol[$i]["name"]][$nr_val]["vl"], 5, 2).".".substr($val[$kol[$i]["name"]][$nr_val]["vl"], 0, 4);
                          $val[$kol[$i]["name"]][$nr_val]["vlf"] = $vlf;
                         }
                      IF (
                          ((stristr($kol[$i]["type"], "datetime")) AND ($val[$kol[$i]["name"]][$nr_val]["vl"] != ""))
                          OR
                          ((stristr($kol[$i]["type"], "timestamp")) AND ($val[$kol[$i]["name"]][$nr_val]["vl"] != ""))
                         )
                         {
                          $vlf    = substr($val[$kol[$i]["name"]][$nr_val]["vl"], 8, 2).".".substr($val[$kol[$i]["name"]][$nr_val]["vl"], 5, 2).".".substr($val[$kol[$i]["name"]][$nr_val]["vl"], 0, 4);
                          $vlf_dt = substr($val[$kol[$i]["name"]][$nr_val]["vl"], 8, 2).".".substr($val[$kol[$i]["name"]][$nr_val]["vl"], 5, 2).".".substr($val[$kol[$i]["name"]][$nr_val]["vl"], 0, 4)." ".substr($val[$kol[$i]["name"]][$nr_val]["vl"], 11, 8);
                          $val[$kol[$i]["name"]][$nr_val]["vlf"]    = $vlf;
                          $val[$kol[$i]["name"]][$nr_val]["vlf_dt"] = $vlf_dt;
                         }
                    //per fushat e tipit date, datetime, timestamp ------------------------------------------------------------
                   }

               $nr_val = $nr_val + 1;
               $rs->MoveNext();
              }

        $val["nr_rec"] = $nr_val;
       }

    RETURN $val;
  //----------------------------------------------------------------------------------------------------
}


//############################################# f_app_select_form_table ##########################################################\\
//######################################################################################################################\\
//##################################################### lov ##############################################################\\


function f_app_lov($lov)
  {
//############################################ HELP #############################################################################
/*
    unset($lov);
    $lov["name"]           = "procesi";     //DS -> sherben vetem per funksionin lov_default_values();
    $lov["obj_or_label"]   = "object";      //DS -> sherben vetem per funksionin lov_name_default_values(); pranon vetem vlerat object, label; default = object;
    $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
    $lov["object_name"]    = "id_proces";   //*DS -> emri i objektit LOV;
    $lov["tab_name"]       = "proces";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
    $lov["id"]             = "id_proces";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
    $lov["field_name"]     = "name_proces"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

    $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, select-multiple, checkbox, radio; default = select;
    $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

    $lov["filter"]         = "WHERE id_proces < 30";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
    $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus

    $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
    $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

    $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default select = 'T', default select-multiple = 'F'; ka kuptim vetem per $lov["layout_object"]  = "select","select-multiple" ;
    $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
    $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

    $lov["id_select"]      = "1";           //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
    $lov["order_by"]       = "name_proces"; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
    $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
    $lov["alert_etiketa"]  = "Serveri";     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
    $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
    $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
    $lov["width"]          = "300";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
    $lov["height"]         = "60";          //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
    
    
    $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
    $lov["isdate"]         = "";            //D   -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
    $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
    $lov["triger_function"]= "";            //DS  -> emri i funksionit qe do trigerohet;

    $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
    $lov['tabindex']       = '';            //tabindex ne html vetem numri 1 ose 12
*/
//############################################ HELP #############################################################################

   $lov_array_id   = "";
   $lov_array_name = "";

   $lov_alert_etiketa = "";
   IF (isset($lov["alert_etiketa"]) AND ($lov["alert_etiketa"]!=""))
      {$lov_alert_etiketa .= "etiketa='".$lov["alert_etiketa"]."'";}

   $lov_valid = "";
   IF (isset($lov["valid"]) AND ($lov["valid"]!=""))
      {$lov_valid .= "valid='".$lov["valid"]."'";}

   $disabled = "";
   IF ($lov["disabled"] == "T")
      {$disabled = " disabled";}

   IF (!isset($lov["layout_object"]) OR ($lov["layout_object"]==""))
      {$lov["layout_object"] = "select";}

   IF (!isset($lov["ndares_vlerash"]) OR ($lov["ndares_vlerash"]==""))
      {$lov["ndares_vlerash"] = ",";}

   $triger_function = "";
   IF (ISSET($lov["triger_function"]) AND ($lov["triger_function"] != ""))
      {$triger_function = $lov["triger_function"];}

   $element_id = "";
   IF (ISSET($lov["element_id"]) AND ($lov["element_id"] != ""))
      {$element_id = " id='".$lov["element_id"]."' ";}
   ELSE
      {$element_id = " id='".$lov["object_name"]."' ";}
   
   $tabindex = '';
   IF (ISSET($lov['tabindex']) AND ($lov['tabindex'] != ''))
      {$tabindex = ' tabindex="'.$lov['tabindex'].'" ';}

   //PER LISTEN DINAMIKE ---------------------------------------------------------------------------------------------------------
     IF ($lov["type"] == "dinamik")
        {
         IF (!isset($lov["order_by"]) OR ($lov["order_by"]==""))
            {$lov["order_by"] = $lov["field_name"];}

         $filter = "";
         IF (ISSET($lov["filter"]) AND ($lov["filter"] != ""))
            {
             $filter = $lov["filter"];
             IF (ISSET($lov["filter_plus"]) AND ($lov["filter_plus"] != ""))
                {$filter .= " ".$lov["filter_plus"];}
            }

         $lov_distinct = "";
         IF (ISSET($lov["distinct"]) AND ($lov["distinct"] == "T"))
            {$lov_distinct = "DISTINCT";}

         IF (!isset($lov["asc_desc"]) OR ($lov["asc_desc"]==""))
            {$lov["asc_desc"] = "ASC";}

         IF ($lov["isdate"] == "T")
            {$sql = "SELECT ".$lov_distinct." ".$lov["id"]." as id_as, IF(".$lov["field_name"]." IS NULL, '',DATE_FORMAT(".$lov["field_name"].",'%d.%m.%Y')) AS fusha_as_as, ".$lov["field_name"]." as field_name_order FROM ".$lov["tab_name"]." ".$filter." ORDER BY field_name_order ".$lov["asc_desc"];}
         ELSE
            {$sql = "SELECT ".$lov_distinct." ".$lov["id"]." as id_as, IF(".$lov["field_name"]." IS NULL, '',".$lov["field_name"].") AS fusha_as_as FROM ".$lov["tab_name"]." ".$filter." ORDER BY ".$lov["order_by"]." ".$lov["asc_desc"];}

         //print $sql;
         $i  = -1;
         $rs = WebApp::execQuery($sql);
         $rs->MoveFirst();
         while (!$rs->EOF())
               {
                $id   = $rs->Field("id_as");
                $name = $rs->Field("fusha_as_as");

                $i                  = $i + 1;
                $lov_array_id[$i]   = $id;
                $lov_array_name[$i] = $name;

                $rs->MoveNext();
               }
        }
   //PER LISTEN DINAMIKE =========================================================================================================

   //PER LISTEN STATIKE ----------------------------------------------------------------------------------------------------------
     IF ($lov["type"] == "statik")
        {
         $lov_array_id   = EXPLODE($lov["ndares_vlerash"], $lov["id"]);
         $lov_array_name = EXPLODE($lov["ndares_vlerash"], $lov["field_name"]);
        }
   //PER LISTEN STATIKE ==========================================================================================================

   //FORMOHET HTML E LOV ---------------------------------------------------------------------------------------------------------
     IF ((($lov["layout_object"] == "checkbox") OR ($lov["layout_object"] == "radio")) AND ($lov["only_options"] == "Y"))
        {
         $lov_html = array();
        }
     ELSE
        {
         $lov_html = "";
        }
        
     IF (($lov["layout_object"] == "select") OR ($lov["layout_object"] == "select-multiple"))
        {
         IF (!isset($lov["class"]) OR ($lov["class"]==""))
            {$lov["class"] = "form-control input-xs";}

         IF (!isset($lov["null_print"]) OR ($lov["null_print"]==""))
            {
             IF ($lov["layout_object"] == "select-multiple")
                {$lov["null_print"] = "F";}
             ELSE
                {$lov["null_print"] = "T";}
            }
         
         
         IF (!isset($lov["null_id"]))
            {$lov["null_id"] = "";}
         IF (!isset($lov["null_etiketa"]))
            {$lov["null_etiketa"] = "";}

         $lov_style = "";
         IF ((isset($lov["width"]) AND ($lov["width"]!="")) OR (isset($lov["height"]) AND ($lov["height"]!="")))
            {
             $lov_style .= "style='";
             IF (isset($lov["width"]) AND ($lov["width"]!=""))
                {$lov_style .= "width:".$lov["width"]."px;";}
             IF (isset($lov["height"]) AND ($lov["height"]!=""))
                {$lov_style .= "height:".$lov["height"]."px;";}
             $lov_style .= "'";
            }

         $multiple = "";
         IF ($lov["layout_object"] == "select-multiple")
            {$multiple = "MULTIPLE";}
         
         
         IF ($lov["only_options"] == "Y")
            {
             //select nuk e perfshime
            }
         ELSE
            {
             $lov_html = "<select ".$multiple." name='".$lov["object_name"]."' ".$disabled." class='".$lov["class"]."' ".$lov_valid." ".$lov_alert_etiketa." ".$lov_style." ".$triger_function." ".$element_id." ".$tabindex.">";
            }
         
         IF ($lov["null_print"] == "T")
            {$lov_html .= "<option value='".$lov["null_id"]."'>".$lov["null_etiketa"]."</option>";}

         //per rastin kur SELECT_ONE perdoret si select-multiple shtojme nje rresht ne fund te LOV; -- 
           $nr_id_select   = 0;
           $name_id_select = "";
         //===========================================================================================
         $id_select_txt = $lov["ndares_vlerash"].$lov["id_select"].$lov["ndares_vlerash"];
         FOR ($i=0; $i < count($lov_array_id); $i++)
             {
              $lov_array_id_txt = $lov["ndares_vlerash"].$lov_array_id[$i].$lov["ndares_vlerash"];

              IF (stristr($id_select_txt, $lov_array_id_txt))
                 {
                  $selected_txt    = " selected";
                  $nr_id_select    = $nr_id_select + 1;
                  $name_id_select .= " | ".$lov_array_name[$i];
                 }
              ELSE
                 {$selected_txt = "";}

              $lov_html .= "<option value='".$lov_array_id[$i]."'".$selected_txt.">".$lov_array_name[$i]."</option>";
             }

         //per rastin kur SELECT_ONE perdoret si select-multiple shtojme nje rresht ne fund te LOV; ------- 
           IF (($lov["layout_object"] == "select") AND ($nr_id_select > 1))
              {
               //po e komentoje kete se do perdor element te tjere
               //$lov_html .= "<option value='".$lov["id_select"]."' selected>".SUBSTR($name_id_select,3)."</option>";
              }
         //================================================================================================
           
         IF ($lov["only_options"] == "Y")
            {
             //select nuk e perfshime
            }
         ELSE
            {
             $lov_html .= "</select>";
            }
        }

     IF (($lov["layout_object"] == "checkbox") OR ($lov["layout_object"] == "radio"))
        {
         IF (!isset($lov["class"]) OR ($lov["class"]==""))
            {$lov["class"] = "";}
         ELSE
            {$lov["class"] = "class='".$lov["class"]."'";}

         IF (!isset($lov["class_etiketa"]) OR ($lov["class_etiketa"]==""))
            {$lov["class_etiketa"] = "";}
         ELSE
            {$lov["class_etiketa"] = "class='".$lov["class_etiketa"]."'";}

         IF (!isset($lov["layout_forma"]) OR ($lov["layout_forma"]==""))
            {$lov["layout_forma"] = "<br>";}

         IF ($lov["layout_forma"] == "table")
            {$lov_html .= "<Table border=0 cellspacing=0 cellpadding=0>";}


         $id_select_txt = $lov["ndares_vlerash"].$lov["id_select"].$lov["ndares_vlerash"];
         FOR ($i=0; $i < count($lov_array_id); $i++)
             {
              $lov_array_id_txt = $lov["ndares_vlerash"].$lov_array_id[$i].$lov["ndares_vlerash"];

              IF (stristr($id_select_txt, $lov_array_id_txt))
                 {$selected_txt = " checked";}
              ELSE
                 {$selected_txt = "";}

              IF ($lov["only_options"] == "Y")
                 {
                  $lov_html[$lov_array_id[$i]]["name"]    = $lov_array_name[$i];
                  $lov_html[$lov_array_id[$i]]["checked"] = SUBSTR($selected_txt, 1);
                 }
              ELSE
                 {
                  $lov_element_id = "<input type='".$lov["layout_object"]."' ".$lov["class"]." name='".$lov["object_name"]."' value='".$lov_array_id[$i]."' ".$lov_valid." ".$lov_alert_etiketa." ".$selected_txt." ".$disabled." ".$triger_function." ".$element_id." ".$tabindex.">";

                  IF ($lov["layout_forma"] == "table")
                     {
                      $lov_html .= "<TR>";
                      $lov_html .= "<TD>".$lov_element_id."</TD>";
                      $lov_html .= "<TD ".$lov["class_etiketa"].">".$lov_array_name[$i]."</TD>";
                      $lov_html .= "</TR>";
                     }
                  ELSE
                     {
                      IF ($i == 0)
                         {$lov_html .= $lov_element_id.$lov_array_name[$i];}
                      ELSE
                         {$lov_html .= $lov["layout_forma"].$lov_element_id.$lov_array_name[$i];}
                     }
                 }
             }
         
         IF ($lov["layout_forma"] == "table")
            {
             IF ($lov["only_options"] == "Y")
                {
                 //
                }
             ELSE
                {
                 $lov_html .= "</Table>";
                }
            }
        }
   //FORMOHET HTML E LOV =========================================================================================================

   RETURN $lov_html;
  }

//##################################################### lov ##########################################################\\
//######################################################################################################################\\
//############################################# lov_return_label ##############################################################\\

function f_app_lov_return_label($lov)
  {
//############################################ HELP #############################################################################
/*
    $lov["name"]           = "procesi";    			//DS  -> sherben vetem per funksionin lov_name_default_values();
    $lov["obj_or_label"]   = "label";      			//DS  -> sherben vetem per funksionin lov_name_default_values(); pranon vetem vlerat object, label; default = object;
    $lov["type"]           = "dinamik";     		//*DS -> pranon vlerat dinamik, statik;
    $lov["tab_name"]       = "proces";      		//*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
    $lov["id"]             = "id_proces";   		//*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
    $lov["field_name"]     = "name_proces"; 		//*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

    $lov["layout_forma"]   = "";            		//DS -> ka kuptim vetem kur kthehet me shume se nje etikete; ne rast se vendoset vlera 'table' elementet strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>; ;

    $lov["distinct"]       = "F";           		//D  -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;

    $lov["filter"]         = "WHERE id_proces < 30";//D  -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE'); kur kjo ka vlere $lov["id_select"] i shtohet si fitrim shtese;
    $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus

    $lov["id_select"]      = "1";           		//DS -> vlerat e kodit per te cilat duhet te meren emertimet te ndara me ndaresin perkates default ,(presje); bosh kur i duam te gjitha vlerat; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje; selektimi behet me id IN ($lov["id_select"]), per id te tipit stringe mos haroni ti mbyllni ne thonjeza teke;
    $lov["order_by"]       = "name_proces"; 		//D  -> emri i kolones qe do perdoret per order, default = $lov["name"] | per tipin="statik" sduhet;

    $lov["ndares_vlerash"] = ",";           		//S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
    $lov["class_etiketa"]  = "";            		//DS -> emri i klases ne style per etiketat, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
    $lov["isdate"]         = "";            		//D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
    $lov["asc_desc"]       = "";            		//D  -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
*/
//###############################################################################################################################

   $lov_array_id   = "";
   $lov_array_name = "";

   IF (!isset($lov["ndares_vlerash"]) OR ($lov["ndares_vlerash"]==""))
      {$lov["ndares_vlerash"] = ",";}

   //PER LISTEN DINAMIKE ---------------------------------------------------------------------------------------------------------
     IF ($lov["type"] == "dinamik")
        {
         IF (!isset($lov["order_by"]) OR ($lov["order_by"]==""))
            {$lov["order_by"] = $lov["field_name"];}

         IF (!isset($lov["asc_desc"]) OR ($lov["asc_desc"]==""))
            {$lov["asc_desc"] = " ASC";}

         $filter = "";
         IF (ISSET($lov["filter"]) AND ($lov["filter"] != ""))
            {
             $filter = $lov["filter"];
             
             IF (ISSET($lov["filter_plus"]) AND ($lov["filter_plus"] != ""))
                {$filter .= " ".$lov["filter_plus"];}
            }

         IF ((isset($lov["id_select"])) AND ($lov["id_select"] != ""))
            {
             IF ($filter == "")
                {$filter  = " WHERE ".$lov["id"]." IN (".$lov["id_select"].") ";}
             ELSE
                {$filter .= " AND ".$lov["id"]." IN (".$lov["id_select"].") ";}
            }

         $lov_distinct = "";
         IF (ISSET($lov["distinct"]) AND ($lov["distinct"] == "T"))
            {$lov_distinct = "DISTINCT";}

         IF ($lov["isdate"] == "T")
            {$sql = "SELECT ".$lov_distinct." ".$lov["id"]." as id_as, IF(".$lov["field_name"]." IS NULL, '',DATE_FORMAT(".$lov["field_name"].",'%d.%m.%Y')) AS fusha_as_as, ".$lov["field_name"]." as field_name_order FROM ".$lov["tab_name"]." ".$filter." ORDER BY field_name_order ".$lov["asc_desc"];}
         ELSE
            {$sql = "SELECT ".$lov_distinct." ".$lov["id"]." as id_as, IF(".$lov["field_name"]." IS NULL, '',".$lov["field_name"].") AS fusha_as_as FROM ".$lov["tab_name"]." ".$filter." ORDER BY ".$lov["order_by"]." ".$lov["asc_desc"];}

         $i  = -1;
         $rs = WebApp::execQuery($sql);
         $rs->MoveFirst();
         while (!$rs->EOF())
               {
                $id                     = $rs->Field("id_as");
                $name                   = $rs->Field("fusha_as_as");

                $i                      = $i + 1;
                $lov_array_id[$i]       = $id;
                $lov_array_name[$i]     = $name;
                
                $lov_array_id_id[$id]   = $id;
                $lov_array_id_name[$id] = $name;

                $rs->MoveNext();
               }
        }
   //PER LISTEN DINAMIKE =========================================================================================================

   //PER LISTEN STATIKE ----------------------------------------------------------------------------------------------------------
     IF ($lov["type"] == "statik")
        {
         $lov_array_id_temp   = EXPLODE($lov["ndares_vlerash"], $lov["id"]);
         $lov_array_name_temp = EXPLODE($lov["ndares_vlerash"], $lov["field_name"]);

         IF ((ISSET($lov["id_select"])) AND ($lov["id_select"] != ""))
            {
             $kaloje_kushtin_stristr = "N";
            }
         ELSE
            {
             $kaloje_kushtin_stristr = "Y";
            }

         $k = -1;
         $id_select_txt = $lov["ndares_vlerash"].$lov["id_select"].$lov["ndares_vlerash"];
         FOR ($i=0; $i < count($lov_array_id_temp); $i++)
             {
              $lov_array_id_txt = $lov["ndares_vlerash"].$lov_array_id_temp[$i].$lov["ndares_vlerash"];

              IF (STRISTR($id_select_txt, $lov_array_id_txt) OR ($kaloje_kushtin_stristr == "Y"))
                 {
                  $k                                         = $k + 1;
                  $lov_array_id[$k]                          = $lov_array_id_temp[$i];
                  $lov_array_name[$k]                        = $lov_array_name_temp[$i];

                  $lov_array_id_id[$lov_array_id_temp[$i]]   = $lov_array_id_temp[$i];
                  $lov_array_id_name[$lov_array_id_temp[$i]] = $lov_array_name_temp[$i];
                 }
             }
        }
   //PER LISTEN STATIKE ==========================================================================================================

   //FORMOHET HTML E LOV ---------------------------------------------------------------------------------------------------------
     IF (($lov["all_data_array"] == "Y") OR ($lov["only_ids"] == "Y"))
        {
         IF ($lov["all_data_array"] == "Y")
            {
             $lov_html = $lov_array_id_name;
            }
         
         IF ($lov["only_ids"] == "Y")
            {
             $lov_html = $lov_array_id_id;
            }
        }
     ELSE
        {
         $lov_html = "";

         IF (!isset($lov["class_etiketa"]) OR ($lov["class_etiketa"]==""))
            {$lov["class_etiketa"] = "";}
         ELSE
            {$lov["class_etiketa"] = "class='".$lov["class_etiketa"]."'";}

         IF (!isset($lov["layout_forma"]) OR ($lov["layout_forma"]==""))
            {$lov["layout_forma"] = "<br>";}

         IF ($lov["layout_forma"] == "table")
            {$lov_html .= "<Table border=0 cellspacing=0 cellpadding=0>";}

         FOR ($i=0; $i < count($lov_array_id); $i++)
             {
              IF ($lov["layout_forma"] == "table")
                 {
                  $lov_html .= "<TR><TD ".$lov["class_etiketa"].">".$lov_array_name[$i]."</TD></TR>";
                 }
              ELSE
                 {
                  IF ($lov["layout_forma"] == "<br>")
                     {
                      IF ($i == 0)
                         {$lov_html .= $lov_array_name[$i];}
                      ELSE
                         {$lov_html .= $lov["layout_forma"].$lov_array_name[$i];}
                     }
                  ELSE
                     {
                      $lov_html .= $lov["layout_forma"].$lov_array_name[$i];
                     }
                 }
             }
             
         IF ($lov["layout_forma"] == "table")
            {
             $lov_html .= "</Table>";
            }
        
        }
   //FORMOHET HTML E LOV =========================================================================================================

   RETURN $lov_html;
  }
//############################################# lov_return_label ##########################################################\\
//######################################################################################################################\\


//############################################ lov_default_values ##############################################################\\
  //percaktohen vlerat default per fushen e zgjedhur... e dedikuar per cdo aplikim --------------------------------------------
    include(dirname(__FILE__)."/form_function_default.php");
//############################################ lov_default_values ##############################################################\\


?>