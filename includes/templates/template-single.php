<?php
/**
 * @author Bill Minozzi
 * @copyright 2017
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

function cardealer_add_custom_js_to_header() {
    ?>
    <script type="text/javascript">
        function cardealer_goBack() {
            window.history.back(); 
        }
    </script>
    <?php
}
add_action('wp_head', 'cardealer_add_custom_js_to_header');


$my_theme =  strtolower(wp_get_theme());
if ($my_theme == 'twenty fourteen')
{
?>
<style type="text/css">
<!--
	.site::before {
    width: 0px !important;
}
-->
</style>
<?php 
}
define('CARDEALERSINGLE_TITLE', get_the_title() );
 get_header();


  ?>
	    <div id="container2"> 
        <?php 
        if(isset($_SERVER['HTTP_REFERER']))
         {?>
          <center>
          <button id="cardealer_goback" onclick="cardealer_goBack()">
          <?php 
          echo esc_attr__('Back', 'cardealer');?> 
          </button>
          <br /><br />
          </center>
        <?php } ?>
            <div id="content2" role="main">

                <?php cardealer_detail();
                
               $CarDealer_enable_contact_form = trim(get_option('CarDealer_enable_contact_form', 'yes'));
               if ($CarDealer_enable_contact_form == 'yes')
               {               
                ?>
                 <br />
                 <center>
                 <button id="CarDealer_cform">
                 <?php echo esc_attr__('Contact Us', 'cardealer'); ?>
                 </button>
                 </center>
                 <br />
			</div> 
            <?php 
            } 
            //
            //   $cardealer_the_title = get_the_title();
               if ($CarDealer_enable_contact_form == 'yes') 
               {
                   include_once (CARDEALERPATH . 'includes/contact-form/multi-contact-show-form.php');  
               }
         ?>  
		</div>
<?php 
        $registered_sidebars = wp_get_sidebars_widgets();
        foreach( $registered_sidebars as $sidebar_name => $sidebar_widgets ) {
        	unregister_sidebar( $sidebar_name );
        }
get_footer(); 
?>