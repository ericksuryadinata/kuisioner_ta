<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
class KuisionerController extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model(array('KuisionerModel' => 'pertanyaankuisioner','AspekKuisionerModel' => 'aspekkuisioner'));
		$this->load->library(array('upload','PHPExcel'));
        $this->surename = $this->session->userdata('surename');
		$this->email = $this->session->userdata('email');
		$this->page->sebar('ctrl',$this);
    }

    public function index(){
		$data['csrf'] = $this->getCsrf();
        $data['active_kuisioner'] = 'active';
        $data['active_kuisioner_pertanyaan'] = 'active';
		echo $this->page->tampil('admin.kuisioner.pertanyaan.index',$data);
    }
    
    public function create(){
        $data['active_kuisioner'] = 'active';
		$data['active_kuisioner_pertanyaan'] = 'active';
		$data['aspek'] = $this->aspekkuisioner->all()->result();
		echo $this->page->tampil('admin.kuisioner.pertanyaan.create',$data);
	}
	
	public function upload(){
        $data['active_kuisioner'] = 'active';
		$data['active_kuisioner_pertanyaan'] = 'active';
		$data['csrf'] = $this->getCsrf();
		echo $this->page->tampil('admin.kuisioner.pertanyaan.upload',$data);
    }

    public function edit($id){
        $id_pertanyaan = array('id' => $id);
        $data['active_kuisioner'] = 'active';
        $data['active_kuisioner_pertanyaan'] = 'active';
		$data['pertanyaan_kuisioner'] = $this->pertanyaankuisioner->search($id_pertanyaan)->first_row();
		$data['aspek'] = $this->aspekkuisioner->all()->result();
		echo $this->page->tampil('admin.kuisioner.pertanyaan.edit',$data);
    }

    public function delete(){
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $id = $this->input->post('id');
        $id_pertanyaan = array('id' => $id);
        if($this->pertanyaankuisioner->delete($id_pertanyaan) != false){
            echo json_encode($this->success('delete',array('pesan' => 'Berhasil hapus data')));
        }else{
            echo json_encode($this->error('delete',array('pesan' => 'Gagal hapus data')));
        }
    }

    public function save(){
		$nama_pertanyaan = $this->security->xss_clean($this->input->post('nama_pertanyaan'));
		$id_aspek = $this->security->xss_clean($this->input->post('id_aspek'));

        $data = array(
			'pertanyaan' => $nama_pertanyaan,
			'id_aspek' => $id_aspek,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by' => $this->session->userdata('userid'),
            'updated_by' => $this->session->userdata('userid'),
        );

        if($this->pertanyaankuisioner->save($data)){
			$this->session->set_flashdata($this->success('save',array('pesan' => 'Berhasil Simpan Data')));
			redirect(route('admin.kuisioner.pertanyaan.index'));
		}else{
			$this->session->set_flashdata($this->error('save',array('pesan' => 'Gagal Simpan Data')));
			redirect(route('admin.kuisioner.pertanyaan.index'));
		}
    }

    public function update(){
        $nama_pertanyaan = $this->security->xss_clean($this->input->post('nama_pertanyaan'));
		$id_aspek = $this->security->xss_clean($this->input->post('id_aspek'));
        $id = $this->input->post('id');
        $id_pertanyaan = array('id' => $id);
        
        $data = array(
            'pertanyaan' => $nama_pertanyaan,
			'id_aspek' => $id_aspek,
            'updated_at' => Carbon::now(),
            'updated_by' => $this->session->userdata('userid'),
        );

        if($this->pertanyaankuisioner->update($data,$id_pertanyaan)){
			$this->session->set_flashdata($this->success('save',array('pesan' => 'Berhasil Simpan Data')));
			redirect(route('admin.kuisioner.pertanyaan.index'));
		}else{
			$this->session->set_flashdata($this->error('save',array('pesan' => 'Gagal Simpan Data')));
			redirect(route('admin.kuisioner.pertanyaan.index'));
		}
	}
	
	public function uploadKuisioner(){
		if(!$this->input->is_ajax_request()){
			show_404();
		}
    	if( !isset($_FILES['file_pertanyaan']['name']) || empty($_FILES['file_pertanyaan']['name'])){
            echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, File Belum Dipilih')));
        }else{
            $ext = pathinfo($_FILES['file_pertanyaan']['name'],PATHINFO_EXTENSION);
            $id = 'KUISIONER.'.$ext;
            //$id = preg_replace('/\s+/', '_', $_FILES['file']['name']);
            $fileName = date('Y_m_d_His').'_QSTN_'.$id;
            $config['upload_path'] = upload_path('','files');
            $config['file_name'] = $fileName;
            $config['allowed_types'] = 'ods|xls|xlsx|csv';
            $config['max_size'] = 10000;
             
            $this->upload->initialize($config);
             
            if(! $this->upload->do_upload('file_pertanyaan') ){
                $err = $this->upload->display_errors();
				echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, Ekstensi File Salah'.$err)));
				die;
            }
                 
            $media = $this->upload->data('file_pertanyaan');
            $inputFileName = upload_path('','files').$fileName; // linux
        
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
				echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage())));
				die;
            }
 
            $sheet = $objPHPExcel->getSheet(0);
            $highestRow = $sheet->getHighestRow();
            $highestColumn = $sheet->getHighestColumn();
             
            for ($row = 2; $row <= $highestRow; $row++){                  //  Read a row of data into an array                 
                $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
                                                NULL,
                                                TRUE,
                                                FALSE);
                                                 
                //Sesuaikan sama nama kolom tabel di database                                
                 $data = array(
                    "pertanyaan" => $rowData[0][0],
					"id_aspek" => $rowData[0][1],
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
					'created_by' => $this->session->userdata('userid'),
					'updated_by' => $this->session->userdata('userid')
                );
                 
                //sesuaikan nama dengan nama tabel
                $insert = $this->db->insert("pertanyaan_kuisioner",$data);
            }
            if($insert){
                echo json_encode($this->success('save',array('pesan' => 'Berhasil Upload Data')));
            } else {
                echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data')));
            }
        }
    }

    public function datatable(){
		if(!$this->input->is_ajax_request()){
            show_404();
        }
		$list = $this->pertanyaankuisioner->get_data();
		$data = array();
		$no = $_GET['start'];
		foreach ($list as $pertanyaankuisioner) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $pertanyaankuisioner->pertanyaan;
			$row[] = $pertanyaankuisioner->nama_aspek;
			$row[] = '<a href="'.route("admin.kuisioner.pertanyaan.edit",['id'=>$pertanyaankuisioner->id_pertanyaan]).'" class="btn bg-indigo waves-effect">Edit</a> 
			<button type="button" class="btn bg-red waves-effect hapus" data-id="'.$pertanyaankuisioner->id_pertanyaan.'">Hapus</button>';
			$data[] = $row;
		}

		$output = array('draw' => $_GET['draw'],
						'recordsTotal' => $this->pertanyaankuisioner->count_all(),
						'recordsFiltered' => $this->pertanyaankuisioner->count_filtered(),
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