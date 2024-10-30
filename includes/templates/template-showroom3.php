<?php /**
 * @author Bill Minozzi
 * @copyright 2017 - 2020
 * List View Search
 */
if (!defined("ABSPATH")) {
    exit();
} // Exit if accessed directly

// return;

global $CarDealer_template_single;

$CarDealer_overwrite_gallery = strtolower(
    get_option("CarDealer_overwrite_gallery", "yes")
);
if ($CarDealer_overwrite_gallery == "yes") {
    require_once CARDEALERPATH . "includes/gallery/gallery.php";
}
?>
<style type="text/css">
<!-- 
<?php if (get_option("sidebar_search_page_result", "no") == "yes") { ?>
    #secondary, .sidebar-container
    {
        display: none !important; 
    }
<?php } ?>
#main
{  width: 100%!important;
   position:  absolute;}
-->
</style>
<?php
global $wp;
global $query, $wp_query, $meta_make, $meta_year, $CarDealer_hp_or_kw;
$wp_query->is_404 = false;
get_header();
$output = '<div style="margin-top: 20px;">';
$output .= '<div id="cardealer_container_search">';

$output .= '<div id="cardealer_content">';
if (!isset($_GET["submit"])) {
    $_GET["submit"] = "";
} else {
    $submit = sanitize_text_field($_GET["submit"]);
}
if (isset($_GET["post_type"])) {
    $post_type = sanitize_text_field($_GET["post_type"]);
}
if (isset($_GET["postNumber"])) {
    $postNumber = sanitize_text_field($_GET["postNumber"]);
}
if (empty($postNumber)) {
    $postNumber = get_option("CarDealer_quantity", 6);
}

if (get_query_var("paged")) {
    $paged = get_query_var("paged");
} elseif (get_query_var("page")) {
    $paged = get_query_var("page");
}
if (!isset($paged)) {
    $paged = cardealer_get_page();
}

if (isset($submit)) {
    require_once CARDEALERPATH . "includes/search/search_get_par.php";
    $output .= CarDealer_search(2);

    /*
    if (get_query_var('paged')) {
        $paged = get_query_var('paged');
    } elseif (get_query_var('page')) {
        $paged = get_query_var('page');
    }
    if(! isset($paged))
       $paged = cardealer_get_page();
*/

    //if (isset($submit)) {
    //   require_once (CARDEALERPATH . 'includes/search/search_get_par.php');
    $cardealer_afieldsId = cardealer_get_fields("all");
    $totfields = count($cardealer_afieldsId);
    $afilter = [];
    $afilter["relation"] = "AND";
    for ($i = 0; $i < $totfields; $i++) {
        $post_id2 = $cardealer_afieldsId[$i];
        $ametadata = cardealer_get_meta($post_id2);
        $keyname = "car-" . $ametadata[12];
        $metaname = "meta_" . $ametadata[12];
        if (isset($_GET[$metaname])) {
            $keyval = trim(sanitize_text_field($_GET[$metaname]));
            if ($keyval != "All") {
                if ($ametadata[1] == "checkbox") {
                    if ($keyval == "enabled") {
                        $afilter[] = [
                            "key" => $keyname,
                            "value" => $keyval,
                            "compare" => "EXISTS",
                        ];
                    } else {
                        echo esc_attr($keyname);
                        $afilter[] = [
                            "key" => $keyname,
                            "value" => "enabled",
                            "compare" => "NOT EXISTS",
                        ];
                    }
                }
                // not checkbox
                else {
                    if (!empty($keyval)) {
                        $afilter[] = [
                            "key" => $keyname,
                            // serialize())
                            "value" => $keyval,
                            "compare" => "LIKE",
                        ];
                    }
                }
            }
        }
    } // end Loop fields
    if ($price != "") {
        $pos = strpos($price, "-");
        if ($pos !== false) {
            $priceMin = trim(substr($price, 0, $pos - 1));
            $priceMax = trim(substr($price, $pos + 1));
        } else {
            $priceMin = "";
            $priceMax = "";
        }
        $afilter[] = [
            // array(
            "relation" => "OR",
            [
                "key" => "car-price",
                "value" => [$priceMin, $priceMax],
                "type" => "numeric",
                "compare" => "BETWEEN",
            ],
            [
                "key" => "car-price",
                "value" => "0",
                "type" => "numeric",
                "compare" => "=",
            ],
        ];
    } // end meta_price
    $afilter[] = [
        [$yearKey => $yearName, $yearVal => $year],
        //  array($conKey => $conName, $conVal => $con),
        [$fuelKey => $fuelName, $fuelVal => $fuel],
        [$transKey => $transName, $transVal => $trans],
        //   array($typeKey => $typeName, $typeVal => $typecar),
    ];
    // Featured
    if (isset($_GET["meta_order"])) {
        $order = trim(sanitize_text_field($_GET["meta_order"]));
    } else {
        $order = "";
    }
    if (!empty($order)) {
        if ($order == "price_high") {
            $wmetakey = "car-price";
            $wmetaorder = "DESC";
        }
        if ($order == "price_low") {
            $wmetakey = "car-price";
            $wmetaorder = "ASC";
        }
        if ($order == "year_high") {
            $wmetakey = "car-year";
            $wmetaorder = "DESC";
        }
        if ($order == "year_low") {
            $wmetakey = "car-year";
            $wmetaorder = "ASC";
        }

        if ($order == "mileage_high") {
            $wmetakey = "car-miles";
            $wmetaorder = "DESC";
        }
        if ($order == "mileage_low") {
            $wmetakey = "car-miles";
            $wmetaorder = "ASC";
        }
    } // no order
    $args = [
        "post_type" => "cars",
        "showposts" => $postNumber,
        "paged" => $paged,
    ];
    if (!empty($order)) {
        $args["orderby"] = "meta_value";
        $args["meta_type"] = "NUMERIC";
        $args["meta_key"] = $wmetakey;
        $args["order"] = $wmetaorder;
    }
    $args["meta_query"] = $afilter;
    if (!empty($make) and $make != "Any") {
        $args["tax_query"] = [
            [
                "taxonomy" => "makes",
                "field" => "name",
                "terms" => $make,
            ],
        ];
    }
}
// submit
else {
    $args = [
        "post_type" => "cars",
        "showposts" => $postNumber,
        "paged" => $paged,
        "order" => "DESC",
    ];
}
//
global $wp_query;
wp_reset_query();
$wp_query = new WP_Query($args);
$qposts = $wp_query->post_count;
// echo 'q posts: '.$qposts;
$CarDealer_measure = get_option("CarDealer_measure", "M2");

//$output .= '<br />';
//$output .= '<br />';

$output .= '<div class="multiGallery">';
$ctd = 0;
while ($wp_query->have_posts()):
    $wp_query->the_post();

    $post_id = get_the_ID();

    $ctd++;
    $price = get_post_meta(get_the_ID(), "car-price", true);
    if (!empty($price)) {
        $price = number_format_i18n($price, 0);
    }
    $image_id = get_post_thumbnail_id();
    if (empty($image_id)) {
        $image = CARDEALERIMAGES . "image-no-available-800x400_br.jpg";
        $image = str_replace("-", "", $image);
    } else {
        $image_url = wp_get_attachment_image_src($image_id, "medium", true);
        $image = str_replace(
            "-" . $image_url[1] . "x" . $image_url[2],
            "",
            $image_url[0]
        );
    }
    $CarDealer_thumbs_format = trim(get_option("CarDealer_thumbs_format", "1"));
    if ($CarDealer_thumbs_format == "2") {
        $thumb = CarDealer_theme_thumb($image, 300, 225, "br");
    }
    // Crops from bottom right
    else {
        $thumb = CarDealer_theme_thumb($image, 400, 200, "br");
    } // Crops from bottom right
    $year = get_post_meta(get_the_ID(), "car-year", true);
    $hp = get_post_meta(get_the_ID(), "car-hp", true);
    $year = get_post_meta(get_the_ID(), "car-year", true);
    $fuel = get_post_meta(get_the_ID(), "car-fuel", true);
    $transmission = get_post_meta(get_the_ID(), "transmission-type", true);
    $miles = get_post_meta(get_the_ID(), "car-miles", true);

    //$output .= '<br /><div class="CarDealer_container17">'
    $output .= '<div class="CarDealer_container17">';
    $output .= '<div class="CarDealer_gallery_17">';

    //$output .= '<a class="nounderline" href="' . get_permalink() . '">';

    if ($CarDealer_template_single == "4") {
        $output .= '<a data-toggle="modal" href="#myModal-' . $post_id . '">';
    } else {
        $output .= '<a class="nounderline" href="' . get_permalink() . '">';
    }

    $output .= '<img class="CarDealer_caption_img17" src="' . $thumb . '" />';
    $output .= "</a>";
    $output .= "</div>";
    $output .= '<div class="multiInfoRight17">';

    //  $output .= '<a class="nounderline" href="' . get_permalink() . '">';

    //  $output .= '<div class="multiTitle17">' . get_the_title() . '</div>';

    if ($CarDealer_template_single == "4") {
        $output .= '<a data-toggle="modal" href="#myModal-' . $post_id . '">';
    } else {
        $output .= '<a class="nounderline" href="' . get_permalink() . '">';
    }

    $output .= '<div class="multiTitle17">' . get_the_title() . "</div>";

    $output .= "</a>";
    $output .= '<div class="multiInforightText17">';
    $output .= '<div class="multiInforightbold">';
    $output .= '<div class="cardealer_smallblock">';
    //         $price = get_post_meta(get_the_ID(), 'car-price', true);
    if ($price != "" and $price != "0") {
        $price = cardealer_get_currency_symbol() . $price;
    } else {
        $price = __("Call for Price", "cardealer");
    }
    $output .= $price;
    $output .= "</div>";
    if ($hp != "") {
        $output .= '<div class="cardealer_smallblock">';
        $output .= '<span class="billcar-belt2">';

        if ($CarDealer_hp_or_kw == "hp") {
            $output .= " " . $hp . __("HP", "cardealer");
        } else {
            $output .= " " . $hp . __("KW", "cardealer");
        }

        $output .= "</div>";
    }
    if ($year != "") {
        $output .= '<div class="cardealer_smallblock">';
        $output .= '<span class="billcar-calendar">';
        $output .= " " . $year;
        $output .= "</div>";
    }
    if ($fuel != "") {
        $output .= '<div class="cardealer_smallblock">';
        $output .= '<span class="billcar-gas-station">';
        $output .= " " . $fuel;
        $output .= "</div>";
    }
    if ($transmission != "") {
        $output .= '<div class="cardealer_smallblock">';
        $output .= '<span class="billcar-gearshift">';
        $output .= " " . $transmission;
        $output .= "</div>";
    }
    if ($miles != "") {
        $output .= '<div class="cardealer_smallblock">';
        $output .= '<span class="billcar-dashboard">';
        $output .= " " . $miles;
        //  $output .= ' ' . $CarDealer_measure;

        // $output .= __($CarDealer_measure, "cardealer");



        if($miles <> '') {
            $miles_label = get_option('CarDealer_measure', 'Miles');
            if ($miles_label == 'Miles') {
                $output .= esc_attr__('Miles','cardealer').': ';
                $output .= '<br />';
                $output .=  esc_html(get_post_meta(get_the_ID(), 'car-miles', 'true'));
            } elseif ($miles_label == 'Hours') {
                $output .= esc_attr__('Hours','cardealer').': ';
                $output .= '<br />';
                $output .=  esc_html(get_post_meta(get_the_ID(), 'car-miles', 'true'));
            } elseif ($miles_label == 'Kms') {
                $output .=  esc_attr__('Kms','cardealer').': ';
                $output .= '<br />';
                $output .= esc_html(get_post_meta(get_the_ID(), 'car-miles', 'true'));
            }
        }






        $output .= "</div>";
    }
    $content_post = get_post(get_the_ID());
    $desc = sanitize_textarea_field($content_post->post_content);
    $desc = preg_replace("/\[([^\[\]]++|(?R))*+\]/", "", $desc);
    $output .= '<div class="cardealer_description">';
    $output .= substr($desc, 0, 100);
    if (substr($desc, 200) != "") {
        $output .= "...";
    }
    $output .= "</div>";
    $output .= "</div>";

    if ($CarDealer_template_single != "4") {

        $output .= '<input type="submit" class="cardealer_btn_view"';
        $output .= ' id="cardealer_btn_view-'.strval($ctd).'"';   
        $output .= ' onClick="location.href=\'' . get_permalink() . '\'"';
        $output .= ' value="' . __('View', 'cardealer') . '" />';


    }

    // <!-- Button trigger modal -->
    if ($CarDealer_template_single == "4") {
        $output .=
            ' <button type="button"  id="cardealer_btn_view-'.strval($ctd).'" class=" cardealer_btn_view btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal-' .
            $post_id .
            '">';
        $output .= __("View", "cardealer");
        $output .= "</button>";
    }

    if ($CarDealer_template_single == "4") {
        $post_id = get_the_ID();

        // die(var_dump($post_id));

        $CarDealer_modal_size = "900";

        $output .=
            '
            <!-- Modal -->
            <div class="modal fade"  id="myModal-' .
            $post_id .
            '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" style="width: 90%; max-width:' .
            $CarDealer_modal_size .
            'px;"  role="document">
                <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <!--  <h4 class="modal-title" id="myModalLabel">Single Car Page</h4> -->
                </div>
                <div class="modal-body">';

        $output .= cardealer_detail($post_id);

        //  $output .= $post_id;

        $output .= '
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>

                </div>
            </div>
            </div>
            ';
    }

    $output .= "</div>";
    $output .= "</a>";
    $output .= "</div>";
    $output .= "</div>";

endwhile;
ob_start();
the_posts_pagination([
    "mid_size" => 2,
    "prev_text" => __("Back", "cardealer"),
    "next_text" => __("Onward", "cardealer"),
]);
$output .= ob_get_contents();
ob_end_clean();
$output .= "</div>";
$output .= "</div>";
wp_reset_postdata();
wp_reset_query();
if ($qposts < 1) {
    $output .= "<br /><h4>" . __("Not Found !", "cardealer") . "</h4>";
}
$cardealer_allowed_atts = [
    "align" => [],
    "class" => [],
    "type" => [],
    "id" => [],
    "dir" => [],
    "lang" => [],
    "style" => [],
    "xml:lang" => [],
    "src" => [],
    "alt" => [],
    "href" => [],
    "rel" => [],
    "rev" => [],
    "target" => [],
    "novalidate" => [],
    "type" => [],
    "value" => [],
    "name" => [],
    "tabindex" => [],
    "action" => [],
    "method" => [],
    "for" => [],
    "width" => [],
    "height" => [],
    "data" => [],
    "title" => [],

    "checked" => [],
    "selected" => [],
    "onclick" => [], 
];

$cardealer_myallowed["form"] = $cardealer_allowed_atts;
$cardealer_myallowed["select"] = $cardealer_allowed_atts;
// select options
$cardealer_myallowed["option"] = $cardealer_allowed_atts;
$cardealer_myallowed["style"] = $cardealer_allowed_atts;
$cardealer_myallowed["label"] = $cardealer_allowed_atts;
$cardealer_myallowed["input"] = $cardealer_allowed_atts;
$cardealer_myallowed["textarea"] = $cardealer_allowed_atts;

//more...future...
$cardealer_myallowed["form"] = $cardealer_allowed_atts;
$cardealer_myallowed["label"] = $cardealer_allowed_atts;
$cardealer_myallowed["input"] = $cardealer_allowed_atts;
$cardealer_myallowed["textarea"] = $cardealer_allowed_atts;
$cardealer_myallowed["iframe"] = $cardealer_allowed_atts;
$cardealer_myallowed["script"] = $cardealer_allowed_atts;
$cardealer_myallowed["style"] = $cardealer_allowed_atts;
$cardealer_myallowed["strong"] = $cardealer_allowed_atts;
$cardealer_myallowed["small"] = $cardealer_allowed_atts;
$cardealer_myallowed["table"] = $cardealer_allowed_atts;
$cardealer_myallowed["span"] = $cardealer_allowed_atts;
$cardealer_myallowed["abbr"] = $cardealer_allowed_atts;
$cardealer_myallowed["code"] = $cardealer_allowed_atts;
$cardealer_myallowed["pre"] = $cardealer_allowed_atts;
$cardealer_myallowed["div"] = $cardealer_allowed_atts;
$cardealer_myallowed["img"] = $cardealer_allowed_atts;
$cardealer_myallowed["h1"] = $cardealer_allowed_atts;
$cardealer_myallowed["h2"] = $cardealer_allowed_atts;
$cardealer_myallowed["h3"] = $cardealer_allowed_atts;
$cardealer_myallowed["h4"] = $cardealer_allowed_atts;
$cardealer_myallowed["h5"] = $cardealer_allowed_atts;
$cardealer_myallowed["h6"] = $cardealer_allowed_atts;
$cardealer_myallowed["ol"] = $cardealer_allowed_atts;
$cardealer_myallowed["ul"] = $cardealer_allowed_atts;
$cardealer_myallowed["li"] = $cardealer_allowed_atts;
$cardealer_myallowed["em"] = $cardealer_allowed_atts;
$cardealer_myallowed["hr"] = $cardealer_allowed_atts;
$cardealer_myallowed["br"] = $cardealer_allowed_atts;
$cardealer_myallowed["tr"] = $cardealer_allowed_atts;
$cardealer_myallowed["td"] = $cardealer_allowed_atts;
$cardealer_myallowed["p"] = $cardealer_allowed_atts;
$cardealer_myallowed["a"] = $cardealer_allowed_atts;
$cardealer_myallowed["b"] = $cardealer_allowed_atts;
$cardealer_myallowed["i"] = $cardealer_allowed_atts;

//echo wp_kses($output, $cardealer_myallowed);
echo $output;
$registered_sidebars = wp_get_sidebars_widgets();
if (get_option("sidebar_search_page_result", "no") == "yes") {
    foreach ($registered_sidebars as $sidebar_name => $sidebar_widgets) {
        unregister_sidebar($sidebar_name);
    }
}
$output .= "</div>"; // cardealer_container_search
get_footer();
 ?>
