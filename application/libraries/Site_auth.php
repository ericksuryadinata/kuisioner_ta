<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Site Authenthication class
 * 
 * Very basic authentication for this site only
 * DO NOT USE AT COMPLEX PRODUCTION
 * 
 * @package     Site Authentication
 * @version     0.1
 * @author      Erick Surya <ericksuryadinata@gmail.com>
 */

class Site_auth{

    /**
     * Codeigniter
     * 
     * @access  private
     */
    private $ci;

    /**
     * Config Items
     * 
     * @access private
     */
    private $table;
    private $identifier;
    private $password;
    private $active;

    /**
     * Constructor
     */

    public function __construct(){
        $this->ci =& get_instance();
        $this->ci->load->library('session');
        $this->table = 'users';
        $this->identifier = array('username','email');
        $this->password = 'password';
        $this->active = 'active';
    }

    /**
     * Hash function
     * 
     * @access  private
     * @param   string      password    password to be stored
     * @return  string      hash        hash to be stored
     */
    private function hash($password){
        return password_hash($password,PASSWORD_DEFAULT);
    }

    /**
     * Verify function
     * 
     * @access  private
     * @param   string      password    password to authenticate
     * @param   string      hash        hash from database
     * @return  boolean     boolean     return TRUE if success
     */
    private function verify($password,$hash){
        if(password_verify($password,$hash)){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * check user function
     * 
     * @access  private
     * @param   string      field       Place that we search
     * @param   string      value       Needle that we search
     * @return  boolean     boolean     return TRUE if found
     */
    private function check_user($field, $value){
        $user = $this->ci->db->where($field, $value)->get($this->table);
        if($user->num_rows() == 0){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    /**
     * unset session
     * @access  private
     */
    private function unset_session(){
        $data = array(
            'username',
            'first_name',
            'last_name',
            'surename',
            'email',
            'userid',
            'platform',
            'browser',
			'login',
            'log_tanggal',
        );
        $this->ci->session->unset_userdata($data);
    }

    /**
     * Login Function
     * 
     * @access  public
     * @param   string  identifier      Identifier to authenticate
     * @param   string  password        Password to authenticate
     * @return  mixed   data            Return some data
     */

    public function login($identifier, $password){
        foreach($this->identifier as $identifier_field){
            if($this->check_user($identifier_field,$identifier)){
                $notif = 1;
                $table_field = $identifier_field;
                break;
            }else{
                $notif = 0;
            }
        }

        if($notif == 0){
            $message = array(
                'message' => 'Username atau Email tidak sesuai',
                'status' => FALSE,
                'data' => ''
            );
            return $message;
        }

        $user = $this->ci->db->where($table_field,$identifier)->get($this->table)->first_row();
        if($this->verify($password,$user->password) == TRUE){
            if($user->active != 0){
                $data = array(
                    'last_login' => time()
                );
                $where = array(
                    'id' => $user->id
                );
                $update = $this->ci->db->update($this->table,$data,$where);
                $user = $this->ci->db->where($table_field,$identifier)->get($this->table)->first_row();
                if($update){
                    $message = array(
                        'message' => 'Sukses',
                        'status' => TRUE,
                        'data' => $user
                    );
                }else{
                    $message = array(
                        'message' => 'Error',
                        'status' => FALSE,
                        'data' => ''
                    );
                }
                return $message;
            }else{
                $message = array(
                    'message' => 'Akun admin belum diaktifkan',
                    'status' => FALSE,
                    'data' => ''
                );
                return $message;
            }
        }else{
            $message = array(
                'message' => 'Username atau Email tidak sesuai',
                'status' => FALSE,
                'data' => ''
            );
            return $message;
        }
    }

    /**
     * logout
     * 
     * @access  public
     */
    public function logout(){
        $this->unset_session();
        $this->ci->session->sess_destroy();
        $this->ci->session->set_userdata(array(
			'logged_out' => $_SERVER['REQUEST_TIME']
        ));
        return TRUE;
    }

    /**
     * is_login
     * 
     * @access  public
     * @return  boolean     boolean     return TRUE if still have the session
     */
    public function is_login(){
        if($this->ci->session->userdata('login') == TRUE){
            return TRUE;
        }

        return FALSE;
    }

    /**
     * Create user
     * 
     * @access  public
     * @param   string  username            username to be stored
     * @param   string  password            password to be stored
     * @param   string  email               email to be stored
     * @param   array   additional_data     another data
     * @return  boolean boolean             return TRUE if success
     */
    public function create_user($username,$password,$email,$additional){
        if(!$this->is_login()){
            show_404();
        }

        $data = array(
            'username' => $username,
            'password' => $this->hash($password),
            'ip_address' => $this->ci->input->ip_address(),
            'email' => $email,
            'active' => 1,
            'created_on' => time(),
        );
        
        foreach($additional as $key => $value){
            $data[$key] = $value;
        }

        $insert = $this->ci->db->insert($this->table,$data);

        if($insert){
            return TRUE;
        }else{
            return FALSE;
        }
    }

    /**
     * Search user
     * @access  public
     * @param   array   id      id to be search
     * @return  mixed   data    data of the user
     */
    public function search_user($id){
        if(!$this->is_login()){
            show_404();
        }

        $user = $this->ci->db->where($id)->get($this->table);

        if($user->num_rows() != 0){
            $data = $user->first_row();
        }else{
            $data = NULL;
        }

        return $data;
    }

    /**
     * Delete user
     * @access  public
     * @param   array   id      id to be search
     * @return  boolean boolean TRUE if success
     */
    public function delete_user($id){
        if(!$this->is_login()){
            show_404();
        }

        $delete = $this->ci->db->delete($this->table,$id);

        if(!$delete){
            return FALSE;
        }else{
            return TRUE;
        }
    }

    /**
     * Update user
     * @access  public
     * @param   array   id          id to be updated
     * @param   string  username    username to be update
     * @param   string  password    password to be update
     * @param   string  email       email to be update
     * @param   array   additional  additional data to be update
     * @return  boolean boolean     TRUE if success update
     */
    public function update_user($id,$username,$password,$email,$additional){
        if(!$this->is_login()){
            show_404();
        }

        $data = array(
            'username' => $username,
            'password' => $this->hash($password),
            'ip_address' => $this->ci->input->ip_address(),
            'email' => $email,
            'active' => 1,
        );

        foreach($additional as $key => $value){
            $data[$key] = $value;
        }

        $update = $this->ci->db->update($this->table,$data,$id);
        
        if($update){
            return TRUE;
        }else{
            return FALSE;
        }
    }
}