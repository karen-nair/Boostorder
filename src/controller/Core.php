<?php
/**
 * Boostorder
 *
 * Class file handles core methods for the plugin to operate
 *
 * php version 7.2.1
 *
 * @category Core
 * @package  Boostorder
 * @author   Kalaivani Nair <kalaivani.dc@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     mailto:kalaivani.dc@gmail.com 
 */
namespace Boostorder;

class Core extends Model {

    private $_postTypes = null;
    private $_capability = null;
    private $_parentMenuSlug = null;
    public $customEndpoint = null;

    public function __construct()
    {

        $this->registerAjaxRequests(array(
            'submitOrder',
            'updateOrderStatus',
            'monitorOrderStatus'
        ));

    }

    /**
     * Registers wordpress hooks
     *
     * Registers wordpress hooks to support native wordpress functions
     * @since  1.0
     */
    public function addHook()
    {
        add_action('plugins_loaded', array($this, 'loadPlugin'));
    }

    /**
     * Registers Ajax calls
     *
     * Registers Wordpress ajax calls made through js script
     *
     * @param array $requests array passed from addHooks function
     *
     * @return null
     * @since  1.0
     */
    public function registerAjaxRequests($requests)
    {

        foreach ($requests as $request) {

            add_action("wp_ajax_nopriv_{$request}", array($this, "getAjaxResults_{$request}"));
            add_action("wp_ajax_{$request}", array($this, "getAjaxResults_{$request}"));
        }
    }

    /**
     * Ajax callback function
     *
     * Callback for submitOrder ajax call
     *
     * @param array $_POST form data submited through form
     *
     * @return string
     * @since  1.0
     */

    public function getAjaxResults_submitOrder()
    {
        $orderResponse = $this->insertOrder($_POST['data']['order'][0]['Total']);

       if ($orderResponse) {
             unset($_POST['data']['order'][0]);
             if ($this->insertProduct($orderResponse, $_POST['data']['order'])) {
                    echo "order submited.Pending approval by admin.";exit;
             }
       } else {
        echo $orderResponse;exit;
       }
       
    }

    /**
     * Ajax callback function
     *
     * Callback for updateOrderStatus ajax call
     *
     * @param array $_POST form data submited through form
     *
     * @return null
     * @since  1.0
     */

    public function getAjaxResults_updateOrderStatus()
    {
      $this->updateOrderStatus($_POST['data']['orderID'], $_POST['data']['status']);
    }


     /**
     * Ajax callback function
     *
     * Callback for monitorOrderStatus ajax call. Monitors for order status change on interval set by js script
     *
     * @return array $approvedOrders
     * @since  1.0
     */
    public function getAjaxResults_monitorOrderStatus()
    {
      $approvedOrders = $this->getApprovedOrders();
      echo json_encode($approvedOrders);exit;
    }

    /**
     * Register custom endpoint
     *
     * register rule to handle url parameters
     * @since  1.0
     */
    public function addCustomEndpoint()
    {
        $this->customEndpoint = array(
            array(
                'endpoint'=> 'catalogue',
                'hiddenValue' => 'products=list',
                'paramRegex'=>false
            ),
            array(
                'endpoint'=> 'manage-orders',
                'hiddenValue' => 'orders=manage',
                'paramRegex'=>false
            ),

         array(
                'endpoint'=> 'order',
                'hiddenValue' => 'order=view',
                'paramRegex'=>'([^a-zA-Z0-9]+)/?'
            ));

        add_rewrite_endpoint('catalogue', EP_ROOT );
        add_rewrite_endpoint('manage-orders', EP_ROOT );
        add_rewrite_endpoint('order', EP_ROOT );


        foreach ($this->customEndpoint as $key => $endPoint) {

           add_rewrite_rule(
            '^'.$endPoint['endpoint'].'/'.($endPoint['paramRegex'] ? $endPoint['paramRegex'] : '?$'),
            'index.php?'.$endPoint['hiddenValue'],
            'top'
            );


        }

    }

    /**
     * Callback for url parameters
     *
     * Handles url parameters accordingly
     *
     * @param string $_SERVER url string
     *
     * @return object
     * @since  1.0
     */
    public function catchQuery()
    {

       if(strpos($_SERVER['REQUEST_URI'], '/catalogue/') !== false) {

        $catString = explode('/catalogue/', $_SERVER['REQUEST_URI']);
        $catalogue  = new Catalogue();

        // check for parameter
          if (isset($catString[1]) && $catString[1] !='') {
            $catalogue->renderCatalogue(strtok($catString[1], '/'));
          } else {
            $catalogue->renderCatalogue();
          }
     
        exit();
       }

       //manage orders
       if(strpos($_SERVER['REQUEST_URI'], '/manage-orders/') !== false) {

        $orders  = new Order();

        $orders->renderOrders();
     
        exit();
       }


       //view single order
       if(strpos($_SERVER['REQUEST_URI'], '/order/') !== false) {

        $orderString = explode('/order/', $_SERVER['REQUEST_URI']);

        $order  = new Order();

        // check for parameter
          if (isset($orderString[1]) && $orderString[1] !='') {
            $order->renderOrders(strtok($orderString[1], '/'));
          } else {
            $order->renderOrders();
          }
     
        exit();
       }
    }

     /**
     * Functions after plugin loaded
     *
     * Assigns tasks or functions to perform on plugin load
     *
     * @since  1.0
     */
    public function loadPlugin()
    {
        add_action('wp_enqueue_scripts', array($this, 'frontEnd'));

        /*rewrite url*/
        add_action( 'init', array($this, 'addCustomEndpoint') );
        add_action( 'template_redirect', array($this, 'catchQuery') );
       
    }

    /**
     * Registers scripts
     *
     * Registers scripts and styles to load responsive theme and execute js scripts
     * @since  1.0
     */
    public function frontEnd()
    {
       
        wp_enqueue_style(
            'foundation-zurb', 
            plugin_dir_url(__FILE__).'../../css/app.css', 
            array(), 
            strtotime(date('dd-mm-yy H:i:s')), 
            'all'
        );

         wp_enqueue_style(
            'front-Boostorder', 
            plugin_dir_url(__FILE__).'../../css/style.css', 
            array(), 
            strtotime(date('dd-mm-yy H:i:s')), 
            'all'
        );

         wp_enqueue_script(
            'foundation-zurb',
            plugin_dir_url(__FILE__).'../../js/app.js',
            array('jquery'), 
            null, 
            true 
        );

         wp_enqueue_script(
            'front-'.__NAMESPACE__,
            plugin_dir_url(__FILE__).'../../js/script.js',
            array('jquery'), 
            null, 
            true 
        );

        $frontend = array( 
            'main_site' => site_url(), 
            'plugin_path'=>plugin_dir_url(__FILE__),
            'ajax_url' => admin_url('admin-ajax.php')
        );
        wp_localize_script('front-'.__NAMESPACE__, 'frontend', $frontend);

    }


}
