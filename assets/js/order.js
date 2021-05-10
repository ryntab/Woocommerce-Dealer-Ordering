/**
 * Plugin Template admin js.
 *
 *  @package Woo Dealer Ordering/JS
 */
 jQuery(function($) {

	$( document ).ready(function() {
        $('#send-alert').on('click', function(){
            event.preventDefault();
            $(this).prop("disabled",true);
            sendCustomerAlert();
        });
        
        let sendCustomerAlert = () => {
			$.ajax({
                url: ajaxurl,
                data: {
                    'action': 'send_customer_alert',
                    'order_ID': woocommerce_admin_meta_boxes.post_id,
                },
                success:function(data) {
                    if (data = true){
                        $("#send-alert").text('Alert sent!').prop("disabled",true);
                    } else {
                        $("#send-alert").text('Error!').prop("disabled",true);
                    }  
                },
                error: function(errorThrown){
                    console.log(errorThrown);
                }
            });  	
        }
	});
});
