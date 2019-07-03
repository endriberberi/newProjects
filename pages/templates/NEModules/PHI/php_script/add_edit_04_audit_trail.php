<?
  //audit trail ----------------------------------------------------------------------------------------------------------
  IF ($editim_konsultim == 'editim')
  {
    IF (
        (($ins_record_user != "") AND ($ins_record_timestamp != ""))
        OR
        (($upd_record_user != "") AND ($upd_record_timestamp != ""))
       )
       {
        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'row_start';
        $Grid_form['data'][$nr]['other_attributes'] = '';

        IF (($ins_record_user != "") AND ($ins_record_timestamp != ""))
           {
            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'col_start';
            $Grid_form['data'][$nr]['width']            = '6';
            $Grid_form['data'][$nr]['other_attributes'] = '';

            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'label';
            $Grid_form['data'][$nr]['value']            = '{{u_rregjistrua_nga_mesg}}:';
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '4';

            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'obj_preview';
            $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($ins_record_user).'';
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '8';
			
            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'label';
            $Grid_form['data'][$nr]['value']            = '{{u_rregjistrua_me_mesg}}:';
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '4';

            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'obj_preview';
            $Grid_form['data'][$nr]['value']            = $ins_record_timestamp;
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '8';


            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'col_end';
           }

        IF (($upd_record_user != "") AND ($upd_record_timestamp != ""))
           {
            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'col_start';
            $Grid_form['data'][$nr]['width']            = '6';
            $Grid_form['data'][$nr]['other_attributes'] = '';

            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'label';
            $Grid_form['data'][$nr]['value']            = '{{u_perditesua_nga_mesg}}:';
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '4';

            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'obj_preview';
            $Grid_form['data'][$nr]['value']            = HTMLSPECIALCHARS($upd_record_user).'';
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '8';
			
            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'label';
            $Grid_form['data'][$nr]['value']            = '{{u_perditesua_me_mesg}}:';
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '4';

            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'obj_preview';
            $Grid_form['data'][$nr]['value']            = $upd_record_timestamp;
            $Grid_form['data'][$nr]['for']              = '';
            $Grid_form['data'][$nr]['id']               = '';
            $Grid_form['data'][$nr]['other_attributes'] = '';
            $Grid_form['data'][$nr]['width']            = '8';

            $nr = $nr + 1;
            $Grid_form['data'][$nr]['type']             = 'col_end';
           }

        $nr = $nr + 1;
        $Grid_form['data'][$nr]['type']             = 'row_end';
       }
    }
  //audit trail ----------------------------------------------------------------------------------------------------------
?>