<?php

namespace App\Http\Controllers;

use App\Models\User;
use Smalot\PdfParser\Parser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use setasign\Fpdi\Fpdi;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;


class PdfCroppersContoller extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function pdfTools()
    {
        return view('pdfTools');
    }

    public function login()
    {
        return view('login');
    }

    public function register()
    {
        return view('register');
    }

    public function redirectToGoogle()
    {
        return Socialite::driver('google')
            ->redirectUrl(route('google.login.callback'))
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    // public function handleGoogleCallback()
    // {
    //     try {
    //         $googleUser = Socialite::driver('google')->user();

    //         $user = User::updateOrCreate(
    //             ['email' => $googleUser->getEmail()],
    //             [
    //                 'name' => $googleUser->getName(),
    //                 'email_verified_at' => now(),
    //                 'password' => Hash::make(Str::random(12)),
    //             ]
    //         );

    //         Auth::login($user);

    //         return redirect()->route('pdfTools');
    //     } catch (\Exception $e) {
    //         return redirect()->route('login')->with('error', 'Login failed, please try again.');
    //     }
    // }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {
                Auth::login($user);
                return redirect()->route('pdfTools');
            }

            return redirect()->route('register')
                ->with('error', 'You are not registered. Please register first.');
        } catch (\Exception $e) {
            return redirect()->route('login')->with('error', 'Login failed.');
        }
    }

    public function redirectToGoogleRegister()
    {
        return Socialite::driver('google')
            ->redirectUrl(route('google.register.callback'))
            ->with(['prompt' => 'select_account'])
            ->redirect();
    }

    public function handleGoogleRegisterCallback()
    {
        try {
            $googleUser = Socialite::driver('google')
                ->redirectUrl(route('google.register.callback'))
                ->user();

            $existingUser = User::where('email', $googleUser->getEmail())->first();

            // already registered
            if ($existingUser) {
                return redirect()->route('login')
                    ->with('error', 'User already registered. Please login.');
            }

            // create new user
            $user = User::create([
                'name' => $googleUser->getName(),
                'email' => $googleUser->getEmail(),
                'email_verified_at' => now(),
                'password' => Hash::make(Str::random(12)),
            ]);

            Auth::login($user);

            return redirect()->route('pdfTools');
        } catch (\Exception $e) {
            return redirect()->route('register')->with('error', 'Register failed.');
        }
    }

    private function makePdfResponse(Request $request, string $pdfContent, string $filename)
    {
        $disposition = $request->input('mode') === 'download' ? 'attachment' : 'inline';

        return response($pdfContent)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', "$disposition; filename=\"$filename\"");
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

            return $this->makePdfResponse($request, $pdf->Output('S'), 'merged.pdf');
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

            return $this->makePdfResponse($request, $pdf->Output('S'), 'invoice-split.pdf');
        }

        // Keep invoice no crop
        if ($request->has('noCrop')) {
            $file = $files[0];
            $path = $file->getRealPath();

            return response()->file($path, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->getClientOriginalName() . '"',
            ]);
        }

        // === REMOVE WHITE SPACE (Fit within same page) ===
        if ($request->has('removeWhitespace')) {
            $pdf = new Fpdi();
            $file = $files[0];
            $path = $file->getRealPath();

            $pageCount = $pdf->setSourceFile($path);

            $leftMargin   = -4;
            $rightMargin  = 12;
            $topMargin    = -4;
            $bottomMargin = 75;

            for ($page = 1; $page <= $pageCount; $page++) {
                $tplId = $pdf->importPage($page);
                $size  = $pdf->getTemplateSize($tplId);

                $croppedWidth  = $size['width'] - ($leftMargin + $rightMargin);
                $croppedHeight = $size['height'] - ($topMargin + $bottomMargin);

                $pdf->AddPage($size['orientation'], [$croppedWidth, $croppedHeight]);

                $pdf->useTemplate(
                    $tplId,
                    $leftMargin * 1,
                    $topMargin * 1,
                    $size['width'],
                    $size['height']
                );
            }

            return $this->makePdfResponse($request, $pdf->Output('S'), 'whitespace-removed.pdf');
        }

        // Print date time on label
        if ($request->has('printDateTime')) {
            $pdf = new Fpdi();
            foreach ($files as $file) {
                $pageCount = $pdf->setSourceFile($file->getRealPath());

                for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
                    $tplId = $pdf->importPage($pageNo);
                    $size  = $pdf->getTemplateSize($tplId);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplId);

                    if ($request->has('printDateTime')) {
                        $pdf->SetFont('Arial', 'B', 10);
                        $pdf->SetTextColor(0, 0, 0);

                        $marginRight = 10;
                        $marginTop = 100;
                        $pdf->SetXY($size['width'] - $marginRight - 50, $marginTop);
                        $pdf->Cell(50, 10, now()->format('d-m-Y H:i:s'), 0, 0, 'R');
                    }
                }
            }
            return $this->makePdfResponse($request, $pdf->Output('S'), 'invoice.pdf');
        }

        // === sort by sold by ===
        if ($request->has('sortBySoldBy')) {
            $parser = new Parser();
            $fileData = [];

            foreach ($files as $file) {
                $path = $file->getRealPath();
                $pdfText = $parser->parseFile($path)->getText();

                // Extract "Sold by" line (you may need to adjust regex)
                preg_match('/Sold by[: ]+([^\n]+)/i', $pdfText, $matches);
                $soldBy = isset($matches[1]) ? trim($matches[1]) : 'ZZZ_Unknown';

                $fileData[] = [
                    'path' => $path,
                    'sold_by' => $soldBy,
                ];
            }

            usort($fileData, function ($a, $b) {
                return strcmp($a['sold_by'], $b['sold_by']);
            });

            $pdf = new Fpdi();
            foreach ($fileData as $data) {
                $pageCount = $pdf->setSourceFile($data['path']);
                for ($page = 1; $page <= $pageCount; $page++) {
                    $tplId = $pdf->importPage($page);
                    $size  = $pdf->getTemplateSize($tplId);

                    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                    $pdf->useTemplate($tplId);
                }
            }

            return $this->makePdfResponse($request, $pdf->Output('S'), 'sorted-by-sold-by.pdf');
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

            return $this->makePdfResponse($request, $pdf->Output('S'), 'sorted_pickup.pdf');
        }

        // === SEPARATE REVIEW ORDERS USING LIST ===
        if ($request->has('separateReviewOrders')) {
            $file = $files[0];
            $path = $file->getRealPath();

            if ($request->filled('reviewPdfIds')) {
                $reviewIds = array_map('trim', explode(',', $request->reviewPdfIds));

                $pdf = new Fpdi();
                $pageCount = $pdf->setSourceFile($path);
                $parser = new Parser();
                $pdfText = $parser->parseFile($path);
                $pages = $pdfText->getPages();
                $matchedPages = 0;

                for ($page = 1; $page <= $pageCount; $page++) {
                    $tplId = $pdf->importPage($page);
                    $size  = $pdf->getTemplateSize($tplId);
                    $pageText = $pages[$page - 1]->getText() ?? '';

                    foreach ($reviewIds as $orderNo) {
                        if (!empty($orderNo) && str_contains($pageText, $orderNo)) {
                            $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                            $pdf->useTemplate($tplId);
                            $matchedPages++;
                            break;
                        }
                    }
                }

                if ($matchedPages > 0) {
                    return $this->makePdfResponse($request, $pdf->Output('S'), 'review_orders.pdf');
                }
            }

            return response()->make(file_get_contents($path), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $file->getClientOriginalName() . '"',
            ]);
        }

        // === Treat valmo express by valmo
        if ($request->has('treatValmoExpress')) {
            $pdf = new Fpdi();
            $file = $files[0];
            $path = $file->getRealPath();

            $pageCount = $pdf->setSourceFile($path);

            $parser = new Parser();
            $pdfText = $parser->parseFile($path);
            $pages = $pdfText->getPages();

            $valmoPages = [];
            $otherPages = [];

            for ($i = 0; $i < $pageCount; $i++) {
                $pageText = $pages[$i]->getText() ?? '';
                $tplId = $pdf->importPage($i + 1);
                $size = $pdf->getTemplateSize($tplId);

                if (stripos($pageText, 'Valmo') !== false || stripos($pageText, 'ValmoExpress') !== false) {
                    $valmoPages[] = ['tplId' => $tplId, 'size' => $size];
                } else {
                    $otherPages[] = ['tplId' => $tplId, 'size' => $size];
                }
            }

            $sortedPages = array_merge($valmoPages, $otherPages);

            foreach ($sortedPages as $page) {
                $pdf->AddPage($page['size']['orientation'], [$page['size']['width'], $page['size']['height']]);
                $pdf->useTemplate($page['tplId']);
            }

            return $this->makePdfResponse($request, $pdf->Output('S'), 'sorted_pickups.pdf');
        }

        // === Multi order at bottom ===
        if ($request->has('multiorderBottom')) {
            $pdf = new Fpdi();
            $parser = new Parser();

            $singlePages = [];
            $multiPages = [];

            // Step 1: Collect pages & detect qty
            foreach ($files as $file) {
                $path = $file->getRealPath();

                $pdfText = $parser->parseFile($path);
                $pagesText = $pdfText->getPages();
                $totalPages = count($pagesText);

                for ($pageIndex = 0; $pageIndex < $totalPages; $pageIndex++) {
                    $text = $pagesText[$pageIndex]->getText();

                    // Default single order
                    $qty = 1;

                    // Detect qty
                    if (preg_match('/\bFree Size\s+(\d+)\s+/i', $text, $matches)) {
                        $qty = (int)$matches[1];
                    } else {
                        $qty = 1; // fallback
                    }
                    $pageData = [
                        'file' => $path,
                        'page' => $pageIndex + 1,
                        'qty' => $qty,
                    ];

                    if ($qty > 1) {
                        $multiPages[] = $pageData;
                    } else {
                        $singlePages[] = $pageData;
                    }
                }
            }

            // Merge single first, multi later
            $sortedPages = array_merge($singlePages, $multiPages);

            // Build new PDF
            foreach ($sortedPages as $pageInfo) {
                $pdf->setSourceFile($pageInfo['file']);
                $tplId = $pdf->importPage($pageInfo['page']);
                $size = $pdf->getTemplateSize($tplId);

                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId, 0, 0, $size['width'], $size['height']);
            }

            // Return PDF
            return $this->makePdfResponse($request, $pdf->Output('S'), 'multi_order_sorted.pdf');
        }

        // === Add picklist page after orders ===
        if (!$request->has('addPicklist')) {
            return response()->json(['error' => 'Option not selected'], 400);
        }

        $parser = new Parser();
        $pdf = new Fpdi();

        $allProducts = [];
        $courierTotals = [];
        $companyTotals = [];
        $totalPackages = 0;

        foreach ($files as $file) {
            $path = $file->getRealPath();
            $pdfText = $parser->parseFile($path)->getText();

            // Clean headers from text
            $cleanText = preg_replace('/SKU\s+Size\s+Qty\s+Color\s+Order\s+No\.?/i', '', $pdfText);

            // Capture order lines
            preg_match_all(
                '/(.+?)\s+(Free Size|\w+)\s+(\d+)\s+([\w\-]+|NA|Multicolor|Blue|White)\s+(\d+_\d+)/i',
                $cleanText,
                $matches,
                PREG_SET_ORDER
            );

            foreach ($matches as $match) {
                $sku = trim($match[1]);
                $size = trim($match[2]);
                $qty = (int)$match[3];
                $color = trim($match[4]);
                $ordNo = trim($match[5]);

                $allProducts[] = [
                    'sku' => $sku,
                    'size' => $size,
                    'qty' => $qty,
                    'color' => $color,
                    'ord' => $ordNo,
                ];

                $totalPackages += $qty;
            }

            // Get package count (default 1 if not found)
            $packageCount = 1;
            if (preg_match('/Package\s*:\s*(\d+)/i', $pdfText, $packageMatch)) {
                $packageCount = (int)$packageMatch[1];
            }

            preg_match_all('/([A-Za-z0-9]+)\s*(?:\R|\s)?Pickup/i', $pdfText, $pickupMatches);
            if (!empty($pickupMatches[1])) {
                foreach ($pickupMatches[1] as $pickupName) {
                    $pickupName = trim($pickupName);
                    if (preg_match('/Package\s*:\s*(\d+)/i', $pdfText, $packageMatch)) {
                        $packageCount = (int)$packageMatch[1];
                    }
                    if (!isset($courierTotals[$pickupName])) $courierTotals[$pickupName] = 0;
                    $courierTotals[$pickupName] += $packageCount;
                }
            }

            preg_match_all('/If undelivered, return to:\s*\n(.+)/i', $pdfText, $companyMatches);
            if (!empty($companyMatches[1])) {
                foreach ($companyMatches[1] as $companyName) {
                    $companyName = trim($companyName);
                    $packageCount = 1;
                    if (preg_match('/Package\s*:\s*(\d+)/i', $pdfText, $packageMatch)) {
                        $packageCount = (int)$packageMatch[1];
                    }
                    if (!isset($companyTotals[$companyName])) $companyTotals[$companyName] = 0;
                    $companyTotals[$companyName] += $packageCount;
                }
            }

            $pageCount = $pdf->setSourceFile($path);
            for ($page = 1; $page <= $pageCount; $page++) {
                $tplId = $pdf->importPage($page);
                $size = $pdf->getTemplateSize($tplId);
                $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                $pdf->useTemplate($tplId);
            }
        }

        $productTotals = [];
        foreach ($allProducts as $product) {
            $key = strtolower($product['sku'] . '|' . $product['size'] . '|' . $product['color']);

            if (!isset($productTotals[$key])) {
                $productTotals[$key] = [
                    'sku' => $product['sku'],
                    'size' => $product['size'],
                    'color' => $product['color'],
                    'orders' => 0,
                    'qty' => 0,
                ];
            }

            $productTotals[$key]['orders'] += 1;
            $productTotals[$key]['qty'] += $product['qty'];
        }

        // --- Add Picklist Page ---
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'This Meesho label is cropped by pdfcroppers.com on ' . date('d, M Y'), 0, 1);
        $pdf->Ln(5);

        // --- Product Table ---
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(20, 8, 'Orders', 1);
        $pdf->Cell(20, 8, 'Qty', 1);
        $pdf->Cell(30, 8, 'Size', 1);
        $pdf->Cell(30, 8, 'Color', 1);
        $pdf->Cell(90, 8, 'Product', 1);
        $pdf->Ln();

        $pdf->SetFont('Arial', '', 10);
        foreach ($productTotals as $row) {
            $pdf->Cell(20, 8, $row['orders'], 1, 0, 'C');
            $pdf->Cell(20, 8, $row['qty'], 1, 0, 'C');
            $pdf->Cell(30, 8, $row['size'], 1);
            $pdf->Cell(30, 8, $row['color'], 1);
            $pdf->Cell(90, 8, $row['sku'], 1);
            $pdf->Ln();
        }

        // --- Total Packages ---
        $pdf->Ln(5);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 8, 'Total packages: ' . $totalPackages, 0, 1);

        // --- Courier / Pickup totals ---
        $pdf->Cell(0, 8, 'Courier wise total package:', 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 8, 'Package', 1);
        $pdf->Cell(60, 8, 'Pickup', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        foreach ($courierTotals as $pickup => $pkg) {
            $pdf->Cell(30, 8, $pkg, 1, 0, 'C');
            $pdf->Cell(60, 8, $pickup, 1);
            $pdf->Ln();
        }

        // --- Company totals ---
        $pdf->Cell(0, 8, 'Company wise total package:', 0, 1);
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(30, 8, 'Package', 1);
        $pdf->Cell(60, 8, 'Sold By', 1);
        $pdf->Ln();
        $pdf->SetFont('Arial', '', 10);
        foreach ($companyTotals as $company => $pkg) {
            $pdf->Cell(30, 8, $pkg, 1, 0, 'C');
            $pdf->Cell(60, 8, $company, 1);
            $pdf->Ln();
        }

        return $this->makePdfResponse($request, $pdf->Output('S'), 'picklist_summary.pdf');

        // Default: return the first uploaded PDF
        $file = $files[0];

        if ($request->input('mode') === 'download') {
            return response()->download(
                $file->getRealPath(),
                $file->getClientOriginalName(),
                ['Content-Type' => 'application/pdf']
            );
        }

        return response()->file($file->getRealPath(), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $file->getClientOriginalName() . '"',
        ]);
    }
}
