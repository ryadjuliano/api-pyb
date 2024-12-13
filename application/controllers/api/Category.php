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
class Category extends REST_Controller {

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
        $category = $this->db->get('sim_group_category')->result(); 
        $message = array(
            "data" => $category,
            "succes" => true
            );
            
        $this->response($message, REST_Controller::HTTP_OK); 
        
    }
    public function listGroup_get()
    {
        $this->db->select('sim_group_category.name as groupName, sim_group_category.id as groupId, sim_stock_item.id, sim_stock_item.productName, sim_stock_item.quantity, sim_stock_item.note, sim_stock_item.imageUrl');
        $this->db->from('sim_group_category');
        $this->db->join('sim_stock_item', 'sim_group_category.id = sim_stock_item.category'); // Adjust column names for joining
        $q = $this->db->get()->result();
    
        // Group data by groupName
        $groupedData = [];
        foreach ($q as $item) {
            $groupId = $item->groupId;
            if (!isset($groupedData[$groupId])) {
                $groupedData[$groupId] = [
                    'groupName' => $item->groupName,
                    'id' => $groupId,
                    'items' => []
                ];
            }
            $groupedData[$groupId]['items'][] = [
                'id' => $item->id,
                'productName' => $item->nama,
                'quantity' => $item->quantity,
                'warna' => $item->warna,
                'ukuran' => $item->ukuran,
                'alert_quantity' => $item->alert_quantity,
                'note' => $item->note,
                'imageUrl' => $item->image
            ];
        }
    
        // Re-index grouped data
        $groupedData = array_values($groupedData);
    
        // Response message
        $message = [
            "data" => $groupedData,
            "success" => true
        ];
        $this->response($message, REST_Controller::HTTP_OK);
    }
    
    

   

  

}
