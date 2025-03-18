<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quotation - Voltronix Switchgear LLC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .wrapper {
            padding: 20px;
        }
        .invoice-title {
            text-align: center;
            font-size: 22px;
            font-weight: bold;
            margin: 3px 0;
        }
        .invoice-info {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .invoice-info td, .invoice-info th {
            border: 0.3px solid #000;
            padding: 3px 5px 3px 5px;
            font-size: 12px;
            vertical-align: top;
        }
        .bank-details {
            width: 100%;
            border-collapse: collapse;
            margin: 0; /* No gap between tables */
        }
        .bank-details td {
            border: 0.3px solid #000;
            padding: 5px;
            font-size: 12px;
            vertical-align: top;
        }
        table {
            table-layout: fixed;
            width: 100%;
        }
        tr {
            page-break-inside: avoid;
        }
        .header-row {
            page-break-inside: avoid; /* Prevent header from splitting */
        }
        .description {
            text-align: left;
            padding-left: 10px;
        }
        .continue-text {
            font-size: 10px;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php
        if (empty($items)) {
            echo '<p>No items available.</p>';
            $items = []; // Ensure $items is an array
        }

        $itemCount = count($items);
        $rowCount = 1;
        $currentHeight = 0;
        $maxHeight = 175; // Adjusted for page height with continuation text
        $pageItems = []; // Buffer items for each page
        $currentPage = 1; // Track current page number
        $totalPages = 0; // Will calculate later

        // First pass: Estimate total pages
        $tempHeight = 0;
        $headerHeight = 40; // Approx height for header
        $totalsHeight = 45; // Approx height for totals section (increased for continuation row)
        $bankDetailsHeight = 30; // Approx height for bank details
        foreach ($items as $item) {
            $descLines = substr_count($item['product_description'] ?? '', "\n") + count(array_filter(explode('•', $item['product_description'] ?? ''))) + 1;
            $rowHeight = 5 + ($descLines * 3);
            if (($tempHeight + $rowHeight + $totalsHeight + $bankDetailsHeight) > $maxHeight) {
                $totalPages++;
                $tempHeight = $headerHeight + $rowHeight;
            } else {
                $tempHeight += $rowHeight;
            }
        }
        if ($tempHeight > $headerHeight) {
            $totalPages++; // Account for the final page
        }

        // Header HTML
        $header = '
            <tr class="header-row" style="border: none;">
                <th colspan="7"><h2 class="invoice-title">QUOTATION</h2></th>
            </tr>
            <tr class="header-row">
                <td colspan="2" style="width: 50%; border: none; border-left: 0.3px solid #000; border-top: 0.3px solid #000;">To: ' . htmlspecialchars($task['customer_name'] ?? '') . '</td>
                <td colspan="3" style="width: 25%;">Invoice No. _</td>
                <td colspan="2" style="width: 25%;">Dated: ' . htmlspecialchars(date('d-m-Y', strtotime($task['updated_at']))) . '</td>
            </tr>
            <tr class="header-row">
                <td colspan="2" style="width: 50%; border: none; border-left: 0.3px solid #000;">' . htmlspecialchars($task['account_name'] ?? '') . '</td>
                <td colspan="3" style="width: 25%;">DO No. _</td>
                <td colspan="2" style="width: 25%;">LPO No. _</td>
            </tr>
            <tr class="header-row">
                <td colspan="2" style="width: 50%; border: none; border-left: 0.3px solid #000;"> </td>
                <td colspan="3" style="width: 25%;">Mode/Terms of Payment</td>
                <td colspan="2" style="width: 25%;">100% ON DELIVERY</td>
            </tr>
            <tr class="header-row">
                <td colspan="2" style="width: 50%; border: none; border-left: 0.3px solid #000; border-bottom: 0.3px solid #000;"></td>
                <td colspan="3" style="width: 25%;">TRN Number</td>
                <td colspan="2" style="width: 25%;">_</td>
            </tr>
            <tr class="header-row">
                <th style="width: 5%; font-weight: normal; padding-right: 2px; padding-left: 2px;">S.No.</th>
                <th style="width: 44%; font-weight: normal;">Description</th>
                <th style="width: 10%; font-weight: normal;">Quantity</th>
                <th style="width: 12%; font-weight: normal;">Rate</th>
                <th style="width: 6%; font-weight: normal;">Tax%</th>
                <th style="width: 7%; font-weight: normal;">Disc.%</th>
                <th style="width: 16%; font-weight: normal;">Amount</th>
            </tr>';

        // Bank Details HTML (defined here before usage)
        $bankDetails = '
            <table class="bank-details">
                <tbody>
                    <tr>
                        <td style="width: 11%; border: none; border-left: 0.3px solid #000; border-top: 0.3px solid #000; padding: 0; padding-left: 5px; padding-top: 2px; vertical-align: bottom;">NAME</td>
                        <td style="width: 22%; border: none; border-top: 0.3px solid #000; vertical-align: bottom; padding: 0;">: VOLTRONIX SWITCHGEAR LLC</td>
                        <td style="width: 26%; border: none; border-top: 0.3px solid #000; border-left: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                        <td style="width: 40%; text-align: right; border: none; border-right: 0.3px solid #000; border-top: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px; padding-top: 2px;">for <strong>Voltronix Switchgear LLC</strong></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px;">A/C NO.</td>
                        <td style="border: none; padding: 0; padding-right: 5px;">: 12784683920001</td>
                        <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                        <td style="border: none; border-right: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px;">BANK</td>
                        <td style="border: none; padding: 0; padding-right: 5px;">: ADCB</td>
                        <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                        <td style="border: none; border-right: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px;">IBAN NO.</td>
                        <td style="border: none; padding: 0; padding-right: 5px;">: AE330030012784683920001</td>
                        <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                        <td style="border: none; border-right: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                    </tr>
                    <tr>
                        <td style="border: none; border-left: 0.3px solid #000; border-bottom: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;">SWIFT CODE</td>
                        <td style="border: none; border-bottom: 0.3px solid #000; padding: 0; padding-right: 5px;">: ADCBAEAA</td>
                        <td style="border: none; border-left: 0.3px solid #000; border-bottom: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;">Received By:</td>
                        <td style="text-align: right; border: none; border-right: 0.3px solid #000; border-bottom: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Authorized Signatory</td>
                    </tr>
                </tbody>
            </table>';

        foreach ($items as $index => $item) {
            $serviceCharge = (float)($item['service_charge'] ?? 0);
            $quantity = (float)($item['quantity'] ?? 0);
            $itemTotal = $quantity * $serviceCharge;

            // Estimate row height
            $description = $item['product_description'] ?? '';
            $descLines = substr_count($description, "\n") + count(array_filter(explode('•', $description))) + 1;
            $rowHeight = 5 + ($descLines * 3); // Base height + extra for description lines

            if (($currentHeight + $rowHeight + $totalsHeight + $bankDetailsHeight) > $maxHeight && !empty($pageItems)) {
                // Totals HTML for non-final pages (empty values)
                $totalsNonFinal = '
                    <tr>
                        <td colspan="7" class="continue-text">Continue to next page Page ' . $currentPage . '/' . $totalPages . '</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: right; border: none; border-top: 0.3px solid #000; border-left: 0.3px solid #000;">Total Amount</td>
                        <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000; border-top: 0.3px solid #000;"></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Vat 5%</td>
                        <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;"></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Grand Total Amount Included VAT</td>
                        <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;"></td>
                    </tr>
                    <tr>
                        <td colspan="6" style="vertical-align: bottom; border: none; border-left: 0.3px solid #000; border-top: 0.3px solid #000; padding-bottom: 2px; padding-top: 2px;"></td>
                        <td style="text-align: right; vertical-align: top; border: none; border-top: 0.3px solid #000; border-right: 0.3px solid #000; padding-top: 2px;">E.&O.E</td>
                    </tr>
                    <tr>
                        <td colspan="6" style="vertical-align: bottom; border: none; border-left: 0.3px solid #000; padding-bottom: 2px; padding-top: 2px;">Amount (In words)</td>
                        <td style="text-align: right; vertical-align: top; border: none; border-right: 0.3px solid #000;"></td>
                    </tr>';

                // Render the current page
                echo '<table class="invoice-info"><tbody>';
                echo $header;
                foreach ($pageItems as $pageItem) {
                    $pageServiceCharge = (float)($pageItem['service_charge'] ?? 0);
                    $pageQuantity = (float)($pageItem['quantity'] ?? 0);
                    $pageItemTotal = $pageQuantity * $pageServiceCharge;

                    echo '<tr>
                        <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . $rowCount++ . '</td>
                        <td class="description" style="border: none; border-left: 0.3px solid #000;">' . htmlspecialchars($pageItem['product_name'] ?? '') . ':';
                    $parts = preg_split('/(Note:)/i', $pageItem['product_description'] ?? '', -1, PREG_SPLIT_DELIM_CAPTURE);
                    if (!empty(trim($parts[0]))) {
                        $points = array_filter(explode('•', trim($parts[0])));
                        foreach ($points as $point) {
                            if (!empty($point)) {
                                echo '<p style="margin: 2px 0;">• ' . htmlspecialchars(trim($point)) . '</p>';
                            }
                        }
                    }
                    if (count($parts) > 1) {
                        echo '<p style="margin: 5px 0;">Note: ' . htmlspecialchars(trim($parts[2] ?? '')) . '</p>';
                    }
                    echo '</td>
                        <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . htmlspecialchars($pageItem['quantity'] ?? '') . ' ' . htmlspecialchars($pageItem['uom'] ?? '') . '</td>
                        <td style="text-align: right; border: none; border-left: 0.3px solid #000;">' . number_format($pageServiceCharge, 2) . '</td>
                        <td style="text-align: center; border: none; border-left: 0.3px solid #000;">5%</td>
                        <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . number_format((float)$pageItem['item_discount'], 2) . '</td>
                        <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($pageItemTotal, 2) . '</td>
                    </tr>';
                }
                echo $totalsNonFinal; // Use empty totals for non-final pages
                echo '</tbody></table>';
                echo $bankDetails; // Now defined and accessible
                if ($index < $itemCount - 1) {
                    echo '<pagebreak />';
                }
                $pageItems = []; // Reset for next page
                $currentHeight = $headerHeight; // Start next page with header height
                $currentPage++; // Increment page number
            }

            $pageItems[] = $item;
            $currentHeight += $rowHeight;
        }

        // Render the final page if there are remaining items
        if (!empty($pageItems)) {
            // Totals HTML for final page (with values)
            $totalsFinal = '
                <tr>
                    <td colspan="6" style="text-align: right; border: none; border-top: 0.3px solid #000; border-left: 0.3px solid #000;">Total Amount</td>
                    <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000; border-top: 0.3px solid #000;">' . number_format($totalAmount, 2) . '</td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Vat 5%</td>
                    <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($vatAmount, 2) . '</td>
                </tr>
                <tr>
                    <td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Grand Total Amount Included VAT</td>
                    <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($grandTotal, 2) . '</td>
                </tr>
                <tr>
                    <td colspan="6" style="vertical-align: bottom; border: none; border-left: 0.3px solid #000; border-top: 0.3px solid #000; padding-bottom: 2px; padding-top: 2px;">' . strtoupper(convert_number_to_words($grandTotal)) . ' ONLY</td>
                    <td style="text-align: right; vertical-align: top; border: none; border-top: 0.3px solid #000; border-right: 0.3px solid #000; padding-top: 2px;">E.&O.E</td>
                </tr>
                <tr>
                    <td colspan="6" style="vertical-align: bottom; border: none; border-left: 0.3px solid #000; padding-bottom: 2px; padding-top: 2px;">Amount (In words)</td>
                    <td style="text-align: right; vertical-align: top; border: none; border-right: 0.3px solid #000;"></td>
                </tr>';

            echo '<table class="invoice-info"><tbody>';
            echo $header;
            foreach ($pageItems as $pageItem) {
                $pageServiceCharge = (float)($pageItem['service_charge'] ?? 0);
                $pageQuantity = (float)($pageItem['quantity'] ?? 0);
                $pageItemTotal = $pageQuantity * $pageServiceCharge;

                echo '<tr>
                    <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . $rowCount++ . '</td>
                    <td class="description" style="border: none; border-left: 0.3px solid #000;">' . htmlspecialchars($pageItem['product_name'] ?? '') . ':';
                $parts = preg_split('/(Note:)/i', $pageItem['product_description'] ?? '', -1, PREG_SPLIT_DELIM_CAPTURE);
                if (!empty(trim($parts[0]))) {
                    $points = array_filter(explode('•', trim($parts[0])));
                    foreach ($points as $point) {
                        if (!empty($point)) {
                            echo '<p style="margin: 2px 0;">• ' . htmlspecialchars(trim($point)) . '</p>';
                        }
                    }
                }
                if (count($parts) > 1) {
                    echo '<p style="margin: 5px 0;">Note: ' . htmlspecialchars(trim($parts[2] ?? '')) . '</p>';
                }
                echo '</td>
                    <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . htmlspecialchars($pageItem['quantity'] ?? '') . ' ' . htmlspecialchars($pageItem['uom'] ?? '') . '</td>
                    <td style="text-align: right; border: none; border-left: 0.3px solid #000;">' . number_format($pageServiceCharge, 2) . '</td>
                    <td style="text-align: center; border: none; border-left: 0.3px solid #000;">5%</td>
                    <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . number_format((float)$pageItem['item_discount'], 2) . '</td>
                    <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($pageItemTotal, 2) . '</td>
                </tr>';
            }
            echo $totalsFinal; // Use totals with values for final page
            echo '</tbody></table>';
            echo $bankDetails; // Now defined and accessible
        }
        ?>
    </div>
</body>
</html>
