<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
class RekomendasiController extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model(array('RekomendasiModel' => 'rekomendasi','AspekKuisionerModel' => 'aspekkuisioner'));
		$this->load->library(array('upload','PHPExcel'));
        $this->surename = $this->session->userdata('surename');
		$this->email = $this->session->userdata('email');
		$this->page->sebar('ctrl',$this);
    }

    public function index(){
		$data['csrf'] = $this->getCsrf();
        $data['active_kuisioner'] = 'active';
        $data['active_kuisioner_rekomendasi'] = 'active';
		echo $this->page->tampil('admin.kuisioner.rekomendasi.index',$data);
    }
    
    public function create(){
        $data['active_kuisioner'] = 'active';
		$data['active_kuisioner_rekomendasi'] = 'active';
		$data['aspek'] = $this->aspekkuisioner->all()->result();
		echo $this->page->tampil('admin.kuisioner.rekomendasi.create',$data);
	}
	
	public function upload(){
        $data['active_kuisioner'] = 'active';
		$data['active_kuisioner_rekomendasi'] = 'active';
		$data['csrf'] = $this->getCsrf();
		echo $this->page->tampil('admin.kuisioner.rekomendasi.upload',$data);
    }

    public function edit($id){
        $id_rekomendasi = array('id' => $id);
        $data['active_kuisioner'] = 'active';
        $data['active_kuisioner_rekomendasi'] = 'active';
		$data['rekomendasi_kuisioner'] = $this->rekomendasi->search($id_rekomendasi)->first_row();
		$data['aspek'] = $this->aspekkuisioner->all()->result();
		echo $this->page->tampil('admin.kuisioner.rekomendasi.edit',$data);
    }

    public function delete(){
        if(!$this->input->is_ajax_request()){
            show_404();
        }
        $id = $this->input->post('id');
        $id_rekomendasi = array('id' => $id);
        if($this->rekomendasi->delete($id_rekomendasi) != false){
            echo json_encode($this->success('delete',array('pesan' => 'Berhasil hapus data')));
        }else{
            echo json_encode($this->error('delete',array('pesan' => 'Gagal hapus data')));
        }
    }

    public function save(){
		$nama_rekomendasi = $this->security->xss_clean($this->input->post('nama_rekomendasi'));
		$id_aspek = $this->security->xss_clean($this->input->post('id_aspek'));

        $data = array(
			'pertanyaan' => $nama_rekomendasi,
			'id_aspek' => $id_aspek,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
            'created_by' => $this->session->userdata('userid'),
            'updated_by' => $this->session->userdata('userid'),
        );

        if($this->rekomendasi->save($data)){
			$this->session->set_flashdata($this->success('save',array('pesan' => 'Berhasil Simpan Data')));
			redirect(route('admin.kuisioner.rekomendasi.index'));
		}else{
			$this->session->set_flashdata($this->error('save',array('pesan' => 'Gagal Simpan Data')));
			redirect(route('admin.kuisioner.rekomendasi.index'));
		}
    }

    public function update(){
        $nama_rekomendasi = $this->security->xss_clean($this->input->post('nama_rekomendasi'));
		$id_aspek = $this->security->xss_clean($this->input->post('id_aspek'));
        $id = $this->input->post('id');
        $id_rekomendasi = array('id' => $id);
        
        $data = array(
            'pertanyaan' => $nama_rekomendasi,
			'id_aspek' => $id_aspek,
            'updated_at' => Carbon::now(),
            'updated_by' => $this->session->userdata('userid'),
        );

        if($this->rekomendasi->update($data,$id_rekomendasi)){
			$this->session->set_flashdata($this->success('save',array('pesan' => 'Berhasil Simpan Data')));
			redirect(route('admin.kuisioner.rekomendasi.index'));
		}else{
			$this->session->set_flashdata($this->error('save',array('pesan' => 'Gagal Simpan Data')));
			redirect(route('admin.kuisioner.rekomendasi.index'));
		}
	}
	
	public function uploadRekomendasi(){
		if(!$this->input->is_ajax_request()){
			show_404();
		}
    	if( !isset($_FILES['file_rekomendasi']['name']) || empty($_FILES['file_rekomendasi']['name'])){
            echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, File Belum Dipilih')));
        }else{
            $ext = pathinfo($_FILES['file_rekomendasi']['name'],PATHINFO_EXTENSION);
            $id = 'REKOMENDASI.'.$ext;
            //$id = preg_replace('/\s+/', '_', $_FILES['file']['name']);
            $fileName = date('Y_m_d_His').'_RKMD_'.$id;
            $config['upload_path'] = upload_path('','files');
            $config['file_name'] = $fileName;
            $config['allowed_types'] = 'ods|xls|xlsx|csv';
            $config['max_size'] = 10000;
             
            $this->upload->initialize($config);
             
            if(! $this->upload->do_upload('file_rekomendasi') ){
                $err = $this->upload->display_errors();
				echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, Ekstensi File Salah'.$err)));
				die;
            }
                 
            $media = $this->upload->data('file_rekomendasi');
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
                    "id_pertanyaan" => $rowData[0][0],
                    "nilai" => $rowData[0][1],
                    "rekomendasi" => $rowData[0][2],
					'created_at' => Carbon::now(),
					'updated_at' => Carbon::now(),
					'created_by' => $this->session->userdata('userid'),
					'updated_by' => $this->session->userdata('userid')
                );
                 
                //sesuaikan nama dengan nama tabel
                $insert = $this->db->insert("rekomendasi_kuisioner",$data);
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
		$list = $this->rekomendasi->get_data();
		$data = array();
		$no = $_GET['start'];
		foreach ($list as $rekomendasi) {
			$no++;
			$row = array();
			$row[] = $no;
            $row[] = $rekomendasi->pertanyaan;
            $row[] = $rekomendasi->nilai;
			$row[] = $rekomendasi->rekomendasi;
			$data[] = $row;
		}

		$output = array('draw' => $_GET['draw'],
						'recordsTotal' => $this->rekomendasi->count_all(),
						'recordsFiltered' => $this->rekomendasi->count_filtered(),
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