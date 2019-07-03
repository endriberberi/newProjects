<?
//  IF ($editim_konsultim == 'editim')
//     {
      $nr = $nr + 1;
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
      $Grid_form['data'][$nr]['button_type']      = 'button';
      $Grid_form['data'][$nr]['name']             = 'but_back';
      $Grid_form['data'][$nr]['value']            = '{{kthehu_te_lista_mesg}}';
      $Grid_form['data'][$nr]['id']               = 'id_but_back';
      $Grid_form['data'][$nr]['other_attributes'] = ' onclick=\'JavaScript:GoTo("thisPage?event='.$arg_webbox.'.back()")\'';
      $Grid_form['data'][$nr]['primary']          = 'default';
      $Grid_form['data'][$nr]['action_type']      = 'back';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'form_footer_end';
//     }
?>