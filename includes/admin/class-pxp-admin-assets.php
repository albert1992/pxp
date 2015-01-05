<?php
/**
 *	Import scripts to plugin.
 *
 *	@author 	Mark Francis C. Flores & Illumedia Outsourcing
 *	@category 	Admin
 *	@package 	PixelPartners/Class
 *	@version    1.0.0
 */

if( !defined( 'ABSPATH' ) ) 
{
	exit; // Exit if accessed directly
}

if( !class_exists( 'PXP_Admin_Assets' ) )
{

class PXP_Admin_Assets
{
	public function __construct()
	{
		add_action( 'admin_enqueue_scripts', array( $this, 'pxp_admin_scripts' ) );
	}
	
	/**
	 * Enqueue & Register scritps and styles.
	 */
	public function pxp_admin_scripts( $hook )
	{
		global $post;
		
		wp_enqueue_script	( 'jquery' );
		
		
		if( isset( $post ) && $post->post_type == "pxp_products" )
		{
			$this->pxp_initiate_bootstrap( $hook );
			
			wp_enqueue_media();
		}
		
		wp_register_style	( 'pxp-style', PXP_URL . '/assets/css/style.css' );
		wp_enqueue_style	( 'pxp-style' );
		
		wp_register_script	( 'pxp-script', PXP_URL . '/assets/js/admin.js' );
		wp_enqueue_script	( 'pxp-script' );
		
		
	}
	
	/**
	 * Initiate Bootstrap CSS and JS.
	 */
	public function pxp_initiate_bootstrap()
	{
		wp_register_style	( 'pxp-bootstrap-css', PXP_URL . '/assets/css/bootstrap.css' );
		wp_enqueue_style	( 'pxp-bootstrap-css' );
		
		wp_register_style	( 'pxp-bootstrap-theme', PXP_URL . '/assets/css/bootstrap-theme.min.css' );
		wp_enqueue_style	( 'pxp-bootstrap-theme' );
		
		wp_register_script	( 'pxp-bootstrap-js', PXP_URL . '/assets/js/bootstrap.min.js' );
		wp_enqueue_script	( 'pxp-bootstrap-js' );
	}
}

}

new PXP_Admin_Assets();