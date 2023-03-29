<?php
/*
Plugin Name: Taxonomy Display by Averni
Description: A simple, clean code plugin that allows the output of a taxonomy key on any page or post. Add styles to ".averni-taxonomy-list".
Version: 1.0.0
Author: Averni Brands
Author URI: https://averni.co/
License: GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

add_shortcode( 'averni_taxonomy', 'averni_taxonomy_display' );

function averni_taxonomy_display($atts) {
    $atts = shortcode_atts( array(
        'key' => '',
        'linked' => 'yes',
        'style' => 'ul',
    ), $atts );

    if ( !empty( $atts['key'] ) ) {
        $terms = get_terms( array(
            'taxonomy' => $atts['key'],
            'hide_empty' => false,
        ) );

        if ( !empty( $terms ) && !is_wp_error( $terms ) ) {
            $output = '<div class="averni-taxonomy-list">';

            if ( $atts['style'] == 'comma' ) {
                $output .= '<span class="averni-taxonomy-comma-list">';

                if ( $atts['linked'] == 'yes' ) {
                    $linked_terms = array();
                    foreach ( $terms as $term ) {
                        $term_link = get_term_link( $term, $atts['key'] );
                        $linked_terms[] = '<a href="' . esc_url( $term_link ) . '">' . $term->name . '</a>';
                    }

                    $output .= implode( '<span class="averni-comma-seperator">, </span>', $linked_terms );
                } else {
                    $output .= implode( '<span class="averni-comma-seperator">, </span>', wp_list_pluck( $terms, 'name' ) );
                }

                $output .= '</span>';
            } else {
                $output .= '<ul class="averni-taxonomy-ul-list">';
                foreach ( $terms as $term ) {
                    if ( $atts['linked'] == 'yes' ) {
                        $term_link = get_term_link( $term, $atts['key'] );
                        $output .= '<li><a href="' . esc_url( $term_link ) . '">' . $term->name . '</a></li>';
                    } else {
                        $output .= '<li>' . $term->name . '</li>';
                    }
                }
                $output .= '</ul>';
            }

            $output .= '</div>';

            return $output;
        }
    }

    return '';
}

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'averni_taxonomy_settings_button');

function averni_taxonomy_settings_button($links)
{
    $settings_link = '<a href="tools.php?page=averni_taxonomy_settings">Settings</a>';
    array_push($links, $settings_link);
    return $links;
}

function my_plugin_menu() {
    add_submenu_page(
        'tools.php',
        'My Plugin Settings',
        'Averni Taxonomy Shortcode',
        'manage_options',
        'averni_taxonomy_settings',
        'my_plugin_settings_page'
    );
}
add_action( 'admin_menu', 'my_plugin_menu' );

function my_plugin_settings_page() {
    ?>
    <div class="wrap">
        <h1>Taxonomy Display by Averni</h1>
        <h2>Build your shortcode</h2>
        <p>Choose a taxonomy:</p>
        <?php
            $taxonomies = get_taxonomies();
            echo '<select id="taxonomyq">';
            foreach ($taxonomies as $taxonomy) {
                echo '<option value="' . $taxonomy . '">' . $taxonomy . '</option>';
            }
            echo '</select>';
        ?>
        <p>Link to archive page?</p>
        <select id="linkedyorn">
            <option value="yes">Yes</option>
            <option value="no">No</option>
        </select>
        <p>How do you want it displayed?</p>
        <select id="commaorul">
            <option value="comma">Comma Seperated</option>
            <option value="ul">List</option>
        </select>



        <p style="padding-top:20px">[averni_taxonomy key="<span id="taxonomyout"></span>" linked="<span id="linkedoutput">yes</span>" style="<span id="listoutput">comma</span>"]</p>
        <h2 style="padding-top:100px">Documentation</h2>
        <p>All your options: [averni_taxonomy key="taxonomy" linked="yes/no" style="comma/ul"]</p>
        <h3>Taxonomy key</h3>
        <p>key: "yourkeyhere"</p>
        <h3>Do you want your list to link to archive pages?</h3>
        <p>linked:  "yes" or "no" </p>
        <h3>What style do you want to display?</h3>
        <p>style: "comma" or "ul"</p>
        <script>
        const commaOrUlSelect = document.getElementById("commaorul");
        const commaOrUlOutput = document.getElementById("listoutput");

        const linkedYOrNSelect = document.getElementById("linkedyorn");
        const linkedYOrNOutput = document.getElementById("linkedoutput");

        const linkedTaxSelect = document.getElementById("taxonomyq");
        const linkedTaxOutput = document.getElementById("taxonomyout");

        commaOrUlSelect.addEventListener("change", (event) => {
            commaOrUlOutput.textContent = event.target.value;
        });

        linkedYOrNSelect.addEventListener("change", (event) => {
            linkedYOrNOutput.textContent = event.target.value;
        });

        linkedTaxSelect.addEventListener("change", (event) => {
            linkedTaxOutput.textContent = event.target.value;
        });
        </script>

    </div>
    
    <?php
}