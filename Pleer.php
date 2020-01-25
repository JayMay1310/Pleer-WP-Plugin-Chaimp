
<?php
/*
Plugin Name: Get Pleer details product. Пла
Plugin URI: https://frlw.info/
Version: 0.0.1
Author: JayMay1310
Author URI: https://frlw.info/
Description: Плагин служит в качестве дополнения к модифицированной версий wpchaimp
*/


require_once(ABSPATH . 'wp-config.php'); 
require_once(ABSPATH . 'wp-includes/wp-db.php'); 
require_once(ABSPATH . 'wp-admin/includes/taxonomy.php');
require_once(ABSPATH . 'wp-includes/post.php');
require_once(plugin_dir_path(__FILE__) . '_inc/simplehtmldom/simple_html_dom.php');


class Pleer {
	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * Returns an instance of this class. 
	 */
	public static function get_instance() {
		if ( null == self::$instance ) {
			self::$instance = new Pleer();
		} 
		return self::$instance;
	} 

	private function __construct() {
         
	}
    public function getAllProducts()
    {
        $args     = array( 'post_type' => 'product', 'numberposts' => -1);
        $products = get_posts( $args );
        return $products;
    } 

    public function getWoocomerceAllUrlProduct($id_post)
    {

        $all_meta_url = get_post_meta($id_post, '_product_url', true); 
        return $all_meta_url;        
    }

    public function getDetailProduct($url)
    {
        $array_desk = [];
        $descriptions_spec = "";//Спецификация.
        $descriptions_spec_two = "";
        $descriptions = "";

        $array_desk['descriptions_spec_two'] = "";
        $array_desk['descriptions_spec'] = "";
        $array_desk['descriptions'] = "";

        $raw_url = explode('www', $url);
        $new_url = str_replace("%2F", "/", $raw_url[1]);
        $new_url = "http://www" . $new_url;

        try {
            $html = new simple_html_dom();
            $html->load_file($new_url);
            if (!empty($html)) {
                $count_descriptions_spec = count($html->find(".//*[@class='text3']/noindex"));
                if($count_descriptions_spec > 0){
                    if($html && is_object($html) && isset($html->nodes)){

                    foreach($html->find(".//*[@class='text3']/noindex") as $element) {
                        $descriptions_spec = $element->outertext;
                        $array_desk['descriptions_spec'] = $descriptions_spec;
                        }
                    }
                }
                    $count_description = count($html->find(".//*[@itemprop='description']"));
                    if($count_description > 0){
                        if($html && is_object($html) && isset($html->nodes)){
                            foreach($html->find(".//*[@itemprop='description']") as $element) {
                                $descriptions = $element->outertext;
                                $array_desk['descriptions'] = $descriptions;
                            }
                        }
                    }
                    $count_descriptions_spec_two = count($html->find(".//*[@class='b-properties"));
                    if($descriptions_spec_two > 0){
                        if($html && is_object($html) && isset($html->nodes)){
                            foreach($html->find(".//*[@class='b-properties']") as $element) {
                                $descriptions_spec_two = $element->outertext;
                                $array_desk['descriptions_spec_two'] = $descriptions_spec_two;
                            }
                        }
                    }

                    return $array_desk;
            }

            return false;
        }       
        catch (Exception $e)
        {
            return false;
        }
             
    }

    public function createDetailProduct($id_post, $descriptions, $url)
    {
        $desc_spec = iconv('windows-1251',"UTF-8//IGNORE", $descriptions['descriptions_spec']);
        $desc_spec_two = iconv('windows-1251',"UTF-8//IGNORE", $descriptions['descriptions_spec_two']);
        $desc = iconv('windows-1251',"UTF-8//IGNORE", $descriptions['descriptions']);


        $desc=preg_replace("/(<img[^>]+>)/", '<a href="' . $url . '">$1</a>', $desc);



        //$desc = preg_replace('/<img[^>]+>/', '<a href="www.wyomingcpa.ru">$1</a>',  $desc);

        $body_post ="<div class='desc_plugin'><strong>Полное описание</strong>" . $desc . "<br><strong>Техническое описание</strong>" . $desc_spec . $desc_spec_two . "</div>";

        $update_post = array(
                'ID'           => $id_post,
                'post_content' => $body_post,
        );
        
        $check_update_post = wp_update_post( $update_post, true ); 
        if ( is_wp_error( $check_update_post ) ) {
            //echo $post_id->get_error_message();
        }
        else {
            update_post_meta( $check_update_post, 'update', 1, false );
        }
    }
} 

add_action( 'plugins_loaded', array( 'Pleer', 'get_instance' ) );
