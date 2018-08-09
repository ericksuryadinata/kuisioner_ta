<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model(array('DashboardModel' => 'dashboard','AssessmentModel' => 'assessment','AspekKuisionerModel' => 'aspekkuisioner','RespondenModel' => 'responden','RekomendasiModel' => 'rekomendasi'));
		$this->surename = $this->session->userdata('surename');
		$this->email = $this->session->userdata('email');
		$this->page->sebar('ctrl',$this);
	}
	
	public function index(){
		$data['csrf'] = $this->getCsrf();
		$data['active_dashboard'] = 'active';
		$iterasi = floor($this->responden->count_status_complete() / $this->assessment->get_max_responden());
		if($iterasi == 0){
			$data['result'] = [];
		}else{
			for ($i=0; $i < $iterasi; $i++) { 
				$aspek = $this->assessment->rata_per_aspek($i)->result();
				for ($j=0; $j < count($aspek); $j++) { 
					$result[$i]['label'][$j] = $aspek[$j]->nama_aspek;
					$result[$i]['data'][$j] = $aspek[$j]->rata;
				}
			}
			$data['result'] = $result;
		}
		$data['aspek'] = $this->aspekkuisioner->all()->result();
		echo $this->page->tampil('admin.dashboard.index',$data);
	}


	/**
	 * Private function for this page only
	 */

	private function getCsrf(){
		return array(
			'name' => $this->security->get_csrf_token_name(),
			'token' => $this->security->get_csrf_hash(),
		);
	}

}
