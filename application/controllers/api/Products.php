<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

class Products extends REST_Controller {

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

    public function list_get()
    {
        $this->db->select('sim_products.*');
        $this->db->from('sim_products');
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $result = $q->result();
            $message = [
                "data" => $result,
                "success" => true
            ];
        } else {
            $message = [
                "data" => [],
                "success" => false,
                "message" => "No records found."
            ];
        }
        
        $this->response($message, REST_Controller::HTTP_OK); 
    }

    public function filterlist_post() {
       
        $limit = $this->post('_limit') ?: 10; // Default to 10 items per page if not provided
        $phone_number = $this->post('phone');
        $name = $this->post('name');
    
    
        if (!empty($phone_number)) {
            $this->db->like('sim_customers_details.phone', $phone_number);
        }
        if (!empty($name)) {
            $this->db->like('sim_customers_details.name', $name);
        }
        

        $this->db->select('sim_customers.address, sim_customers.cf1, sim_customers_details.phone, sim_customers_details.name, sim_customers_details.lokasi');
        $this->db->from('sim_customers');
        $this->db->join('sim_customers_details', 'sim_customers.phone = sim_customers_details.phone'); // Adjust column names for joining
        $this->db->limit($limit); 
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            $result = [];
            foreach ($q->result() as $row) {
                $result[] = [
                    'name' => $row->name,
                    'phone' => $row->phone,
                    'addresses' => [$row->lokasi, $row->address]
                ];
            }

            $message = [
                "data" => $result,
                "success" => true
            ];
        } else {
            $message = [
                "data" => [],
                "success" => false,
                "message" => "No records found."
            ];
        }
        
        $this->response($message, REST_Controller::HTTP_OK); 
    }

    public function reference_get()
    
        {
            $this->db->select('reference_no');
            $this->db->order_by('id', 'DESC'); // Assuming 'id' is the primary key or an auto-increment field
            $this->db->limit(1);
            $q = $this->db->get('sim_sales');
        
            if ($q->num_rows() > 0) {
                $row = $q->row(); // Fetch the single row
                $lastReferenceNo = $row->reference_no; // Get the last reference number
                $nextReferenceNo = $lastReferenceNo + 1; // Increment the last reference number by 1
              
                $message = [
                    "data" => $nextReferenceNo,
                    "success" => true
                ];
            } else {
                $message = [
                    "data" => [],
                    "success" => false,
                    "message" => "No records found."
                ];
            }
            $this->response($message, REST_Controller::HTTP_OK); 
            // return false;
        }
    
}
