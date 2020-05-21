<?php
namespace Boostorder;

class View {

	public $templatePath = null;
    public function __construct()
    {

    }

    /**
     * Display html
     *
     * Receive parameters and lay them in html for end user
     *
     * @param string $filePath
     *
     * @return html template
     * @since  1.0
     */
    public function renderView($filePath, $param = false, $currency = false, $approvedOrders = false )
    {
    	$this->templatePath = plugin_dir_path(__DIR__).'template';
    	get_header();
    	include_once $this->templatePath.'/'.$filePath.'.php';
    	get_footer();
    }


}

?>