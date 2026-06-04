<?php error_reporting(E_ALL); ini_set('display_errors', 1); ?>

<?php
class Templates_frontend extends Trongate {

    function top(array $data = []): void {
        $this->view('top', $data);
    }

    function bottom(array $data = []): void {
        $this->view('bottom', $data);
    }

}