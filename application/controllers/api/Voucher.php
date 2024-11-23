<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;


class Voucher extends REST_Controller {

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
    //   $phone = $this->get('phone');
    //     if (phone == '') {
    //         // $tenant = $this->db->get('tbl_tenant')->result();
    //          //         $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    //     } else {
    //         $this->db->where('phone', $phone);
    //         $tenant = $this->db->get('tbl_customers')->result();
    //     }
        
        // $this->db->where('status', 1);
        // $voucher = $this->db
        // ->limit('5')
        // ->get('tbl_vouchers')->result();
        // $this->response($voucher, REST_Controller::HTTP_OK); 
        
        
         $voucher = $this->db
            ->select('tbl_vouchers.*,tbl_category_vouchers.name as CategoryName ,tbl_tenant.name as NamaTenant')
            ->from('tbl_vouchers')
            ->join('tbl_category_vouchers','tbl_vouchers.category_voucher_id=tbl_category_vouchers.uid','LEFT')
            ->join('tbl_tenant','tbl_vouchers.tenant_id=tbl_tenant.uid','LEFT')
            ->limit('5')
            ->where('tbl_vouchers.status', 1)
            ->get()->result(); 
            
            $this->response($voucher, REST_Controller::HTTP_OK); 
        
        // if ($id === NULL)
        // {
        //     // Check if the users data store contains users (in case the database result returns NULL)
        //     if ($users)
        //     {
        //         // Set the response and exit
        //         $this->response($users, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        //     }
        //     else
        //     {
        //         // Set the response and exit
        //         $this->response([
        //             'status' => FALSE,
        //             'message' => 'No users were found'
        //         ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        //     }
        // }

        // // Find and return a single record for a particular user.
        // else {
        //     $id = (int) $id;

        //     // Validate the id.
        //     if ($id <= 0)
        //     {
        //         // Invalid id, set the response and exit.
        //         $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        //     }

        //     // Get the user from the array, using the id as key for retrieval.
        //     // Usually a model is to be used for this.

        //     $user = NULL;

        //     if (!empty($users))
        //     {
        //         foreach ($users as $key => $value)
        //         {
        //             if (isset($value['id']) && $value['id'] === $id)
        //             {
        //                 $user = $value;
        //             }
        //         }
        //     }

        //     if (!empty($user))
        //     {
        //         $this->set_response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        //     }
        //     else
        //     {
        //         $this->set_response([
        //             'status' => FALSE,
        //             'message' => 'User could not be found'
        //         ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        //     }
        // }
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
