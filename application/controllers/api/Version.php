<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;


class Version extends REST_Controller {

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

    public function app_get()
    {
        // $this->db->where("(status='1')");
        // $voucher = $this->db->get('tbl_promotions')->result();
        // $this->response($voucher, REST_Controller::HTTP_OK); 
        
        $version = array(
                        "android_version" => "1.0.2",
                        "ios_version" => "1.0.2",
                        "web_version" => "1.0.2",
                        "version__platform" => "1.0.2",
                        );
                        
        $this->response($version, REST_Controller::HTTP_OK); 
    }

    public function active_post()
    {
    
        $userId =  $this->post('user_id');
    
        $this->db->where('user_id', $userId);
        // $listCoupon = $this->db->get('tbl_coupon')->result();
        // active
         $activeVocuher = $this->db
            ->select('tbl_transactions.voucher_uid,tbl_transactions.status as StatusTrx, tbl_vouchers.*')
            ->from('tbl_transactions')
            ->join('tbl_vouchers','tbl_transactions.voucher_uid=tbl_vouchers.uid','LEFT')
            ->where('tbl_transactions.user_id',$userId)
            ->where('tbl_transactions.trx_type',2)
            ->where('tbl_transactions.voucher_uid !=',  NULL)
            ->get()->result(); //Getting the results ready...
      
        //  $message = [
        //     'ListCoupon' => $listCoupon->result(),
        //     'totalCoupon' => $quer->result(),
        //     'message' => 'Sucessfully result'
        // ];
        
        $this->response($activeVocuher, REST_Controller::HTTP_OK); 
    }
    

  

}
