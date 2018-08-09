<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssessmentModel extends CI_Model{

    private $table = 'hasil_kuisioner';
    private $column_order = array(null, 'nama_responden','pertanyaan','jawaban','nama_aspek');
	private $column_search = array('nama_responden','pertanyaan','jawaban','nama_aspek');
	private $order_by = array('id_hasil'=>'asc');
	private $max_responden = 286;

    private function _get(){
        $this->db->select($this->table.'.id as id_hasil, responden.nama_responden, pertanyaan_kuisioner.pertanyaan, jawaban, aspek_kuisioner.nama_aspek');
        $this->db->join('responden','responden.id = '.$this->table.'.id_responden');
        $this->db->join('pertanyaan_kuisioner','pertanyaan_kuisioner.id = '.$this->table.'.id_pertanyaan');
		$this->db->join('aspek_kuisioner','aspek_kuisioner.id = pertanyaan_kuisioner.id_aspek');
		$this->db->where('responden.status','1');
		$this->db->from($this->table);
		$i=0;
		foreach ($this->column_search as $item) {
			if($_GET['search']['value']){
				if($i===0){
					$this->db->group_start();
					$this->db->like($item,$_GET['search']['value']);
				} else {
					$this->db->or_like($item, $_GET['search']['value']);
				}

				if(count($this->column_search) - 1 == $i){
					$this->db->group_end();
				}
			}
			$i++;
		}

		if(isset($_GET['order'])){
			$this->db->order_by($this->column_order[$_GET['order']['0']['column']],$_GET['order']['0']['dir']);
		} elseif (isset($this->order_by)) {
			$order = $this->order_by;
			$this->db->order_by(key($order),$order[key($order)]);
		}
	}

	public function get_data(){
		$this->_get();
		if($_GET['length'] != -1)
			$this->db->limit($_GET['length'], $_GET['start']);
		$query = $this->db->get();
		return $query->result();
	}

	public function count_filtered(){
		$this->_get();
		$query = $this->db->get();
		return $query->num_rows();
	}

	public function count_all(){
        $this->db->select($this->table.'.id as id_hasil, responden.nama_responden, pertanyaan_kuisioner.pertanyaan, jawaban, aspek_kuisioner.nama_aspek');
        $this->db->join('responden','responden.id = '.$this->table.'.id_responden');
        $this->db->join('pertanyaan_kuisioner','pertanyaan_kuisioner.id = '.$this->table.'.id_pertanyaan');
		$this->db->join('aspek_kuisioner','aspek_kuisioner.id = pertanyaan_kuisioner.id_aspek');
		$this->db->where('responden.status','1');
		$this->db->from($this->table);
		return $this->db->count_all_results();
	}

	public function save($data){
		return $this->db->insert($this->table, $data);
	}

	public function update($data,$where){
		return $this->db->update($this->table, $data, $where);
	}

	public function search($id){
		$this->db->where($id);
		return $this->db->get($this->table);
	}

	public function delete($id){
        $this->db->where($id);
        return $this->db->delete($this->table);
	}
	
	public function all(){
		return $this->db->get($this->table);
	}

	public function save_batch($data){
		return $this->db->insert_batch($this->table, $data);
	}


	public function get_max_responden(){
		return $this->max_responden;
	}

	public function rata_per_pertanyaan($offset,$cari = ''){
		$where =$cari;
		$query = 'select id_pertanyaan, 
		pertanyaan,
		avg(jawaban) as rata, 
		aspek_kuisioner.id as id_aspek, 
		aspek_kuisioner.nama_aspek as nama_aspek
		from hasil_kuisioner 
		join (select * from responden where status = 1 limit '.$this->max_responden.' offset '.$this->max_responden * $offset.')as v 
		on hasil_kuisioner.id_responden = v.id 
		join pertanyaan_kuisioner 
		on pertanyaan_kuisioner.id = hasil_kuisioner.id_pertanyaan
		join aspek_kuisioner
		on aspek_kuisioner.id = pertanyaan_kuisioner.id_aspek '.$where.' group by id_pertanyaan';
		return $this->db->query($query);
	}

	public function rata_per_aspek($offset){
		$query = 'select 
		sum(jawaban)/'.$this->max_responden.' as rata,
		aspek_kuisioner.id as id_aspek,
		aspek_kuisioner.nama_aspek as nama_aspek
		from hasil_kuisioner 
		join (select * from responden where status = 1 limit '.$this->max_responden.' offset '.$this->max_responden * $offset.')as v 
		on hasil_kuisioner.id_responden = v.id 
		join pertanyaan_kuisioner 
		on pertanyaan_kuisioner.id = hasil_kuisioner.id_pertanyaan
		join aspek_kuisioner
		on aspek_kuisioner.id = pertanyaan_kuisioner.id_aspek
		group by id_aspek';
		return $this->db->query($query);
	}

}