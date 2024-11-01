<?php

$addon = isset($_GET['addon']) ? sanitize_text_field($_GET['addon']) : '';
$bin_status = isset($_GET['bin_status']) ? sanitize_text_field($_GET['bin_status']) : '';
$bin_message = isset($_GET['bin_message']) ? sanitize_text_field($_GET['bin_message']) : '';


/* For Standard Box Sizes */
if ($addon == 'binPackaging') {
    if ($bin_message != '') {
        update_option('en_3dbin_message', $bin_message);
        update_option('en_3dbin_message_status', $bin_status);
    }
}