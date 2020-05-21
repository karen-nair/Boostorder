<?php
namespace Boostorder;

class Model {

    protected $db;

    public function __construct()
    {

    }
    /**
     * Insert order
     *
     * Insert new orders into local database
     *
     * @param string $total total order
     *
     * @return int last inserted id
     * @since  1.0
     */
    public function insertOrder($order)
    {
       global $wpdb;
        try {

        $stmt = $wpdb->prepare("INSERT INTO orders (total) VALUES ('".$order."')");

        $wpdb->query($stmt);
        $orderID = $wpdb->insert_id;
        return $orderID;

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Insert products
     *
     * Loop through products and insert into local database for given order id
     *
     * @param int $orderID array $products
     * @since  1.0
     */
    public function insertProduct($orderID, $products)
    {
       global $wpdb;
        try {

            foreach ($products as $key => $product) {

                $stmt = $wpdb->prepare("INSERT INTO order_products (
                    orderID,
                    productID,
                    image,
                    product_name,
                    qty,
                    unit_price
                ) VALUES (
                '".$orderID."',
                '".$product['ProductID']."',
                '".$product['Image']."',
                '".$product['Name']."',
                '".$product['Qty']."',
                '".$product['Price']."')");

                $wpdb->query($stmt);
            }

        } catch(PDOException $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get orders
     *
     * Fetch all orders by default and single order for given order id
     *
     * @param int $orderID optional
     *
     * @return array $output orders
     * @since  1.0
     */
    public  function getOrders($orderID = false)
    {
        global $wpdb;

        $query = "SELECT * FROM orders";
        if ($orderID) {
            $query .= " WHERE id=$orderID";
        }

        $orders = $wpdb->get_results($query);
    
        $output = array();

        foreach ($orders as $key => $order) {
          $output[$order->id] = array(
                'orderID'=>$order->id,
                'total'=>$order->total,
                'createdDate'=>$order->submit_date,
                'status'=>$order->status,
                'products'=>$this->getProducts($order->id)

            );
           
         }

         return $output;
    }

    /**
     * Get products
     *
     * Get products for given order id
     *
     * @param int $orderiD optional
     *
     * @return array $products
     * @since  1.0
     */
    public  function getProducts($orderID = false)
    {
        global $wpdb;

        $query = "SELECT * FROM  order_products WHERE orderID=$orderID";
        $products = $wpdb->get_results($query);

        return $products;
    }

    /**
     * Update order status
     *
     * Update order for given status
     *
     * @param int $orderiD int $status
     * @since  1.0
     */
    public function updateOrderStatus($orderID , $status)
    {
        global $wpdb;
        $query = "UPDATE orders SET status =$status WHERE id =$orderID";
        $wpdb->query($query);
    }

    /**
     * Get approved orders
     *
     * Get approved order count and ids
     *
     * @return array $output
     * @since  1.0
     */
     public  function getApprovedOrders()
    {
        global $wpdb;

        $query = "SELECT COUNT(*) as approved FROM  orders WHERE status=1";
        $count = $wpdb->get_results($query);

        $query = "SELECT id  as orderID FROM  orders WHERE status=1";
        $orderID = $wpdb->get_results($query);

        $output = array(
            'count'=>$count[0],
            'orderID'=>$orderID
        );

        return $output;
    }

}