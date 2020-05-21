<?php
/**
 * Helper
 *
 * Class file handles helper methods for the plugin to operate
 *
 * php version 7.2.1
 *
 * @category Helper_File
 * @package  Boostorder
 * @author   Kalaivani Nair <kalaivani.dc@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @version  1..0
 * @link     mailto:kalaivani.dc@gmail.com 
 */
namespace Boostorder;
/**
 * Offers helper functions
 *
 * Offers helper functions for core class
 *
 * @category Helper_File
 * @package  Boostorder
 * @author   Kalaivani Nair <kalaivani.dc@gmail.com>
 * @license  http://opensource.org/licenses/gpl-license.php GNU Public License
 * @link     mailto:kalaivani.dc@gmail.com  
 */
class Helper
{
    public static $currency = 'RM';
    /**
     * Construct function
     */
    public function __construct()
    {    

    }

    
    /**
     * Custom define CURL function
     *
     * Make external REST API request by authenticating headers
     *
     * @param array $request  string handle to unique identify cache file
     * @param $url string api url to fetch
     * @param $noEncode tells if result should be json_encoded or not
     *
     * @return null
     * @since  1.0
     */
    public static function fireCURL($request, $url, $noEncode = false)
    {
        $username = 'ck_2682b35c4d9a8b6b6effac126ac552e0bfb315a0';
        $password = 'cs_cab8c9a729dfb49c50ce801a9ea41b577c00ad71';

        $cacheFileName = $request.'.json.cache.txt';
        $cacheUpdateMin = 30;

        $results = array();

            $headers = [];
            $ch = curl_init();
            

            // Curl request to get the json.
            $opts = array(
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_USERAGENT => 'get-request',
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_FILETIME => true,
            // CURLOPT_HEADER => true,
            CURLOPT_HTTPHEADER => array('Accept: application/json'),
            CURLOPT_USERPWD=>$username . ":" . $password,
            CURLOPT_URL => $url,
            CURLOPT_HEADERFUNCTION =>function($ch, $header) use (&$headers)
              {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) // ignore invalid headers
                  return $len;

                $headers[strtolower(trim($header[0]))][] = trim($header[1]);

                return $len;
              }
            );

            
            curl_setopt_array($ch, $opts);
            $results = curl_exec($ch);

            $output = array(
                'total'=> $headers['x-wp-total'][0],
                'pages'=> $headers['x-wp-totalpages'][0],
                'result'=> json_decode($results, true),
                'currency'=>self::$currency
        
            );
        return $output;
    }

   
    
    /**
     * Filter request
     *
     * Filter request before passing to fireCURL 
     *
     * @param array $request  string handle to unique identify cache file
     * @param $url      string api url to fetch
     * @param $noEncode tells if result should be json_encoded or not
     *
     * @return array result if true
     * @since  1.0
     */
    public static function handleRequest($request, $url, $noEncode = false)
    {
        try {

            $result = Helper::fireCURL($request, $url, $noEncode);

            if (!$result) {
                echo "error in fetching api";exit;
            }
            return  $result;
        }
        catch(Exception $e) {
            echo 'Message: ' .$e->getMessage();
        }
    }

    /**
     * Adapter for products
     *
     * Adapter for products function
     *
     * @param array $page parameters for requests
     *
     * @return array consist of url, request
     * @since  1.0
     */
    private static function _ProductsAdapter($page)
    {
        $url = "https://mangomart-autocount.myboostorder.com/wp-json/wc/v1/products";
        if ($page) {
            $url .="?page=$page";
        }
        return array(
                    'url'=>$url);
    }


    /**
     * Filters api
     *
     * Filter api arguments for CURL request
     *
     * @param string $action handle for request                      
     * @param array  $data   iput for CURL request
     *
     * @return array adapter for CURL request
     *
     * @since 1.0
     */
    public static function apiAdapter($action, $page = false)
    {
        return self::{'_'.$action.'Adapter'}($page)['url'];
    }
}
