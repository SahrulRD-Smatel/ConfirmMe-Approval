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
     * @param  string  $orientation Paper orientation ('portrait' or 'landscape')
     * @return \Illuminate\Http\Response
     */
    protected function renderPdfWithPageCounter(string $viewName, array $data, string $filename, string $orientation = 'portrait')
    {
        $orientation = $data['orientation'] ?? $orientation;
        $pdf = Pdf::loadView($viewName, $data);
        $pdf->setPaper('A4', $orientation);

        // Render first to calculate total pages
        $pdf->render();

        $dompdf = $pdf->getDomPDF();
        $canvas = $dompdf->getCanvas();
        $fontMetrics = $dompdf->getFontMetrics();
        $font = $fontMetrics->getFont('DejaVu Sans', 'normal');

        // Page number coordinates
        if ($orientation === 'landscape') {
            // A4 landscape = 841.89pt wide x 595.28pt high (margin: 12mm right = content ends at ~807.87pt)
            $x = 770;
            $y = 28;
            $size = 7;
            $color = [0.3, 0.3, 0.3];
        } else {
            // Portrait - Aligned with mathematical precision to row 4 of the standardized company header table
            // Row 1 (No. Dokumen):    x=449.78, y_cpdf=811.21
            // Row 2 (Status Revisi):  x=449.78, y_cpdf=795.36
            // Row 3 (Tanggal Berlaku):x=449.78, y_cpdf=779.51
            // Row 4 (Halaman):        x=449.78, y_cpdf=763.66 -> canvas y=70.37
            $x = 449.78;
            $y = 70.37;
            $size = 6.75;
            $color = [0.2, 0.2, 0.2];
        }

        // DomPDF's page_text() auto-replaces {PAGE_NUM} and {PAGE_COUNT}
        // on each page during output generation.
        $canvas->page_text($x, $y, "{PAGE_NUM} dari {PAGE_COUNT}", $font, $size, $color);

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
}
