<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Carbon\Carbon;
class AssessmentController extends CI_Controller {

    public function __construct(){
		parent::__construct();
		$this->load->model(array('AssessmentModel' => 'assessment','AspekKuisionerModel' => 'aspekkuisioner','RespondenModel' => 'responden','RekomendasiModel' => 'rekomendasi'));
		$this->load->library(array('upload','PHPExcel'));
        $this->surename = $this->session->userdata('surename');
		$this->email = $this->session->userdata('email');
		$this->page->sebar('ctrl',$this);
    }

    public function tabel(){
		$data['csrf'] = $this->getCsrf();
        $data['active_assessment'] = 'active';
        $data['active_assessment_tabel'] = 'active';
		echo $this->page->tampil('admin.assessment.tabel',$data);
    }

    public function grafik(){
		$data['csrf'] = $this->getCsrf();
        $data['active_assessment'] = 'active';
		$data['active_assessment_grafik'] = 'active';
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
		echo $this->page->tampil('admin.assessment.grafik',$data);
    }

    public function dataGrafik(){
		$id_aspek = $this->input->get('aspek');
		$iterasi = floor($this->responden->count_status_complete() / $this->assessment->get_max_responden());
		if($iterasi == 0){
			$result = [];
		}else{
			if($id_aspek !== 'all'){
				$nama_aspek = $this->aspekkuisioner->search(array('id' => $id_aspek))->first_row();
				$r = array();
				$result['judul'] = $nama_aspek->nama_aspek;
				for ($i=0; $i < $iterasi; $i++) { 
					$aspek = $this->assessment->rata_per_pertanyaan($i,' where id_aspek = '.$id_aspek)->result();
					array_push($r,array());
					for ($j=0; $j < count($aspek); $j++) { 
						$rekomen = $this->rekomendasi->search(array('id_pertanyaan' => $aspek[$j]->id_pertanyaan))->result();
						for ($k=0; $k < count($rekomen); $k++) {
							if(round($aspek[$j]->rata) == $rekomen[$k]->nilai){
								array_push($r[$i],[
									'id_pertanyaan' => 'Q'.$aspek[$j]->id_pertanyaan,
									'n' => $aspek[$j]->rata,
									'nilai' => $rekomen[$k]->nilai,
									'rekomendasi' => $rekomen[$k]->rekomendasi
								]);
							} 
						}
						$result[$i]['label'][$j] = 'Q'.$aspek[$j]->id_pertanyaan;
						$result[$i]['data'][$j] = $aspek[$j]->rata;
					}
				}
			}else{
				$result['judul'] = 'SEMUA ASPEK';
				$r = array();
				for ($i=0; $i < $iterasi; $i++) { 
					array_push($r,array());
					$aspek = $this->assessment->rata_per_aspek($i)->result();
					for ($j=0; $j < count($aspek); $j++) {
						$result[$i]['label'][$j] = $aspek[$j]->nama_aspek;
						$result[$i]['data'][$j] = $aspek[$j]->rata;
					}
				}
			}	
		}
		echo json_encode($this->success('save',array('result' => $result,'rek' => $r)));
	}
	
	public function upload(){
        $data['active_assessment'] = 'active';
        $data['active_assessment_tabel'] = 'active';
		$data['csrf'] = $this->getCsrf();
		echo $this->page->tampil('admin.assessment.upload',$data);
    }
	
	public function uploadAssessment(){
		if(!$this->input->is_ajax_request()){
			show_404();
		}
    	if( !isset($_FILES['file_assessment']['name']) || empty($_FILES['file_assessment']['name'])){
            echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, File Belum Dipilih')));
        }else{
            $ext = pathinfo($_FILES['file_assessment']['name'],PATHINFO_EXTENSION);
            $id = 'ASSESSMENT.'.$ext;
            //$id = preg_replace('/\s+/', '_', $_FILES['file']['name']);
			$fileName = date('Y_m_d_His').'_ASMNT_'.$id;
            $config['upload_path'] = upload_path('','files');
            $config['file_name'] = $fileName;
            $config['allowed_types'] = 'ods|xls|xlsx|csv';
            $config['max_size'] = 10000;
             
            $this->upload->initialize($config);
             
            if(! $this->upload->do_upload('file_assessment') ){
                $err = $this->upload->display_errors();
				echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, Ekstensi File Salah'.$err)));
				die;
            }
                 
            $media = $this->upload->data('file_assessment');
            $inputFileName = upload_path('','files').$fileName; // linux
        
            try {
                $inputFileType = PHPExcel_IOFactory::identify($inputFileName);
                $objReader = PHPExcel_IOFactory::createReader($inputFileType);
                $objPHPExcel = $objReader->load($inputFileName);
            } catch(Exception $e) {
				echo json_encode($this->error('save',array('pesan' => 'Gagal Upload Data, Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage())));
				die;
            }
 
            $sheet = $objPHPExcel->getActiveSheet();
            $highestRow = $sheet->getHighestRow();
			$highestColumn = $sheet->getHighestColumn();
			$maxColumns = PHPExcel_Cell::columnIndexFromString($highestColumn);
			for ($row = 2; $row <= $highestRow; $row++){                  //  Read a row of data into an array                 
				$data = array();
				for($col = 1; $col < $maxColumns; $col++){
					array_push($data, [
						"id_responden" => $sheet->getCellByColumnAndRow(0, $row)->getValue(),
						"id_pertanyaan" => $col,
						"jawaban" => $sheet->getCellByColumnAndRow($col, $row)->getValue(),
					]);
				}                           
                //sesuaikan nama dengan nama tabel
                $insert = $this->db->insert_batch("hasil_kuisioner",$data);
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
		$list = $this->assessment->get_data();
		$data = array();
		$no = $_GET['start'];
		foreach ($list as $assessment) {
			$no++;
			$row = array();
			$row[] = $no;
			$row[] = $assessment->nama_responden;
			$row[] = $assessment->pertanyaan;
			$row[] = $assessment->jawaban;
			$row[] = $assessment->nama_aspek;
			$data[] = $row;
		}

		$output = array('draw' => $_GET['draw'],
						'recordsTotal' => $this->assessment->count_all(),
						'recordsFiltered' => $this->assessment->count_filtered(),
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