<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
class HomeController extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model(array('AspekKuisionerModel' => 'aspekkuisioner','KuisionerModel' => 'kuisioner','RespondenModel' => 'responden','AssessmentModel' => 'assessment'));
		$this->load->helper(array('form'));
	}

	public function index(){
		if($this->session->userdata('complete') != 1 && $this->session->userdata('logged')){
			redirect(route('home.kuisioner'));
		}else{
			if($this->session->userdata('complete') == 1){
				redirect(route('home.kuisioner.selesai'));
			}else{
				echo $this->page->tampil('website.home.index');
			}
		}
	}

	public function signUp(){
		$namalengkap = $this->security->xss_clean($this->input->post('namalengkap'));
		$jeniskelamin = $this->security->xss_clean($this->input->post('jeniskelamin'));
		$nomoridentitas = $this->security->xss_clean($this->input->post('nomoridentitas'));
		$responden = array(
			'nama_responden' => $namalengkap,
			'jenis_kelamin' => $jeniskelamin,
			'nomor_identitas' => $nomoridentitas,
			'status' => 0,
			'created_at' => Carbon::now()
		);
		$insert = $this->responden->save($responden);
		$session = array(
			'id_responden' => $this->db->insert_id(),
			'namalengkap' => $namalengkap,
			'jeniskelamin' => $jeniskelamin,
			'nomoridentitas' => $nomoridentitas,
			'complete' => 0,
			'jawaban' => [],
			'logged' => true,
			'step' => 1,
		);
		$this->session->set_userdata($session);
		redirect(route('home.kuisioner'));
	}

	public function kuisioner(){
		if($this->session->userdata('logged') != true){
			redirect(route('home.index'));
		}
		// $this->session->unset_userdata('jawaban');
		// $this->session->set_userdata('step',1);
		// $this->session->set_userdata('complete', 0);
		if($this->session->userdata('complete') == 1){
			redirect(route('home.kuisioner.selesai'));
		}else{
			$aspek = $this->aspekkuisioner->all()->result_array();
			$aspek_saat_ini = $aspek[$this->session->userdata('step') - 1]['id'];
			$data['aspek'] = $aspek[$this->session->userdata('step') - 1]['nama_aspek'];
			$data['soal'] = $this->kuisioner->search(array('id_aspek' => $aspek_saat_ini))->result();
			echo $this->page->tampil('website.kuisioner.index',$data);
		}
	}

	public function saveStep(){
		$final = $this->input->post('final');
		$answer = explode(',',$final);
		$data = array();
		if($this->session->userdata('jawaban') != NULL){
			$data = $this->session->userdata('jawaban');
		}
		$aspek = $this->aspekkuisioner->all()->result_array();
		$aspek_saat_ini = $aspek[$this->session->userdata('step') - 1]['id'];
		$nomor_soal = $this->kuisioner->search(array('id_aspek' => $aspek_saat_ini))->result_array();
		for ($i=0; $i < count($answer); $i++) { 
			array_push($data, [
				"id_responden" => $this->session->userdata('id_responden'),
				"id_pertanyaan" => $nomor_soal[$i]['id'],
				"jawaban" => (int)$answer[$i],
			]);
		}
		$this->session->set_userdata('jawaban',$data);
		$banyakAspek = $this->aspekkuisioner->count_all();
		if($this->session->userdata('step') == $banyakAspek){
			$this->session->set_userdata('complete', 1);
			redirect(route('home.kuisioner.selesai'));
		}else{
			$step = $this->session->userdata('step') + 1;
			$this->session->set_userdata('step',$step);
			redirect(route('home.kuisioner'));
		}
	}

	public function kuisionerSelesai(){
		echo $this->page->tampil('website.kuisioner.selesai');
	}

	public function signOut(){
		if($this->session->userdata('logged') != true && $this->session->userdata('complete') != 1){
			redirect(route('home.index'));
		}
		$insert = $this->assessment->save_batch($this->session->userdata('jawaban'));
		$update = $this->responden->update(array('status' => $this->session->userdata('complete')),array('id'=>$this->session->userdata('id_responden')));
		if($insert){
			$this->session->sess_destroy();
			redirect(route('home.index'));	
		}else{
			echo 'error';
		}
	}

}
