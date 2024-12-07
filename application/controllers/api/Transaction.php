<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
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

    public function createinvoice_post()
    {

        $rawPayload = file_get_contents('php://input');
    
        // Step 2: Decode the JSON into an associative array
        $data = json_decode($rawPayload, true);
        // echo $data;


        // if ($this->db->insert('sales', $data)) {
        //     $sale_id = $this->db->insert_id();

        //     foreach ($items as $item) {
        //         $item['sale_id'] = $sale_id;
        //         $this->db->insert('sale_items', $item);
        //     }

        //     if ($data['status'] == $this->lang->line('paid') || $data['status'] == 'paid') {
        //         $adata = [
        //             'date'        => $data['date'],
        //             'invoice_id'  => $sale_id,
        //             'customer_id' => $data['customer_id'],
        //             'amount'      => ($data['total'] + $data['shipping']),
        //             'note'        => $this->lang->line('paid_nett'),
        //             'user'        => $this->session->userdata('user_id')
        //         ];
        //         $this->db->insert('payment', $adata);
        //         $this->db->update('sales', ['paid' => ($data['total'] + $data['shipping'])], ['id' => $sale_id]);
        //     }

        //     return true;
        // }

        // return false;
        // $this->db->select('sim_customers.address, sim_customers.cf1, sim_customers.phone, sim_customers.name, sim_customers_details.lokasi');
        // $this->db->from('sim_customers');
        // $this->db->join('sim_customers_details', 'sim_customers.phone = sim_customers_details.phone'); // Adjust column names for joining
        // // $this->db->limit(10); 
        // $this->db->group_by(['sim_customers.phone', 'sim_customers.name']);
        // $q = $this->db->get();
        
        // if ($q->num_rows() > 0) {
        //     $result = [];
        //     $no = 1;
        //     foreach ($q->result() as $row) {
        //         $result[] = [
        //             'id' => $no++,
        //             'name' => $row->name,
        //             'phone' => $row->phone,
        //             'addresses' => [$row->lokasi, $row->address]
        //         ];
        //     }

        //     $message = [
        //         "data" => $result,
        //         "success" => true
        //     ];
        // } else {
        //     $message = [
        //         "data" => [],
        //         "success" => false,
        //         "message" => "No records found."
        //     ];
        // }
        
        $this->response($data, REST_Controller::HTTP_OK); 
    }

    
}
