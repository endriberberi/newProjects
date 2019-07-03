<?
  IF (COUNT($Grid_form['data']) > 0) 
     {
      $Grid_form['AllRecs'] = COUNT($Grid_form['data']);
     }
  WebApp::addVar('Grid_form', $Grid_form);
?>