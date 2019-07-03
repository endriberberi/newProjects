<?php
function f_app_lov_default_values($lov_arg)
  {
    GLOBAL $session;

    $lov_parametrat[0]  = "type";
    $lov_parametrat[1]  = "object_name";
    $lov_parametrat[2]  = "tab_name";
    $lov_parametrat[3]  = "id";
    $lov_parametrat[4]  = "field_name";
    $lov_parametrat[5]  = "name";
    $lov_parametrat[6]  = "layout_object";
    $lov_parametrat[7]  = "layout_forma";
    $lov_parametrat[8]  = "filter";
    $lov_parametrat[9]  = "distinct";
    $lov_parametrat[10] = "disabled";
    $lov_parametrat[11] = "null_print";
    $lov_parametrat[12] = "null_id";
    $lov_parametrat[13] = "null_etiketa";
    $lov_parametrat[14] = "id_select";
    $lov_parametrat[15] = "order_by";
    $lov_parametrat[16] = "valid";
    $lov_parametrat[17] = "alert_etiketa";
    $lov_parametrat[18] = "ndares_vlerash";
    $lov_parametrat[19] = "class";
    $lov_parametrat[20] = "width";
    $lov_parametrat[21] = "class_etiketa";
    $lov_parametrat[22] = "isdate";
    $lov_parametrat[23] = "asc_desc";
    $lov_parametrat[24] = "triger_function";
    $lov_parametrat[25] = "height";
    $lov_parametrat[26] = "element_id";
    $lov_parametrat[27] = "filter_plus";
    $lov_parametrat[28] = "tabindex";
    $lov_parametrat[29] = "all_data_array";
    $lov_parametrat[30] = "only_options";
    $lov_parametrat[31] = "only_ids";
    

    //##########################################################################################################################\\
    //percaktohen vlerat default per fushen e zgjedhur... e dedikuar per cdo aplikim --------------------------------------------

      //Y_N ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "Y_N")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "status";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "Y_N";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "Y,N";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("yes_mesg").",".WebApp::getVar("no_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = ""; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = "Y_N";     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //record_status ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "record_status")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "record_status";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "record_status";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "1,0";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("aktiv_mesg").",".WebApp::getVar("inaktiv_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = ""; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = "status";     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_region ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_region")
         {
          $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_region";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_region";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_region";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_region_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_district ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_district")
         {
          $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_district";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_district";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_district";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_district_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_commune ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_commune")
         {
          $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_commune";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_commune";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_commune";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_commune_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }
      
      //id_district_commune ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_district_id_commune")
         {
          $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_district_id_commune";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_commune";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "CONCAT(id_district,'_X_',id_commune)";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_commune_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_village ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_village")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_village";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_village";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_village";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_village_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //district_commune_name --------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "district_commune_name")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_commune";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_commune INNER JOIN phi_district ON 
                                                phi_commune.id_district = phi_district.id_district 

                                   ";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "phi_commune.id_commune";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "CONCAT(phi_district.name, ' / ', phi_commune.name)";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_commune_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //district_commune_village ---------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "district_commune_village")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_village";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_village INNER JOIN phi_district ON 
                                                phi_village.id_district = phi_district.id_district 
                                                INNER JOIN phi_commune ON 
                                                phi_village.id_commune = phi_commune.id_commune 
                                   ";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "phi_village.id_village";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "CONCAT(phi_district.name, ' / ', phi_commune.name, ' / ', phi_village.name)";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_village_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_municipality ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_municipality")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_municipality";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_municipality";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_municipality";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_municipality_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_municipality_id_village ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_municipality_id_village")
         {
          $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_municipality_id_village";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_village";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "CONCAT(id_municipality,'_X_',id_village)";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_village_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //UserId ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "UserId")
         {
          $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "UserId";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "users";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "users.UserId";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "CONCAT(users.FirstName,' ',users.SecondName)"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = " WHERE UserId != 2 ";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("UserId_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //ids_user_branch ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "ids_user_branch")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "ids_user_branch";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_branch_users";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "UserId";       //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "UserId";    //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("UserId_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_branch ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_branch")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_branch";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_branch";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_branch";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_branch_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //ids_branch_user ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "ids_branch_user")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_branch";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_branch INNER JOIN phi_branch_users ON
                                       phi_branch.id_branch = phi_branch_users.id_branch
                                   ";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "phi_branch.id_branch";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "phi_branch.name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "WHERE phi_branch_users.UserId = '".ValidateVarFun::f_real_escape_string($session->Vars["ses_userid"])."'";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_branch_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_reporting_entity_kind ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_reporting_entity_kind")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_reporting_entity_kind";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_reporting_entity_kind";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_reporting_entity_kind";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "id_reporting_entity_kind";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_reporting_entity_kind_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";            //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //state_or_private ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "state_or_private")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "state_or_private";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "state_or_private";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "S,P";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("shteteror_mesg").",".WebApp::getVar("privat_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = ""; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = "Y_N";     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //fills_alert_form ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "fills_alert_form")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "fills_alert_form";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "fills_alert_form";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "Y,N";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("fills_alert_form_y_mesg").",".WebApp::getVar("fills_alert_form_n_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = ""; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = WebApp::getVar("fills_alert_form_mesg");     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }


      //ids_user_reporting_entity ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "ids_user_reporting_entity")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "ids_user_reporting_entity";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_reporting_entity_users";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "UserId";       //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "UserId";    //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("UserId_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //ids_village_reporting_entity ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "ids_village_reporting_entity")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "ids_village_reporting_entity";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_reporting_entity_village";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_village";    //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "id_village";    //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("fshatrat_qe_mbulohen_nga_qsh_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_reporting_entity ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_reporting_entity")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_reporting_entity";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_reporting_entity";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_reporting_entity";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_reporting_entity_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_work_position ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_work_position")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_work_position";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_work_position";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_work_position";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_work_position_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_branch_id_reporting_entity ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_branch_id_reporting_entity")
         {
          $lov["type"]           = "dinamik";                       //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_branch_id_reporting_entity"; //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_reporting_entity";      //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "CONCAT(id_branch,'_X_',id_reporting_entity)";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name"; //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_reporting_entity_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //gender ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "gender")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "gender";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "gender";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "M,F";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("mashkull_mesg").",".WebApp::getVar("femer_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = ""; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = WebApp::getVar("gender_mesg");     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_case_classification ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_case_classification")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_case_classification";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_case_classification";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_case_classification";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_case_classification_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_type_of_sample ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_type_of_sample")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_type_of_sample";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_type_of_sample";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_type_of_sample";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_type_of_sample_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_analysis ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_analysis")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_analysis";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_analysis";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_analysis";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_analysis_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_analysis_result ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_analysis_result")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_analysis_result";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_analysis_result";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_analysis_result";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_analysis_result_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_reporting_entity_dhe_tipi ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_reporting_entity_dhe_tipi")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_reporting_entity";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_reporting_entity INNER JOIN phi_reporting_entity_kind ON 
                                      phi_reporting_entity.id_reporting_entity_kind = phi_reporting_entity_kind.id_reporting_entity_kind
                                   ";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "phi_reporting_entity.id_reporting_entity";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "CONCAT(phi_reporting_entity.name, ' (', phi_reporting_entity_kind.name, ')')";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_reporting_entity_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }


      //id_branch_id_reporting_entity_dhe_tipi ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_branch_id_reporting_entity_dhe_tipi")
         {
          $lov["type"]           = "dinamik";                       //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_branch_id_reporting_entity"; //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_reporting_entity INNER JOIN phi_reporting_entity_kind ON 
                                      phi_reporting_entity.id_reporting_entity_kind = phi_reporting_entity_kind.id_reporting_entity_kind
                                   ";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "CONCAT(phi_reporting_entity.id_branch,'_X_',phi_reporting_entity.id_reporting_entity)";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "CONCAT(phi_reporting_entity.name, ' (', phi_reporting_entity_kind.name, ')')";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_reporting_entity_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_syndrome ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_syndrome")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_syndrome";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_syndrome";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_syndrome";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_syndrome_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }


      //id_agegroup ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_agegroup")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_agegroup";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_agegroup";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_agegroup";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_agegroup_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_disease ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_disease")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_disease";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_disease";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_disease";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_disease_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_doctor ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_doctor")
         {
          $lov["type"]           = "dinamik";     //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_doctor";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_doctor";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_doctor";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "CONCAT(first_name, ' ', IF(father_name IS NULL, '', CONCAT(father_name, ' ')), last_name)";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_doctor_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //id_event_source ----------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "id_event_source")
         {
          $lov["type"]           = "dinamik";      //*DS -> pranon vlerat dinamik, statik;
          $lov["object_name"]    = "id_event_source";   //*DS -> emri i objektit;
          $lov["tab_name"]       = "phi_event_source";  //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
          $lov["id"]             = "id_event_source";   //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
          $lov["field_name"]     = "name";         //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

          $lov["layout_object"]  = "";            //DS -> pranon vlerat: select, checkbox, radio; default = select;
          $lov["layout_forma"]   = "";            //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;

          $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
          $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
          $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

          $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
          $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
          $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';

          $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
          $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
          $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
          $lov["alert_etiketa"]  = WebApp::getVar("id_event_source_mesg");       //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
          $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
          $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
          $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
          $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
          $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
          $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //channel_registration ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "channel_registration")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "channel_registration";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "channel_registration";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "1,2,3";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("sms_mesg").",".WebApp::getVar("web_mesg").",".WebApp::getVar("personeli_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = ""; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = WebApp::getVar("gender_mesg");     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //event_status ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "event_status")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "event_status";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "event_status";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "1,2,3";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("event_status_1_mesg").",".WebApp::getVar("event_status_2_mesg").",".WebApp::getVar("event_status_3_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = "";            //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = WebApp::getVar("event_status_mesg");     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

      //share ------------------------------------------------------------------------------------------------------------------
      IF ($lov_arg["name"] == "share")
	     {
	      $lov["type"]           = "statik";         //*DS -> pranon vlerat dinamik, statik;
	      $lov["object_name"]    = "share";         //*DS -> emri i objektit LOV;
	      $lov["tab_name"]       = "share";         //*D  -> per tipin="dinamik" emri tabeles ku do behet select | per tipin="statik" sduhet;
	      $lov["id"]             = "Y,N";            //*DS -> per tipin="dinamik" emri i fushes qe ruan kodin | per tipin="statik" lista e kodeve te ndara me ndaresin perkates, default ,;
	      $lov["field_name"]     = WebApp::getVar("share_yes_mesg").",".WebApp::getVar("share_no_mesg"); //*DS -> per tipin="dinamik" emri i fushes qe ruan emertimin, mund te perdoret p.sh: Concat(id_proces, ' ', name_proces) | per tipin="statik" lista e emertimeve te ndara me ndaresin perkates, default ,;

	      $lov["layout_object"]  = "";          //DS -> pranon vlerat: select, checkbox, radio; default = select;
	      $lov["layout_forma"]   = "";          //DS -> ka kuptim vetem per $lov["layout_object"] = checkbox ose radio; ne rast se vendoset vlera 'table' elementet e lov strukturohen ne nje tabele; vlera te ndryshme jane: <br> = elementet thyhen njeri poshte tjetrit; &nbsp = elementet ndahen me nje ose me shume spacio; default = <br>;
    
	      $lov["filter"]         = "";            //D -> per tipin="dinamik" kushti i filtrimit te te dhenave, default = '', p.sh: WHERE status = 1 AND id > 0 (mos haroni 'WHERE');
          $lov["filter_plus"]    = "";            //D -> per tipin="dinamik" kusht filtrimi shtese, default = '', p.sh: AND status = 1 AND id > 0 (mos haroni qe 'WHERE' ne kete rast nuk duhet sepse duhet ta kete variabli $filter); meret parasysh vetem kur $filter != ''; filtrimi i vlerave kryhet: $filter." ".$filter_plus
	      $lov["distinct"]       = "F";           //D -> per tipin="dinamik", pranon vlerat T,F (pra true ose false), default = 'F', pra ti beje distinct vlerat apo jo;
	      $lov["disabled"]       = "F";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'F';

	      $lov["null_print"]     = "T";           //DS -> pranon vlerat T,F  (pra true ose false), default = 'T'; ka kuptim vetem per $lov["layout_object"]  = "select";
	      $lov["null_id"]        = "";            //DS -> ne rast se $lov["null_print"] == 'T' id e rekordit te pare ne liste, zakonisht lihet bosh, default = '';
	      $lov["null_etiketa"]   = "";            //DS -> ne rast se $lov["null_print"] == 'T' etiketa e rekordit te pare ne liste, zakonisht lihet bosh, ose Zgjidhni emertimin, default = '';
    
	      $lov["id_select"]      = "";            //DS -> vlerat e kodit qe duhet te dalin te selektuara te ndara me ndaresin perkates default ,(presje); bosh kur s'duam te dali ndonje vlere e selektuar; kur eshte vetem nje vlere vendoset vetem vlera e kodit pa presje;
	      $lov["order_by"]       = ""; //D -> emri i kolones qe do perdoret per order, default = $lov["field_name"] | per tipin="statik" sduhet;
	      $lov["valid"]          = "1,0,0,0,0,0"; //DS -> Validimet e ndryshme JS, normalisht kjo fushe testohet vetem me shifren e pare pra te lejoje ose jo NULL;
	      $lov["alert_etiketa"]  = WebApp::getVar("share_mesg");     //DS -> Etiketa e fushes qe do perdoret ne alertet ne validimet JS, mos perdorni thonjeza teke dopjo;
	      $lov["ndares_vlerash"] = ",";           //S  -> stringa qe ndan vlerat e listes statike (kod dhe emertim), default = ,(presje);
	      $lov["class"]          = "";            //DS  -> emri i klases ne style, default = 'txtbox'; per $lov["layout_object"] = checkbox, radio po spati vlere nuk meret parasysh
	      $lov["width"]          = "";         //DS  -> gjeresia ne piksel e LOV, ne rast se lihet bosh LOV i bindet gjeresise qe eshte percaktuar tek klasa, perndryshe gjeresia e LOV mer vleren e percaktuar;
          $lov["height"]         = "";            //DS  -> lartesia ne piksel e LOV; vlen vetem per tipin select-multiple; ne rast se lihet bosh LOV i bindet lartesise qe eshte percaktuar tek klasa, perndryshe lartesia e LOV mer vleren e percaktuar;
	      $lov["class_etiketa"]  = "";            //DS  -> emri i klases ne style per etiketat per $lov["layout_object"] = checkbox, radio, vetem ne rastet kur strukturohen ne tabele; po spati vlere nuk meret parasysh;
	      $lov["isdate"]         = "";      	  //D  -> pranon vlerat T,F  (pra true ose false), default = 'F'; perdoret kur etiketa eshte e tipit date;
	      $lov["asc_desc"]       = "";            //D   -> per orderin, pranon vlerat ASC,DESC; default = 'ASC';;
          $lov["element_id"]     = "";            //DS ID qe deshironi te kete elementi; sintaksa qe kthen lov kur ky variabel eshte i ndryshem nga bosh : id='$lov["element_id"]'; kur variabli eshte bosh lov kthen default: id='$lov["object_name"]';
          $lov["tabindex"]       = "";            //tabindex ne html
          $lov["all_data_array"] = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_options"]   = "N";           //Y/N Y -> kthen array me datat [id] = name;
          $lov["only_ids"]       = "N";           //Y/N Y -> kthen array me datat [id] = id;
         }

   //percaktohen vlerat default per fushen e zgjedhur... e dedikuar per cdo aplikim ============================================
    //##########################################################################################################################\\

    //Ripopullohen vlerat default te LOV, me vlerat qe kane ardhur si parameter -------------------------------------------------
      FOR ($i=0; $i < count($lov_parametrat); $i++)
          {
           $parameter_select = $lov_parametrat[$i];
           
           IF (isset($lov_arg[$parameter_select]))
              {
               $lov[$parameter_select] = $lov_arg[$parameter_select];
              }
          }
    //Ripopullohen vlerat default te LOV, me vlerat qe kane ardhur si parameter =================================================

    //FORMOHET LOV -----------------------------
      IF ($lov_arg["obj_or_label"] == "label")
         {
          $lov_html = f_app_lov_return_label($lov);
         }
      ELSE
         {
          $lov_html = f_app_lov($lov);
         }
    //FORMOHET LOV =============================

   RETURN $lov_html;
  }

?>