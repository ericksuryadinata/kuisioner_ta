<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
class AspekKuisionerController extends CI_Controller {

    public function __construct(){
        parent::__construct();
        $this->load->model(array('AspekKuisionerModel' => 'aspekkuisioner'));
        $this->surename = $this->session->userdata('surename');
		$this->email = $this->session->userdata('email');
		$this->page->sebar('ctrl',$this);
    }

    public function index(){
		$data['csrf'] = $this->getCsrf();
        $data['active_kuisioner'] = 'active';
        $data['active_kuisioner_aspek'] = 'active';
		echo $this->page->tampil('admin.kuisioner.aspek.index',$data);
    }
    
    public function create(){
        $data['active_kuisioner'] = 'active';
        $data['active_kuisioner_aspek'] = 'active';
		echo $this->page->tampil('admin.kuisioner.aspek.create',$data);
    }

    public function edit($id){
        $id_aspek = array('id' => $id);
        $data['active_kuisioner'] = 'active';
        $data['active_kuisioner_aspek'] = 'active';
        $data['aspek_kuisioner'] = $this->aspekkuisioner->search($id_aspek)->first_row();
		echo $this->page->tampil('admin.kuisioner.aspek.edit',$data);
    }

    public function delete(){
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $id = $this->input->post('id');
        $id_aspek = array('id' => $id);
        if($this->aspekkuisioner->delete($id_aspek) != false){
            echo json_encode($this->success('delete',array('pesan' => 'Berhasil hapus data')));
        }else{
            echo json_encode($this->error('delete',array('pesan' => 'Gagal hapus data')));
        }
    }

    public function save(){
        $nama_aspek = $this->security->xss_clean($this->input->post('nama_aspek'));

        $data = array(
            'nama_aspek' => $nama_aspek,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by' => $this->session->userdata('userid'),
            'updated_by' => $this->session->userdata('userid'),
        );

        if($this->aspekkuisioner->save($data)){
			$this->session->set_flashdata($this->success('save',array('pesan' => 'Berhasil Simpan Data')));
			redirect(route('admin.kuisioner.aspek.index'));
		}else{
			$this->session->set_flashdata($this->error('save',array('pesan' => 'Gagal Simpan Data')));
			redirect(route('admin.kuisioner.aspek.index'));
		}
    }

    public function update(){
        $nama_aspek = $this->security->xss_clean($this->input->post('nama_aspek'));
        $id = $this->input->post('id');
        $id_aspek = array('id' => $id);
        
        $data = array(
            'nama_aspek' => $nama_aspek,
            'updated_at' => Carbon::now(),
            'updated_by' => $this->session->userdata('userid'),
        );

        if($this->aspekkuisioner->update($data,$id_aspek)){
			$this->session->set_flashdata($this->success('save',array('pesan' => 'Berhasil Simpan Data')));
			redirect(route('admin.kuisioner.aspek.index'));
		}else{
			$this->session->set_flashdata($this->error('save',array('pesan' => 'Gagal Simpan Data')));
			redirect(route('admin.kuisioner.aspek.index'));
		}
    }

    public function datatable(){
		if(!$this->input->is_ajax_request()){
            show_404();
        }
		$list = $this->aspekkuisioner->get_data();
		$data = array();
		$no = $_GET['start'];
		foreach ($list as $aspekkuisioner) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $aspekkuisioner->nama_aspek;
			$row[] = '<a href="'.route("admin.kuisioner.aspek.edit",['id'=>$aspekkuisioner->id]).'" class="btn bg-indigo waves-effect">Edit</a> 
			<button type="button" class="btn bg-red waves-effect hapus" data-id="'.$aspekkuisioner->id.'">Hapus</button>';
			$data[] = $row;
		}

		$output = array('draw' => $_GET['draw'],
						'recordsTotal' => $this->aspekkuisioner->count_all(),
						'recordsFiltered' => $this->aspekkuisioner->count_filtered(),
						'data' => $data
						);
		echo json_encode($output);
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

	private function success($param,array $condition = []){
		foreach($condition as $key => $value){
			$data[$key]= $value; 
		}
		if($param === 'save'){
			$data['method'] = 'save';
		}else if($param === 'update'){
			$data['method'] = 'update';
		}else if($param === 'delete'){
            $data['method'] = 'delete';
        }

		$data['message'] = 'success';
		$data['csrf'] = $this->getCsrf();
		return $data;
	}

	private function error($param,array $condition = []){
		foreach($condition as $key => $value){
			$data[$key]= $value; 
		}
		if($param === 'save'){
			$data['method'] = 'save';
		}else if($param === 'update'){
			$data['method'] = 'update';
		}else if($param === 'delete'){
            $data['method'] = 'delete';
        }

		$data['message'] = 'error';
		$data['csrf'] = $this->getCsrf();
		return $data;
	}


}