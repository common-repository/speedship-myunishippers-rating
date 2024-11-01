/**
 * trim function remove white spaces ..
 * @param {type} box_sizing_row
 * @returns {String}
 */
var en_woo_addons_trim_string = function (box_sizing_row) {
  return jQuery.trim(box_sizing_row);
};

/**
 * show delete confirmation popup.
 * @returns none
 */
var en_woo_addons_show_del_confirmation_popup = function () {
  jQuery(".box_size_delete_popup_overly").css({
    visibility: "visible",
    opacity: "1",
  });
};

/**
 * hide delete confirmation popup.
 * @returns none
 */
var en_woo_addons_hide_del_confirmation_popup = function () {
  jQuery(".box_size_delete_popup_overly").css({
    visibility: "hidden",
    opacity: "0",
  });
};

/**
 * Show popup
 * @param string step_for_class_exist
 * @returns none
 */
var en_woo_addons_show_popup = function (step_for_class_exist) {
  if (step_for_class_exist) {
    jQuery(".en_multiple_packages").addClass("en_multiple_packages_append");
    jQuery(".en_add_multi_box_sizing_overlay").css({
      visibility: "visible",
      opacity: "1",
    });
  } else {
    jQuery(".en_add_box_sizing_overlay").css({
      visibility: "visible",
      opacity: "1",
    });
  }
};

/**
 * Hide popup
 * @returns none
 */
var en_woo_addons_hide_popup = function () {
  jQuery(".en_add_box_sizing_overlay").css({
    visibility: "hidden",
    opacity: "0",
  });

  /* Multiple Package */
  jQuery(".en_add_multi_box_sizing_overlay").css({
    visibility: "hidden",
    opacity: "0",
  });
  jQuery(".en_multiple_packages").removeClass("en_multiple_packages_append");

  //  One Rate
  jQuery(".en_add_box_sizing_one_rate_overlay").css({
    visibility: "hidden",
    opacity: "0",
  });
};

/**
 * One Rate
 */
var en_woo_addons_one_rate_show_popup = function () {
  jQuery(".en_add_box_sizing_one_rate_overlay").css({
    visibility: "visible",
    opacity: "1",
  });
};

/**
 * available_click() when we click on ancher hover available convert yes to no and no to yes
 * @returns {undefined}
 */
var availableClick = function (availVal, postId, product_id) {
  var availableLabel = "";
  if (availVal == "Yes") {
    availableLabel = "No";
    jQuery("#box_sizing_row_id_" + postId)
      .find(".en_small_action_available_td a")
      .html("No");
    jQuery("#box_sizing_row_id_" + postId)
      .find(".en_small_action_available_td a")
      .attr("onclick", 'availableClick("No","' + postId + '")');
  }
  if (availVal == "No") {
    availableLabel = "Yes";
    jQuery("#box_sizing_row_id_" + postId)
      .find(".en_small_action_available_td a")
      .html("Yes");
    jQuery("#box_sizing_row_id_" + postId)
      .find(".en_small_action_available_td a")
      .attr("onclick", 'availableClick("Yes","' + postId + '")');
  }
  var data = {
    postId: postId,
    action: "en_box_update_available",
    availableLabel: availableLabel,
  };
  var response = en_woo_addons_box_sizing_ajax_req(data);
  /* Multiple Package */
  if (product_id > 0) {
    var en_box_size_notifications_block =
      ".en_box_size_notifications_block_" + product_id;
    jQuery(
      en_box_size_notifications_block +
        " .en_box_sizing_notification_box_availiable"
    ).show("slow");
    setTimeout(function () {
      jQuery(
        en_box_size_notifications_block +
          " .en_box_sizing_notification_box_availiable"
      ).hide("slow");
    }, 5000);
  } else {
    jQuery(".en_box_sizing_notification_box_availiable").first().show("slow");
    setTimeout(function () {
      jQuery(".en_box_sizing_notification_box_availiable").first().hide("slow");
    }, 5000);
  }
};

/**
 * Delete box sizing.
 * @param int postId
 * @returns boolean
 */
var delete_box_sizing = function (postId, product_id) {
  /* Multiple Package */
  var step_for_class_exist = false;
  var en_add_box_sizing_overlay_template = ".en_add_box_sizing_overlay";
  if (product_id > 0) {
    step_for_class_exist = true;
    en_add_box_sizing_overlay_template = ".en_add_multi_box_sizing_overlay";
  }
  jQuery(
    en_add_box_sizing_overlay_template + " #en_multipackage_product_id"
  ).val(product_id);

  en_woo_addons_show_del_confirmation_popup();
  jQuery(".cancel_delete_box_sizing").on("click", function () {
    en_woo_addons_hide_del_confirmation_popup();
    /* Unbind click to avoid multiple click issue */
    jQuery(".cancel_delete_box_sizing").unbind("click");
    return false;
  });
  jQuery(".confirm_delete_box_sizing").on("click", function () {
    en_woo_addons_hide_del_confirmation_popup();
    var data = { postId: postId, action: "en_box_sizing_delete" };
    var response = en_woo_addons_box_sizing_ajax_req(data);
    /* Multiple Package */
    if (step_for_class_exist) {
      var en_box_size_notifications_block =
        "#en_multiple_package_num_" + product_id;
      jQuery(
        en_box_size_notifications_block + " tbody #box_sizing_row_id_" + postId
      ).remove();
    } else {
      jQuery("#box_sizing_row_id_" + postId).remove();
    }

    /* Hide other notifications */
    jQuery(".en_box_sizing_notification_added").hide();
    jQuery(".en_box_sizing_notification_delete ").hide();
    jQuery(".en_box_sizing_notification_update").hide();
    jQuery(".en_box_sizing_notification_box_availiable").hide();
    /* Multiple Package */
    if (step_for_class_exist) {
      var en_box_size_notifications_block =
        ".en_box_size_notifications_block_" + product_id;
      jQuery(
        en_box_size_notifications_block + " .en_box_sizing_notification_delete"
      ).show("slow");

      setTimeout(function () {
        jQuery(
          en_box_size_notifications_block +
            " .en_box_sizing_notification_delete"
        ).hide("slow");
      }, 5000);
    } else {
      jQuery(".en_box_sizing_notification_delete").first().show("slow");
      setTimeout(function () {
        jQuery(".en_box_sizing_notification_delete").first().hide("slow");
      }, 5000);
    }

    /* Unbind click to avoid multiple click issue */
    jQuery(".confirm_delete_box_sizing").unbind("click");

    /* Multiple Package */
    en_none_of_multi_box_added();

    return false;
  });
};

/**
 * Change heading to edit.
 */
function en_change_heading_to_edit() {
  jQuery(".sm_add_warehouse_popup h2").text("Edit Box Properties");
}

/**
 * Change heading to add.
 */
function en_change_heading_to_add() {
  jQuery(".sm_add_warehouse_popup h2").text("Box Properties");
}

var edit_box_sizing = function (postId, product_id) {
  // Custom Work
  var step_for_class_exist = false;
  var en_add_box_sizing_overlay_template = ".en_add_box_sizing_overlay";
  if (product_id > 0) {
    step_for_class_exist = true;
    en_add_box_sizing_overlay_template = ".en_add_multi_box_sizing_overlay";
  }
  jQuery(
    en_add_box_sizing_overlay_template + " #en_multipackage_product_id"
  ).val(product_id);

  setTimeout(function () {
    jQuery("#sm_box_sizing_nickname:input:enabled:visible").first().focus();
  }, 500);
  en_change_heading_to_edit();
  jQuery(".err").html("");
  jQuery(".add_box_popup #sm_add_box_sizing input").removeClass("red-border");
  jQuery(".girth_error").hide();
  jQuery(".outer_box_girth_error").hide();

  var clickedRowId = "#box_sizing_row_id_" + postId;

  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_nickname")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_sizing_nickname_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_length")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_sizing_length_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_width")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_sizing_width_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_height")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_sizing_height_td").text()
      )
    )
    .trigger("change");
  // Outer Box
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_outer_sizing_length")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_outer_sizing_length_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_outer_sizing_width")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_outer_sizing_width_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_outer_sizing_height")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_outer_sizing_height_td").text()
      )
    )
    .trigger("change");
  // End Outer Box
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_weight")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_sizing_max_weight_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_max_weight")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_sizing_weight_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_fee")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_usps_box_fee_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_quantity")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".en_box_sizing_quantity_td").text()
      )
    )
    .trigger("change");
  jQuery(en_add_box_sizing_overlay_template + " #fedex_box_type")
    .val(
      en_woo_addons_trim_string(
        jQuery(clickedRowId).find(".fedex_box_type").text()
      )
    )
    .trigger("change");
  var en_usps_boxes = {
    upm_default: "Merchant defined box (default)",
    upm_express_box: "USPS Priority Mail Express Box",
    upm_box: "USPS Priority Mail Box",
    upm_large_flat_rate_box: "USPS Priority Mail Large Flat Rate Box",
    upm_medium_flat_rate_box: "USPS Priority Mail Medium Flat Rate Box",
    upm_small_flat_rate_box: "USPS Priority Mail Small Flat Rate Box",
    upm_padded_flat_rate_envelope:
      "USPS Priority Mail Padded Flat Rate Envelope",
  };

  var en_fedex_boxes = {
    0: "FedEx Envelope (9.5 x 12.5)",
    1: "FedEx Reusable Envelope (9.5 x 15.5)",
    2: "FedEx Pak - Small (10.25 x 12.75 x 1.5)",
    3: "FedEx Pak - Large (12 x 15.5 x 1.5)",
    4: "FedEx Pak - Padded (11.75 x 14.75 x 1.25)",
    5: " FedEx Pak - Reusable (10 x 14.5 x 1.25)",
    6: "FedEx Small Box (10.875 x 1.5 x 12.375)",
    7: " FedEx Small Box (8.75 x 2.625 x 11.25)",
    9: "FedEx Medium Box (11.5 x 2.375 x 13.25)",
    10: "FedEx Medium Box (8.75 x 4.375 x 11.25)",
    11: "FedEx Large Box (12.375 x 3 x 17.5)",
    12: "FedEx Large Box (8.75 x 7.75 x 11.25)",
    13: "FedEx Extra Large Box (11.875 x 10.75 x 11)",
    14: "FedEx Extra Large Box (15.75 x 14.125 x 6)",
  };

  // Start
  var en_host_name = jQuery(location).attr("hostname");
  if (
    "drhoys.com" == en_host_name ||
    "localhost" == en_host_name ||
    "wpqa1.eniture-qa.com" == en_host_name
  ) {
    var en_fedex_usps_boxes = {
      upm_default: "Merchant defined box (default)",
      upm_express_box: "USPS Priority Mail Express Box",
      upm_box: "USPS Priority Mail Box",
      upm_large_flat_rate_box: "USPS Priority Mail Large Flat Rate Box",
      upm_medium_flat_rate_box: "USPS Priority Mail Medium Flat Rate Box",
      upm_small_flat_rate_box: "USPS Priority Mail Small Flat Rate Box",
      upm_padded_flat_rate_envelope:
        "USPS Priority Mail Padded Flat Rate Envelope",
      0: "FedEx Envelope (9.5 x 12.5)",
      1: "FedEx Reusable Envelope (9.5 x 15.5)",
      2: "FedEx Pak - Small (10.25 x 12.75 x 1.5)",
      3: "FedEx Pak - Large (12 x 15.5 x 1.5)",
      4: "FedEx Pak - Padded (11.75 x 14.75 x 1.25)",
      5: "FedEx Pak - Reusable (10 x 14.5 x 1.25)",
      6: "FedEx Pak - Dr Hoys (7.5 x 8.5 x 3.5)",
      7: "FedEx Small Box (10.875 x 1.5 x 12.375)",
      8: "FedEx Small Box (8.75 x 2.625 x 11.25)",
      9: "FedEx Medium Box (11.5 x 2.375 x 13.25)",
      10: "FedEx Medium Box (8.75 x 4.375 x 11.25)",
      11: "FedEx Large Box (12.375 x 3 x 17.5)",
      12: "FedEx Large Box (8.75 x 7.75 x 11.25)",
      13: "FedEx Extra Large Box (11.875 x 10.75 x 11)",
      14: "FedEx Extra Large Box (15.75 x 14.125 x 6)",
    };
  } else {
    var en_fedex_usps_boxes = {
      upm_default: "Merchant defined box (default)",
      upm_express_box: "USPS Priority Mail Express Box",
      upm_box: "USPS Priority Mail Box",
      upm_large_flat_rate_box: "USPS Priority Mail Large Flat Rate Box",
      upm_medium_flat_rate_box: "USPS Priority Mail Medium Flat Rate Box",
      upm_small_flat_rate_box: "USPS Priority Mail Small Flat Rate Box",
      upm_padded_flat_rate_envelope:
        "USPS Priority Mail Padded Flat Rate Envelope",
      0: "FedEx Envelope (9.5 x 12.5)",
      1: "FedEx Reusable Envelope (9.5 x 15.5)",
      2: "FedEx Pak - Small (10.25 x 12.75 x 1.5)",
      3: "FedEx Pak - Large (12 x 15.5 x 1.5)",
      4: "FedEx Pak - Padded (11.75 x 14.75 x 1.25)",
      5: " FedEx Pak - Reusable (10 x 14.5 x 1.25)",
      6: "FedEx Small Box (10.875 x 1.5 x 12.375)",
      7: " FedEx Small Box (8.75 x 2.625 x 11.25)",
      8: "FedEx Medium Box (11.5 x 2.375 x 13.25)",
      9: "FedEx Medium Box (8.75 x 4.375 x 11.25)",
      10: "FedEx Large Box (12.375 x 3 x 17.5)",
      11: "FedEx Large Box (8.75 x 7.75 x 11.25)",
      12: "FedEx Extra Large Box (11.875 x 10.75 x 11)",
      13: "FedEx Extra Large Box (15.75 x 14.125 x 6)",
    };
  }

  var en_plugin_url = en_get_url_vars()["tab"];
  var box_type = en_woo_addons_trim_string(
    jQuery(clickedRowId).find(".en_box_usps_box_type_td").text()
  );
  !box_type > 0 ? (box_type = "upm_default") : "";
  if (
    !step_for_class_exist &&
    box_type != "upm_default" &&
    (en_plugin_url == "usps_small" || en_plugin_url == "fedex_small")
  ) {
    var en_usps_boxes_action =
      typeof en_usps_boxes[box_type] !== "undefined" &&
      en_usps_boxes[box_type] !== null &&
      en_usps_boxes[box_type] !== "";
    var en_fedex_boxes_action =
      typeof en_fedex_boxes[box_type] !== "undefined" &&
      en_fedex_boxes[box_type] !== null &&
      en_fedex_boxes[box_type] !== "";
    unselect_box_type();
    if (
      (en_usps_boxes_action == true && en_plugin_url != "usps_small") ||
      (en_fedex_boxes_action == true && en_plugin_url != "fedex_small")
    ) {
      jQuery(en_add_box_sizing_overlay_template + " #sm_box_size_type").prepend(
        '<option class="sm_box_size_type_prepend" value="sm_box_size_type_prepend" selected="selected">' +
          en_fedex_usps_boxes[box_type] +
          "</option>"
      );
      jQuery("#sm_add_box_sizing input,#sm_add_box_sizing select").prop(
        "disabled",
        true
      );
      jQuery("#sm_add_box_sizing .sm_add_box_submit").attr(
        "id",
        "en_click_to_disabled"
      );
      jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_type").val(
        "default"
      );
      jQuery(
        en_add_box_sizing_overlay_template + " #sm_box_sizing_old_type"
      ).val("default");
    } else if (en_plugin_url == "fedex_small") {
      if (box_type != "upm_default") {
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_type").val(
          "fedex_box"
        );
        jQuery(
          en_add_box_sizing_overlay_template + " #sm_box_sizing_old_type"
        ).val("fedex_box");
      }
      en_fedex_usps_reset_settings();
      en_woo_addons_popup_disabled_fields_fedex();
      jQuery(
        en_add_box_sizing_overlay_template +
          " #sm_box_size_type option[value=" +
          box_type +
          "]"
      ).prop("selected", true);
    } else if (en_plugin_url == "usps_small") {
      en_fedex_usps_reset_settings();
      jQuery(
        en_add_box_sizing_overlay_template +
          " #sm_box_size_type option[value=" +
          box_type +
          "]"
      ).prop("selected", true);
    }
    // TODO
  } else if (!step_for_class_exist && box_type != "upm_default") {
    jQuery(en_add_box_sizing_overlay_template + " #sm_box_size_type").prepend(
      '<option class="sm_box_size_type_prepend" value="sm_box_size_type_prepend" selected="selected">' +
        en_fedex_usps_boxes[box_type] +
        "</option>"
    );
    jQuery("#sm_add_box_sizing .sm_add_box_submit").attr(
      "id",
      "en_click_to_disabled"
    );
    // enough
    var en_usps_boxes_action =
      typeof en_usps_boxes[box_type] !== "undefined" &&
      en_usps_boxes[box_type] !== null &&
      en_usps_boxes[box_type] !== "";
    var en_fedex_boxes_action =
      typeof en_fedex_boxes[box_type] !== "undefined" &&
      en_fedex_boxes[box_type] !== null &&
      en_fedex_boxes[box_type] !== "";
    if (
      (en_usps_boxes_action == true && en_plugin_url != "usps_small") ||
      (en_fedex_boxes_action == true && en_plugin_url != "fedex_small")
    ) {
      jQuery("#sm_add_box_sizing input,#sm_add_box_sizing select").prop(
        "disabled",
        true
      );
    } else {
      jQuery("#sm_add_box_sizing input,#sm_add_box_sizing select").prop(
        "disabled",
        true
      );
      jQuery(
        "#sm_add_box_sizing #en_box_sizing_available,#sm_add_box_sizing #sm_box_sizing_fee"
      ).prop("disabled", false);
    }
  } else {
    en_fedex_usps_reset_settings();
    jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_type").val(
      "default"
    );
    jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_old_type").val(
      "default"
    );
    jQuery(
      en_add_box_sizing_overlay_template +
        " #sm_box_size_type option[value=" +
        box_type +
        "]"
    ).prop("selected", true);
  }
  // End

  /* Available checkbox */
  var available = en_woo_addons_trim_string(
    jQuery(clickedRowId).find(".en_small_action_available_td a").html()
  );
  if (available == "Yes") {
    jQuery("#en_box_sizing_available").prop("checked", true);
  }
  if (available == "No") {
    jQuery("#en_box_sizing_available").prop("checked", false);
  }

  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_action").val(
    "update_action"
  );
  jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_row_id").val(
    postId
  );

  en_woo_addons_show_popup(step_for_class_exist);
};

/**
 * disabled the sbs fields.
 * @returns {jQuery}
 */
var en_woo_addons_popup_disabled_fields_fedex = function () {
  jQuery("#sm_add_box_sizing input").prop("disabled", true);
  // enough
  jQuery(
    "#sm_add_box_sizing #en_box_sizing_available,#sm_add_box_sizing #sm_box_sizing_fee,#sm_add_box_sizing #sm_box_outer_sizing_length,#sm_add_box_sizing #sm_box_outer_sizing_width,#sm_add_box_sizing #sm_box_outer_sizing_height"
  ).prop("disabled", false);
};

var en_fedex_usps_reset_settings = function () {
  jQuery(".sm_box_size_type_prepend").remove();
  jQuery("#sm_add_box_sizing input,#sm_add_box_sizing select").prop(
    "disabled",
    false
  );
  jQuery("#sm_add_box_sizing .sm_add_box_submit").removeAttr("id");
};

var hide_notification_other = function () {
  /* Hide other notifications */
  jQuery(".box_sizing_package_msg").hide();
};

//    One Rate
var add_box_packaging_click = function () {
  //  Hide notifications
  hide_notification_other();

  //  Popup page Scroll to top
  jQuery(".en_add_box_sizing_one_rate_overlay").animate(
    { scrollTop: 0 },
    "slow"
  );

  var data = { action: "en_add_box_sizing_one_rate" };
  var response = en_woo_addons_box_sizing_ajax_req(data);
  response = JSON.parse(response.responseText);
  jQuery(".en_add_box_sizing_one_rate_overlay").html(response);

  setTimeout(function () {
    jQuery("#sm_box_sizing_nickname:input:enabled:visible").first().focus();
  }, 500);
  en_woo_addons_one_rate_show_popup();
};

var unselect_box_type = function () {
  jQuery.each(jQuery("#sm_box_sizing_fee option:selected"), function () {
    jQuery(this).prop("selected", false);
  });
};

function en_none_of_multi_box_added() {
  jQuery(".en_multiple_package_list").each(function () {
    var table_exist = jQuery(this).find(".en_box_sizing_list tbody tr").length;
    if (!table_exist > 0) {
      jQuery(this).find(".en_box_sizing_list").css("display", "none");
    } else {
      jQuery(this).find(".en_box_sizing_list").css("display", "");
    }
  });
}

jQuery(document).ready(function () {
  /* One Rate */
  jQuery("#sm_box_size_type").on("change", function () {
    var en_plugin_url = en_get_url_vars()["tab"];
    var en_selected_fedex_box = jQuery(this).val();
    var en_add_box_sizing_overlay_template = ".en_add_box_sizing_overlay";
    if (
      en_plugin_url == "fedex_small" &&
      en_selected_fedex_box != "upm_default"
    ) {
      jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_type").val(
        "fedex_box"
      );
      var data = {
        en_selected_fedex_box: en_selected_fedex_box,
        action: "or_get_box_sizing_details",
      };
      var response = en_woo_addons_box_sizing_ajax_req(data);
      response = JSON.parse(response.responseText);
      if (typeof response["message"] != "undefined") {
        var message = response["message"];
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_nickname")
          .val(message.sm_box_sizing_nickname)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_length")
          .val(message.sm_box_sizing_length)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_width")
          .val(message.sm_box_sizing_width)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_height")
          .val(message.sm_box_sizing_height)
          .trigger("change");
        // Outer Box
        jQuery(
          en_add_box_sizing_overlay_template + " #sm_box_outer_sizing_length"
        )
          .val(message.sm_box_outer_sizing_length)
          .trigger("change");
        jQuery(
          en_add_box_sizing_overlay_template + " #sm_box_outer_sizing_width"
        )
          .val(message.sm_box_outer_sizing_width)
          .trigger("change");
        jQuery(
          en_add_box_sizing_overlay_template + " #sm_box_outer_sizing_height"
        )
          .val(message.sm_box_outer_sizing_height)
          .trigger("change");
        // End Outer Box
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_weight")
          .val(message.sm_box_sizing_weight)
          .trigger("change");
        jQuery(
          en_add_box_sizing_overlay_template + " #sm_box_sizing_max_weight"
        )
          .val(message.sm_box_sizing_max_weight)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_fee")
          .val(message.sm_box_sizing_fee)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_quantity")
          .val(message.sm_box_sizing_quantity)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #fedex_box_type")
          .val(message.sm_box_size_type)
          .trigger("change");
        en_woo_addons_popup_disabled_fields_fedex();
      }
    } else {
      var sm_box_sizing_action = jQuery(
        en_add_box_sizing_overlay_template + " #sm_box_sizing_action"
      ).val();
      var sm_box_sizing_row_id = jQuery(
        en_add_box_sizing_overlay_template + " #sm_box_sizing_row_id"
      ).val();
      var sm_box_sizing_old_type = jQuery(
        en_add_box_sizing_overlay_template + " #sm_box_sizing_old_type"
      ).val();
      en_plugin_url != "usps_small"
        ? en_woo_addons_reset_popup_box_sizing_form()
        : "";
      if (sm_box_sizing_action == "update_action") {
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_action")
          .val(sm_box_sizing_action)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_row_id")
          .val(sm_box_sizing_row_id)
          .trigger("change");
        jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_old_type")
          .val(sm_box_sizing_old_type)
          .trigger("change");
      }
      jQuery(en_add_box_sizing_overlay_template + " #sm_box_sizing_type").val(
        "default"
      );
      jQuery("#sm_add_box_sizing input,#sm_add_box_sizing select").prop(
        "disabled",
        false
      );
    }
  });
  /* Multiple Package */
  jQuery(
    "._en_multiple_packages_clicked,._en_own_pack_clicked,._en_rot_ver_clicked"
  ).on("click", function () {
    var checkbox_clicked_class = jQuery(this).attr("class");
    if (checkbox_clicked_class == "checkbox _en_multiple_packages_clicked") {
      jQuery("._en_own_pack_clicked").prop("checked", false);
      jQuery("._en_rot_ver_clicked").prop("checked", false);
    }

    if (checkbox_clicked_class == "checkbox _en_own_pack_clicked") {
      jQuery("._en_multiple_packages_clicked").prop("checked", false);
      jQuery("._en_rot_ver_clicked").prop("checked", false);
    }

    if (checkbox_clicked_class == "checkbox _en_rot_ver_clicked") {
      jQuery("._en_multiple_packages_clicked").prop("checked", false);
      jQuery("._en_own_pack_clicked").prop("checked", false);
    }
  });

  en_none_of_multi_box_added();

  /* Multiple Package */
  if (jQuery(".en_multiple_packages_append").length > 0) {
    jQuery(".fedex_small_connection_section")
      .addClass("en_multiple_packages_section")
      .removeClass("fedex_small_connection_section");
  }

  jQuery("#bin-del").on("click", function () {
    var data = { action: "en_woo_addons_hide_bin_message" };
    jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      data: data,
      success: function (response) {
        jQuery(".notice-dismiss-bin").remove();
      },
      error: function () {},
    });
  });

  var length_box_id = jQuery("#en_box_sizing_options_plans").length;

  if (typeof (length_box_id != "undefined") && length_box_id > 0) {
    jQuery("#en_box_sizing_options_plans")
      .parent()
      .closest("div")
      .attr("class", "quote_section_class_spq_sbs en_woo_addons_box_sizing");
    jQuery(".en_woo_addons_box_sizing").find(".warning-msg").hide();
    jQuery(".en_woo_addons_box_sizing").find(".warning-msg-ltl").hide();
  }

  /**
   * Edit show in form fields box sizing
   * @param {type} postId
   * @returns {undefined}
   */
  jQuery(".edit_box_sizing").on("click", function () {
    var postId = jQuery(this).attr("id");
    edit_box_sizing(postId);
  });

  /**
   * valid_number validation
   * @param {type} value
   * @returns {Boolean}
   */
  function valid_number(value) {
    var validNum = false;

    if (value == "" || value == parseInt(value) || value == parseFloat(value)) {
      validNum = true;
    }

    return validNum;
  }

  /**
   * Integer validation
   * @param {type} str
   * @returns {Boolean}
   */
  function is_normal_integer(str) {
    // non decimal values
    var n = Math.floor(Number(str));
    return String(n) === str && n > 0;
  }

  /**
   * validate string from box sizning popup form fields validation
   * @param {type} string
   * @returns {String|Boolean}
   */
  var en_woo_addons_validate_string_box_sizing = function (string, id) {
    /* Skip nickname for validation */
    if (id == "sm_box_sizing_nickname" && string != "") {
      return true;
    }
    var charReg = /^\s*[a-zA-Z0-9 d+\.\d{0,4},\s]+\s*$/;

    if (string == "") {
      return "empty";
    }
    if (string == undefined) {
      return "undefined";
    }
    if (charReg.test(string)) {
      return true;
    } else {
      return false;
    }
  };

  /**
   * reset popup form box sizing
   * @returns {undefined}
   */
  var en_woo_addons_reset_popup_box_sizing_form = function () {
    jQuery(".err").html("");
    jQuery(".add_box_popup #sm_add_box_sizing input").removeClass("red-border");
    jQuery("#sm_add_box_sizing input").val("");
    jQuery(".girth_error").hide();
    jQuery(".outer_box_girth_error").hide();
    jQuery("#en_box_sizing_available").prop("checked", true);
    // Disabled fields reset.
    jQuery("#sm_add_box_sizing input,#sm_add_box_sizing select").prop(
      "disabled",
      false
    );
    jQuery("#sm_add_box_sizing .sm_add_box_submit").removeAttr("id");
    jQuery(".sm_box_size_type_prepend").remove();
  };

  var en_woo_addons_error_reset_popup_box_sizing_form = function () {
    jQuery(".err").html("");
    jQuery(".add_box_popup #sm_add_box_sizing input").removeClass("red-border");
    jQuery(".girth_error").hide();
    jQuery(".outer_box_girth_error").hide();
    jQuery("#en_box_sizing_available").prop("checked", true);
  };

  /**
   * all form validation box sizing
   * @param {type} form_id
   * @returns {Boolean}
   */
  var en_woo_addons_validate_input_box_sizing = function (
    form_id,
    en_add_box_sizing_overlay_append
  ) {
    var exceed_arr = [];
    var has_err = true;
    en_woo_addons_error_reset_popup_box_sizing_form();
    jQuery(form_id + " input[type='text']").each(function () {
      var input = jQuery(this).val();
      var id = jQuery(this).attr("id");

      var response = en_woo_addons_validate_string_box_sizing(input, id);

      if (response != "empty" && response != "undefined" && response != false) {
        response =
          jQuery(this).data("type") == "number"
            ? valid_number(jQuery.trim(input))
            : jQuery(this).data("type") == "int"
            ? is_normal_integer(jQuery.trim(input))
            : response;
        var length = jQuery(this).data("length");
        if (length != "undefined" && response != false) {
          var lower_error = false;
          id == "sm_box_outer_sizing_width" ||
          id == "sm_box_outer_sizing_length" ||
          id == "sm_box_outer_sizing_height"
            ? (length = 108)
            : "";
          response = input > length ? "greater_error" : true;
          var sign = "inches";
          if (
            id == "sm_box_sizing_max_weight" ||
            id == "sm_box_sizing_weight"
          ) {
            sign = "LBS";
          } else if (id == "sm_box_sizing_quantity") {
            if (input == "0") {
              lower_error = true;
              response = "greater_error";
            }
            sign = "boxes";
          } else if (id == "sm_box_sizing_nickname") {
            if (input.length > length) {
              response = "greater_error";
            } else {
              response = true;
            }

            sign = "characters";
          }

          var greaterd_error_label =
            response == "greater_error"
              ? "Can not be greater than " + length + " " + sign + "."
              : "";

          /* Multiple Package */
          greaterd_error_label =
            lower_error && response == "greater_error"
              ? "Quantity should be greater than 0."
              : greaterd_error_label;
          var en_popup_form_attr_id = jQuery(this).attr("id");

          if (
            en_popup_form_attr_id == "sm_box_sizing_width" ||
            en_popup_form_attr_id == "sm_box_sizing_length" ||
            en_popup_form_attr_id == "sm_box_sizing_height"
          ) {
            // girth formula implemnt here
            exceed_arr.push(response);
            var in_arr_greatr_error = jQuery.inArray(
              "greater_error",
              exceed_arr
            );
            if (in_arr_greatr_error == -1) {
              response = en_woo_addons_validation_for_exceed(
                en_add_box_sizing_overlay_append
              )
                ? "exceed_error"
                : true;
            }
          } else if (
            en_popup_form_attr_id == "sm_box_outer_sizing_width" ||
            en_popup_form_attr_id == "sm_box_outer_sizing_length" ||
            en_popup_form_attr_id == "sm_box_outer_sizing_height"
          ) {
            // girth formula implemnt here
            exceed_arr.push(response);
            var in_arr_greatr_error = jQuery.inArray(
              "greater_error",
              exceed_arr
            );
            if (in_arr_greatr_error == -1) {
              // Outer Box
              response = en_woo_addons_outer_box_validation_for_exceed(
                en_add_box_sizing_overlay_append
              )
                ? "outer_exceed_error"
                : true;
            }
          }
        }
      }

      var error_element = jQuery(this).parent().find(".err");
      jQuery(error_element).html("");
      var error_text = jQuery(this).attr("title");
      var optional = jQuery(this).data("optional");
      optional = optional === undefined ? 0 : 1;
      error_text = error_text != undefined ? error_text : "";
      response != "empty" &&
      (id == "sm_box_outer_sizing_width" ||
        id == "sm_box_outer_sizing_length" ||
        id == "sm_box_outer_sizing_height")
        ? (optional = 0)
        : "";
      if (
        optional == 0 &&
        (response == false ||
          response == "empty" ||
          response == "greater_error" ||
          response == "exceed_error" ||
          response == "outer_exceed_error")
      ) {
        if (response == "empty") {
          error_text = error_text + " is required.";
        }
        if (response == false) {
          error_text = error_text + " not valid.";
        }
        if (response == "greater_error") {
          error_text = greaterd_error_label;
        }
        if (response == "exceed_error") {
          jQuery(".girth_error").show();
          jQuery(".girth_error").fadeTo(100, 0.1).fadeTo(200, 1.0);
        }
        if (response == "outer_exceed_error") {
          jQuery(".outer_box_girth_error").show();
          jQuery(".outer_box_girth_error").fadeTo(100, 0.1).fadeTo(200, 1.0);
        }
        if (response != "exceed_error" && response != "outer_exceed_error") {
          jQuery(error_element).html(error_text);
          jQuery(this).addClass("red-border");
        }
      }

      has_err = response != true && optional == 0 ? false : has_err;
    });
    if (!has_err) {
      var sm_box_sizing_type = jQuery(form_id + " #sm_box_sizing_type").val();
      if (sm_box_sizing_type == "fedex_box") {
        en_woo_addons_popup_disabled_fields_fedex();
      }
    }
    return has_err;
  };

  /**
   * girth formula length + height * 2 + width * 2 can not exceed 165
   * @returns boolean
   */
  function en_woo_addons_validation_for_exceed(
    en_add_box_sizing_overlay_append
  ) {
    var validNum = false;
    var length = Number(
      jQuery(en_add_box_sizing_overlay_append + " #sm_box_sizing_length").val()
    );
    var width = Number(
      jQuery(en_add_box_sizing_overlay_append + " #sm_box_sizing_width").val()
    );
    var height = Number(
      jQuery(en_add_box_sizing_overlay_append + " #sm_box_sizing_height").val()
    );

    if (length + width * 2 + height * 2 > 165) {
      validNum = true;
    }

    return validNum;
  }

  /**
   * girth formula length + height * 2 + width * 2 can not exceed 165
   * @returns boolean
   */
  function en_woo_addons_outer_box_validation_for_exceed(
    en_add_box_sizing_overlay_append
  ) {
    var validNum = false;
    var length = Number(
      jQuery(
        en_add_box_sizing_overlay_append + " #sm_box_outer_sizing_length"
      ).val()
    );
    var width = Number(
      jQuery(
        en_add_box_sizing_overlay_append + " #sm_box_outer_sizing_width"
      ).val()
    );
    var height = Number(
      jQuery(
        en_add_box_sizing_overlay_append + " #sm_box_outer_sizing_height"
      ).val()
    );

    if (length + width * 2 + height * 2 > 165) {
      validNum = true;
    }

    return validNum;
  }

  jQuery(".sm_add_box_submit").on("click", function () {
    var en_click_to_disabled = jQuery(this).attr("id");
    if (en_click_to_disabled == "en_click_to_disabled") {
      en_woo_addons_hide_popup();
      return false;
    }

    var en_add_box_sizing_overlay_append = ".en_add_box_sizing_overlay";
    var en_add_multi_box_sizing_overlay_temp = jQuery(this)
      .closest(".add_box_popup")
      .hasClass("en_add_multi_box_sizing_overlay");
    if (en_add_multi_box_sizing_overlay_temp) {
      en_add_box_sizing_overlay_append = ".en_add_multi_box_sizing_overlay";
    }

    jQuery("#sm_add_box_sizing input").prop("disabled", false);
    var form_data = jQuery(
      en_add_box_sizing_overlay_append + " #sm_add_box_sizing"
    ).serialize();

    var validatForm = en_woo_addons_validate_input_box_sizing(
      en_add_box_sizing_overlay_append + " #sm_add_box_sizing",
      en_add_box_sizing_overlay_append
    );

    if (validatForm) {
      var data = { form_data: form_data, action: "en_box_sizing_submit" };
      var response = en_woo_addons_box_sizing_ajax_req(data);

      response = JSON.parse(response.responseText);

      if (
        typeof response["available_response"] != "undefined" &&
        response["available_response"] == true
      ) {
        if (
          typeof response["en_post_type"] != "undefined" &&
          response["en_post_type"] == "or_box_sizing"
        ) {
          en_woo_addons_popup_disabled_fields_fedex();
          jQuery(en_add_box_sizing_overlay_append + " #sm_box_sizing_nickname")
            .next(".err")
            .html("Box already exists.");
        } else {
          jQuery(en_add_box_sizing_overlay_append + " #sm_box_sizing_nickname")
            .next(".err")
            .html("Nickname already exists.");
        }
        jQuery(
          en_add_box_sizing_overlay_append + " #sm_box_sizing_nickname"
        ).addClass("red-border");

        // Scroll to top.
        jQuery(".en_add_box_sizing_overlay").animate({ scrollTop: 0 }, "slow");

        return false;
      }
      if (
        typeof response["success"] != "undefined" &&
        response["success"] == true
      ) {
        en_woo_addons_popup_disabled_fields_fedex();
        /* Hide other notifications */
        jQuery(".en_box_sizing_notification_added").hide();
        jQuery(".en_box_sizing_notification_delete ").hide();
        jQuery(".en_box_sizing_notification_update").hide();
        jQuery(".en_box_sizing_notification_box_availiable").hide();
        var message = response["message"];
        if (
          jQuery(
            en_add_box_sizing_overlay_append + " #sm_box_sizing_action"
          ).val() == "update_action"
        ) {
          /* Edit condition */
          var rowID = jQuery(
            en_add_box_sizing_overlay_append + " #sm_box_sizing_row_id"
          ).val();

          /* Multiple Package */
          if (en_add_multi_box_sizing_overlay_temp) {
            var en_multipackage_product_id = jQuery(
              en_add_box_sizing_overlay_append + " #en_multipackage_product_id"
            ).val();

            var en_multiple_package_list =
              "#en_multiple_package_num_" + en_multipackage_product_id;
            var en_box_size_notifications_block =
              ".en_box_size_notifications_block_" + en_multipackage_product_id;
            jQuery(
              en_multiple_package_list + " tbody #box_sizing_row_id_" + rowID
            ).replaceWith(message);
            jQuery(
              en_box_size_notifications_block +
                " .en_box_sizing_notification_added"
            ).show("slow");
            setTimeout(function () {
              jQuery(
                en_box_size_notifications_block +
                  " .en_box_sizing_notification_added"
              ).hide("slow");
            }, 5000);
          } else {
            jQuery(
              ".en_box_sizing_list:first tbody #box_sizing_row_id_" + rowID
            ).replaceWith(message);
            jQuery(".en_box_sizing_notification_added").first().show("slow");
            setTimeout(function () {
              jQuery(".en_box_sizing_notification_added").first().hide("slow");
            }, 5000);
          }
        } else {
          /* Add condition */
          /* Multiple Package */
          if (en_add_multi_box_sizing_overlay_temp) {
            var en_multipackage_product_id = jQuery(
              en_add_box_sizing_overlay_append + " #en_multipackage_product_id"
            ).val();

            var en_multiple_package_list =
              "#en_multiple_package_num_" + en_multipackage_product_id;
            var en_box_size_notifications_block =
              ".en_box_size_notifications_block_" + en_multipackage_product_id;
            jQuery(en_multiple_package_list + " tbody").prepend(message);
            jQuery(
              en_box_size_notifications_block +
                " .en_box_sizing_notification_update"
            ).show("slow");
            setTimeout(function () {
              jQuery(
                en_box_size_notifications_block +
                  " .en_box_sizing_notification_update"
              ).hide("slow");
            }, 5000);
          } else {
            var obj = jQuery(".en_box_sizing_list:first tbody").prepend(
              message
            );
            jQuery(".en_box_sizing_notification_update").first().show("slow");
            setTimeout(function () {
              jQuery(".en_box_sizing_notification_update").first().hide("slow");
            }, 5000);
          }

          /* Multiple Package */
          en_none_of_multi_box_added();
        }
      }
      /* Hide add popup */
      en_woo_addons_hide_popup();
    }

    jQuery(".en_add_box_sizing_overlay").animate({ scrollTop: 0 }, "slow");

    return false;
  });

  jQuery(
    "#sm_box_sizing_length, #sm_box_sizing_width, #sm_box_sizing_height, #sm_box_outer_sizing_length, #sm_box_outer_sizing_width, #sm_box_outer_sizing_height, #sm_box_sizing_max_weight, #sm_box_sizing_weight, #sm_box_sizing_fee"
  ).on("keypress", function (event) {
    var input = jQuery(this).val();
    if (
      event.which != 8 &&
      event.which != 9 &&
      event.keyCode != 9 &&
      event.which != 46 &&
      (event.which < 48 || event.which > 57)
    ) {
      event.preventDefault();
    }
    if (
      input.indexOf(".") != -1 &&
      input.substring(input.indexOf(".")).length > 3
    ) {
      event.preventDefault();
    }
  });

  jQuery(".add_box_popup_click,.add_multi_box_popup_click").on(
    "click",
    function () {
      jQuery(
        ".en_add_box_sizing_overlay #sm_box_size_type option[value='upm_default']"
      ).prop("selected", true);
      var class_name = jQuery(this).hasClass("add_multi_box_popup_click");
      //      Hide notifications
      hide_notification_other();
      var en_add_box_sizing_overlay_template = ".en_add_box_sizing_overlay";

      setTimeout(function () {
        /* Multiple Package */
        if (class_name) {
          en_add_box_sizing_overlay_template =
            ".en_add_multi_box_sizing_overlay";
          jQuery(
            ".en_add_multi_box_sizing_overlay #sm_box_sizing_quantity:input:enabled:visible"
          )
            .first()
            .focus();
        } else {
          jQuery(
            ".en_add_box_sizing_overlay #sm_box_sizing_nickname:input:enabled:visible"
          )
            .first()
            .focus();
        }
      }, 500);
      en_change_heading_to_add();
      en_woo_addons_reset_popup_box_sizing_form();

      /* Multiple Package */
      var en_multi_pckg_product_id = jQuery(this).data(
        "en_multi_pckg_product_id"
      );
      jQuery("#en_multipackage_product_id").val(en_multi_pckg_product_id);

      en_woo_addons_show_popup(class_name);
    }
  );

  jQuery(".add_box_popup .sm_add_box_cancel").on("click", function () {
    en_woo_addons_hide_popup();
  });

  //     Add classes for further style css
  jQuery("#en_box_sizing_start_title")
    .closest("tr")
    .addClass("en_box_sizing_start_title");

  jQuery(".en_woo_addons_always_include_threed_fee")
    .closest("tr")
    .addClass("en_woo_addons_always_include_threed_fee_style");
  jQuery(".en_woo_addons_always_include_threed_fee")
    .closest("tr")
    .addClass("en_woo_addons_always_include_threed_fee_style");
  jQuery("#box_sizing_options_label_heading")
    .closest("tr")
    .addClass("box_sizing_options_label_heading_style");
  jQuery("#box_sizing_options_label_description")
    .closest("tr")
    .addClass("box_sizing_options_label_description_style");
  jQuery("#suspend_automatic_detection_of_box_sizing")
    .closest("tr")
    .addClass("suspend_automatic_detection_of_box_sizing_style");

  jQuery("#box_sizing_plan_auto_renew")
    .closest("tr")
    .addClass("threed_options_plans_style");
  jQuery("#box_sizing_current_subscription")
    .closest("tr")
    .addClass("box_sizing_current_subscription_style");
  jQuery("#box_sizing_current_usage")
    .closest("tr")
    .addClass("box_sizing_current_usage_style");

  jQuery("#en_box_sizing_plugin_name")
    .closest("tr")
    .addClass("box_sizing_plugin_name_style");
  jQuery("#en_box_sizing_subscription_status")
    .closest("tr")
    .addClass("en_box_sizing_subscription_status_style");

  /**
   * when user switch from disable to plan popup hide
   * @returns {jQuery}
   */
  var en_woo_addons_popup_notifi_disabl_to_plan_hide = function () {
    return jQuery(".sm_notification_disable_to_plan_overlay_box").css({
      display: "none",
      visibility: "hidden",
      opacity: "0",
    });
  };

  /**
   * when user switch from disable to plan popup show
   * @returns {jQuery}
   */
  var en_woo_addons_popup_notifi_disabl_to_plan_show_box = function () {
    var selected_plan = jQuery("#en_box_sizing_options_plans")
      .find("option:selected")
      .text();
    jQuery(".sm_notification_disable_to_plan_overlay_box")
      .last()
      .find("#selected_plan_popup_box")
      .text(selected_plan);
    return jQuery(".sm_notification_disable_to_plan_overlay_box").css({
      display: "block",
      visibility: "visible",
      opacity: "1",
    });
  };

  /**
   * When user from disable to plan popup actions.
   * @returns {undefined}
   */
  jQuery(".cancel_plan").on("click", function () {
    en_woo_addons_popup_notifi_disabl_to_plan_hide();
    jQuery("#en_box_sizing_options_plans").prop("selectedIndex", 0);
    return false;
  });
  /**
   * Confirm click function.
   */
  jQuery(".confirm_plan").on("click", function () {
    var params = "";
    en_woo_addons_popup_notifi_disabl_to_plan_hide();
    var monthly_pckg = jQuery("#en_box_sizing_options_plans").val();
    var plugin_name = jQuery("#en_box_sizing_plugin_name").attr("placeholder");

    var data = {
      plugin_name: plugin_name,
      selected_plan: monthly_pckg,
      action: "en_woo_addons_upgrade_plan_submit_box",
    };
    params = {
      loading_id: "en_box_sizing_options_plans",
      message_id: "plan_to_disable_message",
      disabled_id: "en_box_sizing_options_plans",
      message_ph: "Your choice of plans has been updated. ",
    };

    ajax_request(params, data, monthly_packg_response);
    return false;
  });

  /**
   *
   * @param object params
   * @param onject response
   * @returns none
   */
  var monthly_packg_response = function (params, response) {
    var parsRes = JSON.parse(response);

    if (parsRes.severity == "SUCCESS") {
      if (parsRes.subscription_packages_response == "yes") {
        jQuery("#en_box_sizing_current_subscription")
          .next(".description")
          .html(parsRes.current_subscription);
        jQuery("#en_box_sizing_current_usage")
          .next(".description")
          .html(parsRes.current_usage);
        jQuery("#en_box_sizing_subscription_status").attr("placeholder", "yes");
      }

      if (
        typeof params.message_ph != "undefined" &&
        params.message_ph.length > 0
      ) {
        jQuery(".en_woo_addons_box_sizing p:nth-child(1)")
          .first()
          .after(
            '<div class="notice notice-success box_sizing_package_msg"><p><strong>Success! </strong>' +
              params.message_ph +
              "</p></div>"
          );
      }

      suspend_automatic_detection();
    } else {
      jQuery(".en_woo_addons_box_sizing p:nth-child(1)")
        .first()
        .after(
          '<div class="notice notice-error box_sizing_package_msg" ><p><strong>Error! </strong>' +
            parsRes.Message +
            "</p></div>"
        );
      jQuery("#en_box_sizing_options_plans").prop("selectedIndex", 0);
    }

    setTimeout(function () {
      jQuery(".box_sizing_package_msg").fadeOut("fast");
    }, 3000);
    jQuery("#box_sizing_plan_auto_renew").focus();
  };

  /**
   * Monthly package select actions.
   * @param string monthly_pckg
   * @returns boolean
   */
  var en_woo_addons_monthly_packg_box = function (monthly_pckg) {
    jQuery(".box_sizing_package_msg").remove();
    var plugin_name = jQuery("#en_box_sizing_plugin_name").attr("placeholder");
    var data = {
      plugin_name: plugin_name,
      selected_plan: monthly_pckg,
      action: "en_woo_addons_upgrade_plan_submit_box",
    };
    var params = "";

    if (window.existing_plan_box == "disable") {
      en_woo_addons_popup_notifi_disabl_to_plan_show_box();
      return false;
    } else if (monthly_pckg == "disable") {
      params = {
        loading_id: "en_box_sizing_options_plans",
        disabled_id: "en_box_sizing_options_plans",
        message_ph:
          "You have disabled the Standard Box Sizes plugin. The plugin will stop working when the current plan is depleted or expires.",
      };
    } else {
      params = {
        loading_id: "en_box_sizing_options_plans",
        disabled_id: "en_box_sizing_options_plans",
        message_ph: "Your choice of plans has been updated. ",
      };
    }

    ajax_request(params, data, monthly_packg_response);
  };

  /**
   *
   * @param object params
   * @param object response
   * @returns none
   */
  var suspend_automatic_detection = function (params, response) {
    var selected_plan = jQuery("#en_box_sizing_options_plans").val();
    window.existing_plan_box = selected_plan;
    var suspend_automatic = jQuery(
      "#suspend_automatic_detection_of_box_sizing"
    ).prop("checked");
    var subscription_status = jQuery("#en_box_sizing_subscription_status").attr(
      "placeholder"
    );
    subscription_status = "yes";
    if (subscription_status == "yes") {
      jQuery("#suspend_automatic_detection_of_box_sizing").prop(
        "disabled",
        false
      );
      //   jQuery("label[for='en_box_sizing_options_plans']").text("Auto-renew");
      if (suspend_automatic) {
        jQuery(".add_box .add_box_packaging_click").addClass("disable");
      } else {
        jQuery(".add_box .add_box_packaging_click").removeClass("disable");
      }
    } else {
      // jQuery(".add_box .add_box_packaging_click").addClass("disable");
      // jQuery("label[for='en_box_sizing_options_plans']").text("Select a plan");
      // jQuery("#suspend_automatic_detection_of_box_sizing").prop({checked: false, disabled: true});
    }
  };

  /**
   * existing user plan for box sizing.
   * @param {type} params
   * @param {type} data
   * @param {type} call_back_function
   * @returns {undefined}
   */
  suspend_automatic_detection();

  function ajax_request(params, data, call_back_function) {
    jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      data: data,
      beforeSend: function () {
        typeof params.loading_id != "undefined" && params.loading_id.length > 0
          ? jQuery("#" + params.loading_id).css(
              "background",
              "rgba(255, 255, 255, 1) url(" +
                sbs.en_sbs_plugin_path +
                "admin/assets/images/processing.gif) no-repeat scroll 50% 50%"
            )
          : "";
        typeof params.disabled_id != "undefined" &&
        params.disabled_id.length > 0
          ? jQuery("#" + params.disabled_id).prop({ disabled: true })
          : "";
        typeof params.loading_msg != "undefined" &&
        params.loading_msg.length > 0 &&
        typeof params.disabled_id != "undefined" &&
        params.disabled_id.length > 0
          ? jQuery("#" + params.disabled_id).after(params.loading_msg)
          : "";
      },
      success: function (response) {
        jQuery(".notice-dismiss-bin-php").remove();
        typeof params.loading_id != "undefined" && params.loading_id.length > 0
          ? jQuery("#" + params.loading_id).css("background", "#fff")
          : "";
        typeof params.loading_id != "undefined" &&
        params.loading_id.length > 0 &&
        params.loading_id == "en_box_sizing_options_plans"
          ? jQuery("#" + params.loading_id).attr("style", "")
          : "";
        typeof params.loading_id != "undefined" && params.loading_id.length > 0
          ? jQuery("#" + params.loading_id).focus()
          : "";
        typeof params.disabled_id != "undefined" &&
        params.disabled_id.length > 0
          ? jQuery("#" + params.disabled_id).prop({ disabled: false })
          : "";
        typeof params.loading_msg != "undefined" &&
        params.loading_msg.length > 0 &&
        typeof params.disabled_id != "undefined" &&
        params.disabled_id.length > 0
          ? jQuery("#" + params.disabled_id)
              .next(".suspend-loading")
              .remove()
          : "";
        return call_back_function(params, response);
      },
      error: function () {},
    });
  }

  /**
   * plan change function for box sizing.
   */
  jQuery("#en_box_sizing_options_plans").on("change", function () {
    en_woo_addons_monthly_packg_box(jQuery(this).val());
    return false;
  });

  /**
   * suspend template.
   * @returns none
   */
  var suspend_automatic_detection_params = function () {
    return {
      loading_msg: " <span class='suspend-loading'>Loading ...</span>",
      disabled_id: "suspend_automatic_detection_of_box_sizing",
    };
  };

  /**
   * suspend enabled.
   * @returns none
   */
  var suspend_automatic_detection_anable = function () {
    return {
      suspend_automatic_detection_of_box_sizing: "yes",
      action: "suspend_automatic_detection_box",
    };
  };

  /**
   * suspend disabled.
   * @returns none
   */
  var suspend_automatic_detection_disabled = function () {
    var always_include_threed = jQuery(
      ".en_woo_addons_always_include_threed_fee"
    ).attr("id");
    return {
      suspend_automatic_detection_of_box_sizing: "no",
      action: "suspend_automatic_detection_box",
    };
  };

  /**
   * When click on suspend checkbox.
   */
  jQuery("#suspend_automatic_detection_of_box_sizing").on("click", function () {
    var data = "";
    var params = "";
    if (this.checked) {
      data = suspend_automatic_detection_anable();
      params = suspend_automatic_detection_params();
    } else {
      data = suspend_automatic_detection_disabled();
      params = suspend_automatic_detection_params();
    }
    ajax_request(params, data, suspend_automatic_detection);
  });
});

/**
 * box sizing ajax request....
 * @param array data
 * @returns {response|jqXHR}
 */
var en_woo_addons_box_sizing_ajax_req = function (data) {
  var ajaxResponse = jQuery.ajax({
    type: "POST",
    url: ajaxurl,
    data: data,
    async: false,
    beforeSend: function () {},
    success: function (response) {
      ajaxResponse = response;
    },
    error: function () {},
  });

  return ajaxResponse;
};

//    One Rate
var or_add_box_cancel = function () {
  /* Hide add popup */
  en_woo_addons_hide_popup();
};

/**
 * Read a page's GET URL variables and return them as an associative array.
 */
var en_get_url_vars = function () {
  var vars = [],
    hash;
  var hashes = window.location.href
    .slice(window.location.href.indexOf("?") + 1)
    .split("&");
  for (var i = 0; i < hashes.length; i++) {
    hash = hashes[i].split("=");
    vars.push(hash[0]);
    vars[hash[0]] = hash[1];
  }
  return vars;
};

//    One Rate
var or_add_box_submit = function () {
  var form_data = jQuery("#or_add_box_sizing").serialize();
  var data = { form_data: form_data, action: "or_box_sizing_submit" };
  var response = en_woo_addons_box_sizing_ajax_req(data);

  response = JSON.parse(response.responseText);

  if (typeof response["message"] != "undefined") {
    var message = response["message"];
    var obj = jQuery(".en_box_sizing_list:first tbody").prepend(message);

    var message_len = jQuery(message + "tr").length;

    if (message_len == 1) {
      jQuery(".en_boxes_sizing_notification_data_saved").first().show("slow");
      setTimeout(function () {
        jQuery(".en_boxes_sizing_notification_data_saved").first().hide("slow");
      }, 5000);
    } else if (message_len > 1) {
      jQuery(".en_boxes_sizing_notification_data_saved").first().show("slow");
      setTimeout(function () {
        jQuery(".en_boxes_sizing_notification_data_saved").first().hide("slow");
      }, 5000);
    }
  }

  if (typeof response["delete"] != "undefined") {
    var remove_arr = response["delete"];

    var delete_count = 0;
    jQuery.each(remove_arr, function (ind, postId) {
      jQuery("#box_sizing_row_id_" + postId).remove();
      delete_count++;
    });

    if (delete_count == 1) {
      jQuery(".en_boxes_sizing_notification_data_saved").first().show("slow");
      setTimeout(function () {
        jQuery(".en_boxes_sizing_notification_data_saved").first().hide("slow");
      }, 5000);
    } else if (delete_count > 1) {
      jQuery(".en_boxes_sizing_notification_data_saved").first().show("slow");
      setTimeout(function () {
        jQuery(".en_boxes_sizing_notification_data_saved").first().hide("slow");
      }, 5000);
    }
  }

  /* Hide add popup */
  en_woo_addons_hide_popup();
};
