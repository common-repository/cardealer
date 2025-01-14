<?php
/**
 * @author William Sergio Minozzi
 * @copyright 2017
 */
if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly
// ob_start();
// define('CARDEALERHOMEURL',admin_url());
$cardealer_urlfields = CARDEALERHOMEURL . "/edit.php?post_type=cardealerfields";
$cardealer_urlcars = CARDEALERHOMEURL . "/edit.php?post_type=cars";
$cardealer_urllocations =
    CARDEALERHOMEURL . "/edit-tags.php?taxonomy=locations&post_type=cars";
$cardealer_urlmakes = CARDEALERHOMEURL . "/edit-tags.php?taxonomy=makes&post_type=cars";
$cardealer_urlsettings = CARDEALERHOMEURL . "/options.php?page=cardealer_settings";
$cardealer_urlteam = CARDEALERHOMEURL . "/edit-tags.php?taxonomy=team&post_type=cars";
add_action("admin_init", "cardealer_settings_init");
add_action("admin_menu", "cardealer_add_admin_menu");
/*
function cardealer_enqueue_scripts()
{
    wp_enqueue_style(
        "bill-help-cardealer",
        CARDEALERURL . "/dashboard/css/help.css"
    );
    wp_enqueue_style(
        "bill-pointer",
        CARDEALERURL . "/dashboard/css/pointer.css"
    );
}
*/
function cardealer_enqueue_scripts() {
    wp_enqueue_style(
        "bill-help-cardealer",
        CARDEALERURL . "/dashboard/css/help.css",
        array(),
        CARDEALERVERSION  
    );
    wp_enqueue_style(
        "bill-pointer",
        CARDEALERURL . "/dashboard/css/pointer.css",
        array(),
        CARDEALERVERSION  
    );
}
add_action('wp_enqueue_scripts', 'cardealer_enqueue_scripts');

add_action("admin_init", "cardealer_enqueue_scripts");
function cardealer_fields_callback()
{
    global $cardealer_urlfields; ?>
    <script type="text/javascript">
    <!--
     window.location  = "<?php echo esc_attr($cardealer_urlfields); ?>";
    -->
    </script>
<?php
}
function cardealer_cars_callback()
{
    global $cardealer_urlcars; ?>
    <script type="text/javascript">
    <!--
      window.location  = "<?php echo esc_url($cardealer_urlcars); ?>";
    -->
</script>
<?php
}
function cardealer_cardealer_team_callback()
{
    global $cardealer_urlteam; ?>
    <script type="text/javascript">
    <!--
     window.location  = "<?php echo esc_url($cardealer_urlteam); ?>";
    -->
</script>
<?php
}
function cardealer_locations_callback()
{
    global $cardealer_urllocations; ?>
    <script type="text/javascript">
    <!--
     window.location  = "<?php echo esc_url($cardealer_urllocations); ?>";
    -->
</script>
<?php
}
function cardealer_makes_callback()
{
    global $cardealer_urlmakes; ?>
    <script type="text/javascript">
    <!--
     window.location  = "<?php echo esc_url($cardealer_urlmakes); ?>";
    -->
</script>
<?php
}
function cardealer_settings_callback()
{
    global $cardealer_urlsettings; ?>
    <script type="text/javascript">
    <!--
     window.location  = "<?php echo esc_url($cardealer_urlsettings); ?>";
    -->
</script>
<?php
}
function cardealer_add_admin_menu()
{
    //   global $vmtheme_hook;
    //   $vmtheme_hook = add_theme_page( 'For Dummies', 'For Dummies Help', 'manage_options', 'for_dummies', 'cardealer_options_page' );
    //   add_action('load-'.$vmtheme_hook, 'vmtheme_contextual_help');
    global $menu;
    add_menu_page(
        "Car Dealer",
        "Car Dealer",
        "manage_options",
        "car_dealer_plugin",
        "cardealer_options_page",
        esc_url(CARDEALERURL) . "assets/images/car-icon.png",
        "30"
    );
    include_once ABSPATH . "wp-includes/pluggable.php";
    /*
 add_submenu_page( 
     string $parent_slug, 
     string $page_title, 
     string $menu_title, 
     string $capability, 
     string $menu_slug, 
     callable $function = '' )
*/
    $link_our_new_CPT = urlencode("edit.php?post_type=cardealerfields");
    add_submenu_page(
        "car_dealer_plugin",
        "Fields Table",
        __("Fields Table", "cardealer"),
        "manage_options",
        "fields-table",
        "cardealer_fields_callback",
        1
    );
    add_submenu_page(
        "car_dealer_plugin",
        "Cars table",
        __("Cars Table", "cardealer"),
        "manage_options",
        "cars-table",
        "cardealer_cars_callback",
        2
    );
    add_submenu_page(
        "car_dealer_plugin",
        "Makes",
        __("Makes", "cardealer"),
        "manage_options",
        "md-makes",
        "cardealer_makes_callback",
        3
    );
    add_submenu_page(
        "car_dealer_plugin",
        "Locations",
        __("Locations", "cardealer"),
        "manage_options",
        "md-locations",
        "cardealer_locations_callback",
        4
    );
    add_submenu_page(
        "car_dealer_plugin",
        "Team",
        __("Team", "cardealer"),
        "manage_options",
        "md-team",
        "cardealer_cardealer_team_callback",
        5
    );

    add_submenu_page(
        "car_dealer_plugin",
        "Designer",
        "CarDealer Designer",
        "manage_options",
        "md-designer",
        "cardealer_designer_callback",
        7
    );

    function cardealer_designer_callback()
    {
        if (strpos(wp_get_referer(), "bill_designer") == false) {
            $cardealer_temp =
                home_url() .
                "/wp-admin/customize.php?autofocus[panel]=bill_designer";
        } else {
            $cardealer_temp =
                home_url() . "/wp-admin/index.php?customize_changeset_uuid=";
        }
        echo "<script>";
        echo 'window.location.href = "' . esc_url($cardealer_temp) . '";';

        echo "</script>";
    }

    add_submenu_page(
        "car_dealer_plugin",
        "Settings",
        __("Settings", "cardealer"),
        "manage_options",
        "cardealer-settings",
        "cardealer_settings_callback",
        6
    );

    if (is_multisite()) {
        add_submenu_page(
            "car_dealer_plugin", // $parent_slug
            "More Tools Same Author", // string $page_title
            "More Tools Same Author", // string $menu_title
            "manage_options", // string $capability
            "cardealer_more_plugins", // menu slug
            "cardealer_more_plugins", // callable function
            9 // position
        );
    } else {
        add_submenu_page(
            "car_dealer_plugin", // $parent_slug
            "More Tools Same Author", // string $page_title
            "More Tools Same Author", // string $menu_title
            "manage_options", // string $capability
            // 'wptools_options39', // menu slug
            // 'wptools_new_more_plugins', // callable function
            "cardealer_new_more_plugins", // menu slug
            "cardealer_new_more_plugins", // callable function
            9 // position
        );
    }

    function cardealer_gopro3_callback()
    {
        $urlgopro = "http://cardealerplugin.com/premium/"; ?>
        <script type="text/javascript">
            <!--
            window.location = "<?php echo esc_url($urlgopro); ?>";
            -->
        </script>
    <?php
    }

    add_submenu_page(
        "car_dealer_plugin", // $parent_slug
        "Go Pro", // string $page_title
        '<font color="#FF6600">' . __("Go Pro", "cardealer") . "</font>", // string $menu_title
        "manage_options", // string $capability
        "cardealer_my-custom-submenu-page3",
        "cardealer_gopro3_callback",
        10
    );
}

function cardealer_settings_init()
{
    register_setting("cardealer", "cardealer_settings");
}

function cardealer_options_page()
{
    // global $activated;
    global $cardealer_update_theme;
    $wpversion = get_bloginfo("version");
    $current_user = wp_get_current_user();
    $plugin = plugin_basename(__FILE__);
    $email = $current_user->user_email;
    $username = trim($current_user->user_firstname);
    $user = $current_user->user_login;
    $user_display = trim($current_user->display_name);
    if (empty($username)) {
        $username = $user;
    }
    if (empty($username)) {
        $username = $user_display;
    }
    $theme = wp_get_theme();
    $themeversion = $theme->version;
    ?>
    <!-- Begin Page -->
    <div id = "cardealer-theme-help-wrapper">   
        <div id="cardealer-not-activated"></div>
        <div id="cardealer-logo">
            <img alt="logo" src="<?php echo esc_url(
                CARDEALERIMAGES
            ); ?>logosmall.png" />
        </div>

        <div id="cardealer-social">
            <a href="http://cardealerplugin.com/share/"><img alt="social bar" src="<?php echo esc_url(
                CARDEALERIMAGES
            ); ?>/social-bar.png" width="250px" /></a>
        </div>

        <div id="cardealer_help_title">
            Help and Support Page
        </div> 

        <?php

        /*
        $current_url =
            "http://" .
            $_SERVER["HTTP_HOST"] .
            wp_unslash($_SERVER["REQUEST_URI"]);
        */

        $current_url = sanitize_text_field("http://" . sanitize_text_field($_SERVER["HTTP_HOST"]) . sanitize_text_field(wp_unslash($_SERVER["REQUEST_URI"])));

        $tab = null;
        $url_parts = wp_parse_url($current_url);
        if (isset($url_parts["query"])) {
            $query_params = wp_parse_args($url_parts["query"]);
            if (isset($query_params["tab"])) {
                $tab = sanitize_text_field($query_params["tab"]);
            }
        }

        if (isset($tab)) {
            $active_tab = $tab;
        } else {
            $active_tab = "dashboard";
        }
        ?>
        <h2 class="nav-tab-wrapper">
            <a href="?page=car_dealer_plugin&tab=memory&tab=dashboard" class="nav-tab">Dashboard</a>
            <a href="?page=car_dealer_plugin&tab=memory" class="nav-tab">Memory Check Up</a>
            <a href="?page=car_dealer_plugin&tab=tools" class="nav-tab">More Tools</a>   
        </h2>
        <?php
        if ($active_tab == "memory") {
            require_once CARDEALERPATH . "dashboard/memory.php";
        } elseif ($active_tab == "tools") {
            // require_once (CARDEALERPATH . 'dashboard/freebies.php');
            cardealer_new_more_plugins();
        } else {
            require_once CARDEALERPATH . "dashboard/dashboard.php";
        }
        echo '</div> <!-- "cardealer-theme_help-wrapper"> -->';
} // end Function cardealer_options_page


require_once ABSPATH . "wp-admin/includes/screen.php";
// ob_end_clean();
include_once ABSPATH . "wp-includes/pluggable.php";
if (!function_exists("cardealer_is_bill_theme")) {
    function cardealer_is_bill_theme()
    {
        $my_theme = wp_get_theme();
        $theme = trim($my_theme->get("Name"));
        $mythemes = [
            "boatdealer",
            "KarDealer",
            "verticalmenu",
            "fordummies",
            "Real Estate Right Now",
        ];
        // boatseller
        $count = count($mythemes);
        $theme = strtolower(trim($theme));
        for ($i = 0; $i < $count; $i++) {
            if ($theme == strtolower(trim($mythemes[$i]))) {
                return true;
            }
        }
        return false;
    }
}
?>
