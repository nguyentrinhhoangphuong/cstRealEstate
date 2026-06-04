<?php
class Home extends Trongate {

    private array $data = [];

    function __construct() {
        parent::__construct();
        $this->data['page_title'] = 'PropViet';
        $this->data['meta_desc'] = 'Nền tảng bất động sản cao cấp';
    }

    private function _render(string $view): void {
        $this->module('templates_frontend');
        $this->templates_frontend->top($this->data);
        $this->view($view, $this->data);
        $this->templates_frontend->bottom($this->data);
    }

    function index(): void {
        $this->data['page_title'] = 'PropViet — Trang chủ';
        $this->_render('index');
    }
}