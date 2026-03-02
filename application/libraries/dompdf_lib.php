<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dompdf_lib {

    protected $dompdf;

    public function __construct() {
        require_once APPPATH . 'libraries/dompdf/autoload.inc.php';
        $this->dompdf = new Dompdf\Dompdf();
    }

    public function set_option($key, $value) {
        $this->dompdf->set_option($key, $value);
    }

    public function set_paper($paper, $orientation = 'portrait') {
    $this->dompdf->setPaper($paper, $orientation);
}

    public function load_html($html) {
        $this->dompdf->loadHtml($html);
    }

    public function render() {
        try {
            $this->dompdf->render();
        } catch (\Exception $e) {
            log_message('error', 'Dompdf render error: ' . $e->getMessage());
            echo "Dompdf Error: " . $e->getMessage();
            exit;
        }
    }

    public function stream($filename = "document.pdf", $options = array("Attachment" => 0)) {
        $this->dompdf->stream($filename, $options);
    }

    public function output() {
        return $this->dompdf->output();
    }
}
