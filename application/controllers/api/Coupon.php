<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;


class Coupon extends REST_Controller {

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

    // public function login_get()
    // {
    //   $phone = $this->get('phone');
    //     if (phone == '') {
    //         // $tenant = $this->db->get('tbl_tenant')->result();
    //          //         $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    //     } else {
    //         $this->db->where('phone', $phone);
    //         $tenant = $this->db->get('tbl_customers')->result();
    //     }
    //     // $this->response($kontak, 200);
    //     $this->response($tenant, REST_Controller::HTTP_OK); 
        
    //     // if ($id === NULL)
    //     // {
    //     //     // Check if the users data store contains users (in case the database result returns NULL)
    //     //     if ($users)
    //     //     {
    //     //         // Set the response and exit
    //     //         $this->response($users, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    //     //     }
    //     //     else
    //     //     {
    //     //         // Set the response and exit
    //     //         $this->response([
    //     //             'status' => FALSE,
    //     //             'message' => 'No users were found'
    //     //         ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
    //     //     }
    //     // }

    //     // // Find and return a single record for a particular user.
    //     // else {
    //     //     $id = (int) $id;

    //     //     // Validate the id.
    //     //     if ($id <= 0)
    //     //     {
    //     //         // Invalid id, set the response and exit.
    //     //         $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
    //     //     }

    //     //     // Get the user from the array, using the id as key for retrieval.
    //     //     // Usually a model is to be used for this.

    //     //     $user = NULL;

    //     //     if (!empty($users))
    //     //     {
    //     //         foreach ($users as $key => $value)
    //     //         {
    //     //             if (isset($value['id']) && $value['id'] === $id)
    //     //             {
    //     //                 $user = $value;
    //     //             }
    //     //         }
    //     //     }

    //     //     if (!empty($user))
    //     //     {
    //     //         $this->set_response($user, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
    //     //     }
    //     //     else
    //     //     {
    //     //         $this->set_response([
    //     //             'status' => FALSE,
    //     //             'message' => 'User could not be found'
    //     //         ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
    //     //     }
    //     // }
    // }

    public function point_post()
    {
    
        $userId =  $this->post('user_id');
        // $offset =  $this->post('_offset');
        $offset = $this->post('_offset');
        $limit = $this->post('_limit') ?: 10; // Default to 10 items per page if not provided

    
        $this->db->where('user_id', $userId);
        // $listCoupon = $this->db->get('tbl_coupon')->result();
        
        //  $listCoupon = $this->db
        //     ->select('coupon_code, tbl_coupon.created_at, tbl_events.description as event, tbl_category_tenant.description as categoryTenant')
        //     ->from('tbl_coupon')
        //     ->join('tbl_events','tbl_coupon.event_id=tbl_events.uid','LEFT')
        //     ->join('tbl_category_tenant','tbl_coupon.category_id=tbl_category_tenant.uid','LEFT')
        //     ->where('user_id',$userId)
        //     // ->limit(10, $offset)
        //     ->get(); //Getting the results ready...
        
        $listCoupon = $this->db
        ->select('coupon_code, tbl_coupon.created_at, tbl_events.description as event, tbl_category_tenant.description as categoryTenant')
        ->from('tbl_coupon')
        ->join('tbl_events', 'tbl_coupon.event_id=tbl_events.uid', 'LEFT')
        ->join('tbl_category_tenant', 'tbl_coupon.category_id=tbl_category_tenant.uid', 'LEFT')
        ->where('user_id', $userId)
        ->limit($limit, $offset)
        ->get();
      
            $quer = $this->db
            ->select('count(*) as Total')
            ->from('tbl_coupon')
            // ->join('course_details','assign_tble.ccode=course_details.ccode','LEFT')
            ->where('user_id',$userId)
            
            ->get(); //Getting the results ready...
            
            $points = $this->db
            ->select('sum(value_point) as TotalPoint')
            ->from('tbl_transactions')
            // ->join('course_details','assign_tble.ccode=course_details.ccode','LEFT')
            ->where('user_id',$userId)
            
            ->get(); //Getting the results ready...
            
            $voucher = $this->db
            ->select('count(trx_type) as TotalVoucher')
            ->from('tbl_transactions')
            ->where('user_id',$userId)
            ->where('trx_type',2)
            ->where('status',1)
            ->get(); //Getting the results ready...
            
            
            // return 
         $message = [
            'ListCoupon' => $listCoupon->result(),
            'totalCoupon' => $quer->result(),
            'totalPoint' => $points->result(),
            'totalVoucher' => $voucher->result(),
            'message' => 'Sucessfully result'
        ];
        
        $this->response($message, REST_Controller::HTTP_OK); 
    }
    
    public function pointfilter_post()
    {
        $userId = $this->post('user_id');
        $limit = $this->post('_limit') ?: 10; // Default to 10 items per page if not provided
        $coupon_code = $this->post('coupon_code');
    
        // Apply filters
        $this->db->where('user_id', $userId);
    
        // Use LIKE instead of WHERE for coupon_code
        if (!empty($coupon_code)) {
            $this->db->like('coupon_code', $coupon_code);
        }
    
        $listCoupon = $this->db
            ->select('coupon_code, tbl_coupon.created_at, tbl_events.description as event, tbl_category_tenant.description as categoryTenant')
            ->from('tbl_coupon')
            ->join('tbl_events', 'tbl_coupon.event_id=tbl_events.uid', 'LEFT')
            ->join('tbl_category_tenant', 'tbl_coupon.category_id=tbl_category_tenant.uid', 'LEFT')
            ->limit($limit)
            ->get();
    
        // Return response
        $message = [
            'data' => $listCoupon->result(),
            'message' => 'Successfully fetched results'
        ];
    
        $this->response($message, REST_Controller::HTTP_OK);
    }


  

}
