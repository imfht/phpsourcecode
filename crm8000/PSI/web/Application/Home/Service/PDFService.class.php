<?php

namespace Home\Service;

require __DIR__ . '/../Common/tcpdf/tcpdf.php';

/**
 * pdf文件 Service
 *
 * @author 李静波
 */
class PDFService
{

  public function getInstance()
  {
    $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor("PSI");

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    return $pdf;
  }

  public function getInstanceForReport()
  {
    $pdf = new \TCPDF("L", PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor("PSI");

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

    return $pdf;
  }
}
