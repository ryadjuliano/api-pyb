<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

class Customers extends REST_Controller {

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
        $this->db->select('sim_customers.address,sim_customers.cf1, sim_customers_details.phone, sim_customers_details.name,sim_customers_details.lokasi ');
        $this->db->from('sim_customers');
        $this->db->join('sim_customers_details', 'sim_customers.phone = sim_customers_details.phone'); // Adjust column names for joining
        // $this->db->where('sim_customers.name', $name); // Filter by name from sim_customers
        // $this->db->where('sim_customers_details.phone', $phone); // Filter by phone from sim_customers_details
        
        $q = $this->db->get();
        
        if ($q->num_rows() > 0) {
            foreach (($q->result()) as $row) {
                $data[] = $row;
            }
            return $data;
        }
        
        $message = array(
            "data" => $q,
            "success" => true
        );
        
        $this->response($message, REST_Controller::HTTP_OK);
          
    }
}