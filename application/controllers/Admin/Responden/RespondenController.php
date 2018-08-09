<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
class RespondenController extends CI_Controller {

    public function __construct(){
        parent::__construct();
		$this->load->model(array('RespondenModel' => 'responden'));
		$this->load->library(array('upload','PHPExcel'));
        $this->surename = $this->session->userdata('surename');
		$this->email = $this->session->userdata('email');
		$this->page->sebar('ctrl',$this);
    }

    public function index(){
		$data['csrf'] = $this->getCsrf();
        $data['active_responden'] = 'active';
        $data['all'] = $this->responden->count_all();
        $data['completed'] = $this->responden->count_status_complete();
		echo $this->page->tampil('admin.responden.index',$data);
    }

	public function upload(){
        $data['active_responden'] = 'active';
		$data['csrf'] = $this->getCsrf();
		echo $this->page->tampil('admin.responden.upload',$data);
    }
	
	public function uploadResponden(){
		if(!$this->input->is_ajax_request()){
			show_404();
		}
    	if( !isset($_FILES['file_responden']['name']) || empty($_FILES['file_responden']['name'])){
            echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, File Belum Dipilih')));
        }else{
            $ext = pathinfo($_FILES['file_responden']['name'],PATHINFO_EXTENSION);
            $id = 'RESPONDEN.'.$ext;
            //$id = preg_replace('/\s+/', '_', $_FILES['file']['name']);
            $fileName = date('Y_m_d_His').'_RSPDN_'.$id;
            $config['upload_path'] = upload_path('','files');
            $config['file_name'] = $fileName;
            $config['allowed_types'] = 'ods|xls|xlsx|csv';
            $config['max_size'] = 10000;
             
            $this->upload->initialize($config);
             
            if(! $this->upload->do_upload('file_responden') ){
                $err = $this->upload->display_errors();
				echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, Ekstensi File Salah'.$err)));
				die;
            }
                 
            $media = $this->upload->data('file_responden');
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
                    "nama_responden" => $rowData[0][0],
                    "jenis_kelamin" => $rowData[0][1],
                    "nomor_identitas" => $rowData[0][2],
                    "status" => $rowData[0][3],
					'created_at' => Carbon::now(),
                );
                 
                //sesuaikan nama dengan nama tabel
                $insert = $this->db->insert("responden",$data);
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
		$list = $this->responden->get_data();
		$data = array();
		$no = $_GET['start'];
		foreach ($list as $responden) {
			$no++;
			$row = array();
            $row[] = $no;
            $row[] = $responden->nomor_identitas;
            $row[] = $responden->nama_responden;
            if($responden->jenis_kelamin === 'L'){
                $row[] = 'Laki - Laki';
            }else{
                $row[] = 'Perempuan';
            }
            if($responden->status == 0){
                $row[] = '<span class="label bg-red">Belum Selesai</span>';
            }else{
                $row[] = '<span class="label bg-green">Selesai</span>';
            }
			$data[] = $row;
		}

		$output = array('draw' => $_GET['draw'],
						'recordsTotal' => $this->responden->count_all(),
						'recordsFiltered' => $this->responden->count_filtered(),
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