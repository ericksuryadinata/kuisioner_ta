<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RedirectController extends CI_Controller {

	public function index(){
        redirect(route('admin.auth.index'));
    }
}
