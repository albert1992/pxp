<?php
/**
 *	Selcted Product Shortcode.
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

if( !class_exists( 'PXP_Shortcode_Selected_Product' ) )
{

class PXP_Shortcode_Selected_Product
{
	public function __construct()
	{
	}
	
	public static function output( $atts )
	{
		pxp_get_template( 'class-pxp-template-selected_product.php' );
	}
}

}

return new PXP_Shortcode_Selected_Product();

?>