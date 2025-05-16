<br /><br /><br /><br />
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    td {
        padding-top: 3px;
        padding-bottom: 3px;
        font-family: 'Roboto', sans-serif;
    }
    table {
        page-break-inside: avoid;
        width: 100%;
        border-collapse: collapse;
    }
    tr, td {
        page-break-inside: avoid;
    }
    p {
        page-break-inside: auto;
    }
    .page-break {
        page-break-before: always;
    }
    .header-section {
        margin-top: -70px;
    }
    .items-table thead {
        background-color: #ee473f;
        color: #fff;
    }
    .items-table th, .items-table td {
        border: 1px solid #e3dfcb;
        padding: 10px;
    }
    .spacer-row td {
        border: none;
        height: 10px;
    }
</style>

<div style="color: #000; font-family: 'Roboto', sans-serif;">
    <?php
    // Constants for height estimation (in mm, approximate)
    $headerHeight = 40; // QUOTATION title, kind attention, tables
    $itemRowBaseHeight = 20; // Base height per item row (adjust based on padding/content)
    $lineHeight = 5; // Height per line in description
    $paymentTermsHeight = 10;
    $priceHeight = 10;
    $exclusionHeaderHeight = 10;
    $footerHeight = 50; // Signature, stamp, etc.
    $maxHeight = 297 - 4 - 4; // A4 height (297mm) minus top/bottom margins (4mm each)

    // Initialize variables
    $currentHeight = 0;
    $pageNumber = 1;
    $totalPages = 1;
    $itemsPerPage = [];
    $currentPageItems = [];

    // Estimate total height and pages
    $tempHeight = $headerHeight + $paymentTermsHeight + $priceHeight + $exclusionHeaderHeight;
    foreach ($items as $item) {
        $descLines = substr_count($item['product_description'] ?? '', "\n") + count(array_filter(explode('•', $item['product_description'] ?? ''))) + 1;
        $rowHeight = $itemRowBaseHeight + ($descLines * $lineHeight);
        if ($tempHeight + $rowHeight + $footerHeight > $maxHeight) {
            $totalPages++;
            $tempHeight = $headerHeight + $rowHeight;
        } else {
            $tempHeight += $rowHeight;
        }
    }
    // Add exclusion lines
    $exclusionLines = preg_split('/\r\n|\r|\n/', trim($task['general_exclusion'] ?? ''));
    $tempHeight += count($exclusionLines) * $lineHeight;
    if ($tempHeight + $footerHeight > $maxHeight) {
        $totalPages++;
    }

    // Header content (to be repeated on each page)
    $header = '
        <div class="header-section">
            <p style="text-align: center; font-size: 23px; font-weight: bold;">QUOTATION</p>
            <p style="text-align: center; margin: 0; font-size: 11px; padding-bottom: 10px;">
                <b><span style="border-bottom: 1px solid #000;">Kind Attention: ' . htmlspecialchars($task['kind_attention'] ?? '') . '</span></b>
            </p>
            <table style="width: 100%;">
                <tr>
                    <td style="width: 50%;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="font-weight: bold; font-size: 11px;">Subject:</td>
                                <td style="font-size: 11px;"><b>' . htmlspecialchars($task['subject'] ?? '') . '</b></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-size: 11px;">Project:</td>
                                <td style="font-size: 11px;"><b>' . htmlspecialchars($task['project_name'] ?? '') . '</b></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-size: 11px;">Sales Person:</td>
                                <td style="font-size: 11px;"><b>' . htmlspecialchars(ucfirst($username ?? '')) . '</b></td>
                            </tr>
                        </table>
                    </td>
                    <td style="width: 50%; padding: 0;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="font-weight: bold; font-size: 11px; text-align: right;">Quote Ref:</td>
                                <td style="text-align: right; font-size: 11px;"><b>' . htmlspecialchars($task['quote_number'] ?? '') . '</b></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-size: 11px; text-align: right;">Date:</td>
                                <td style="text-align: right; font-size: 11px;"><b>' . htmlspecialchars(date('M j, Y h:i A', strtotime($task['updated_at']))) . '</b></td>
                            </tr>
                            <tr>
                                <td style="font-weight: bold; font-size: 11px; text-align: right;">Valid Till:</td>
                                <td style="text-align: right; font-size: 11px;"><b>' . htmlspecialchars(date('M j, Y', strtotime($task['valid_until']))) . '</b></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <div style="border-top: 2px dotted #d9d9d9; margin: 5px 0;"></div>
            <p style="font-size: 12px; margin: 0; padding-top: 5px; padding-bottom: 10px;">We thank you for inviting us to quote for the subject and are pleased to submit our proposal in the following:</p>
            <p style="font-size: 12px; margin: 0; padding-bottom: 15px;"><b>1. <span style="border-bottom: 1px solid #000;">SCOPE OF WORK</span>:</b></p>
        </div>';

    // Start rendering first page
    echo $header;
    $currentHeight = $headerHeight;

    echo '<table class="items-table">';
    echo '<thead>
		<tr style="background-color: #ee473f; color: #fff;">
			<th style="border: 1px solid #e3dfcb; width: 7%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">S.No</td>
			<th style="border: 1px solid #e3dfcb; width: 45%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Item & Description</td>
			<th style="border: 1px solid #e3dfcb; width: 10%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Qty.</td>
			<th style="border: 1px solid #e3dfcb; width: 10%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">U.O.M</td>
			<th style="border: 1px solid #e3dfcb; width: 15%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Unit Price</td>
			<th style="border: 1px solid #e3dfcb; width: 15%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Amount</td>
		</tr>
    </thead><tbody>';

    foreach ($items as $index => $item) {
        $descLines = substr_count($item['product_description'] ?? '', "\n") + count(array_filter(explode('•', $item['product_description'] ?? ''))) + 1;
        $rowHeight = $itemRowBaseHeight + ($descLines * $lineHeight);

        // Check if this item fits on the current page
        if ($currentHeight + $rowHeight + $footerHeight > $maxHeight && !empty($currentPageItems)) {
            // End current page
            echo '</tbody></table>';
            echo '<p style="font-size: 10px; text-align: right; margin: 5px 0;">Continue to next page ' . $pageNumber . '/' . $totalPages . '</p>';
            echo '<pagebreak />';

            // Start new page
            $pageNumber++;
            echo $header;
            echo '<table class="items-table">';
            echo '<thead>
				<tr style="background-color: #ee473f; color: #fff;">
					<th style="border: 1px solid #e3dfcb; width: 7%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">S.No</td>
					<th style="border: 1px solid #e3dfcb; width: 45%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Item & Description</td>
					<th style="border: 1px solid #e3dfcb; width: 10%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Qty.</td>
					<th style="border: 1px solid #e3dfcb; width: 10%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">U.O.M</td>
					<th style="border: 1px solid #e3dfcb; width: 15%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Unit Price</td>
					<th style="border: 1px solid #e3dfcb; width: 15%; height: 30px; font-weight: 600; text-align: center; color: #fff; padding-top: 3px !important; padding-bottom: 3px !important;">Amount</td>
				</tr>
            </thead><tbody>';
            $currentHeight = $headerHeight;
            $currentPageItems = [];
        }

        // Render item
        echo '<tr>
            <td style="text-align: center; vertical-align: top;">' . ($index + 1) . '</td>
            <td style="text-align: left;">
                <b style="font-size: 12px;">' . htmlspecialchars($item['product_name'] ?? '') . ':</b>';
        $description = $item['product_description'] ?? '';
        $parts = preg_split('/(Note:)/i', $description, 2, PREG_SPLIT_DELIM_CAPTURE);
        if (!empty(trim($parts[0]))) {
            $points = explode('•', trim($parts[0]));
            foreach ($points as $point) {
                $trimmedPoint = trim($point);
                if (!empty($trimmedPoint)) {
                    echo '<p style="font-size: 12px; margin: 0; padding-left: 20px;">• ' . htmlspecialchars($trimmedPoint) . '</p>';
                }
            }
        }
        if (count($parts) > 1) {
            echo '<br><p style="font-size: 12px; padding-top: 30px;">' . htmlspecialchars($parts[1] . ' ' . trim($parts[2] ?? '')) . '</p>';
        }
        echo '</td>
            <td style="text-align: center; vertical-align: top; font-size: 13px;">' . htmlspecialchars($item['quantity'] ?? '') . '</td>
            <td style="text-align: center; vertical-align: top; font-size: 13px;">' . htmlspecialchars($item['uom'] ?? '') . '</td>
            <td style="text-align: center; vertical-align: top; font-size: 13px;">AED ' . number_format((float)($item['service_charge'] ?? 0), 2) . '</td>
            <td style="text-align: center; vertical-align: top; font-size: 13px;">AED ' . number_format((float)($item['quantity'] ?? 0) * (float)($item['service_charge'] ?? 0) * (1 - (float)($item['item_discount'] ?? 0) / 100), 2) . '</td>
        </tr>';

        $currentHeight += $rowHeight;
        $currentPageItems[] = $item;
    }

    echo '</tbody></table>';

    // Add payment terms, price, and exclusion on the last page only
    if ($currentHeight + $paymentTermsHeight + $priceHeight + $exclusionHeaderHeight + (count($exclusionLines) * $lineHeight) + $footerHeight > $maxHeight) {
        echo '<p style="font-size: 10px; text-align: right; margin: 5px 0;">Continue to next page ' . $pageNumber . '/' . $totalPages . '</p>';
        echo '<pagebreak />';
        echo $header;
        $currentHeight = $headerHeight;
    }

    // Fill remaining space with spacers if needed
    $remainingHeight = $maxHeight - ($currentHeight + $paymentTermsHeight + $priceHeight + $exclusionHeaderHeight + (count($exclusionLines) * $lineHeight) + $footerHeight);
    if ($remainingHeight > 0) {
        $spacerCount = floor($remainingHeight / 10); // 10mm per spacer
        for ($i = 0; $i < $spacerCount; $i++) {
            echo '<table><tr class="spacer-row"><td></td></tr></table>';
            $currentHeight += 10;
        }
    }

    // Payment terms, price, and exclusion
    echo '<p style="font-size: 12px; margin: 0; padding-bottom: 13px; padding-right: 5px; padding-top: 10px;"><b>2. <span style="border-bottom: 1px solid #000;">PAYMENT TERMS</span>: </b>' . htmlspecialchars($task['terms_of_payment'] ?? '') . '</p>';
    $currentHeight += $paymentTermsHeight;

    echo '<p style="font-size: 12px; margin: 0; padding-bottom: 13px; padding-right: 5px;"><b>3. <span style="border-bottom: 1px solid #000;">PRICE</span>: AED ' . number_format($totalAmount, 2) . ' - Excl. VAT</b></p>';
    $currentHeight += $priceHeight;

    echo '<p style="font-size: 12px; margin: 0; padding-bottom: 13px; padding-right: 5px;"><b>4. <span style="border-bottom: 1px solid #000;">GENERAL EXCLUSION</span>: </b></p>';
    $currentHeight += $exclusionHeaderHeight;

    echo '<span>';
    $general_exclusion = $task['general_exclusion'] ?? '';
    if (!empty(trim($general_exclusion))) {
        $lines = preg_split('/\r\n|\r|\n/', trim($general_exclusion));
        foreach ($lines as $line) {
            $line = trim($line);
            if (stripos($line, 'Note:') === 0 || stripos($line, 'Note-') === 0 || stripos($line, 'Notes-') === 0) {
                $noteContent = substr($line, strpos($line, ':') + 1);
                echo '<p style="font-size: 12px; margin: 0; padding-left: 20px; font-style: italic;">Note: ' . htmlspecialchars($noteContent) . '</p><br>';
            } elseif (!empty($line)) {
                echo '<p style="font-size: 12px; margin: 0; padding-left: 20px;">' . htmlspecialchars($line) . '</p>';
            }
            $currentHeight += $lineHeight;
        }
    } else {
        echo '<p style="font-size: 12px; padding-left: 20px;">No exclusions specified.</p>';
        $currentHeight += $lineHeight;
    }
    echo '</span>';

    // Footer (signature and stamp) on the last page
    echo '<p style="font-size: 12px; margin: 0; padding-top: 20px;">We trust the above offer is in line with your requirements and look forward to receiving your valued order.</p>';
    echo '<p style="margin: 0; padding-bottom: 15px; font-size: 12px;">Yours faithfully,</p>';
    echo '<p style="padding-bottom: 10px; font-size: 12px; margin: 0;"><b>For VOLTRONIX CONTRACTING LLC,</b></p>';
    echo '<table style="width: 45%; margin: 0; padding: 0;">
        <tr>
            <td style="width: 25%;">
                <div style="margin: 0; padding: 0; display: flex;">
                    <img src="' . base_url('assets/photos/logo/sign.jpg') . '" alt="Stamp" style="width: 100px;">
                </div>
                <p style="font-size: 14px;"><b>Marieswaran</b><br>Managing Partner<br>Mobile: 0502420957</p>
            </td>
            <td style="text-align: center; width: 20%;">
                <img src="' . base_url('assets/photos/logo/stamp.jpg') . '" alt="Stamp" style="width: 130px;">
            </td>
        </tr>
    </table>';
    ?>
</div>
