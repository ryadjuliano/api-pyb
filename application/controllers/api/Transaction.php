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

    public function point_get()
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

    public function valuepoint_post()
    {
    
        $userId =  $this->post('user_id');
    
        $this->db->where('user_id', $userId);
        // $listCoupon = $this->db->get('tbl_coupon')->result();
        
         $valuePoint = $this->db
            ->select('SUM(value_point) as PointValue')
            ->from('tbl_transactions')
            // ->join('tbl_events','tbl_coupon.event_id=tbl_events.uid','LEFT')
            // ->join('tbl_category_tenant','tbl_coupon.category_id=tbl_category_tenant.uid','LEFT')
            ->where('user_id',$userId)
            ->get(); //Getting the results ready...
      
        // $quer = $this->db
        //     ->select('count(*) as Total')
        //     ->from('tbl_coupon')
        //     // ->join('course_details','assign_tble.ccode=course_details.ccode','LEFT')
        //     ->where('user_id',$userId)
            
        //     ->get(); //Getting the results ready...
            
            
            // return 
         $message = [
            'value' => $valuePoint->result(),
            // 'totalCoupon' => $quer->result(),
            'message' => 'Sucessfully result'
        ];
        
        $this->response($message, REST_Controller::HTTP_OK); 
    }
    
    public function trxvoucher_post()
    {
    
        $InsertArray = [
                    'user_id' =>  $this->post('user_id'),
                    'trx_type' =>  $this->post('trx_type'),
                    'que_type' => $this->post('que_type'),
                    'voucher_uid' =>  $this->post('voucher_uid'),
                    'trx_amount' =>  $this->post('trx_amount'),
                    'value_point' => $this->post('value_point'),
                    'voucher_uid' =>  $this->post('voucher_uid'),
                    'trx_code' => 'TRX-'.date('syimdh'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => $this->post('isMode')
            ];
        //  $ins = $this->db('tbl_transactions')->insert($InsertArray);
         $insert = $this->db->insert('tbl_transactions', $InsertArray);
         $message = [
            'status' => $insert,
            'message' => 'Sucessfully Added data'
        ];
        
        $this->response($message, REST_Controller::HTTP_OK); 
    }
    
    public function history_post()
    {
    
        $userId =  $this->post('user_id');
    
        $this->db->where('user_id', $userId);
        $trx = $this->db->get('tbl_transactions')->result();
        
        $this->response($trx, REST_Controller::HTTP_OK); 
    }

  

}
