<br /><br /><br /><br />
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
<style>
    body {
        font-family: 'Roboto', sans-serif;
    }
  td{
    padding-top:3px;
    padding-bottom:3px;
  }
</style>
<div style="color: #000; font-family: Arial, sans-serif;">

    <h6 style="text-align: center; font-size:16px; margin-top:-70px !important;font-weight:bold;">QUOTATION</h6>
    <p style="text-align:center; margin:0 !important; font-size:11px;">
      <b><u>Kind Attention: <?= htmlspecialchars($task['kind_attention'] ?? '') ?></u></b>
    </p>

    <table style="width: 100%;">
        <tr>
            <td style="width: 50%;">
              <table style="width: 100%;">
                <tr>
                  <td style="font-weight:bold;">Subject:</td>
                  <td><b><?= htmlspecialchars($task['subject'] ?? '') ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Project:</td>
                  <td><b><?= htmlspecialchars($task['project_name'] ?? '') ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Sales Person:</td>
                  <td><b>Marieswaran</b></td>
                </tr>
              </table>
            </td>
            <td style="width: 50%; padding: 0px 0;">
               <table style="width: 100%;">
                <tr>
                  <td style="font-weight:bold;">Quote Ref:</td>
                  <td style="text-align:right;"><b><?= htmlspecialchars($task['quote_number'] ?? '') ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Date:</td>
                  <!--<td style="text-align:right;"><b><?= date('M j, Y h:i A') ?></b></td>-->
                  <td style="text-align:right;"><b><?= htmlspecialchars(date('M j, Y h:i A', strtotime($task['updated_at']))) ?></b></td>
                </tr>
                <tr>
                  <td style="font-weight:bold;">Valid Till:</td>
                  <td style="text-align:right;"><b><?= htmlspecialchars(date('M j, Y', strtotime($task['valid_until']))) ?></b></td>
                </tr>
              </table>
              
            </td>
        </tr>
    </table>

    <hr>

    <span style="font-size: 12px; padding-top: 5px;">We thank you for inviting us to quote for the subject and are pleased to submit our proposal in the following:</span>
    <p style="font-size: 12px; margin: 5 !important;"><b><u>1. SCOPE OF WORK</u></b></p>
    <table style="border: 1px solid #e3dfcb; width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #ee473f; color: #fff;">
                <td style="border: 1px solid #e3dfcb; width: 10%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">S.No</td>
                <td style="border: 1px solid #e3dfcb; width: 40%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Item & Description</td>
                <td style="border: 1px solid #e3dfcb; width: 10%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Qty.</td>
                <td style="border: 1px solid #e3dfcb; width: 10%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">U.O.M</td>
                <td style="border: 1px solid #e3dfcb; width: 15%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Unit Price</td>
                <td style="border: 1px solid #e3dfcb; width: 15%;height:30px;font-weight:600;text-align:center;color:#fff;padding-top:3px !important;padding-bottom:3px !important;">Amount</td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="border: 1px solid #e3dfcb; padding: 0px; width: 10%; text-align:center;">1</td>
                <td style="border: 1px solid #e3dfcb; padding: 0px; width: 40%; text-align:left;"><b style="font-size:12px;"><?= htmlspecialchars($task['product_name'] ?? '') ?></b> <br> <?= htmlspecialchars($task['product_description']) ?></td>
                <td style="border: 1px solid #e3dfcb; padding: 0px; width: 10%; text-align:center;"><?= htmlspecialchars($task['quantity'] ?? '') ?></td>
                <td style="border: 1px solid #e3dfcb; padding: 0px; width: 10%; text-align:center;"><?= htmlspecialchars($task['uom'] ?? '') ?></td>
                <td style="border: 1px solid #e3dfcb; padding: 0px; width: 15%; text-align:center;">AED <?= number_format((float)$task['service_charge'], 2) ?></td>
                <td style="border: 1px solid #e3dfcb; padding: 0px; width: 15%; text-align:center;">AED <?= number_format((float)$task['quantity'] * (float)$task['service_charge'], 2) ?></td>
            </tr>
        </tbody>
    </table>
    <br>

    <span style="font-size: 12px; margin: 5 !important;"><b><u>2. PAYMENT TERMS: </u></b><?= htmlspecialchars($task['terms_of_payment']) ?></span><br>
    
    <span style="font-size: 12px; margin: 5 !important;"><b><u>3. PRICE:</u> AED <?= number_format((float)$task['quantity'] * (float)$task['service_charge'], 2) ?> - Excl. VAT</b></span><br>
    <span style="font-size: 12px; margin: 5 !important;"><b><u>4. GENERAL EXCLUSION:  </u></b><br>
        <span> <?= htmlspecialchars($task['general_exclusion']) ?> </span>
    </span><br>
    
    <!--<span style="font-size: 12px !important; margin: 5 !important;"><b>4. GENERAL EXCLUSION: </b><br>-->
            <!--<span>   <b>.</b> All kind of other authority approvals like DM/DCD/DDA/RTA/TARKHEES.</span><br>-->
            <!--<span>   <b>.</b> Any other items/area not prescribed in the above scope of work will be done with additional cost.</span><br>-->
            <!--<span>   <b>.</b> Any kind of authority fees like security deposit, estimation fees and connection charges.</span><br>-->
    <!--</span>-->
    
    <hr>
    <br>
    <span style="font-size: 12px;">We trust the above offer is in line with your requirements and look forward to receiving your valued order.</span><br>
    <span style="margin: 0px; padding-bottom: 0px; font-size: 12px;">Yours faithfully,<br><br><br></span>

    <table style="width: 60%; margin: 0px; padding-bottom: 0px;">
        <tr>
            <td style=" width: 25%;">
                <span style="padding-bottom: 0px; font-size: 10px; margin: 0 0 0 0;"><b>For VOLTRONIX CONTRACTING LLC,</b></span>
                <div style="margin: 0px; padding: 0px; display: flex;">
                    <img src="<?= base_url('assets/photos/logo/sign.jpg') ?>" alt="Stamp" style="width: 100px;">
                  	
                </div>
                <span style="font-size: 12px;"><b>Marieswaran</b><br>Managing Partner<br>Mobile: 0502420957</span>
                
            </td>
            <td style="text-align: center; width: 25%;">
                <img src="<?= base_url('assets/photos/logo/stamp.jpg') ?>" alt="Stamp" style="width: 130px;">
            </td>
        </tr>
    </table>

</div>
