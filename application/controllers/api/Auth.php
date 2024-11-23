<?php

defined('BASEPATH') OR exit('No direct script access allowed');

// This can be removed if you use __autoload() in config.php OR use Modular Extensions
/** @noinspection PhpIncludeInspection */
require APPPATH . '/libraries/REST_Controller.php';

// use namespace
use Restserver\Libraries\REST_Controller;

/**
 * This is an example of a few basic user interaction methods you could use
 * all done with a hardcoded array
 *
 * @package         CodeIgniter
 * @subpackage      Rest Server
 * @category        Controller
 * @author          Phil Sturgeon, Chris Kacerguis
 * @license         MIT
 * @link            https://github.com/chriskacerguis/codeigniter-restserver
 */
class Auth extends REST_Controller {

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

    public function login_get()
    {
       $phone = $this->get('phone');
        if (phone == '') {
            // $tenant = $this->db->get('tbl_tenant')->result();
             //         $this->response(NULL, REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        } else {
            $this->db->where('phone', $phone);
            $tenant = $this->db->get('tbl_customers')->result();
        }
        // $this->response($kontak, 200);
        $this->response($tenant, REST_Controller::HTTP_OK); 
        
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

    public function login_post()
    {
    
        $phone =  $this->post('phone');
        $fcm_token = $this->post('fcm_token');
        if ($fcm_token !== null || $fcm_token !== 'undefined') {
            
            $data = array(
                'fcm_token' => $fcm_token
                );
            $this->db->where('phone', $phone);
            $updates = $this->db->update('tbl_customers', $data);
            if ($updates > 0) {
                 $this->db->where('phone', $phone);
                $usersLogin = $this->db->get('tbl_customers')->result();
                $this->response($usersLogin, REST_Controller::HTTP_OK); 
            } else {
                $message = [
                        'status' => false,
                        'message' => 'Failed Login'
                    ];
                $this->response($message, REST_Controller::HTTP_OK); 
                
            }
           
            
            
        }
        
      
    }
    
    public function profile_post()
    {
    
        $phone =  $this->post('user_id');
        $userLogin = $this->db
            ->select('tbl_customers.status as status, tbl_customer_data.verified as verified, tbl_customer_data.image as image,tbl_customers.name as nama,tbl_customers.phone as phone')
            ->from('tbl_customers')
            ->join('tbl_customer_data','tbl_customers.uid=tbl_customer_data.user_id','LEFT')
            ->where('tbl_customers.uid',$phone)
            // ->limit(10, $offset)
            ->get(); //Getting the results ready...
        
        // $this->db->where('uid', $phone);
        $usersLogins =$userLogin->result();
        $this->response($usersLogins, REST_Controller::HTTP_OK); 
    }
    
    
    public function gen_uuid() {
     sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
}
    
     public function register_post()
    {
        
        $uuid =  sprintf( '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        // 32 bits for "time_low"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ),

        // 16 bits for "time_mid"
        mt_rand( 0, 0xffff ),

        // 16 bits for "time_hi_and_version",
        // four most significant bits holds version number 4
        mt_rand( 0, 0x0fff ) | 0x4000,

        // 16 bits, 8 bits for "clk_seq_hi_res",
        // 8 bits for "clk_seq_low",
        // two most significant bits holds zero and one for variant DCE1.1
        mt_rand( 0, 0x3fff ) | 0x8000,

        // 48 bits for "node"
        mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff ), mt_rand( 0, 0xffff )
    );
    
       
       

        $NewRegister = array(
            'name' => $this->post('name'),
            'uid' => $uuid,
            'phone' => substr_replace($this->post('phone'),"+62",0,1),
            'email' => $this->post('email'),
            'password' => $this->post('password'),
            'isMode' => $this->post('isMode'),
            'status' => $this->post('status'),
            'created_at' => date('Y-m-d H:i:s'),
        );
        
        //   $this->db->where('uid', $phone);
        $phone = substr_replace($this->post('phone'),"+62",0,1);
        // $checkPhone = $this->db->where('phone', $phone);
        // $result = $checkPhone->result();
        
        $this->db->where('phone', $phone);
        $usersLogin = $this->db->get('tbl_customers')->result();
        $numberOfUsers = count($usersLogin);
        
        if ($numberOfUsers == 1) {
             $message = [
                'status' => false,
                'message' => 'Your phone already registered'
            ];
             $this->response($message, REST_Controller::HTTP_OK); 
        } else {
            
              $newCustome = array (
                 'user_id' => $uuid,
                 'verified' => 0,
                );
            $insert = $this->db->insert('tbl_customers', $NewRegister);
            $insert2 = $this->db->insert('tbl_customer_data', $newCustome);
            $message = [
                'status' => $insert,
                'message' => 'Sucessfully Added data'
            ];
            
            $this->response($message, REST_Controller::HTTP_OK); 
            
        }
        
        // echo json_encode($numberOfUsers);
        //   $message = [
        //     'status' => $result,
        //     'message' => 'Sucessfully Added data'
        // ];
       
    }
    
      
    

     
        // $this->response($usersLogin, REST_Controller::HTTP_OK); 

  

}
