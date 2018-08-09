<?php

/**
 * Middleware khusus admin
 * Dilakukan pengecekan dahulu sebelum bisa masuk ke dashboard
 */
class AdminMiddleware{

    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->library('session');
    }
    
    public function run(){
        if($this->ci->session->userdata('login') == false  || empty($this->ci->session->userdata('login'))){
            redirect(route('admin.auth.index'));
        }
    }
    
}