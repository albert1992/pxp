<?php
/**
 *	Manage plugin front-end page templates.
 *
 *	@author 	Mark Francis C. Flores & Illumedia Outsourcing
 *	@category 	Admin
 *	@package 	PixelPartners/Classes
 *	@version    1.0.0
 */

if( !defined( 'ABSPATH' ) ) 
{
	exit; // Exit if accessed directly
}

if( !class_exists( 'PXP_Templates' ) )
{

class PXP_Templates
{
	/**
	 * A Unique Identifier
	 */
	 protected $plugin_slug;

	/**
	 * A reference to an instance of this class.
	 */
	private static $instance;

	/**
	 * The array of templates that this plugin tracks.
	 */
	protected $templates;


	/**
	 * Returns an instance of this class. 
	 */
	public static function get_instance() {

		if( null == self::$instance ) {
				self::$instance = new PageTemplater();
		} 

		return self::$instance;
	} 
	
	public function __construct()
	{	
		$this->templates = array();

		// Add a filter to the attributes metabox to inject template into the cache.
		add_filter(	'page_attributes_dropdown_pages_args', array( $this, 'register_pxp_templates' ) );


		// Add a filter to the save post to inject out template into the page cache
		add_filter(	'wp_insert_post_data', array( $this, 'register_pxp_templates' ) );


		// Add a filter to the template include to determine if the page has our 
		// template assigned and return it's path
		add_filter(	'template_include', array( $this, 'view_pxp_template' ) );


		// Add your templates to this array.
		$this->templates = array(
				'pxp-page-template.php'     => 'PXP Page Template',
		);
	}
	
	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 *
	 */
	public function register_pxp_templates( $atts ) 
	{

		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

		// Retrieve the cache list. 
		// If it doesn't exist, or it's empty prepare an array
		$templates = wp_get_theme()->get_page_templates();
		if ( empty( $templates ) ) 
		{
				$templates = array();
		} 

		// New cache, therefore remove the old one
		wp_cache_delete( $cache_key , 'themes');

		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );

		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );

		return $atts;
	} 

	/**
	 * Checks if the template is assigned to the page
	 */
	public function view_pxp_template( $template ) 
	{
		global $post, $pxp_products;
		
		if( !$post )
		{
			return $template;
		}
		
		// Return archive template for 'pxp_products'
		if( is_post_type_archive( 'pxp_products' ) )
		{
			$file = plugin_dir_path(__FILE__). '/products/archive-pxp-products.php';
			
			return $file;
		}
		
		// Return single template for 'pxp_products'
		if( is_singular( 'pxp_products' ) )
		{
			$file = plugin_dir_path(__FILE__). '/products/single-pxp-product.php';
			
			return $file;
		}
		
		if( !isset( $this->templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) 
		{
				return $template;	
		} 
		
		$file = plugin_dir_path(__FILE__). get_post_meta( $post->ID, '_wp_page_template', true );
		
		// Just to be safe, we check if the file exist first
		if( file_exists( $file ) ) 
		{
				return $file;
		}
		else 
		{ 
			echo $file; 
		}
		
		return $template;
	}
}

}

return new PXP_Templates();

?>