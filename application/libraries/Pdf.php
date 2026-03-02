<?php

if(!defined('BASEPATH')) exit('No direct script access allowed');

require_once dirname(__FILE__) . '/tcpdf/tcpdf.php';

class Pdf extends TCPDF { 
	function __construct() { 
		parent::__construct(); 
	}
	
	//Page header
    public function Header() {
        // Set font
		$image_file = K_PATH_IMAGES.'/img/Capture.png';
        $this->Image($image_file, 10, 10, 15, '', 'png', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->SetFont('helvetica', 'R', 12);
        // Title
		$this->Ln(4);
        $this->Cell(0, 15, 'Republic of the Philippines', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(6);
		$this->Cell(0, 15, 'Lung Center of the Philippines', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(6);
        $this->Cell(0, 15, 'PhilHealth eClaims and eSOA', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		$this->Ln(6);
		
    }

    // Page footer
    // public function Footer() {
    //     // Position at 15 mm from bottom
    //     $this->SetY(-50);
    //     // Set font
    //     $this->SetFont('helvetica', '', 8);
    //     // Add the custom footer HTML
    //     $footerHtml = '
    //         <table>
    //             <tr>
    //                 <td colspan="3">
    //                     <table>
    //                         <tr>
    //                             <td style="border: 1px solid black; text-align: center;">
    //                                 Requested By:<br><br> ___________________<br>
    //                                 Signature Over Printed name <br><br>___________________<br>
    //                                 Designation<br> <br>Date: _______________<br>
    //                             </td>
    //                             <td style="border: 1px solid black; text-align: center;">
    //                                 Approved By:<br><br> ___________________<br>
    //                                 Signature Over Printed name <br><br>___________________<br>
    //                                 Designation <br><br>Date: _______________<br>
    //                             </td>
    //                         </tr>
    //                     </table>
    //                 </td>
    //                 <td colspan="3">
    //                     <table>
    //                         <tr>
    //                             <td style="border: 1px solid black; text-align: center;">
    //                                 Received By:<br><br> ___________________<br>
    //                                 Signature Over Printed name <br><br>___________________<br>
    //                                 Designation <br><br>Date: _______________<br>
    //                             </td>
    //                             <td style="border: 1px solid black; text-align: center;">
    //                                 Checked By:<br><br> ___________________<br>
    //                                 Signature Over Printed name <br><br>___________________<br>
    //                                 Designation <br><br>Date: _______________<br>
    //                             </td>
    //                         </tr>
    //                     </table>
    //                 </td>
    //             </tr>
    //         </table>';
    //     $this->writeHTMLCell(0, 0, '', '', $footerHtml, 0, 1, 0, true, 'C', true);
    // }
}

?>