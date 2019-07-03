<?
//vetem butoni regjistro ... zakonisht kur jemi ne popup 
  IF ($editim_konsultim == 'editim')
     {
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
      $Grid_form['data'][$nr]['button_type']      = 'submit';
      $Grid_form['data'][$nr]['name']             = 'but_regj';
      $Grid_form['data'][$nr]['value']            = $but_regj_label;
      $Grid_form['data'][$nr]['id']               = 'id_but_regj';
      $Grid_form['data'][$nr]['other_attributes'] = $but_regj_action.$but_regj_disabled;
      $Grid_form['data'][$nr]['primary']          = 'primary';
      $Grid_form['data'][$nr]['action_type']      = 'save';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'col_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'row_end';

      $nr = $nr + 1;
      $Grid_form['data'][$nr]['type']             = 'form_footer_end';
     }
?>