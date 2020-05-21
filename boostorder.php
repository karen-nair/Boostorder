<?php
/**
 * Plugin Name: Boostorder
 * Author: Kalaivani Nair @ Karen
 * Description: Boostorder e-commerce
 * Version: 1.0
 * php version 7.2.1
 *
 * @category Bootstrap file
 * @package  Boostorder
 * @author   Kalaivani Nair <kalaivani.dc@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1.0
 * @link     mailto:kalaivani.dc@gmail.com 
 */
namespace Boostorder;

define('ROOT_DIR', 'http://localhost/boostorder/');
/*auto load class*/
spl_autoload_register( __NAMESPACE__.'\autoloader' );
function autoloader( $class_name )
{
  if ( false !== strpos( $class_name, __NAMESPACE__ ) ) {

    $classes_dir = realpath( plugin_dir_path( __FILE__ ) ) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $class_file = str_replace( '\\', DIRECTORY_SEPARATOR, 
    	'controller'.DIRECTORY_SEPARATOR. explode(__NAMESPACE__.DIRECTORY_SEPARATOR, $class_name)[1]) . '.php';

    require_once $classes_dir . $class_file;
  }
}

/*initialize plugin*/
$core = new Core();
$core->addHook();
?>