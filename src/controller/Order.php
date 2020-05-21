<?php
namespace Boostorder;
use Boostorder\Model;
use Boostorder\View;


class Order extends Model {

public $view = null;

    public function __construct($a = false, $b = false)
    {
    	$this->view = new View();

    }

    /**
     * Get orders
     *
     * fetch all orders if no $orderID given and pass to view controller to display it to end user
     *
     * @param int $orderID optional
     * @since  1.0
     */
    public function renderOrders($orderID = false)
    {   
        $orders = $this->getOrders($orderID);
        $this->view->renderView('orders', $orders);
    }

}

?>