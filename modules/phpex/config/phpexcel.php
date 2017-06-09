<?php

defined('SYSPATH') or die('No direct script access.');

/*return array(
    'header' => array(
        'font' => array(
            'bold' => true
        ),
        'alignment' => array(
            'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER
        ),
        'borders' => array(
            'right' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FFA0A0A0'),
            ),
            'bottom' => array(
                'style' => PHPExcel_Style_Border::BORDER_THIN,
                'color' => array('argb' => 'FFA0A0A0'),
            )
        ),
        'fill' => array(
            'type' => PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
            'rotation' => 90,
            'startcolor' => array('argb' => 'FFA0A0A0'),
            'endcolor' => array('argb' => 'FFFFFFFF')
        )
    )
);*/

return array(
                 'font'    => array(
                      'name'      => 'Arial',
                      'bold'      => true,
                      'italic'    => false,
                      'underline' => PHPExcel_Style_Font::UNDERLINE_DOUBLE,
                      'strike'    => false,
                      'color'     => array(
                          'rgb' => '808080'
                      )
                  ),
                  'borders' => array(
                      'bottom'     => array(
                          'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                          'color' => array(
                              'rgb' => '808080'
                          )
                      ),
                      'top'     => array(
                          'style' => PHPExcel_Style_Border::BORDER_DASHDOT,
                          'color' => array(
                              'rgb' => '808080'
                          )
                      )
                  ),
                  'quotePrefix'    => true
              );


