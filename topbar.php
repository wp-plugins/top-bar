<?php
/**
* Plugin Name: Top Bar
* Plugin URI: http://wpdarko.com/top-bar/
* Description: Simply the easiest way to add a topbar to your website. This plugin adds a simple and clean notification bar at the top your website, allowing you to display a nice message to your visitors. Find help and information on our <a href="http://wpdarko.com/support/">support site</a>. This is a free plugin, it is NOT limited and does not contain any ad. Check out the <a href='http://wpdarko.com/top-bar/'>PRO version</a> for more great features.
* Version: 1.2
* Author: WP Darko
* Author URI: http://wpdarko.com
* Text Domain: top-bar
* Domain Path: /lang/
* License: GPL2
 */


// Loading text domain
add_action( 'plugins_loaded', 'tpbr_load_plugin_textdomain' );
function tpbr_load_plugin_textdomain() {
  load_plugin_textdomain( 'top-bar', FALSE, basename( dirname( __FILE__ ) ) . '/lang/' );
}


// Checking for the PRO version
add_action('admin_init', 'topbar_free_pro_check');
function topbar_free_pro_check() {
    if (is_plugin_active('topbar-pro/topbar_pro.php')) {

        function tpbr_admin_notice(){
        echo '<div class="updated">
                <p><strong>PRO</strong> version is activated.</p>
              </div>';
        }
        add_action('admin_notices', 'tpbr_admin_notice');

        deactivate_plugins(__FILE__);
    }
}


/* --- Enqueue plugin stylsheet --- */
add_action( 'wp_enqueue_scripts', 'add_topbar_style' );
function add_topbar_style() {
    wp_enqueue_script( 'topbar_cookiejs', plugins_url('js/jquery.cookie.js', __FILE__), array( 'jquery' ));
    wp_enqueue_script( 'topbar_frontjs', plugins_url('js/tpbr_front.min.js', __FILE__), array( 'jquery' ));

    if ( is_admin_bar_showing() ) {
        $tpbr_is_admin_bar = 'yes';
    } else {
        $tpbr_is_admin_bar = 'no';
    }

    // getting the options
    $tpbr_fixed = get_option('tpbr_fixed');
    $tpbr_message = get_option('tpbr_message');
    $tpbr_status = get_option('tpbr_status');
    $tpbr_yn_button = get_option('tpbr_yn_button');
    $tpbr_color = get_option('tpbr_color');
    $tpbr_button_text = get_option('tpbr_btn_text');
    $tpbr_button_url = get_option('tpbr_btn_url');
    $tpbr_settings = array(
        'fixed' => $tpbr_fixed,
        'message' => $tpbr_message,
        'status' => $tpbr_status,
        'yn_button' => $tpbr_yn_button,
        'color' => $tpbr_color,
        'button_text' => $tpbr_button_text,
        'button_url' => $tpbr_button_url,
        'close_image' => $tpbr_close_image,
        'is_admin_bar' => $tpbr_is_admin_bar,
    );
    // sending the options to the js file
    wp_localize_script( 'topbar_frontjs', 'tpbr_settings', $tpbr_settings );
}


/* --- Enqueue plugin stylsheet --- */
add_action( 'admin_enqueue_scripts', 'add_admin_topbar_style' );
function add_admin_topbar_style() {
    $screen = get_current_screen();
    if ($screen->base == 'toplevel_page_top-bar/topbar'){
    	  wp_enqueue_style( 'topbar', plugins_url('css/admin_topbar_style.min.css', __FILE__));
        wp_enqueue_script( 'topbar_cpjs', plugins_url('js/tpbr.min.js', __FILE__), array( 'jquery' ));
        wp_enqueue_script( 'topbar_cpljs', plugins_url('js/jquery.tinycolorpicker.min.js', __FILE__), array( 'jquery' ));
    }
}


// create custom plugin settings menu
add_action('admin_menu', 'tpbr_create_menu');

function tpbr_create_menu() {

    if (is_plugin_active('topbar-pro/topbar_pro.php')) {

    } else {
        //create new top-level menu
	add_menu_page('Top Bar', 'Top Bar', 'administrator', __FILE__, 'tpbr_settings_page', 'dashicons-admin-generic');

	//call register settings function
	add_action( 'admin_init', 'register_tpbr_settings' );
    }

}

function register_tpbr_settings() {
	//register our settings
    register_setting( 'tpbr-settings-group', 'tpbr_fixed' );
	register_setting( 'tpbr-settings-group', 'tpbr_status' );
    register_setting( 'tpbr-settings-group', 'tpbr_yn_button' );
	register_setting( 'tpbr-settings-group', 'tpbr_color' );
	register_setting( 'tpbr-settings-group', 'tpbr_message' );
    register_setting( 'tpbr-settings-group', 'tpbr_btn_text' );
    register_setting( 'tpbr-settings-group', 'tpbr_btn_url' );
}

function tpbr_settings_page() {
?>

<div class="tpbr_wrap">
    <div class="tpbr_inner">
        <h2>Top Bar <span style='color:lightgrey;'>— <?php echo __( 'options', 'top-bar' );?></span></h2>

        <form method="post" action="options.php">
            <?php settings_fields( 'tpbr-settings-group' ); ?>
            <?php do_settings_sections( 'tpbr-settings-group' ); ?>

            <h4><?php echo __( 'Top Bar status', 'top-bar' );?></h4>
            <p style='color:lightgrey;'><?php echo __( 'You can activate/deactivate your topbar at anytime.', 'top-bar' );?></p>
            <?php $current_status = esc_attr( get_option('tpbr_status') ); ?>
            <select name="tpbr_status">
                <?php if ($current_status == 'active') { ?>
                    <option value="active" selected><?php echo __( 'Active', 'top-bar' );?></option>
                    <option value="inactive"><?php echo __( 'Inactive', 'top-bar' );?></option>
                <?php } else if ($current_status == 'inactive') { ?>
                    <option value="inactive" selected><?php echo __( 'Inactive', 'top-bar' );?></option>
                    <option value="active"><?php echo __( 'Active', 'top-bar' );?></option>
                <?php } else { ?>
                    <option value="inactive" selected><?php echo __( 'Inactive', 'top-bar' );?></option>
                    <option value="active"><?php echo __( 'Active', 'top-bar' );?></option>
                <?php } ?>
            </select>
            <?php if ($current_status == 'active') { ?>
                <div class='tpbr_led_green'></div>
            <?php } else if ($current_status == 'inactive') { ?>
                <div class='tpbr_led_red'></div>
            <?php } else { ?>
                <div class='tpbr_led_red'></div>
            <?php } ?>

            <h4><?php echo __( 'Always visible?', 'top-bar' );?></h4>
            <p style='color:lightgrey;'><?php echo __( 'If this is set to \'Yes\' the top bar will stick to the top of the screen.', 'top-bar' );?></p>
            <?php $current_fixed = esc_attr( get_option('tpbr_fixed') ); ?>
            <select class="tpbr_fixed" name="tpbr_fixed">
                    <?php if ($current_fixed == 'fixed') { ?>
                        <option value="fixed" selected><?php echo __( 'Yes', 'top-bar' );?></option>
                        <option value="notfixed"><?php echo __( 'No', 'top-bar' );?></option>
                    <?php } else if ($current_fixed == 'notfixed') { ?>
                        <option value="notfixed" selected><?php echo __( 'No', 'top-bar' );?></option>
                        <option value="fixed"><?php echo __( 'Yes', 'top-bar' );?></option>
                    <?php } else { ?>
                        <option value="notfixed" selected><?php echo __( 'No', 'top-bar' );?></option>
                        <option value="fixed"><?php echo __( 'Yes', 'top-bar' );?></option>
                    <?php } ?>
            </select>

            <h5>— <?php echo __( 'Message', 'top-bar' );?></h5>
            <input class='tpbr_tx_field' type="text" name="tpbr_message" placeHolder="<?php echo __( 'eg. Check out our new product right now!', 'top-bar' );?>" value="<?php echo esc_attr( get_option('tpbr_message') ); ?>" />
            <div class='tpbr_big_btnbox'>
                <h4><?php echo __( 'Button', 'top-bar' );?></h4>
                <?php $current_status = esc_attr( get_option('tpbr_yn_button') ); ?>
                <select class="tpbr_yn_button" name="tpbr_yn_button">
                    <?php if ($current_status == 'button') { ?>
                        <option value="button" selected><?php echo __( 'Yes', 'top-bar' );?></option>
                        <option value="nobutton"><?php echo __( 'No', 'top-bar' );?></option>
                    <?php } else if ($current_status == 'nobutton') { ?>
                        <option value="nobutton" selected><?php echo __( 'No', 'top-bar' );?></option>
                        <option value="button"><?php echo __( 'Yes', 'top-bar' );?></option>
                    <?php } else { ?>
                        <option value="nobutton" selected><?php echo __( 'No', 'top-bar' );?></option>
                        <option value="button"><?php echo __( 'Yes', 'top-bar' );?></option>
                    <?php } ?>
                </select>
                <div class='tpbr_button_box'>
                    <h5>— <?php echo __( 'Button text', 'top-bar' );?></h5>
                    <input class='tpbr_tx_field' type="text" name="tpbr_btn_text" placeHolder="<?php echo __( 'eg. See product', 'top-bar' );?>" value="<?php echo esc_attr( get_option('tpbr_btn_text') ); ?>" />

                    <h5>— <?php echo __( 'Button URL', 'top-bar' );?></h5>
                    <input class='tpbr_tx_field' type="text" name="tpbr_btn_url" placeHolder="<?php echo __( 'eg. http://wpdarko.com', 'top-bar' );?>" value="<?php echo esc_attr( get_option('tpbr_btn_url') ); ?>" />
                </div>
            </div>

            <?php $current_color = esc_attr( get_option('tpbr_color') ); ?>

            <?php if (empty($current_color)) {
                $current_color = '#333333';
            }?>

            <span id="tpbrcc" style='display:none;'><?php echo $current_color; ?></span>
            <br/>
            <h4 style='display:inline-block;'><?php echo __( 'Top Bar color', 'top-bar' );?></h4>
            <div style='position:relative; top:5px; left:14px;display:inline;' id="colorPicker">
                <a style='display:inline-block;' class="color"><div class="colorInner"></div></a>
                <div class="track"></div>
                <ul class="dropdown"><li></li></ul>
                <input name="tpbr_color" value="<?php echo $current_color; ?>" type="hidden" class="colorInput"/>
            </div>

            <?php submit_button(); ?>

        </form>
        <strong>This is a free plugin, it is NOT limited and does not contain any ad.</strong><br/><br/>Check out the <a target='_blank' href='http://wpdarko.com/items/top-bar-pro/'>PRO version</a> for many great new features. <br/><br/><span style="color:#999999;">Use coupon 7832346 and get a 10% discount.</span><br/><br/>
    </div>
</div>
<?php }

?>
