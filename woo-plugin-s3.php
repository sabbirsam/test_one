<?php
/**
 * Plugin Name: woocommerce-plugin-s3
 * Plugin URI: http://techsambd.com
 * Author: Md Sabbir Ahmed
 * Author URI: http://techsambd.com
 * Description: This Plugin can remove add to cart on shop and single page also the price so the shop become a catalog
 * License: GPL2 or later
 * Version: 1.00
 * Text-domain: sam_woocommerce_s2
 *
 */

define("ASSETS_DIR",plugin_dir_url(__FILE__)."/assets");
define("ASSETS_ADMIN_DIR",plugin_dir_url(__FILE__)."/assets/admin");
define("ASSETS_PUBLIC_DIR",plugin_dir_url(__FILE__)."/assets/public");


//1-Declare Class and initate
class WooPluginS3{
	private $version;
	//2.1
	function  __construct(){
		$this->version= time(); //for version num
		add_action('plugin_loaded', array($this,'load_textdomain')); //2.2
		add_action('wp_enqueue_scripts',array($this,'load_front_assets'));//2.5
		add_action('admin_enqueue_scripts',array($this,'load_admin_assets'));
		add_action('admin_menu',array($this,'sam_woocommerce_s2_add_metabox'));
	}

	function load_textdomain(){ //2.3
		load_plugin_textdomain('sam_woocommerce_s2',false,plugin_dir_url(__FILE__)."/languages");//2.4
	}

	function load_front_assets(){ //2.6
		wp_enqueue_style("sam_woocommerce-s2_woo_main_css",ASSETS_PUBLIC_DIR."/css/main.css",null,$this->version);
		wp_enqueue_script('sam_woocommerce_s2_woo_plugin_main_js',ASSETS_PUBLIC_DIR."/js/main.js",array('jquery'),$this->version,true);
	}

	function  load_admin_assets($screen){

		$css_files=array(
			'sam_woocommerce_s2_woo_admin_main_css'=>array('path'=>ASSETS_ADMIN_DIR."/css/main.css"),
			'sam_woocommerce_s2_woo_admin_style_css'=>array('path'=>ASSETS_ADMIN_DIR."/css/style_ok.css"),

		);
		foreach ($css_files as $handle=>$fileinfo){
			wp_enqueue_style($handle,$fileinfo['path'],$this->version);
		}


		$_screen=get_current_screen();
		//this one from page setting permalink ->edit.php?post_type=page
		if('edit.php' == $screen && 'page'==$_screen->post_type){
			$js_files=array(
				'sam_woocommerce_s2_woo_admin_plugin_main_js'=>array('path'=>ASSETS_ADMIN_DIR."/js/main.js",'dep'=>array('jquery')),
				'sam_woocommerce_s2_woo_admin_plugin_other_main_js'=>array('path'=>ASSETS_ADMIN_DIR."/js/other-main.js",'dep'=>array('jquery')),

			);
			foreach ($js_files as $handle=>$fileinfo){
				 wp_enqueue_script($handle,$fileinfo['path'],$fileinfo['dep'],$this->version, true);
			}
		}
	}
    //here add a metabox in post that take input of location in post
	function sam_woocommerce_s2_add_metabox(){
			add_meta_box(
				//id
				'sam_woocommerce_s2_post_location',
				//give a name and text domain
				__('Location Info','sam_woocommerce_s2'),
				//below name is used to add html
				array($this,'sam_woocommerce_s2_display_location'),
				//where i want to show its post
				'post',
				'side',
				'high'
			);
	}
	//display from
	function sam_woocommerce_s2_display_location(){
		$label= __('Location','sam_woocommerce_s2');
		$metabox= <<<EOD
<p>
<label for="sam_woo_location">{$label} </label>
<input type="text" name="sam_woo_location" id="sam_omb_location"/>
</p>
EOD;
		echo $metabox;
	}
}

new WooPluginS3();//2-initate