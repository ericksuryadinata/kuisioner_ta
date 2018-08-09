<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthController extends CI_Controller {
	
	public function __construct(){
		parent::__construct();
	}

	public function index(){
		if (!$this->site_auth->is_login()){
			echo $this->page->tampil('admin.auth.index');
		}else{
			redirect(route('admin.dashboard'));
		}
	}

	public function auth(){

		$this->form_validation->set_rules('username', 'Username', 'required');
		$this->form_validation->set_rules('password', 'Password', 'required');
		if ($this->form_validation->run() === TRUE){
			$login = $this->site_auth->login($this->input->post('username'), $this->input->post('password'));
			if ($login['status'] == TRUE)
			{
				$this->set_session($login['data']);
				redirect(route('admin.dashboard'));
			}
			else
			{
				$data['message'] = $login['message'];
				$data['hidden'] = '';
				echo $this->page->tampil('admin.auth.index',$data);
			}
		}else{
			$data['message'] = 'Data yang dimasukkan tidak sesuai';
			$data['hidden'] = '';
			echo $this->page->tampil('admin.auth.index',$data);
		}
	}

	public function logout(){

		$this->site_auth->logout();
		redirect(route('admin.auth.index'));
	}

	private function set_session($user){
        if ($this->agent->is_browser())
        {
            $agent = $this->agent->browser().' '.$this->agent->version();
        }
        elseif ($this->agent->is_robot())
        {
            $agent = $this->agent->robot();
        }
        elseif ($this->agent->is_mobile())
        {
            $agent = $this->agent->mobile();
        }
        else
        {
            $agent = 'Unidentified User Agent';
        }              
        $data = array(
            'username' => $user->username,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'surename' => $user->first_name.' '.$user->last_name,
            'email' => $user->email,
            'userid' => $user->id,
            'platform' => $this->agent->platform(),
            'browser' => $agent,
			'login' => true,
            'log_tanggal' => $user->last_login,
        );
       
        $this->session->set_userdata($data);
    }

}
