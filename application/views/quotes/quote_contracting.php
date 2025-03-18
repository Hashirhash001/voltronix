<br /><br /><br /><br />
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
    
    td {
        padding-top:3px;
        padding-bottom:3px;
        font-family: 'Roboto', sans-serif;
     }
     
     table {
        page-break-inside: avoid;
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
    
 
</style>
<div style="color: #000; font-family: 'Roboto', sans-serif;">

    <p style="text-align: center; font-size: 23px; margin-top:-70px !important; font-weight: bold;">QUOTATION</p>
    <p style="text-align:center; margin:0 !important; font-size:11px; padding-bottom: 10px;">
      <b><span style="border-bottom: 1px solid #000;">Kind Attention  : <?= htmlspecialchars($task['kind_attention'] ?? '') ?></span></b>
    </p>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">
              <table style="width: 100%;">
                <tr>
                  <td style="font-weight:bold; font-size:11px;">Subject:</td>
                  <td style="font-size:11px;"><b><?= htmlspecialchars($task['subject'] ?? '') ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold; font-size:11px;">Project:</td>
                  <td style="font-size:11px;"><b><?= htmlspecialchars($task['project_name'] ?? '') ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;font-size:11px;">Sales Person:</td>
                  <td style="font-size:11px;"><b><?= htmlspecialchars(ucfirst($username ?? '')) ?></b></td>
                </tr>
              </table>
            </td>
            <td style="width: 50%; padding: 0px 0;">
               <table style="width: 100%;">
                <tr>
                  <td style="font-weight:bold; font-size:11px; text-align: right;">Quote Ref:</td>
                  <td style="text-align:right; font-size:11px;"><b><?= htmlspecialchars($task['quote_number'] ?? '') ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold; font-size:11px; text-align: right;">Date:</td>
                  <!--<td style="text-align:right;"><b><?= date('M j, Y h:i A') ?></b></td>-->
                  <td style="text-align:right; font-size:11px;"><b><?= htmlspecialchars(date('M j, Y h:i A', strtotime($task['updated_at']))) ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold; font-size:11px; text-align: right;">Valid Till:</td>
                  <td style="text-align:right; font-size:11px;"><b><?= htmlspecialchars(date('M j, Y', strtotime($task['valid_until']))) ?></b></td>
                </tr>
              </table>
              
            </td>
        </tr>
    </table>

    <div style="border-top: 2px dotted #d9d9d9; background-size: 1px 1px; background-repeat: repeat-x; margin: 5px 0;"></div>

    <p style="font-size: 12px; margin: 0 !important; padding-top: 5px;padding-bottom: 10px;">We thank you for inviting us to quote for the subject and are pleased to submit our proposal in the following:</p>
    <p style="font-size: 12px; margin: 0 !important; padding-bottom: 15px;"><b>1. <span style="border-bottom: 1px solid #000;">SCOPE OF WORK</span>:</b></p>
    <table style="border: 1px solid #e3dfcb; width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #ee473f; color: #fff;">
                <td style="border: 1px solid #e3dfcb; width: 7%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">S.No</td>
                <td style="border: 1px solid #e3dfcb; width: 45%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Item & Description</td>
                <td style="border: 1px solid #e3dfcb; width: 10%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Qty.</td>
                <td style="border: 1px solid #e3dfcb; width: 10%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">U.O.M</td>
                <td style="border: 1px solid #e3dfcb; width: 15%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Unit Price</td>
                <td style="border: 1px solid #e3dfcb; width: 15%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Amount</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #e3dfcb; padding: 10px; width: 7%; text-align:center; vertical-align: top;">1</td>
                <td style="border: 1px solid #e3dfcb; padding: 10px; width: 45%; text-align:left;"><b style="font-size:12px;">
					<?= htmlspecialchars($task['product_name'] ?? '') ?>:</b> 
						<?php 
							// Extract product description
							$description = $task['product_description'] ?? '';
						
							// Check if "Note:" exists in the description
							$parts = preg_split('/(Note:)/i', $description, 2, PREG_SPLIT_DELIM_CAPTURE);
						
							// Process the main description before "Note:"
							if (!empty(trim($parts[0]))) {
								// Split the main description by bullets
								$points = explode('•', trim($parts[0]));
								foreach ($points as $point) {
									$trimmedPoint = trim($point);
									if (!empty($trimmedPoint)) {
										// Use <p> tag with custom bullet point symbol
										echo "<p style='font-size: 12px; margin: 0; padding-left: 20px;'>• " . htmlspecialchars($trimmedPoint) . "</p>";
									}
								}
							}
						
							// Display the "Note:" and its content if present
							if (count($parts) > 1) {
								echo "<br><p style='font-size: 12px; padding-top: 30px;'>" . htmlspecialchars($parts[1] . ' ' . trim($parts[2])) . "</p>";
							}
                    ?>
                </td>
                <td style="border: 1px solid #e3dfcb; padding: 10px; width: 7%; text-align:center; vertical-align: top; font-size:13px;"><?= htmlspecialchars($task['quantity'] ?? '') ?></td>
                <td style="border: 1px solid #e3dfcb; padding: 10px; width: 10%; text-align:center; vertical-align: top; font-size:13px;"><?= htmlspecialchars($task['uom'] ?? '') ?></td>
                <td style="border: 1px solid #e3dfcb; padding: 10px; width: 15%; text-align:center; vertical-align: top; font-size:13px;">AED <?= number_format((float)$task['service_charge'], 2) ?></td>
                <td style="border: 1px solid #e3dfcb; padding: 10px; width: 15%; text-align:center; vertical-align: top; font-size:13px;">AED <?= number_format((float)$task['quantity'] * (float)$task['service_charge'], 2) ?></td>
            </tr>
        </tbody>
    </table>

    <p style="font-size: 12px; margin: 0 !important; padding-bottom: 13px; padding-right: 5px; padding-top: 10px;"><b>2. <span style="border-bottom: 1px solid #000;">PAYMENT TERMS</span>  : </b><?= htmlspecialchars($task['terms_of_payment']) ?><p/>
    <p style="font-size: 12px; margin: 0 !important; padding-bottom: 13px; padding-right: 5px;"><b>3. <span style="border-bottom: 1px solid #000;">PRICE</span>  : AED <?= number_format((float)$task['quantity'] * (float)$task['service_charge'], 2) ?> - Excl. VAT
    </b>
    </p>
    <p style="font-size: 12px; margin: 0 !important; padding-bottom: 13px; padding-right: 5px;"><b>4. <span style="border-bottom: 1px solid #000;">GENERAL EXCLUSION</span>  : </b></p>
        <span>
            <?php 
                // Extract general exclusion content
                $general_exclusion = $task['general_exclusion'] ?? '';
    
                if (!empty(trim($general_exclusion))) {
                    // Split the content into lines for processing
                    $lines = preg_split('/\r\n|\r|\n/', trim($general_exclusion));
    
                    foreach ($lines as $line) {
                        $line = trim($line);
    
                        // Check if the line starts with "Note:"
                        if (stripos($line, 'Note:') === 0 || stripos($line, 'Note-') === 0 || stripos($line, 'Notes-') === 0) {
                            // Render the note as a separate paragraph with <br> after it
                            $noteContent = substr($line, strpos($line, ':') + 1); // Extract content after 'Note:' or 'Note-'
                            echo "<p style='font-size: 12px; margin: 0; padding-left: 20px; font-style: italic;'>Note: " . htmlspecialchars($noteContent) . "</p><br>";
                        } elseif (!empty($line)) {
                            // Render other lines as bullet points
                            echo "<p style='font-size: 12px; margin: 0; padding-left: 20px;'>" . htmlspecialchars($line) . "</p>";
                        }
                    }
                } else {
                    // If the field is empty, show a fallback message
                    echo "<p style='font-size: 12px; padding-left: 20px;'>No exclusions specified.</p>";
                }
            ?>
        </span>
        
    <p style="font-size: 12px; margin: 0; padding-top: 20px;">We trust the above offer is in line with your requirements and look forward to receiving your valued order.</p>
    <p style="margin: 0px; padding-bottom: 15px; font-size: 12px;">Yours faithfully,</p>
    <p style="padding-bottom: 10px; font-size: 12px; margin: 0;"><b>For VOLTRONIX CONTRACTING LLC,</b></p>
    <table style="width: 45%; margin: 0px; padding: 0px;">
        <tr>
            <td style=" width: 25%;">
                <div style="margin: 0px; padding: 0px; display: flex;">
                    <img src="<?= base_url('assets/photos/logo/sign.jpg') ?>" alt="Stamp" style="width: 100px;">
                  	
                </div>
                <p style="font-size: 14px;"><b>Marieswaran</b><br>Managing Partner<br>Mobile: 0502420957</p>
                
            </td>
            <td style="text-align: center; width: 20%;">
                <img src="<?= base_url('assets/photos/logo/stamp.jpg') ?>" alt="Stamp" style="width: 130px;">
            </td>
        </tr>
    </table>

</div>
