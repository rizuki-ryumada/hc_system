<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Jobs extends CI_Controller {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Jobpro_model');
        $this->load->model('Divisi_model');
        $this->load->model('Dept_model');
        is_logged_in();
    }

    public function report()
    {
        $data['title'] = 'Report';
        $data['divisi'] = $this->Divisi_model->getDivisi();
        $data['dept'] = $this->Dept_model->getAll();
        $data['user'] = $this->db->get_where('employe', ['nik' => $this->session->userdata('nik')])->row_array();
        $this->load->view('templates/report_header', $data);
        $this->load->view('templates/user_sidebar', $data);
        $this->load->view('templates/user_topbar', $data);
        $this->load->view('reportjobs/index', $data);
        $this->load->view('templates/report_footer');
    }

    public function index()
    {
        $nik = $this->session->userdata('nik');
        $data['my'] = $this->Jobpro_model->getMyProfile($nik);
        $data['mydiv'] = $this->Jobpro_model->getMyDivisi($nik);
        $data['mydept'] = $this->Jobpro_model->getMyDept($nik);
        $data['posisi'] = $this->Jobpro_model->getPosisi($nik);
		$data['staff'] = $this->Jobpro_model->getStaff($data['posisi']['position_id']);
        $data['tujuanjabatan'] = $this->Jobpro_model->getProfileJabatan($data['posisi']['position_id']);
        $data['pos'] = $this->Jobpro_model->getAllPosition();
        
        // MENGOLAH DATA Master Position menjadi orgchart data
        //sebelumnya ingat ada beberapa hal yang harus diperhatikan
        // 1. posisi Asistant dan bukan assistant berbeda perlakuannya juga berbeda
        // 2. kode ini digunakan untuk mengolah data dari database menjadi JSON
        // ambil my detail 
        $my_pos_detail = $this->Jobpro_model->getPositionDetail($data['my']['position_id']); //ambil informasi detail posisi saya //200
        // print_r($my_pos_detail);
        //output $my_pos_detail
        // Array ( [id] => 200 [position_name] => Recruitment Officer [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 [assistant] => 0 ) 
        
        $x = 0; $y = 0; //untuk penanda 

        if(!empty($my_pos_detail)){//if data exist

            $my_atasan[$x]['id_atasan1'] = $my_pos_detail['id_atasan1'];
            $id_atasan1 = $my_pos_detail['id_atasan1'];

            while($x<2){ //hanya ambil data sampai 2 tingkat ke atas
                //cari posisi yang bukan assistant
                $whois_sama[$x] = $this->Jobpro_model->getWhoisSama($id_atasan1); //200 dan 201 ambil data yang sama sama saya yang bukan assistant
                $my_atasan[$x] = $this->Jobpro_model->getPositionDetail($id_atasan1); //ambil informasi daftar atasan saya yang bukan assistant

                //cari posisi yang assistant
                if(!empty($whois_sama_assistant[$y] = $this->Jobpro_model->getWhoisSamaAssistant($id_atasan1))){ //biar kalo nilainya null jangan biarkan bikin array kosong
                    $y++;
                } else {
                    //nothing
                } //200 dan 201 ambil data yang sama sama saya yang assistant)
                // $my_atasan_assistant[$x] = $this->Jobpro_model->getPositionDetailAssistant($id_atasan1); //ambil informasi daftar atasan saya yang assistant
                $id_atasan1 = $my_atasan[$x]['id_atasan1'];
                $x++;
            }

            // exit;
            

            //cari id yang sama dengan $my_pos_detail di $whois_sama, lalu tambahin 'className': 'my-position'
            foreach($whois_sama as $k => $v){
                foreach ($v as $key => $value){
                    if($my_pos_detail['id'] == $value['id']){
                        $whois_sama[$k][$key]['className'] = 'my-position';
                    }
                }
            }

            //assistant dicari atasannya

            //initialize Assistant
            // $x = 0;
            // $org_assistant = array();
            // //cari apa ada assistant
            // foreach($whois_sama as $k => $v){
            //     foreach($v as $key => $value){
            //         if($value['assistant_of'] != 0){
            //             $org_assistant['assistant'][$x] = $whois_sama[$k][$key];
            //             unset($whois_sama[$k][$key]);
            //             $x++;
            //         }
            //     }
            // }
            // //cari informasi atasan si assistant jika ga kosong
            // if(!empty($org_assistant)){
            //     $org_assistant['assistant_leader'] = $this->Jobpro_model->getPositionDetail($org_assistant['assistant'][0]['assistant_of']); //ambil informasi daftar atasan saya
            // }else{
            //     $org_assistant['assistant_leader'] = "";
            // }
            // $data['org_assistant'] = json_encode($org_assistant); //data assistant dibawa ke view

            // print_r(json_encode($whois_sama));
            // print('<br>');
            // print('<br>');
            // print_r($my_atasan);
            // print('<br>');
            // print('<br>');
            // print_r($whois_sama_assistant);
            // print('<br>');
            // print('<br>');
            // // var_dump($my_atasan_assistant);
            // exit;

            //output dari array $whois_sama_assistant
            // Array ( [0] => Array ( [0] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 [assistant] => 1 )
            //                        [1] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 [assistant] => 1 ) 
            //                     ) 
            //         ) 

            //output $whois_sama
            // Array ( [0] => Array ( [0] => Array ( [id] => 200 [position_name] => Recruitment Officer [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
            //                        [1] => Array ( [id] => 201 [position_name] => Talent & Performance Management Specialist [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
            //                     ) 
            //         [1] => Array ( [0] => Array ( [id] => 182 [position_name] => Compensation & Benefit Dept. Head [dept_id] => 27 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [1] => Array ( [id] => 190 [position_name] => General Affairs & GovRel Dept. Head [dept_id] => 28 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [2] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [3] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [4] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                     ) 
            //     )
            //output $my_atasan
            // Array ( [0] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //         [1] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 )
            //     )

            //reverse arraynya dulu
            $whois_sama = array_reverse($whois_sama);
            $my_atasan = array_reverse($my_atasan);

            // print_r($whois_sama);
            // print('<br>');
            // print('<br>');
            // print_r($my_atasan);
            //output $whois_sama
            // Array ( [0] => Array ( [0] => Array ( [id] => 182 [position_name] => Compensation & Benefit Dept. Head [dept_id] => 27 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [1] => Array ( [id] => 190 [position_name] => General Affairs & GovRel Dept. Head [dept_id] => 28 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [2] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [3] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                        [4] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 )
            //                     ) 
            //         [1] => Array ( [0] => Array ( [id] => 200 [position_name] => Recruitment Officer [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 )
            //                        [1] => Array ( [id] => 201 [position_name] => Talent & Performance Management Specialist [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
            //                     )
            //     )
            //output $my_atasan
            // Array ( [0] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 )
            //         [1] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //     ) 

            //gabungkan array $whois_sama dengan $my_atasan
            $org_struktur = $my_atasan;
            foreach($my_atasan as $k => $v){
                $org_struktur[$k]['children'] = $whois_sama[$k];
            }

            // print_r($org_struktur);
            //output $org_struktur
            // Array ( [0] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 
            //         [children] => Array ( [0] => Array ( [id] => 182 [position_name] => Compensation & Benefit Dept. Head [dept_id] => 27 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 )
            //                               [1] => Array ( [id] => 190 [position_name] => General Affairs & GovRel Dept. Head [dept_id] => 28 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 )
            //                               [2] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                               [3] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
            //                               [4] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 )
            //                             ) 
            //                     ) 
            //         [1] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 
            //         [children] => Array ( [0] => Array ( [id] => 200 [position_name] => Recruitment Officer [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 )
            //                               [1] => Array ( [id] => 201 [position_name] => Talent & Performance Management Specialist [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
            //                             )
            //                     )
            //     ) 

            //gabungkan array[1] dengan [0];
            $i = 0;
            foreach($org_struktur[1]['children'] as $key => $value){
                foreach($org_struktur[0]['children'] as $k => $v){
                    if($org_struktur[1]['id'] == $org_struktur[0]['children'][$k]['id']){
                        $org_struktur[0]['children'][$k]['children'][$i] = $value;
                        $i++;
                    }
                }
            }

            $data['orgchart_data'] = json_encode($org_struktur[0]); //masukkan orgchart yang sudah diolah ke JSON

            //ASSISTANT DATA
            //keluarkan semua assistant jadi di level teratas
            $org_assistant = array(); $x = 0; //initialize assistant
            foreach($whois_sama_assistant as $k => $v){
                foreach($v as $key => $value){
                    $org_assistant[$x] = $value; //tambah value ke org_struktur
                    foreach($this->Jobpro_model->getAtasanAssistant($value['id_atasan1']) as $kunci => $nilai){ //cari atasannya 
                        // array_push($org_assistant[$x], $nilai); //tambah nama posisi atasannya
                        $org_assistant[$x]['atasan_assistant'] = $nilai; //tambah nama posisi atasannya
                    }

                    $x++;
                }
            }

            // print_r($org_assistant);
            // Array ( [0] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 [assistant] => 1 [0] => Human Capital Division Head )
            //         [1] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 [assistant] => 1 [0] => Human Capital Division Head )
            //     ) 
            
            //simpan data dalam bentuk JSON
            $data['orgchart_data_assistant'] = json_encode($org_assistant);
        } else { //if data doesn't exist
            $data['orgchart_data_assistant'] = json_encode("");
            $data['orgchart_data'] = json_encode(""); //masukkan orgchart yang sudah diolah ke JSON
        }
        // End of Pengolahan data orgchart


        $data['title'] = 'Job Profile';
        $data['user'] = $this->db->get_where('employe', ['nik' => $this->session->userdata('nik')])->row_array();
        $statusApproval = $this->db->get_where('job_approval', ['nik' => $nik, 'id_posisi' => $data['posisi']['position_id']])->row_array();
		if ($statusApproval) {
			$data['approval'] = $statusApproval;
			$this->load->view('templates/user_header', $data);
			$this->load->view('templates/user_sidebar', $data);
			$this->load->view('templates/user_topbar', $data);
			$this->load->view('jobs/save_jobs', $data);
			$this->load->view('templates/user_footer');
		} else {
			$this->load->view('templates/user_header', $data);
			$this->load->view('templates/user_sidebar', $data);
			$this->load->view('templates/user_topbar', $data);
			$this->load->view('jobs/index', $data);
			$this->load->view('templates/jobs_footer');
		}
    }

    public function insatasan()
    {
        $data= [
            'id_atasan1' => $this->input->post('position')
        ];

        $this->db->where('id', $this->input->post('id'));
        $this->db->update('position', $data);

        $datajabatan = [
            'id_posisi' => $this->session->userdata('position_id')
        ];
        $this->db->insert('profile_jabatan', $datajabatan);
        
        redirect('jobs','refresh');
    }
    
    //tujuan jabatan
    public function edittujuanjbtn($id)
    {
        $data['title'] = 'Ubah Tujuan Jabatan';
        $data['tujab'] = $this->Jobpro_model->getTujabById($id);
        $data['user'] = $this->db->get_where('employe', ['nik' => $this->session->userdata('nik')])->row_array();

        $this->form_validation->set_rules('tujuan_jabatan', 'Form', 'trim|required');

        if ($this->form_validation->run() == FALSE) {
            $this->load->view('templates/user_header', $data);
            $this->load->view('templates/user_sidebar', $data);
            $this->load->view('templates/user_topbar', $data);
            $this->load->view('jobs/edittujab', $data);
            $this->load->view('templates/jobs_footer');
        } else {
            $this->Jobpro_model->updateTuJab();
            $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert">
            Your Profile Has Been Updated ! </div>');
            redirect('user/jobprofile');
        }
    }

    public function uptuj()
    {
        $id = $this->input->post('id_posisi');
        $tujuan = $this->input->post('tujuan_jabatan');
        $this->Jobpro_model->UpTuj($id,$tujuan);
        redirect('jobs','refresh');
    }

    public function edittujuan()
    {
        $data = [
            'tujuan_jabatan' => $this->input->post('tujuan')
        ];
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('profile_jabatan', $data);
    }

    public function addTanggungJawab()
    {
        $nik = $this->session->userdata('nik');
        $this->db->select('position_id');
        $this->db->from('employe');
        $this->db->where('nik', $nik);
        $pos = $this->db->get()->row_array();
        
        $data = [
            'keterangan' => $this->input->post('tanggung_jawab'),
            'list_aktivitas' => $this->input->post('aktivitas'),
            'list_pengukuran' => $this->input->post('pengukuran'),
            'id_posisi' => $pos['position_id']
        ];

        $this->db->insert('tanggung_jawab', $data);
        $this->session->set_flashdata('flash', 'Added');
        redirect('jobs');
    }

    public function editTanggungJawab()
    {
        $data = [
            'keterangan' => $this->input->post('tanggung_jawab'),
            'list_aktivitas' => $this->input->post('aktivitas'),
            'list_pengukuran' => $this->input->post('pengukuran')
        ];

        $this->db->where('id_tgjwb', $this->input->post('idtgjwb'));
        $this->db->update('tanggung_jawab', $data);
        $this->session->set_flashdata('flash', 'Update');
        redirect('jobs');
    }
    
    public function getTjByID()
    {
        echo json_encode($this->Jobpro_model->getTjById($_POST['id']));
    }

    public function hapusTanggungJawab($id)
    {
        $this->db->where('id_tgjwb', $id);
        $this->db->delete('tanggung_jawab');
        $this->session->set_flashdata('flash', 'Deleted');
        redirect('jobs', 'refresh');
    }

    // -----ruang lingkup
    public function addruanglingkup()
    {
        $id = $this->input->post('id');
        $ruling = $this->input->post('ruangl');
        if (!$ruling) {
            $array = [
                'r_lingkup' => '<b>-</b>',
                'id_posisi' => $id
            ];
            $this->db->insert('ruang_lingkup', $array);
            $this->session->set_flashdata('flash', 'Inserted');
            redirect('jobs/#hal6');
        }else {
            $array = [
                'r_lingkup' => $this->input->post('ruangl'),
                'id_posisi' => $id
            ];
            $this->db->insert('ruang_lingkup', $array);
            $this->session->set_flashdata('flash', 'Inserted');
            redirect('jobs/#hal6');
        }
    }
    public function editruanglingkup()
    {
        $ruang = $this->input->post('ruang');
        if ($ruang) {
            $this->db->set('r_lingkup', $ruang);
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('ruang_lingkup');
            echo 'Success';
        }else{
            $ruang = '<b>-</b>';
            $this->db->set('r_lingkup', $ruang);
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('ruang_lingkup');
            echo $ruang;
        }
    }

    //wewenang
    public function addwen()
    {
        $pos = $this->db->get_where('employe', ['nik' => $this->session->userdata('nik')])->row_array();
        $data = [
            'kewenangan' => $this->input->post('wewenang'),
            'wen_sendiri' => $this->input->post('wen_sendiri'),
            'wen_atasan1' => $this->input->post('wen_atasan1'),
            'wen_atasan2' => $this->input->post('wen_atasan2'),
            'id_posisi' => $pos['position_id']
        ];
        $this->db->insert('wewenang', $data);
        $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert"> Data Berhasil Ditambah! </div>');
        redirect('jobs', 'refresh');
    }

    public function aksiwewenang()
    {
        $aksi = $this->input->post('action');
        $data = array(
            'id' => $this->input->post('id'),
            'kewenangan' => $this->input->post('kewenangan'),
            'wen_sendiri' => $this->input->post('wen_sendiri'),
            'wen_atasan1' => $this->input->post('wen_atasan1'),
            'wen_atasan2' => $this->input->post('wen_atasan2')
        );
        if ($aksi == 'edit') {
            $tableV='';
            if (isset($data['kewenangan'])) {
                $tableV .= "`kewenangan` = '".$data['kewenangan']."'";
            } elseif (isset($data['wen_sendiri'])) {
                $tableV .= "`wen_sendiri` = '".$data['wen_sendiri']."'";
            } elseif (isset($data['wen_atasan1'])) {
                $tableV .= "`wen_atasan1` = '".$data['wen_atasan1']."'";
            } elseif (isset($data['wen_atasan2'])) {
                $tableV .= "`wen_atasan2` = '".$data['wen_atasan2']."'";
            }
            if($tableV && $data['id']){
                $this->db->query("UPDATE `wewenang` SET $tableV WHERE id='".$data['id']."' ");
            }
        }
        if ($aksi == 'delete') {
            $this->db->where('id', $data['id']);
            $this->db->delete('wewenang');
        }
        echo json_encode($aksi);
    }

    // aksihubungan
    public function addHubungan()
    {
        $id = $this->input->post('id');
        $internal = $this->input->post('internal');
        $eksternal = $this->input->post('eksternal');

        
        if (!$internal && !$eksternal) {
            $array = [
                'hubungan_int' => '<b>-</b>',
                'hubungan_eks' => '<b>-</b>',
                'id_posisi' => $id
            ];
            $this->db->insert('hub_kerja', $array);
            $this->session->set_flashdata('flash', 'Update');
            redirect('jobs/#hal5');
        }elseif ($internal && !$eksternal) {
            $array = [
                'hubungan_int' => $internal,
                'hubungan_eks' => '<b>-</b>',
                'id_posisi' => $id
            ];
            $this->db->insert('hub_kerja', $array);
            $this->session->set_flashdata('flash', 'Update');
            redirect('jobs/#hal5');
        }elseif (!$internal && $eksternal) {
            $array = [
                'hubungan_int' => '<b>-</b>',
                'hubungan_eks' => $eksternal,
                'id_posisi' => $id
            ];
            $this->db->insert('hub_kerja', $array);
            $this->session->set_flashdata('flash', 'Update');
            redirect('jobs/#hal5');
        }
    }

    public function edithub()
    {
        $data = [
            'id' => $this->input->post('id'),
            'hubInt' => $this->input->post('hubInt'),
            'hubEks' => $this->input->post('hubEks'),
            'tipe' => $this->input->post('tipe')
        ];

        if ($data['tipe'] == 'internal') {
            $this->db->set('hubungan_int', $data['hubInt']);
            $this->db->where('id', $data['id']);
            $this->db->update('hub_kerja');
            echo 'success';
        }
        if ($data['tipe'] == 'eksternal') {
            $this->db->set('hubungan_eks', $data['hubEks']);
            $this->db->where('id', $data['id']);
            $this->db->update('hub_kerja');
            echo 'success';
        }
    }

    //aksitantangan
    public function addtantangan()
    {
        $data = [
            'text' => $this->input->post('tantangan'),
            'id_posisi' => $this->input->post('id')
        ];
        $this->db->insert('tantangan', $data);
        redirect('jobs','refresh');
    }

    public function edittantangan()
    {
        $data = [
            'text' => $this->input->post('tantangan')
        ];
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('tantangan', $data);
    }

    // kualifikasi aksi
    public function addkualifikasi()
    {
        $data = [
            'id_posisi' => $this->input->post('id'),
            'pendidikan' => $this->input->post('pend'),
            'pengalaman' => $this->input->post('pengalmn'),
            'pengetahuan' => $this->input->post('pengtahu'),
            'kompetensi' => $this->input->post('kptnsi')
        ];
        $this->db->insert('kualifikasi', $data);
        redirect('jobs','refresh');
    }

    public function getKualifikasiById()
    {
        echo json_encode($this->Jobpro_model->getKualifikasiById($_POST['id']));
    }

    public function updateKualifikasi()
    {
        $id = $this->input->post('id_posisi');
        $data = [
            'pendidikan' => $this->input->post('pendidikan'),
            'pengalaman' => $this->input->post('pengalaman'),
            'pengetahuan' => $this->input->post('pengetahuan'),
            'kompetensi' => $this->input->post('kompetensi')
        ];


        $this->db->where('id_posisi', $id);
        $this->db->update('kualifikasi', $data);
        // $this->session->set_flashdata('message', '<div class="alert alert-success" role="alert"> Data Berhasil Diubah! </div>');
        // redirect('jobs');
    }

    //jenjang karir aksi
    public function addjenjangkarir()
    {
        $data = [
            'id_posisi' => $this->input->post('id'),
            'text' => $this->input->post('jenkar')
        ];
        $this->db->insert('jenjang_kar', $data);
        redirect('jobs','refresh');
    }
    public function editjenjang()
    {
        $data = [
            'text' => $this->input->post('jenkar')
        ];
        $this->db->where('id', $this->input->post('id'));
        $this->db->update('jenjang_kar', $data);
    }
	public function updateStaff()
	{
		$data = [
			'manager' => $this->input->post('mgr'),
			'supervisor' => $this->input->post('spvr'),
			'staff' => $this->input->post('staf')
		];
		$this->db->where('id_posisi', $this->input->post('id_posisi'));
		$this->db->update('jumlah_staff', $data);
		echo 'staff updated';
	}
	public function setApprove()
	{
		$data = [
			'nik' => $this->input->post('nik'),
			'id_posisi' => $this->input->post('id_posisi'),
			'atasan1' => $this->input->post('atasan1'),
			'atasan2' => $this->input->post('atasan2'),
			'tanggal_pengajuan' => time(),
			'status_approval' => 'Dikirim',
			'is_active' => 1
		];
		$this->db->insert('job_approval', $data);
		print_r($data);
	}
}

/* End of file Jobs.php */


//Lab Area, you know I want to try if we can get all those hiearchy from my position to the last top hierachy, I'm still stuck here. here is my progress
//SHIFT+CTRL+END then CTRL+/ to remove comments

// $x = 0;
// $i = 0;
// //ambil semua data sampai pada id_atasan = 1
// while($i<1) {
//     $whois_sama[$x] = $this->Jobpro_model->getWhoisSama($id_atasan1); //200 dan 201
//     $my_atasan[$x] = $this->Jobpro_model->getPositionDetail($id_atasan1); //ambil informasi detail posisi saya
//     $id_atasan1 = $my_atasan[$x]['id_atasan1'];
//     if($id_atasan1 == 1){
//         $i++;
//     }else{
//         $x++;
//     }
// }

// $my_atasan_tertinggi = $this->Jobpro_model->getPositionDetail($id_atasan1); //ambil informasi detail posisi saya //200


// // print_r($whois_sama);
// // print('<br>');
// // print('<br>');
// // print_r($my_atasan);
// // print('<br>');
// // print('<br>');
// // print_r($my_atasan_tertinggi);
// // print('<br>');
// // print('<br>');
// // output $my_atasan
// // Array ( [0] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //         [1] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 )
// //       ) 

// //output $whois_sama
// // Array ( [0] => Array ( [0] => Array ( [id] => 200 [position_name] => Recruitment Officer [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
// //                        [1] => Array ( [id] => 201 [position_name] => Talent & Performance Management Specialist [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
// //                        ) 
// //         [1] => Array ( [0] => Array ( [id] => 182 [position_name] => Compensation & Benefit Dept. Head [dept_id] => 27 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        [1] => Array ( [id] => 190 [position_name] => General Affairs & GovRel Dept. Head [dept_id] => 28 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        [2] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        [3] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        [4] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        ) 
// //     ) 
// //output my_atasan_tertinggi
// // Array ( [id] => 1 [position_name] => Chief Executive Officer [dept_id] => 1 [div_id] => 1 [id_atasan1] => 0 [id_atasan2] => 0 ) 

// //reverse arraynya dulu
// $whois_sama = array_reverse($whois_sama);
// $my_atasan = array_reverse($my_atasan);

// // print_r($whois_sama);
// // print('<br>');
// // print('<br>');
// // print_r($my_atasan);
// // print('<br>');
// // print('<br>');
// // print_r($my_atasan_tertinggi);

// //output $whois_sama
// // Array ( [0] => Array ( [0] => Array ( [id] => 182 [position_name] => Compensation & Benefit Dept. Head [dept_id] => 27 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        [1] => Array ( [id] => 190 [position_name] => General Affairs & GovRel Dept. Head [dept_id] => 28 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        [2] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                        [3] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 )
// //                        [4] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                     ) 
// //         [1] => Array ( [0] => Array ( [id] => 200 [position_name] => Recruitment Officer [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
// //                        [1] => Array ( [id] => 201 [position_name] => Talent & Performance Management Specialist [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
// //                     ) 
// //     )

// //output $my_atasan
// // Array ( [0] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 ) 
// //         [1] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //     ) 

// //output my_atasan_tertinggi
// // Array ( [id] => 1 [position_name] => Chief Executive Officer [dept_id] => 1 [div_id] => 1 [id_atasan1] => 0 [id_atasan2] => 0 ) 

// //masukkin anak2 dari atasan
// $org_struktur = $my_atasan;
// foreach($my_atasan as $k => $v){
//     $org_struktur[$k]['children'] = $whois_sama[$k];
// }

// print_r($org_struktur);
// //output $org_struktur
// // Array ( [0] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 
// //                        [children] => Array ( [0] => Array ( [id] => 182 [position_name] => Compensation & Benefit Dept. Head [dept_id] => 27 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 )
// //                                              [1] => Array ( [id] => 190 [position_name] => General Affairs & GovRel Dept. Head [dept_id] => 28 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 )
// //                                              [2] => Array ( [id] => 194 [position_name] => Employee Relation & Safety Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                                              [3] => Array ( [id] => 195 [position_name] => HCIS Officer [dept_id] => 26 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                                              [4] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) 
// //                                              ) 
// //                     ) 
// //         [1] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 
// //                        [children] => Array ( [0] => Array ( [id] => 200 [position_name] => Recruitment Officer [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
// //                                              [1] => Array ( [id] => 201 [position_name] => Talent & Performance Management Specialist [dept_id] => 29 [div_id] => 6 [id_atasan1] => 199 [id_atasan2] => 196 ) 
// //                                              ) 
// //                     ) 
// //     ) 
// exit;

// $org_struktur = $my_atasan_tertinggi;

// //masukin atasan ke dalam jabatan tertinggi array
// foreach($whois_sama as $k => $v){
//     if($org_struktur['id'] == $my_atasan[$k]['id_atasan1']){
//         $org_children = $my_atasan[$k];
//     }
// } 

// //buat pointer
// $org_pointer = array();
// foreach($my_atasan as $k => $v){
//     $org_pointer[$k] = "children";
// }

// $in  = $org_pointer; // Array with incoming params
// $res = array();        // Array where we will write result
// $t   = &$res;          // Link to first level
// foreach ($in as $k) {  // Walk through source array
// if (empty($t[$k])) { // Check if current level has required key
//     $t[$k] = $my_atasan;  // If does not, create empty array there
//     $t = &$t[$k];      // And link to it now. So each time it is link to deepest level.
// }
// }
// unset($t); // Drop link to last (most deep) level
// print_r($res);
// die();



// // $org_struktur[$org_pointer] = $org_children;

// //buat pointer
// $org_pointer;
// // foreach($my_atasan as $v){

// // }
// $a = array("1","5","6");
// $b = array();
// $c =& $b;

// foreach ($a as $k) {
//     $c[$k] = array();
//     $c     =& $c[$k];
// }



// var_dump($org_struktur);
// // Array ( [id] => 1 [position_name] => Chief Executive Officer [dept_id] => 1 [div_id] => 1 [id_atasan1] => 0 [id_atasan2] => 0 
// //         [children] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 ) 
// //         )



// //penunjuk lokasi tingkatan org_struktur
// $org_pointer = "children";
// // print_r($org_struktur[$org_pointer]);

// // while($i<1){
// //     if($my_atasan)
// // }

// exit;
// // Array ( [id] => 1 
// // [position_name] => Chief Executive Officer 
// // [dept_id] => 1 
// // [div_id] => 1 
// // [id_atasan1] => 0 
// // [id_atasan2] => 0 
// // [0] => Array ( [id] => 196 [position_name] => Human Capital Division Head [dept_id] => 26 [div_id] => 6 [id_atasan1] => 1 [id_atasan2] => 0 ) 
// // [children] => 9 [1] => Array ( [id] => 199 [position_name] => Organization Development Dept. Head [dept_id] => 29 [div_id] => 6 [id_atasan1] => 196 [id_atasan2] => 1 ) ) 

// print_r($org_struktur);
// exit;

// while($i<1) {
//     $org_struktur['children'] = array_push($org_struktur, $my_atasan[$k]);


//     $whois_sama[$x] = $this->Jobpro_model->getWhoisSama($id_atasan1); //200 dan 201
//     $my_atasan[$x] = $this->Jobpro_model->getPositionDetail($id_atasan1); //ambil informasi detail posisi saya
//     $id_atasan1 = $my_atasan[$x]['id_atasan1'];
//     if($id_atasan1 == 1){
//         $i++;
//     }else{
//         $x++;
//     }
// }

// // var datasource = {
// // 	'id': '1',
// // 	'name': 'Lao Lao',
// // 	'title': 'general manager',
// // 	'children': [
// // 	  	{ 'id': '2', 'name': 'Bo Miao', 'title': 'department manager' },
// // 	  	{ 'id': '3', 'name': 'Su Miao', 'title': 'department manager',
// // 			'children': [
// // 				{ 'id': '4', 'name': 'Tie Hua', 'title': 'senior engineer' },
// // 				{ 'id': '5', 'name': 'Hei Hei', 'title': 'senior engineer',
// // 					'children': [
// // 						{ 'id': '6', 'name': 'Pang Pang', 'title': 'engineer' },
// // 						{ 'id': '7', 'name': 'Xiang Xiang', 'title': 'UE engineer' }
// // 					]
// // 				}
// // 			]
// // 	   	},
// // 	   	{ 'id': '8', 'name': 'Hong Miao', 'title': 'department manager' },
// // 	   	{ 'id': '9', 'name': 'Chun Miao', 'title': 'department manager' }
// // 	]
// // };

// // print_r($my_atasan);
// exit;
// // cari yang sama-sama memiliki atasan1 yang sama
// $whois_sama1 = $this->Jobpro_model->getWhoisSama($my_pos_detail['id_atasan1']); //200 dan 201
// // retrieve id yang sama-sama punya atasan2
// $my_atasan1 = $this->Jobpro_model->getPositionDetail($my_pos_detail['id_atasan1']); //ambil informasi detail posisi saya //199
// // ambil atasan2
// $whois_sama2 = $this->Jobpro_model->getWhoisSama($my_atasan1['id_atasan1']);
// // cari yang sama
// $my_atasan2 = $this->Jobpro_model->getPositionDetail($my_atasan1['id_atasan1']); //ambil informasi detail posisi saya

// //if $my_atasan2 == 1 thrn stop
// print_r($my_atasan2);
// exit;


// //ambil data diri
// //ambil data teman
// //ambil data diri
// //ambil data teman
// //ambil data diri
// //ambil data sampai id_atasan=1

// // $myJSON = json_encode($print); convert any array to JSON

// // print('<br>');
// // print_r($myJSON);
// // $myObj->name = "John";
// // $myObj->age = 30;
// // $myObj->city = "New York";

// // print('<br>');
// // print_r($myObj);
// // print_r($print);
// exit();

// // var datasource = {
// // 	'id': '1',
// // 	'name': 'Lao Lao',
// // 	'title': 'general manager',
// // 	'children': [
// // 	  	{ 'id': '2', 'name': 'Bo Miao', 'title': 'department manager' },
// // 	  	{ 'id': '3', 'name': 'Su Miao', 'title': 'department manager',
// // 			'children': [
// // 				{ 'id': '4', 'name': 'Tie Hua', 'title': 'senior engineer' },
// // 				{ 'id': '5', 'name': 'Hei Hei', 'title': 'senior engineer',
// // 					'children': [
// // 						{ 'id': '6', 'name': 'Pang Pang', 'title': 'engineer' },
// // 						{ 'id': '7', 'name': 'Xiang Xiang', 'title': 'UE engineer' }
// // 					]
// // 				}
// // 			]
// // 	   	},
// // 	   	{ 'id': '8', 'name': 'Hong Miao', 'title': 'department manager' },
// // 	   	{ 'id': '9', 'name': 'Chun Miao', 'title': 'department manager' }
// // 	]
// // };