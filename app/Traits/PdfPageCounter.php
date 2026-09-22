<?php

namespace App\Traits;

use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Trait for rendering PDF documents with dynamic page numbering.
 * 
 * Injects "X dari Y" page counter into the header metadata table
 * using DomPDF's canvas page_text() API after rendering.
 */
trait PdfPageCounter
{
    /**
     * Render a PDF with dynamic page numbering in the header.
     *
     * @param  string  $viewName   Blade view name (e.g. 'pdf.document')
     * @param  array   $data       Data to pass to the view
     * @param  string  $filename   Output filename
     * @return \Illuminate\Http\Response
     */
    protected function renderPdfWithPageCounter(string $viewName, array $data, string $filename)
    {
        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('A4', 'portrait');

        // Render first to calculate total pages
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');

        // ------------------------------------------------------------------
        // Page number position in the header metadata table (Halaman row).
        //
        // A4 = 595.28pt wide. @page margin: right 50px ≈ 37.5pt.
        // Meta-cell width 220px ≈ 165pt → starts at 595.28 - 37.5 - 165 ≈ 393pt
        // Label(90px≈67.5pt) + borders + sep(8px≈6pt) + padding(6px≈4.5pt)
        // → value column x ≈ 473pt
        //
        // Header top: margin-top 130px≈97.5pt, offset -100px≈-75pt → 22.5pt
        // Row height ≈ 14pt. Halaman = 4th row → y ≈ 22.5 + 0.75 + 42 + 7 ≈ 72pt
        // ------------------------------------------------------------------
        $x = 448;
        $y = 72;
        $size = 6.75;
        $color = [0.2, 0.2, 0.2]; // #333

        // DomPDF's page_text() auto-replaces {PAGE_NUM} and {PAGE_COUNT}
        // on each page during output generation.
        $canvas->page_text($x, $y, "{PAGE_NUM} dari {PAGE_COUNT}", $font, $size, $color);

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
