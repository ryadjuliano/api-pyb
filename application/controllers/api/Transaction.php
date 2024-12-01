<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;


class Transaction extends REST_Controller {

    function __construct()
    {
        // Construct the parent class
        parent::__construct();
    
         $this->load->database();
        // Configure limits on our controller methods
        // Ensure you have created the 'limits' table and enabled 'limits' within application/config/rest.php
        $this->methods['list_get']['limit'] = 500; // 500 requests per hour per user/key
        $this->methods['users_post']['limit'] = 100; // 100 requests per hour per user/key
        $this->methods['users_delete']['limit'] = 50; // 50 requests per hour per user/key
    }

    public function allinvoice_get($customer_id = null, $check = false, $user_id = null)
    {
        
        $sales = $this->db
        ->select("{$this->db->dbprefix('sales')}.id as sid, 
                    date, 
                    ballon_status, 
                    company_name, 
                    reference_no, 
                    CONCAT({$this->db->dbprefix('users')}.first_name, ' ', {$this->db->dbprefix('users')}.last_name) as user, 
                    customer_name, 
                    grand_total, 
                    paid, 
                    (grand_total - COALESCE(paid, 0)) as balance, 
                    customers.phone as telephone, 
                    due_date, 
                    status, 
                    recurring, 
                    {$this->db->dbprefix('sales')}.customer_id as cid, 
                    company_id as bid")
        ->from('sales')
        ->join('users', 'users.id = sales.user', 'LEFT')
        ->join('customers', 'customers.phone = users.phone', 'LEFT')
        ->group_by('sales.id');
    
    // Filter berdasarkan parameter
    if ($customer_id) {
        $this->db->where('sales.customer_id', $customer_id);
    }
    
    if ($check) {
        $this->db->where('sales.user', $user_id);
    }
    
    // Tambahkan pengurutan berdasarkan tanggal terbaru
    $this->db->order_by('sales.date', 'DESC'); // Mengurutkan berdasarkan tanggal (baru di atas)
    
    // Menambahkan limit
    $this->db->limit(30); // Batasi hasil query (misalnya 5 data)
    
    // Eksekusi query
    $result = $this->db->get()->result();
    
    // Mengirimkan respons dalam format JSON
    $this->response($result, REST_Controller::HTTP_OK);    
    }

}
