<?
      //error_reporting(E_ALL);
      //ini_set('display_errors', TRUE);
      //ini_set('display_startup_errors', TRUE);
      date_default_timezone_set('Europe/London');
   
      /** Include PHPExcel */
      require_once (EASY_PATH.'lib_classes/PHPExcel/PHPExcel.php');
      
      
      $exp_sheet = "Sheet1";
      
      IF ($content_title != "")
         {
          $exp_filename = $content_title.".xlsx";
          $exp_titull   = $content_title;
         }
      ELSE
         {
          $exp_filename = "Book1.xlsx";
          $exp_titull   = "";
         }
      
      $row_start = 0;
      IF (ISSET($data_arr[0]["properties"]) AND IS_ARRAY($data_arr[0]["properties"]))
         {
          $row_start = 1;
          
          IF (ISSET($data_arr[0]["properties"]["exp_file_name"]) AND ($data_arr[0]["properties"]["exp_file_name"] != ""))
             {
              $exp_filename = $data_arr[0]["properties"]["exp_file_name"].".xlsx";
             }

          IF (ISSET($data_arr[0]["properties"]["exp_sheet_name"]) AND ($data_arr[0]["properties"]["exp_sheet_name"] != ""))
             {
              $exp_sheet = $data_arr[0]["properties"]["exp_sheet_name"];
             }

          IF (ISSET($data_arr[0]["properties"]["exp_titull"]) AND ($data_arr[0]["properties"]["exp_titull"] != ""))
             {
              $exp_titull = $data_arr[0]["properties"]["exp_titull"];
             }
         }
      
      // Create new PHPExcel object
      $objPHPExcel = new PHPExcel();
   
      // Set document properties
      //$objPHPExcel->getProperties()->setCreator($setCreator_mseg);

      //fleta -----------------------------------------------------------------------------------------------------
        $sheet_indx = -1;
        $objPHPExcel->getActiveSheet()->setTitle(SUBSTR($exp_sheet, 0, 31));
      //fleta -----------------------------------------------------------------------------------------------------

      $sheet_indx = $sheet_indx + 1;
      $objPHPExcel->setActiveSheetIndex($sheet_indx);

      //koka -----------------------------------------------------------------------------------------------------
        $exel_row  = 0;
        $exel_kol  = -1;

        IF ($exp_titull != "")
           {
            $exel_row        = $exel_row + 1;
            $exel_kol        = $exel_kol + 1;
            
            $exel_kol_string = PHPExcel_Cell::stringFromColumnIndex($exel_kol);
        
            $objPHPExcel->getActiveSheet()->setCellValue($exel_kol_string.$exel_row, $exp_titull);
            $objPHPExcel->getActiveSheet()->getStyle($exel_kol_string.$exel_row)->getFont()->setBold(true);
           }
      //koka -----------------------------------------------------------------------------------------------------

      //popullojme objektin --------------------------------------------------------------------------------------
        IF ((count($data_arr) > 6000) AND (count($data_arr) < 10000))
           {
            INI_SET("memory_limit", "512M");
           }
        IF (count($data_arr) > 10000)
           {
            INI_SET("memory_limit", "1024M");
           }
        
        FOR ($i=$row_start; $i < count($data_arr); $i++)
            {
             $exel_row = $exel_row + 1;
             $exel_kol = -1;

             $row_arr = $data_arr[$i];
             FOR ($j=0; $j < count($row_arr); $j++)
                 {
                  $cel_sel = $row_arr[$j];
                  
                  IF (IS_ARRAY($cel_sel))
                     {
                      $cel_vl            = $cel_sel['vl'];
                      $cel_bold          = $cel_sel['bold'];
                      $cel_colspan       = $cel_sel['colspan'];
                      $cel_format_number = $cel_sel['format_number'];
                     }
                  ELSE
                     {
                      $cel_vl            = $cel_sel;
                      $cel_bold          = '';
                      $cel_colspan       = '';
                      $cel_format_number = '';
                     }
                  
                  $exel_kol        = $exel_kol + 1;
                  $exel_kol_string = PHPExcel_Cell::stringFromColumnIndex($exel_kol);
                  
                  $objPHPExcel->getActiveSheet()->setCellValue($exel_kol_string.$exel_row, $cel_vl);

                  IF ($cel_bold == 'Y')
                     {
                      $objPHPExcel->getActiveSheet()->getStyle($exel_kol_string.$exel_row)->getFont()->setBold(true); //jemi tek koka
                     }

                  IF ($cel_format_number != '')
                     {
                      $objPHPExcel->getActiveSheet()->getStyle($exel_kol_string.$exel_row)->getNumberFormat()->setFormatCode($cel_format_number);
                     }

                  IF (($cel_colspan != "") AND ($cel_colspan > 1))
                     {
                      $exel_kol_span        = $exel_kol + $cel_colspan - 1;
                      $exel_kol_span_string = PHPExcel_Cell::stringFromColumnIndex($exel_kol_span);
                      
                      $objPHPExcel->getActiveSheet()->mergeCells($exel_kol_string.$exel_row.':'.$exel_kol_span_string.$exel_row);

                      $exel_kol      = $exel_kol + $cel_colspan - 1;
                     }
                 }
            }
      //popullojme objektin --------------------------------------------------------------------------------------

      // Set active sheet index to the first sheet, so Excel opens this as the first sheet
      $objPHPExcel->setActiveSheetIndex(0);

      IF ($output_browser == "Y")
         {
          // Redirect output to a client’s web browser (Excel2007)
          header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
          header('Content-Disposition: attachment;filename="'.$exp_filename.'"');
          header('Cache-Control: max-age=0');
          // If you're serving to IE 9, then the following may be needed
          header('Cache-Control: max-age=1');

          // If you're serving to IE over SSL, then the following may be needed
          header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
          header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
          header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
          header ('Pragma: public'); // HTTP/1.0

          $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
          $objWriter->save('php://output');
         }
?>