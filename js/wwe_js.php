<?php
/**
 * WWE Small Main JS
 *
 * @package     WWE Small Quotes
 * @author      Eniture-Technology
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_footer', 'smallpkg_ajax_carrrier_button');

/**
 * JS
 */
function smallpkg_ajax_carrrier_button()
{
    ?>
    <script>



        jQuery(document).ready(function () {



        });




    </script>
    <?php
}

add_action('admin_footer', 'smallpkg_no_service_select');

/**
 * Quote Services Select
 */
function smallpkg_no_service_select()
{
    ?>
    <script>
        jQuery(document).ready(function () {


        });


    </script>
    <?php
}

add_action('admin_footer', 'smpkg_check_all');

/**
 * Select All Service
 */
function smpkg_check_all()
{
    ?>
    <script>

    </script>
    <?php
}

add_action('admin_footer', 'smallpkg_admin_quote_setting_input');

/**
 * Admin Input
 */
function smallpkg_admin_quote_setting_input()
{
    ?>

    <script>
        jQuery(document).ready(function () {


        });

        jQuery(document).ready(function () {


        });


    </script>
    <?php
}
