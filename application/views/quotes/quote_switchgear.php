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
            margin: 0;
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
            page-break-inside: avoid;
        }
        .description {
            text-align: left;
            padding-left: 10px;
        }
        .continue-text {
            font-size: 10px;
            text-align: right;
        }
        .spacer-row td {
            border: none;
            border-left: 0.3px solid #000;
            padding: 0;
            height: 10px;
        }
        .spacer-row td:last-child {
            border-right: 0.3px solid #000;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <?php
        if (empty($items)) {
            echo '<p>No items available.</p>';
            $items = [];
        }

        $itemCount = count($items);
        $rowCount = 1;
        $currentHeight = 0;
        $maxHeight = 180; // Usable height in mm
        $pageItems = [];
        $currentPage = 1;
        $totalPages = 0;

        // Predefined heights
        $headerHeight = 40;
        $totalsHeight = 40; // Increased to account for new rows (Discount, Adjustment)
        $bankDetailsHeight = 30;
        $spacerHeight = 2.8;

        // Initialize totals
        $totalAmount = 0; // Sum of discounted item totals
        $vatRate = 0.05;  // 5% VAT
        $discount = (float)($task['discount'] ?? 0); // Additional discount from Zoho CRM, default to 0
        $adjustment = (float)($task['adjustment'] ?? 0); // Adjustment from Zoho CRM, default to 0

        // First pass: Estimate total pages
        $tempHeight = $headerHeight;
        foreach ($items as $item) {
            $descLines = substr_count($item['product_description'] ?? '', "\n") + count(array_filter(explode('•', $item['product_description'] ?? ''))) + 1;
            $rowHeight = 5 + ($descLines * 3);
            if (($tempHeight + $rowHeight) > ($maxHeight - $totalsHeight - $bankDetailsHeight)) {
                $totalPages++;
                $tempHeight = $headerHeight + $rowHeight;
            } else {
                $tempHeight += $rowHeight;
            }
        }
        if ($tempHeight > $headerHeight) {
            $totalPages++;
        }

        // Calculate validity days
        $validUntilTimestamp = strtotime($task['valid_until'] ?? '');
        $currentTimestamp = time();
        $daysLeft = ($validUntilTimestamp > $currentTimestamp) ? ceil(($validUntilTimestamp - $currentTimestamp) / (24 * 60 * 60)) : 0;

        // Header HTML
        $header = '
        <tr class="header-row" style="border: none;">
            <th colspan="7"><h2 class="invoice-title">SALES QUOTATION</h2></th>
        </tr>
        <tr class="header-row">
            <td colspan="2" style="width: 50%; border: none; border-left: 0.3px solid #000; border-top: 0.3px solid #000; padding: 0 0 0 5px;">To: <br>
            </td>
            <td colspan="3" style="width: 25%;">Quote Ref: ' . htmlspecialchars($task['quote_deal_number'] ?? '') . '</td>
            <td colspan="2" style="width: 25%;">Date: ' . htmlspecialchars(date('d-m-Y', strtotime($task['updated_at']))) . '</td>
        </tr>
        <tr class="header-row">
            <td colspan="2" style="width: 50%; border: none; border-left: 0.3px solid #000; padding: 0 0 0 5px;">
                <strong>Contact: </strong>' . htmlspecialchars($task['deal_name'] ?? '') . '  <br>
                <strong>Customer: </strong>' . htmlspecialchars($task['account_name'] ?? '') . '
            </td>
            <td colspan="3" style="width: 25%; vertical-align: middle;">Validity: ' . $daysLeft . ' days</td>
            <td colspan="2" style="width: 25%; vertical-align: middle;">Currency: AED</td>
        </tr>
        <tr class="header-row">
            <td colspan="2" style="width: 50%; border: none; border-left: 0.3px solid #000; vertical-align: top; padding: 0 0 0 5px;">
                <strong>Address: </strong>' . htmlspecialchars($task['address'] ?? '') . ' <br>
            </td>
            <td colspan="3" style="width: 25%;">Payment: CDC/CASH</td>
            <td colspan="2" style="width: 25%;">Sales By: ' . htmlspecialchars($sale_name ?? ucfirst($username ?? '')) . '</td>
        </tr>
        <tr class="header-row">
            <td colspan="2" style="width: 49%; border: none; border-left: 0.3px solid #000; padding: 0; vertical-align: top; margin: 0;">
                <table style="width: 100%;">
                    <tr>
                        <td style="width: 40%; border: none; padding: 0 0 0 5px; vertical-align: middle;">
                            <strong>P.O Box: </strong>' . htmlspecialchars($task['p_box'] ?? '') . '
                        </td>
                        <td style="width: 60%; border: none; padding: 0 0 0 5px; vertical-align: middle;">
                            <strong>TRN: </strong>' . htmlspecialchars($task['trn'] ?? '') . '
                        </td>
                    </tr>
                    <tr>
                        <td style="width: 40%; border: none; padding: 0 0 0 5px; vertical-align: middle;">
                            <strong>TEL: </strong>' . htmlspecialchars($task['customer_contact'] ?? '') . '
                        </td>
                        <td style="width: 60%; border: none; padding: 0 0 0 5px; vertical-align: middle;">
                            <strong>Email: </strong>' . htmlspecialchars($task['customer_email'] ?? '') . '
                        </td>
                    </tr>
                </table>
            </td>
            <td colspan="3" rowspan="2" style="width: 25%; vertical-align: middle; border-bottom: 0.3px solid #000;">TRN: 104900827700003</td>
            <td colspan="2" rowspan="2" style="width: 25%; vertical-align: middle; border-bottom: 0.3px solid #000;">Delivery Terms: ' . htmlspecialchars($task['delivery'] ?? '') . '</td>
        </tr>
        <tr class="header-row">
            <!-- Empty row to account for rowspan="2" of TRN and Delivery Terms -->
        </tr>
        <tr class="header-row">
            <th style="width: 5%; font-weight: normal; padding-right: 2px; padding-left: 2px;">S.No.</th>
            <th style="width: 44%; font-weight: normal;">Description</th>
            <th style="width: 12%; font-weight: normal;">Quantity</th>
            <th style="width: 11%; font-weight: normal;">Rate</th>
            <th style="width: 6%; font-weight: normal;">Tax%</th>
            <th style="width: 7%; font-weight: normal;">Disc.%</th>
            <th style="width: 16%; font-weight: normal;">Amount</th>
        </tr>';

        // Bank Details HTML
        $bankDetails = '
        <table class="bank-details">
            <tbody>
                <tr>
                    <td style="height: px; width: 22%; border: none; border-left: 0.3px solid #000; border-top: 0.3px solid #000; padding: 0; padding-left: 5px; padding-top: 2px; vertical-align: top;" >
                        Note if any: ' . htmlspecialchars($task['notes'] ?? '') . '
                    </td>
                    <td style="height: px; width: 26%; border: none; border-top: 0.3px solid #000; border-left: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                    <td style="width: 40%; text-align: right; border: none; border-right: 0.3px solid #000; border-top: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px; padding-top: 2px;">for <strong>Voltronix Switchgear LLC</strong></td>
                </tr>
                <tr>
                    <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px; height: 30px; border-bottom: 0.3px solid #000;" rowspan="2">Warranty: ' . htmlspecialchars($task['warranty'] ?? '') . '</td>
                    <td style="border: none; border-left: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px; height: 30px;"></td>
                    <td style="border: none; border-right: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;"></td>
                </tr>
                <tr>
                    <td style="border: none; border-left: 0.3px solid #000; border-bottom: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px;">Received By:</td>
                    <td style="text-align: right; border: none; border-right: 0.3px solid #000; border-bottom: 0.3px solid #000; padding: 0; padding-left: 5px; padding-right: 5px; padding-bottom: 2px;">Authorized Signatory</td>
                </tr>
            </tbody>
        </table>';

        // Start rendering
        echo '<table class="invoice-info"><tbody>';
        echo $header;
        $currentHeight = $headerHeight;

        foreach ($items as $index => $item) {
            $serviceCharge = (float)($item['service_charge'] ?? 0);
            $quantity = (float)($item['quantity'] ?? 0);
            $discountPercent = (float)($item['item_discount'] ?? 0); // Discount as percentage
            $subTotal = $quantity * $serviceCharge; // Before discount
            $discountAmount = $subTotal * ($discountPercent / 100); // Discount value
            $itemTotal = $subTotal - $discountAmount; // After discount

            // Add to running total
            $totalAmount += $itemTotal;

            // Estimate row height
            $description = $item['product_description'] ?? '';
            $descLines = substr_count($description, "\n") + count(array_filter(explode('•', $description))) + 1;
            $rowHeight = 5 + ($descLines * 3.6);

            // Check if the item fits on the current page
            if (($currentHeight + $rowHeight) > ($maxHeight - $totalsHeight - $bankDetailsHeight) && $currentHeight > $headerHeight) {
                $remainingHeight = $maxHeight - ($currentHeight + $totalsHeight + $bankDetailsHeight);
                if ($remainingHeight > 0) {
                    $spacerCount = floor($remainingHeight / $spacerHeight);
                    for ($i = 0; $i < $spacerCount; $i++) {
                        echo '<tr class="spacer-row">
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td style="border-right: 0.3px solid #000;"> </td>
                        </tr>';
                        $currentHeight += $spacerHeight;
                    }
                }

                $totalsNonFinal = '
					<tr>
						<td colspan="7" class="continue-text">Continue to next page Page ' . $currentPage . '/' . $totalPages . '</td>
					</tr>
					<tr>
						<td colspan="6" style="text-align: right; border: none; border-top: 0.3px solid #000; border-left: 0.3px solid #000;">Total Amount</td>
						<td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000; border-top: 0.3px solid #000;"></td>
					</tr>';

				if ($discount != 0 && !is_null($discount)) {
					$totalsNonFinal .= '
					<tr>
						<td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Discount</td>
						<td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;"></td>
					</tr>';
				}

				$totalsNonFinal .= '
					<tr>
						<td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Vat 5%</td>
						<td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;"></td>
					</tr>';

				if ($adjustment != 0 && !is_null($adjustment)) {
					$totalsNonFinal .= '
					<tr>
						<td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Adjustment</td>
						<td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;"></td>
					</tr>';
				}

				$totalsNonFinal .= '
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

                echo $totalsNonFinal;
                echo '</tbody></table>';
                echo $bankDetails;
                echo '<pagebreak />';

                echo '<table class="invoice-info"><tbody>';
                echo $header;
                $currentHeight = $headerHeight;
            }

            // Render item with discounted amount
            echo '<tr>
                <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . $rowCount++ . '</td>
                <td class="description" style="border: none; border-left: 0.3px solid #000;"><b style="font-size: 12px;">' . htmlspecialchars($item['product_name'] ?? '') . '</b>:';
                    $description = $item['product_description'] ?? '';
                    if (!empty($description)) {
                        // Split the description into lines
                        $lines = preg_split('/\n|\r\n?/', trim($description));
                        $inNoteSection = false;
                        foreach ($lines as $line) {
                            $trimmedLine = trim($line);
                            if (empty($trimmedLine)) {
                                continue;
                            }
                            // Check if line starts the Note section
                            if (preg_match('/^Note:/i', $trimmedLine)) {
                                $inNoteSection = true;
                                $trimmedLine = rtrim($trimmedLine, '.');
                                echo '<p style="font-size: 12px; margin: 0; padding-left: 20px;"><b>' . htmlspecialchars($trimmedLine) . '</b></p>';
                                continue;
                            }
                            // Replace numbering (e.g., "1.", "2.") with bold bullet point for all lines
                            $displayLine = preg_replace('/^\d+\.\s*/', ' ', $trimmedLine);
                            // Handle lines with or without numbering
                            if (!empty($displayLine) && !preg_match('/^Any kind of faulty parts/i', $trimmedLine)) {
                                $padding = ($inNoteSection || preg_match('/^<b>•<\/b>/', $displayLine)) ? '40px' : '20px';
                                echo '<p style="font-size: 12px; margin: 0; padding-left: ' . $padding . ';">' . htmlspecialchars($displayLine, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', false) . '</p>';
                            }
                        }
                    }
            echo '</td>
                <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . htmlspecialchars(number_format((float)($item['quantity'] ?? 0), 0)) . ' ' . htmlspecialchars($item['uom'] ?? '') . '</td>
                <td style="text-align: right; border: none; border-left: 0.3px solid #000;">' . number_format($serviceCharge, 2) . '</td>
                <td style="text-align: center; border: none; border-left: 0.3px solid #000;">5%</td>
                <td style="text-align: center; border: none; border-left: 0.3px solid #000;">' . number_format($discountPercent, 2) . '</td>
                <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($itemTotal, 2) . '</td>
            </tr>';

            $currentHeight += $rowHeight;
            $pageItems[] = $item;
        }

        // Calculate final totals
        $vatAmount = (float)($task['tax_total'] ?? ($totalAmount * $vatRate)); // Use Zoho CRM VAT if available
        $subtotalAfterDiscount = $totalAmount - ($discount != 0 && !is_null($discount) ? $discount : 0);
        $grandTotal = $subtotalAfterDiscount + $vatAmount + ($adjustment != 0 && !is_null($adjustment) ? $adjustment : 0);

        // Fill remaining space on the final page
        $remainingHeight = $maxHeight - ($currentHeight + $totalsHeight + $bankDetailsHeight);
        if ($remainingHeight > 0) {
            $spacerCount = floor($remainingHeight / $spacerHeight);
            for ($i = 0; $i < $spacerCount; $i++) {
                echo '<tr class="spacer-row">
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td> </td>
                    <td style="border-right: 0.3px solid #000;"> </td>
                </tr>';
                $currentHeight += $spacerHeight;
            }
        }

        // Final page totals with conditional discount and adjustment
        $totalsFinal = '
            <tr>
                <td colspan="6" style="text-align: right; border: none; border-top: 0.3px solid #000; border-left: 0.3px solid #000;">Total Amount</td>
                <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000; border-top: 0.3px solid #000;">' . number_format($totalAmount, 2) . '</td>
            </tr>';

        // Conditionally add Discount row
        if ($discount != 0 && !is_null($discount)) {
            $totalsFinal .= '
            <tr>
                <td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Discount</td>
                <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($discount, 2) . '</td>
            </tr>';
        }

        $totalsFinal .= '
            <tr>
                <td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Vat 5%</td>
                <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($vatAmount, 2) . '</td>
            </tr>';

        // Conditionally add Adjustment row
        if ($adjustment != 0 && !is_null($adjustment)) {
            $totalsFinal .= '
            <tr>
                <td colspan="6" style="text-align: right; border: none; border-left: 0.3px solid #000;">Adjustment</td>
                <td style="text-align: right; border: none; border-left: 0.3px solid #000; border-right: 0.3px solid #000;">' . number_format($adjustment, 2) . '</td>
            </tr>';
        }

        $totalsFinal .= '
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

        echo $totalsFinal;
        echo '</tbody></table>';
        echo $bankDetails;
        ?>
    </div>
</body>
</html>
