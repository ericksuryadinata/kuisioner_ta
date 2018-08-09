<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminUserModel extends CI_Model{

    private $table = 'users';
    private $column_order = array(null,'first_name','last_name','username','email','active');
	private $column_search = array('first_name','last_name','username','email','active');
    private $order_by = array('id'=>'asc');

    private function _get(){
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
	
	public function total(){
		return $this->db->count_all($this->table);
	}

}