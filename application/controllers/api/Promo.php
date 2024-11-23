<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;


class Promo extends REST_Controller {

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

    public function banner_get()
    {
        $this->db->where("(status='1')");
        $voucher = $this->db->get('tbl_promotions')->result();
        $this->response($voucher, REST_Controller::HTTP_OK); 
    }

     public function winner_get()
    {
        $winner = $this->db->get('tbl_winner')->result();
        $this->response($winner, REST_Controller::HTTP_OK); 
        
    }
    

  

}
