<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Report extends CI_Controller { // need to be separated because the user access level

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Jobpro_model');
        is_logged_in();
        date_default_timezone_set('Asia/Jakarta');
    }

    public function index(){
        $data['title'] = 'Report';
        $data['divisi'] = $this->Jobpro_model->getAllAndOrder('division', 'divisi');
        $data['dept'] = $this->Jobpro_model->getAllAndOrder('nama_departemen', 'departemen');
        $data['user'] = $this->db->get_where('employe', ['nik' => $this->session->userdata('nik')])->row_array();

        $task = $this->Jobpro_model->getAllAndOrder('nik', 'job_approval');
        $data['approval_data'] = $this->getApprovalDetails($task);

        // print_r($data['dept']);
        
        $this->load->view('templates/user_header', $data);
        $this->load->view('templates/user_sidebar', $data);
        $this->load->view('templates/user_topbar', $data);
        $this->load->view('jobs/report_v', $data);
        $this->load->view('templates/report_footer');
    }

    public function getApprovalDetails($my_task){ // Copied from Jobs Controller
        //lengkapi division, departement, nama position, nama employee nya
        foreach($my_task as $key => $value){
            $temp_employe = $this->Jobpro_model->getDetail("emp_name, id_div, id_dep, position_id", "employe", array('nik' => $value['nik']));
            $my_task[$key]['name'] = $temp_employe['emp_name'];
            foreach ($this->Jobpro_model->getDetail("position_name", "position", array('id' => $temp_employe['position_id'])) as $v){
                $my_task[$key]['posisi'] = $v;
            }
            foreach($this->Jobpro_model->getDetail("nama_departemen", "departemen", array('id' => $temp_employe['id_dep'])) as $v){
                $my_task[$key]['departement'] = $v;
            }
            foreach($this->Jobpro_model->getDetail("division", "divisi", array('id' => $temp_employe['id_div'])) as $v){
                $my_task[$key]['divisi'] = $v;
            }
        }

        return $my_task;
    }

    public function getHistoryApproval(){ //archived, need to change the job_approval database structure
        // print_r($this->input->post('divisi'));
        // print_r($this->input->post('departement'));
        // print_r($this->input->post('status'));
        // print_r($_POST['search']); //get search value from dataTables
        //output
        // Array
        // (
        //     [value] => ra
        //     [regex] => false
        // )

        
        // print_r($_POST['order']); //get order value from dataTables
        //output
        // Array
        // (
        //     [0] => Array
        //         (
        //             [column] => 1
        //             [dir] => asc
        //         )

        // )

        $approval_data = $this->Jobpro_model->getAll('job_approval');
        $approval_data = $this->getApprovalDetails($approval_data);

        $data = array();
        $no = $this->input->post('start'); // get zero number from post variable? WTF this is good.

        print_r($approval_data);

        foreach($approval_data as $v){
            $no++;
            $row = array();
            $row[]= $v['divisi'];
            $row[]= $v['departement'];
            $row[]= $v['posisi'];
            $row[]= $v['name'];
            $row[]= $v['status_approval'];
            $row[]= $v['divisi'];

            $data[]=$row;
        }

        $output = [
            'draw' => $this->input->post('draw'),
            'recordsTotal' => '0',
            'recordsFiltered' => '0',
            'data' => $data
        ];

        // print_r($output);
    }

}