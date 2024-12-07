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
        if ($data) {
            // Step 3: Extract and map fields from the payload
            $customer = $data['customer'] ?? [];
            $invoiceDetails = $data['invoiceDetails'] ?? [];
            $products = $data['products'] ?? [];
            $totals = $data['totals'] ?? [];
            $status = $data['status'] ?? '';
    
            // Prepare data for insertion into `sales` table
            $saleData = [
                'reference_no' => '',
                'company_id' => '1',
                'company_name' => 'Pick Your Ballon',
                'date' => '',
                'recurring' => '',
                'expiry_date' => '',
                'user' => '',
                'user_id' => '',
                'product_discount' => '',
                'product_tax' => '',
                'order_tax_id' => '',
                'order_tax' => '',
                'shipping' => '',
                'note' => '',
                'total_tax' => '',
                'total_tax' => '',
                'customer_id' => '',
                'customer_name' => $customer['name'],
                'customer_address' => $customer['address'],
                'invoice_number' => $invoiceDetails['invoiceNumber'],
                'due_date' => $invoiceDetails['dueDate'],
                'shipment' => $invoiceDetails['deliveryTime'],
                'invoice_date' => $invoiceDetails['invoiceDate'],
                'total' => $totals['subtotal'],
                'tax' => $totals['tax'],
                'grand_total' => $totals['grandTotal'],
                'status' => 'pending',
            ];



            // Insert into `sales` table
            if ($this->db->insert('sales', $saleData)) {
                $saleId = $this->db->insert_id();
    
                // Step 4: Insert products into `sale_items` table
                foreach ($products as $product) {
                    $itemData = [
                        'sale_id' => $saleId,
                        'product_name' => $product['name'],
                        'quantity' => $product['quantity'],
                        'price' => $product['price'],
                        'net_unit_price' => $product['price'],
                        'real_unit_price' => $product['price'],
                        'subtotal' => $product['total'],
                        'details' => '8',
                        'tax_amt' => 0,
                        'tax_rate_id' => 1,
                        'tax' => 0.00,
                        'discount' => 0,
                        'tax_method' => 'exclusive',
                    ];
                    // $this->db->insert('sale_items', $itemData);
                }
    
                $message = [
                    "success" => true,
                    "message" => "Invoice created successfully.",
                    "sales" => $itemData,
                    "saleData" => $saleData
                ];
            } else {
                $message = [
                    "success" => false,
                    "message" => "Failed to create invoice.",
                ];
            }
        } else {
            $message = [
                "success" => false,
                "message" => "Invalid JSON payload.",
            ];
        }
    
        // Step 5: Send response
        $this->response($message, REST_Controller::HTTP_OK); 
    }

    
}
