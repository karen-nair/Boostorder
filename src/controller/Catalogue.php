<?php
namespace Boostorder;
use Boostorder\Model;
use Boostorder\View;


class Catalogue extends Model {

public $view = null;

    public function __construct($a = false, $b = false)
    {
    	$this->view = new View();

    }

    public function renderCatalogue($page = false)
    {   
    	$approvedOrders = $this->getApprovedOrders();

        $catalogue = Helper::handleRequest('products', Helper::apiAdapter('products', $page), true);
        $this->view->renderView('catalogue', $catalogue, 0, $approvedOrders);
    }

   


}

?>