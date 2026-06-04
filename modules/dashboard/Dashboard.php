<?php
class Dashboard extends Trongate {

    public function index(): void {
        $this->trongate_security->make_sure_allowed();
        $data['headline'] = 'Dashboard';
        $data['view_module'] = $this->module_name;
        $data['view_file'] = 'index';
        $data['additional_includes_top'] = [
            BASE_URL . 'media_module/css/media.css'
        ];
        $this->templates->admin($data);
    }
}
