<?php
/**
 *	Manage Credit Adjustments Page.
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

if( !class_exists( 'PXP_Credit_Adjustments' ) )
{

class PXP_Credit_Adjustments
{
	public function __construct()
	{	
		// Add Actions
		add_action( 'save_post', array( $this, 'pxp_credit_adjustment_save_post' ), 10, 2 );
		add_action( 'manage_pxp_adjustments_posts_custom_column', array( $this, 'pxp_credit_adjustments_posts_custom_column' ), 10, 2);
		
		// Add Filters
		add_filter( 'manage_edit-pxp_adjustments_columns', array( $this, 'pxp_set_custom_edit_pxp_credit_adjustments_columns' ) );
		add_filter( 'manage_edit-pxp_adjustments_sortable_columns', array( $this, 'pxp_edit_credit_adjustments_sortable_columns' ) );
	}
	
	/**
	 * Set Columns of credit adjustments.
	 *
	 * @param $columns An array of columns.
	 */
	public function pxp_set_custom_edit_pxp_credit_adjustments_columns( $columns )
	{
		$columns = array(
			'cb'			=> '<input type="checkbox" />',
			'order_number' 	=> __( 'Order Number' ),
			'job_reference'	=> __( 'Job Reference' ),
			'contact_name'	=> __( 'Contact Name' ),
			'amount'		=> __( 'Amount' ),
			'adjustment'	=> __( 'Adjustment' ),
			'author' 		=> __( 'Author' ),
			'status' 		=> __( 'Status' ),
			'date' 			=> __( 'Date' ),
		);
		
		return $columns;
	}
	
	/**
	 * Set values of the columns per credit adjustment.
	 *
	 * @param $columns An array of columns.
	 * @param $post_id Post ID of credit adjusment list.
	 */
	public function pxp_credit_adjustments_posts_custom_column( $column, $post_id ) 
	{		
		switch ($column) 
		{
			case 'date':
				$date = get_the_date();
				
				printf( __( '%s', '%s' ), $date );
				break;
			case 'order_number':
				$order_number = get_post_meta( $post_id, '_order_number', true );
				
				$edit 		= get_edit_post_link( $post_id );
				$trash 		= get_delete_post_link( $post_id );
				$view 		= get_permalink( $post_id );
				$restore 	= get_restore_post_link( $post_id );
				$delete 	= get_delete_permanent_post_link( $post_id );
				$post_status = ( isset( $_GET['post_status'] ) ) ? $_GET['post_status'] : NULL;
				
				if($post_status == "trash")
				{
					$row_action = '<div class="row-actions">
						<span class="edit">
							<a title="Restore this item from the Trash" href="' . $restore . '">Restore</a> | 
						</span>
						<span class="trash">
							<a class="submitdelete" href="' . $delete . '" title="Move this item to the Trash">Delete Permanently</a>
						</span>
					</div>';
				}
				else
				{
					$row_action = '<div class="row-actions">
						<span class="edit">
							<a title="Edit this item" href="' . $edit . '">Edit</a> | 
						</span>
						<span class="trash">
							<a class="submitdelete" href="' . $trash . '" title="Move this item to the Trash">Trash</a> | 
						</span>
						<span class="view">
							<a title="Edit this item" href="' . $view . '">View</a>
						</span>
					</div>';
				}
				
				printf( __( '<a href="%s"><strong>%s</strong></a> %s', '%s' ), $edit, $order_number, $row_action );
				break;
			case 'job_reference':
				$job_reference = get_post_meta( $post_id, '_job_reference', true );
				
				printf( __( '%s', '%s' ), $job_reference  );
				break;
			case 'contact_name':
				$client = get_post_meta( $post_id, '_contact_name', true );
				
				$user_info 	= get_userdata( $client );
				$first_name	= $user_info->first_name;
				$last_name 	= $user_info->last_name;
				
				$company_name	= get_user_meta( $client, 'pxp_company_name', true );
				
				$contact_name = $first_name . ' ' . $last_name . ' (' . $company_name . ')';
				
				printf( __( '%s', '%s' ), $contact_name );
				break;
			case 'amount':
				
				$amount = get_post_meta( $post_id, '_amount', true );
				
				printf( __( '%s', '%s' ), $amount );
				break;
			case 'adjustment':
				$adjustment = get_post_meta( $post_id, '_adjustment', true );
				
				printf( __( '%s', '%s' ), $adjustment );
				break;
			case 'status':
				global $post;

				$status = $post->post_status;
				
				$status = ( $status == "publish" ) ? "Approved" : ucfirst( $status );
				
				printf( __( '%s' ), $status );
				break;
		}
	}
	
	/**
	 * Set columns as sortable.
	 *
	 * @param $columns An array of columns.
	 */
	public function pxp_edit_credit_adjustments_sortable_columns( $columns ) {

		$columns['date'] 			= 'date';
		$columns['order_number'] 	= 'order_number';
		$columns['job_reference'] 	= 'job_reference';
		$columns['contact_name'] 	= 'contact_name';
		$columns['amount'] 			= 'amount';
		$columns['adjustment'] 			= 'adjustment';
		$columns['status'] 			= 'status';
		
		return $columns;
	}
	/**
	 * Display Credit Adjustment details metebox.
	 */
	public static function pxp_credit_adjustments_general_box()
	{
		global $post_id;
		
		//Add an nonce field so we can check it later.
		wp_nonce_field('pxp_adjustments', 'pxp_adjustments_nonce');
		
		$clients = PXP_Admin_Clients::pxp_admin_get_clients();
		
		$order_number	= get_post_meta( $post_id, '_order_number', true );
		$job_reference 	= get_post_meta( $post_id, '_job_reference', true );
		$contact_name	= get_post_meta( $post_id, '_contact_name', true );
		$amount			= get_post_meta( $post_id, '_amount', true );
		$notes			= get_post_meta( $post_id, '_notes', true );
		$adjustment		= get_post_meta( $post_id, '_adjustment', true );
		
		$approved 		= get_post_meta( $post_id, '_approved', true );
?>
		<table class="form-table pxp_adjustments">
			<tbody>
				<tr valign="top">
					<th><?php _e( 'Contact Name:' ); ?></th>
					<td>
						<select name="contact_name" id="contact_name">
					<?php 
						foreach( $clients as $client ): 
							$selected = ( $client['ID'] == $contact_name ) ? "selected" : "";
					?>
							<option value="<?php echo $client['ID']; ?>" <?php _e( $selected ); ?>><?php _e( $client['contact_name'] . ' (' . $client['company_name'] . ')' ); ?></option>
					<?php 
						endforeach; 
					?>
						</select>
					</td>
				</tr>
				<tr valign="top">
					<th><?php _e( 'Order Number:' ); ?></th>
					<td><input type="text" name="order_number" id="order_number" value="<?php echo $order_number; ?>" class="regular-text" /></td>
				</tr>
				<tr valign="top">
					<th><?php _e( 'Job Reference:' ); ?></th>
					<td><input type="text" name="job_reference" id="job_reference" value="<?php echo $job_reference; ?>" class="regular-text" /></td>
				</tr>
				<tr valign="top">
					<th><?php _e( 'Amount:' ); ?></th>
						<td><input type="text" name="amount" id="amount" value="<?php echo $amount; ?>" class="regular-text" /></td>
				</tr>
				<tr valign="top">
					<th><?php _e( 'Notes:' ); ?></th>
					<td>
						<textarea name="notes" id="notes" class="half-width" rows="6"><?php echo $notes; ?></textarea>
					</td>
				</tr>
				<tr valign="top">
					<th><?php _e( 'Adjustment:' ); ?></th>
					<td>
						<label for="adjustment_charge"><input type="radio" name="adjustment" id="adjustment_charge" value="Charge" <?php echo ( $adjustment == "Charge" ) ? "checked" : ""; ?> /> <?php _e( 'Charge' ); ?></label>
						<label for="adjustment_refund"><input type="radio" name="adjustment" id="adjustment_refund" value="Refund" <?php echo ( $adjustment == "Refund" ) ? "checked" : ""; ?>  /> <?php _e( 'Refund' ); ?></label>
					</td>
				</tr>
			<?php if( current_user_can( 'manage_options' ) ): ?>
				<tr valign="top">
					<th><label for="adjustment_approve"><?php _e( 'Approve:' ); ?></label></th>
					<td>
						<input type="checkbox" name="approve" id="adjustment_approve" value="1" <?php echo ( $approved == 1 ) ? "checked disabled" : ""; ?> />
					</td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
<?php
	}
 
 	/**
	 * Update credit adjustments details.
	 * @param int 		$post_id 	The ID of post.
	 * @param WP_Post 	$post 		The post object.
	 */
	public function pxp_credit_adjustment_save_post($post_id, $post)
	{
		if( !isset($_POST['pxp_adjustments_nonce'] ) ) { return $post_id; }
		
		$nonce = $_POST['pxp_adjustments_nonce'];
		
		if( !wp_verify_nonce($nonce, 'pxp_adjustments') ) { return $post_id; }
		
		if( 'page' == $_POST['post_type'] )
		{	
			if( !current_user_can('edit_page', $post_id) )  { return $post_id; }
		}
		else
		{	
			if( !current_user_can('edit_post', $post_id) ) { return $post_id; }
		}
		
		if( $post->post_type != "pxp_adjustments") 
		{
			return $post_id;
		}

		$approved 		= get_post_meta( $post_id, '_approved', true );
		
		$order_id		= $_POST['order_number'];
		$job_reference 	= $_POST['job_reference'];
		$user_id		= $_POST['contact_name'];
		$amount			= $_POST['amount'];
		$notes			= $_POST['notes'];
		$adjustment		= $_POST['adjustment'];
		
	
		$data = array (
			'order_number'	=> $order_id,
			'job_reference'	=> $job_reference,
			'contact_name'	=> $user_id,
			'amount'		=> $amount,
			'notes'			=> $notes,
			'adjustment'	=> $adjustment,
		);
		
		if( current_user_can( 'manage_options' ) && $approved != 1 ):
			$data['approved'] = $_POST['approve'];
			
			if( isset( $_POST['approve'] ) && $_POST['approve'] == 1 ):
				switch( $adjustment )
				{
					case 'Charge':
						$this->pxp_credit_adjust_charge( $user_id, $order_id, $amount );
						break;
					case 'Refund':
						$this->pxp_credit_adjust_refund( $user_id, $order_id, $amount );
						break;
				}	
			endif;
		endif;
		
		foreach( $data as $key => $value )
		{
			update_post_meta( $post_id, '_' .$key, $value );
		}
		
		// Update post status as pending or publish.
		if ( ! wp_is_post_revision( $post_id ) )
		{
			remove_action( 'save_post', array( $this, 'pxp_credit_adjustment_save_post' ) );
			
			$args = array(
				'ID'			=> $post_id,
				'post_status'	=> 'pending'
			);
			
			if( current_user_can( 'manage_options' ) || $approved == 1 ):
				$args['post_status'] = 'publish';
			endif;
			
			wp_update_post( $args );
			
			add_action( 'save_post', array( $this, 'pxp_credit_adjustment_save_post' ) );
		}
	}
	
	/**
	 * Charge client additional credit.
	 *
	 * @param int $user_id Client to be charge.
	 * @param int $order_id Order ID to charge additional credits.
	 * @param int $amount Amount of credits to charge client.
	 */
	public function pxp_credit_adjust_charge( $user_ID, $order_number, $amount )
	{
		$user_credits = get_user_meta( $user_ID, 'pxp_user_credits', true );
		
		if( $amount < $user_credits )
		{
			$update_credits = $user_credits - $amount;
			update_user_meta( $user_ID, 'pxp_user_credits', $update_credits );
		}
	}
	
	/**
	 * Refund client credits.
	 *
	 * @param int $user_id Client to be refunded.
	 * @param int $order_id Order ID to refund credits.
	 * @param int $amount Amount of credits refunded to client.
	 */
	public function pxp_credit_adjust_refund( $user_ID, $order_number, $amount )
	{
		$user_credits 	= get_user_meta( $user_ID, 'pxp_user_credits', true );
		$update_credits = $user_credits + $amount;
		
		echo $user_credits;
		
		update_user_meta( $user_ID, 'pxp_user_credits', $update_credits );
	}
}

}

return new PXP_Credit_Adjustments();

?>

