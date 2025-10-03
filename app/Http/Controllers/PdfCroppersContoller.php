<?php

namespace App\Http\Controllers;

use Smalot\PdfParser\Parser;
use Illuminate\Http\Request;
use setasign\Fpdi\Fpdi;


class PdfCroppersContoller extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function PdfProcess(Request $request)
    {
        $request->validate([
            'files.*' => 'required|mimes:pdf|max:20480',
        ]);

        $files = $request->file('files');

        // === MERGE PDFs ===
        if ($request->has('merge')) {
            $pdf = new Fpdi();

            foreach ($files as $file) {
                $path = $file->getRealPath();
                $pageCount = $pdf->setSourceFile($path);

                for ($page = 1; $page <= $pageCount; $page++) {
                    $tplId = $pdf->importPage($page);
                    $size  = $pdf->getTemplateSize($tplId);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplId);
                }
            }

            return response($pdf->Output('S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="merged.pdf"');
        }

        // keep invoice only
        if ($request->has('keepInvoice')) {
            $pdf = new Fpdi();
            $file = $files[0];
            $path = $file->getRealPath();

            $pageCount = $pdf->setSourceFile($path);

            for ($page = 1; $page <= $pageCount; $page++) {
                $tplId = $pdf->importPage($page);
                $size  = $pdf->getTemplateSize($tplId);

                $splitY = 123;

                // Top page
                $pdf->AddPage($size['orientation'], [$size['width'], ($size['height'])]);
                $pdf->useTemplate($tplId, 0, 0, $size['width']);
                $pdf->SetFillColor(255, 255, 255); // white 
                $pdf->Rect(0, $splitY, $size['width'], $size['height'], 'F');

                // Bottom page
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId, 0, -$splitY, $size['width'], $size['height']);
            }

            return response($pdf->Output('S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="invoice-split.pdf"');
        }


        // === Sort By Courier Wise ===
        if ($request->has('sortCourierWise')) {
            $parser = new Parser();
            $courierFiles = [];

            foreach ($files as $file) {
                $path = $file->getRealPath();
                $pdfText = $parser->parseFile($path);
                $pages = $pdfText->getPages();

                $pickups = [];

                foreach ($pages as $pageNo => $page) {
                    $pageText = $page->getText();
                    if (preg_match('/([A-Za-z0-9]+)\s*(?:\R|\s)?Pickup/i', $pageText, $match)) {
                        $pickup = strtoupper(trim($match[1]));
                        $courierFiles[] = [
                            'pickup' => $pickup,
                            'file'   => $file,
                            'page'   => $pageNo + 1,
                        ];
                    }
                }

                $pickups = array_unique($pickups);
                sort($pickups);

                foreach ($pickups as $pickup) {
                    $courierFiles[] = [
                        'pickup' => $pickups[0] ?? 'ZZZ',
                        'file'   => $file,
                    ];
                }
            }

            usort($courierFiles, fn($a, $b) => strcmp($a['pickup'], $b['pickup']));

            // Debug
            // dd(array_column($courierFiles, 'pickup'));

            $pdf = new Fpdi();
            foreach ($courierFiles as $item) {
                $path = $item['file']->getRealPath();
                $pdf->setSourceFile($path);
                $tplId = $pdf->importPage($item['page']);
                $size = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }

            return response($pdf->Output('S'))
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="sorted_pickup.pdf"');
        }

        // === SINGLE FILE DOWNLOAD ===
        $file = $files[0];
        return response()->download(
            $file->getRealPath(),
            $file->getClientOriginalName(),
            ['Content-Type' => 'application/pdf']
        );
    }
}


        // === sort by sold by ===
        // if ($request->has('sortBySoldBy')) {
        //     $parser = new Parser();
        //     $fileData = [];

        //     foreach ($files as $file) {
        //         $path = $file->getRealPath();
        //         $pdfText = $parser->parseFile($path)->getText();

        //         // Extract "Sold by" line (you may need to adjust regex)
        //         preg_match('/Sold by[: ]+([^\n]+)/i', $pdfText, $matches);
        //         $soldBy = isset($matches[1]) ? trim($matches[1]) : 'ZZZ_Unknown';

        //         $fileData[] = [
        //             'path' => $path,
        //             'sold_by' => $soldBy,
        //         ];
        //     }

        //     usort($fileData, function ($a, $b) {
        //         return strcmp($a['sold_by'], $b['sold_by']);
        //     });

        //     $pdf = new Fpdi();
        //     foreach ($fileData as $data) {
        //         $pageCount = $pdf->setSourceFile($data['path']);
        //         for ($page = 1; $page <= $pageCount; $page++) {
        //             $tplId = $pdf->importPage($page);
        //             $size  = $pdf->getTemplateSize($tplId);

        //             $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        //             $pdf->useTemplate($tplId);
        //         }
        //     }

        //     return response($pdf->Output('S'))
        //         ->header('Content-Type', 'application/pdf')
        //         ->header('Content-Disposition', 'inline; filename="sorted-by-sold-by.pdf"');
        // }