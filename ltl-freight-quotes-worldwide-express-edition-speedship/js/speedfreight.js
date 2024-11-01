jQuery(window).on("load", function () {
  var saved_mehod_value = jQuery("#show_wwe_saved_method").val();
  if (saved_mehod_value == "Cheapest") {
    jQuery(".wwe_delivery_estimate").removeAttr("style");
    jQuery(".wwe_Number_of_label_as").removeAttr("style");
    jQuery(".wwe_Number_of_options_class").removeAttr("style");

    jQuery("#wc_settings_wwe_Number_of_options")
      .closest("tr")
      .addClass("wwe_Number_of_options_class");
    jQuery("#wc_settings_wwe_Number_of_options")
      .closest("tr")
      .css("display", "none");
    jQuery("#wc_settings_wwe_label_as")
      .closest("tr")
      .addClass("wwe_Number_of_label_as");
    jQuery("#wc_settings_wwe_delivery_estimate")
      .closest("tr")
      .addClass("wwe_delivery_estimate");
    jQuery("#wc_settings_wwe_rate_method")
      .closest("tr")
      .addClass("wwe_rate_mehod");

    jQuery(".wwe_rate_mehod td span").html(
      "Displays only the cheapest returned Rate."
    );
    jQuery(".wwe_Number_of_label_as td span").html(
      'What the user sees during checkout, e.g. "Freight". Leave blank to display the carrier name.'
    );
  }
  if (saved_mehod_value == "cheapest_options") {
    jQuery(".wwe_delivery_estimate").removeAttr("style");
    jQuery(".wwe_Number_of_label_as").removeAttr("style");
    jQuery(".wwe_Number_of_options_class").removeAttr("style");

    jQuery("#wc_settings_wwe_delivery_estimate")
      .closest("tr")
      .addClass("wwe_delivery_estimate");
    jQuery("#wc_settings_wwe_label_as")
      .closest("tr")
      .addClass("wwe_Number_of_label_as");
    jQuery("#wc_settings_wwe_label_as").closest("tr").css("display", "none");
    jQuery("#wc_settings_wwe_Number_of_options")
      .closest("tr")
      .addClass("wwe_Number_of_options_class");
    jQuery("#wc_settings_wwe_rate_method")
      .closest("tr")
      .addClass("wwe_rate_mehod");

    jQuery(".wwe_rate_mehod td span").html(
      "Displays a list of a specified number of least expensive options."
    );
    jQuery(".wwe_Number_of_options_class td span").html(
      "Number of options to display in the shopping cart."
    );
  }
  if (saved_mehod_value == "average_rate") {
    jQuery(".wwe_delivery_estimate").removeAttr("style");
    jQuery(".wwe_Number_of_label_as").removeAttr("style");
    jQuery(".wwe_Number_of_options_class").removeAttr("style");

    jQuery("#wc_settings_wwe_delivery_estimate")
      .closest("tr")
      .addClass("wwe_delivery_estimate");
    jQuery("#wc_settings_wwe_delivery_estimate")
      .closest("tr")
      .css("display", "none");
    jQuery("#wc_settings_wwe_label_as")
      .closest("tr")
      .addClass("wwe_Number_of_label_as");
    jQuery("#wc_settings_wwe_Number_of_options")
      .closest("tr")
      .addClass("wwe_Number_of_options_class");
    jQuery("#wc_settings_wwe_rate_method")
      .closest("tr")
      .addClass("wwe_rate_mehod");

    jQuery(".wwe_rate_mehod td span").html(
      "Displays a single rate based on an average of a specified number of least expensive options."
    );
    jQuery(".wwe_Number_of_options_class td span").html(
      "Number of options to include in the calculation of the average."
    );
    jQuery(".wwe_Number_of_label_as td span").html(
      'What the user sees during checkout, e.g. "Freight". If left blank will default to "Freight".'
    );
  }
});

jQuery(document).ready(function () {
  // Weight threshold for LTL freight
  en_weight_threshold_limit();

  // Cuttoff Time
  jQuery("#wwe_lfq_freight_shipment_offset_days")
    .closest("tr")
    .addClass("wwe_lfq_freight_shipment_offset_days_tr");
  jQuery("#all_shipment_days_wwe_lfq")
    .closest("tr")
    .addClass("all_shipment_days_wwe_lfq_tr");
  jQuery(".wwe_lfq_shipment_day")
    .closest("tr")
    .addClass("wwe_lfq_shipment_day_tr");
  jQuery("#wwe_lfq_freight_order_cut_off_time")
    .closest("tr")
    .addClass("wwe_lfq_freight_cutt_off_time_ship_date_offset");
  var wwe_lfq_current_time =
    en_speedfreight_admin_script.wwe_lfq_freight_order_cutoff_time;
  if (wwe_lfq_current_time == "") {
    jQuery("#wwe_lfq_freight_order_cut_off_time").wickedpicker({
      now: "",
      title: "Cut Off Time",
    });
  } else {
    jQuery("#wwe_lfq_freight_order_cut_off_time").wickedpicker({
      now: wwe_lfq_current_time,
      title: "Cut Off Time",
    });
  }

  var delivery_estimate_val = jQuery(
    "input[name=wwe_lfq_delivery_estimates]:checked"
  ).val();
  if (delivery_estimate_val == "dont_show_estimates") {
    jQuery("#wwe_lfq_freight_order_cut_off_time").prop("disabled", true);
    jQuery("#wwe_lfq_freight_shipment_offset_days").prop("disabled", true);
    jQuery("#wwe_lfq_freight_shipment_offset_days").css(
      "cursor",
      "not-allowed"
    );
    jQuery("#wwe_lfq_freight_order_cut_off_time").css("cursor", "not-allowed");
  } else {
    jQuery("#wwe_lfq_freight_order_cut_off_time").prop("disabled", false);
    jQuery("#wwe_lfq_freight_shipment_offset_days").prop("disabled", false);
    // jQuery("#wwe_lfq_freight_order_cut_off_time").css("cursor", "auto");
    jQuery("#wwe_lfq_freight_order_cut_off_time").css("cursor", "");
  }

  jQuery("input[name=wwe_lfq_delivery_estimates]").change(function () {
    var delivery_estimate_val = jQuery(
      "input[name=wwe_lfq_delivery_estimates]:checked"
    ).val();
    if (delivery_estimate_val == "dont_show_estimates") {
      jQuery("#wwe_lfq_freight_order_cut_off_time").prop("disabled", true);
      jQuery("#wwe_lfq_freight_shipment_offset_days").prop("disabled", true);
      jQuery("#wwe_lfq_freight_order_cut_off_time").css(
        "cursor",
        "not-allowed"
      );
      jQuery("#wwe_lfq_freight_shipment_offset_days").css(
        "cursor",
        "not-allowed"
      );
    } else {
      jQuery("#wwe_lfq_freight_order_cut_off_time").prop("disabled", false);
      jQuery("#wwe_lfq_freight_shipment_offset_days").prop("disabled", false);
      jQuery("#wwe_lfq_freight_order_cut_off_time").css("cursor", "auto");
      jQuery("#wwe_lfq_freight_shipment_offset_days").css("cursor", "");
    }
  });

  /*
   * Uncheck Week days Select All Checkbox
   */
  jQuery(".wwe_lfq_shipment_day").on("change load", function () {
    var checkboxes = jQuery(".wwe_lfq_shipment_day:checked").length;
    var un_checkboxes = jQuery(".wwe_lfq_shipment_day").length;
    if (checkboxes === un_checkboxes) {
      jQuery(".all_shipment_days_wwe_lfq").prop("checked", true);
    } else {
      jQuery(".all_shipment_days_wwe_lfq").prop("checked", false);
    }
  });

  /*
   * Select All Shipment Week days
   */

  var all_int_checkboxes = jQuery(".all_shipment_days_wwe_lfq");
  if (
    all_int_checkboxes.length === all_int_checkboxes.filter(":checked").length
  ) {
    jQuery(".all_shipment_days_wwe_lfq").prop("checked", true);
  }

  jQuery(".all_shipment_days_wwe_lfq").change(function () {
    if (this.checked) {
      jQuery(".wwe_lfq_shipment_day").each(function () {
        this.checked = true;
      });
    } else {
      jQuery(".wwe_lfq_shipment_day").each(function () {
        this.checked = false;
      });
    }
  });

  //** End: Order Cut Off Time

  jQuery("#wwe_quests_notify_delivery_as_option")
    .closest("tr")
    .addClass("wwe_quests_notify_delivery_as_option");
  //          JS for nested fields on product details
  jQuery("._nestedMaterials").closest("p").addClass("_nestedMaterials_tr");
  jQuery("._nestedPercentage").closest("p").addClass("_nestedPercentage_tr");
  jQuery("._maxNestedItems").closest("p").addClass("_maxNestedItems_tr");
  jQuery("._nestedDimension").closest("p").addClass("_nestedDimension_tr");
  jQuery("._nestedStakingProperty")
    .closest("p")
    .addClass("_nestedStakingProperty_tr");

  if (!jQuery("._nestedMaterials").is(":checked")) {
    jQuery("._nestedPercentage_tr").hide();
    jQuery("._nestedDimension_tr").hide();
    jQuery("._maxNestedItems_tr").hide();
    jQuery("._nestedDimension_tr").hide();
    jQuery("._nestedStakingProperty_tr").hide();
  } else {
    jQuery("._nestedPercentage_tr").show();
    jQuery("._nestedDimension_tr").show();
    jQuery("._maxNestedItems_tr").show();
    jQuery("._nestedDimension_tr").show();
    jQuery("._nestedStakingProperty_tr").show();
  }

  jQuery("._nestedPercentage").attr("min", "0");
  jQuery("._maxNestedItems").attr("min", "0");
  jQuery("._nestedPercentage").attr("max", "100");
  jQuery("._maxNestedItems").attr("max", "100");
  jQuery("._nestedPercentage").attr("maxlength", "3");
  jQuery("._maxNestedItems").attr("maxlength", "3");

  if (jQuery("._nestedPercentage").val() == "") {
    jQuery("._nestedPercentage").val(0);
  }

  jQuery(
    "#wc_settings_wwe_label_as , #wwe_ltl_hold_at_terminal_fee , #wwe_freight_handling_weight , #wc_settings_wwe_hand_free_mark_up"
  ).focus(function (e) {
    jQuery("#" + this.id).css({ "border-color": "#ddd" });
  });

  var prevent_text_box = jQuery(".prevent_text_box").length;
  if (!prevent_text_box > 0) {
    jQuery("input[name*='wc_pervent_proceed_checkout_eniture']")
      .closest("tr")
      .addClass("wc_pervent_proceed_checkout_eniture");
    jQuery(".wc_pervent_proceed_checkout_eniture input[value*='allow']").after(
      'Allow user to continue to check out and display this message<br><textarea  name="allow_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' +
        en_speedfreight_admin_script.allow_proceed_checkout_eniture +
        '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>'
    );
    jQuery(
      ".wc_pervent_proceed_checkout_eniture input[value*='prevent']"
    ).after(
      'Prevent user from checking out and display this message <br><textarea name="prevent_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' +
        en_speedfreight_admin_script.prevent_proceed_checkout_eniture +
        '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>'
    );
  }

  jQuery("#wwe_freight_handling_weight")
    .closest("tr")
    .addClass("wwe_freight_handling_weight_tr");
  jQuery("#wwe_freight_maximum_handling_weight")
    .closest("tr")
    .addClass("wwe_freight_maximum_handling_weight_tr");
  jQuery("#wc_settings_wwe_residential_delivery")
    .closest("tr")
    .addClass("wc_settings_wwe_residential_delivery");
  jQuery("#avaibility_auto_residential")
    .closest("tr")
    .addClass("avaibility_auto_residential");
  jQuery("#avaibility_lift_gate")
    .closest("tr")
    .addClass("avaibility_lift_gate");
  jQuery("#wc_settings_wwe_lift_gate_delivery")
    .closest("tr")
    .addClass("wc_settings_wwe_lift_gate_delivery");
  jQuery("#wwe_quests_liftgate_delivery_as_option")
    .closest("tr")
    .addClass("wwe_quests_liftgate_delivery_as_option");
  jQuery("#residential_delivery_options_label")
    .closest("tr")
    .addClass("residential_delivery_options_label");
  jQuery("#liftgate_delivery_options_label")
    .closest("tr")
    .addClass("liftgate_delivery_options_label");
  jQuery("#wwe_ltl_hold_at_terminal_fee")
    .closest("tr")
    .addClass("wwe_ltl_hold_at_terminal_fee_tr");
  jQuery("#wc_settings_wwe_hand_free_mark_up")
    .closest("tr")
    .addClass("wc_settings_wwe_hand_free_mark_up_tr");
  jQuery("#wc_settings_wwe_allow_other_plugins")
    .closest("tr")
    .addClass("wc_settings_wwe_allow_other_plugins_tr");

  jQuery("#wwe_ltl_hold_at_terminal_checkbox_status")
    .closest("tr")
    .addClass("wwe_ltl_hold_at_terminal_checkbox_status");

  //** START: Validation for Quote_setting Hold_a_terminal fee

  jQuery("#wwe_ltl_hold_at_terminal_fee").keydown(function (e) {
    if (
      jQuery(this).val().indexOf(".") != -1 &&
      jQuery(this)
        .val()
        .substring(
          jQuery(this).val().indexOf("."),
          jQuery(this).val().indexOf(".").length
        ).length > 2
    ) {
      if (e.keyCode !== 8 && e.keyCode !== 46) {
        //exception
        e.preventDefault();
      }
    }

    // Allow: backspace, delete, tab, escape, enter and .
    if (
      jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190, 53, 189]) !== -1 ||
      // Allow: Ctrl+A, Command+A
      (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
      // Allow: home, end, left, right, down, up
      (e.keyCode >= 35 && e.keyCode <= 40)
    ) {
      // let it happen, don't do anything
      return;
    }
    // Ensure that it is a number and stop the keypress
    if (
      (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
      (e.keyCode < 96 || e.keyCode > 105)
    ) {
      e.preventDefault();
    }
  });

  jQuery("#wwe_ltl_hold_at_terminal_fee").keyup(function (e) {
    var val = jQuery("#wwe_ltl_hold_at_terminal_fee").val();

    if (val.split(".").length - 1 > 1) {
      var newval = val.substring(0, val.length - 1);
      var countDots = newval.substring(newval.indexOf(".") + 1).length;
      newval = newval.substring(0, val.length - countDots - 1);
      jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
    }

    if (val.split("%").length - 1 > 1) {
      var newval = val.substring(0, val.length - 1);
      var countPercentages = newval.substring(newval.indexOf("%") + 1).length;
      newval = newval.substring(0, val.length - countPercentages - 1);
      jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
    }
    if (val.split(">").length - 1 > 0) {
      var newval = val.substring(0, val.length - 1);
      var countGreaterThan = newval.substring(newval.indexOf(">") + 1).length;
      newval = newval.substring(newval, newval.length - countGreaterThan - 1);
      jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
    }
    if (val.split("_").length - 1 > 0) {
      var newval = val.substring(0, val.length - 1);
      var countUnderScore = newval.substring(newval.indexOf("_") + 1).length;
      newval = newval.substring(newval, newval.length - countUnderScore - 1);
      jQuery("#wwe_ltl_hold_at_terminal_fee").val(newval);
    }
  });

  /**
   * Offer lift gate delivery as an option and Always include residential delivery fee
   * @returns {undefined}
   */

  jQuery(".checkbox_fr_add").on("click", function () {
    var id = jQuery(this).attr("id");
    if (id == "wc_settings_wwe_lift_gate_delivery") {
      jQuery("#wwe_quests_liftgate_delivery_as_option").prop({
        checked: false,
      });
      jQuery("#en_woo_addons_liftgate_with_auto_residential").prop({
        checked: false,
      });
    } else if (
      id == "wwe_quests_liftgate_delivery_as_option" ||
      id == "en_woo_addons_liftgate_with_auto_residential"
    ) {
      jQuery("#wc_settings_wwe_lift_gate_delivery").prop({ checked: false });
    }
  });

  var url = getUrlVarsWWELTL()["tab"];
  if (url === "wwe_quests") {
    jQuery("#footer-left").attr("id", "wc-footer-left");
  }

  /*
   * Restrict Handling Fee with 8 digits limit
   */

  jQuery("#wc_settings_wwe_hand_free_mark_up").attr("maxlength", "8");

  //      Nested fields validation on product details
  jQuery("._nestedPercentage").keydown(function (eve) {
    stopSpecialCharacters(eve);
    var nestedPercentage = jQuery("._nestedPercentage").val();
    if (nestedPercentage.length == 2) {
      var newValue = nestedPercentage + "" + eve.key;
      if (newValue > 100) {
        return false;
      }
    }
  });

  jQuery("._maxNestedItems").keydown(function (eve) {
    stopSpecialCharacters(eve);
  });

  jQuery("._nestedMaterials").change(function () {
    if (!jQuery("._nestedMaterials").is(":checked")) {
      jQuery("._nestedPercentage_tr").hide();
      jQuery("._nestedDimension_tr").hide();
      jQuery("._maxNestedItems_tr").hide();
      jQuery("._nestedDimension_tr").hide();
      jQuery("._nestedStakingProperty_tr").hide();
    } else {
      jQuery("._nestedPercentage_tr").show();
      jQuery("._nestedDimension_tr").show();
      jQuery("._maxNestedItems_tr").show();
      jQuery("._nestedDimension_tr").show();
      jQuery("._nestedStakingProperty_tr").show();
    }
  });

  jQuery(".ltl_connection_section_class .button-primary").click(function () {
    var input = validateInput(".ltl_connection_section_class");
    if (input === false) {
      return false;
    }
  });
  // jQuery(".ltl_connection_section_class .woocommerce-save-button").before('<a href="javascript:void(0)" class="button-primary ltl_test_connection">Test Connection</a>');
  // jQuery('.ltl_test_connection').click(function (e) {
  //     var input = validateInput('.ltl_connection_section_class');
  //     if (input === false) {
  //         return false;
  //     }

  //     var postForm = {
  //         'world_wide_express_account_number': jQuery('#wc_settings_wwe_world_wide_express_account_number').val(),
  //         'speed_freight_username': jQuery('#wc_settings_wwe_speed_freight_username').val(),
  //         'speed_freight_password': jQuery('#wc_settings_wwe_speed_freight_password').val(),
  //         'speed_freight_licence_key': jQuery('#wc_settings_wwe_licence_key').val(),
  //         'authentication_key': jQuery('#wc_settings_wwe_authentication_key').val(),
  //         'action': 'ltl_validate_keys'
  //     };
  //     jQuery.ajax({
  //         type: 'POST',
  //         url: ajaxurl,
  //         data: postForm,
  //         dataType: 'json',
  //         beforeSend: function () {
  //             jQuery(".ltl_test_connection").css("color", "#fff");
  //             jQuery(".ltl_connection_section_class .button-primary").css("cursor", "pointer");
  //             jQuery('#wc_settings_wwe_world_wide_express_account_number').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedfreight_admin_script.plugins_url + '/ltl-freight-quotes-worldwide-express-edition-speedship/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
  //             jQuery('#wc_settings_wwe_speed_freight_username').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedfreight_admin_script.plugins_url + '/ltl-freight-quotes-worldwide-express-edition-speedship/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
  //             jQuery('#wc_settings_wwe_speed_freight_password').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedfreight_admin_script.plugins_url + '/ltl-freight-quotes-worldwide-express-edition-speedship/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
  //             jQuery('#wc_settings_wwe_licence_key').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedfreight_admin_script.plugins_url + '/ltl-freight-quotes-worldwide-express-edition-speedship/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
  //             jQuery('#wc_settings_wwe_authentication_key').css('background', 'rgba(255, 255, 255, 1) url("' + en_speedfreight_admin_script.plugins_url + '/ltl-freight-quotes-worldwide-express-edition-speedship/warehouse-dropship/wild/assets/images/processing.gif") no-repeat scroll 50% 50%');
  //         },
  //         success: function (data) {

  //             if (data.success) {
  //                 jQuery(".updated").hide();
  //                 jQuery('#wc_settings_wwe_world_wide_express_account_number').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_speed_freight_username').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_speed_freight_password').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_licence_key').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_authentication_key').css('background', '#fff');
  //                 jQuery(".class_success_message").remove();
  //                 jQuery(".class_error_message").remove();
  //                 jQuery(".ltl_connection_section_class .button-primary").attr("disabled", false);
  //                 jQuery('.warning-msg-ltl').before('<p class="class_success_message" ><b> Success! The test resulted in a successful connection. </b></p>');
  //             } else {
  //                 jQuery(".updated").hide();
  //                 jQuery(".class_error_message").remove();
  //                 jQuery('#wc_settings_wwe_world_wide_express_account_number').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_speed_freight_username').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_speed_freight_password').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_licence_key').css('background', '#fff');
  //                 jQuery('#wc_settings_wwe_authentication_key').css('background', '#fff');
  //                 jQuery(".class_success_message").remove();
  //                 jQuery(".ltl_connection_section_class .button-primary").attr("disabled", false);
  //                 if (data.error_desc) {
  //                     var error_message = data.error_desc;
  //                     if (error_message == 'Connection failed due to license expired. Please upgrade / renew you license from eniture.com dashboard.') {
  //                         jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! Connection failed due to invalid license key. </b></p>');
  //                     } else if (error_message == 'Invalid authentication info') {
  //                         jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! please verify your credentials and try again. </b></p>');
  //                     } else {
  //                         jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! ' + data.error_desc + ' </b></p>');
  //                     }
  //                 } else {
  //                     jQuery('.warning-msg-ltl').before('<p class="class_error_message" ><b>Error! The credentials entered did not result in a successful test. Confirm your credentials and try again. </b></p>');
  //                 }
  //             }
  //         }
  //     });
  //     e.preventDefault();
  // });

  jQuery(".wwex_connection_section_class .woocommerce-save-button").addClass(
    "savebtn"
  );
  jQuery(".wwex_connection_section_class .savebtn").before(
    '<a href="javascript:void(0)" class="button-primary sm_test_connection ltltest">Test Connection</a>'
  );
  if (jQuery(".sptest").length) {
    jQuery(".sptest").hide();
  }
  jQuery(".sm_test_connection").click(function (e) {
    var input = validateInput(".wwex_connection_section_class");
    if (input === false) {
      return false;
    }

    var postForm = {
      action: "speedship_action1",
      // world_wide_express_account_number: jQuery(
      //   "#wc_settings_account_number_wwe_small_packages_quotes"
      // ).val(),
      // speed_freight_username: jQuery(
      //   "#wc_settings_username_wwe_small_packages_quotes"
      // ).val(),
      // speed_freight_password: jQuery(
      //   "#wc_settings_password_wwe_small_packages"
      // ).val(),
      // speed_freight_licence_key: jQuery(
      //   "#wc_settings_plugin_licence_key_wwe_small_packages_quotes"
      // ).val(),
      // authentication_key: jQuery(
      //   "#wc_settings_authentication_key_wwe_small_packages_quotes"
      // ).val(),
      // speedship_url: jQuery(
      //   "#wc_settings_speedship_url_wwe_small_packages_quotes"
      // ).val(),
      // oauth_url: jQuery(
      //   "#wc_settings_oauth_url_wwe_small_packages_quotes"
      // ).val(),
      oauth_clientid: jQuery(
        "#wc_settings_oauth_clientid_wwe_small_packages_quotes"
      ).val(),
      oauth_client_secret: jQuery(
        "#wc_settings_oauth_client_secret_wwe_small_packages_quotes"
      ).val(),
      // oauth_audience: jQuery(
      //   "#wc_settings_oauth_audience_wwe_small_packages_quotes"
      // ).val(),
      // oauth_username: jQuery(
      //   "#wc_settings_oauth_username_wwe_small_packages_quotes"
      // ).val(),
      // oauth_password: jQuery(
      //   "#wc_settings_oauth_password_wwe_small_packages_quotes"
      // ).val(),
    };

    jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      data: postForm,
      dataType: "json",
      beforeSend: function () {
        jQuery(".sm_test_connection").css("color", "#fff");
        jQuery(".wwex_connection_section_class .button-primary").css(
          "cursor",
          "pointer"
        );
        // jQuery("#wc_settings_account_number_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_username_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_password_wwe_small_packages").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_plugin_licence_key_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_authentication_key_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_speedship_url_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_oauth_url_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        jQuery("#wc_settings_oauth_clientid_wwe_small_packages_quotes").css(
          "background",
          'rgba(255, 255, 255, 1) url("' +
            en_speedship_admin_script.plugins_url +
            '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        );
        jQuery(
          "#wc_settings_oauth_client_secret_wwe_small_packages_quotes"
        ).css(
          "background",
          'rgba(255, 255, 255, 1) url("' +
            en_speedship_admin_script.plugins_url +
            '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        );
        // jQuery("#wc_settings_oauth_audience_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_oauth_username_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        // jQuery("#wc_settings_oauth_password_wwe_small_packages_quotes").css(
        //   "background",
        //   'rgba(255, 255, 255, 1) url("' +
        //     en_speedship_admin_script.plugins_url +
        //     '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        // );
        jQuery("#wc_settings_googleapi_wwe_small_packages_quotes").css(
          "background",
          'rgba(255, 255, 255, 1) url("' +
            en_speedship_admin_script.plugins_url +
            '/speedship-myunishippers-rating/asset/processing.gif") no-repeat scroll 50% 50%'
        );
      },
      success: function (data) {
        if (data.success) {
          jQuery(".updated").hide();
          jQuery(".test_conn_msg").hide();
          jQuery(".test_err_msg").remove();
          // jQuery("#wc_settings_account_number_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_username_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_password_wwe_small_packages").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery(
          //   "#wc_settings_plugin_licence_key_wwe_small_packages_quotes"
          // ).css("background", "#fff");
          // jQuery(
          //   "#wc_settings_authentication_key_wwe_small_packages_quotes"
          // ).css("background", "#fff");

          // jQuery("#wc_settings_speedship_url_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_oauth_url_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          jQuery("#wc_settings_oauth_clientid_wwe_small_packages_quotes").css(
            "background",
            "#fff"
          );
          jQuery(
            "#wc_settings_oauth_client_secret_wwe_small_packages_quotes"
          ).css("background", "#fff");
          // jQuery("#wc_settings_oauth_audience_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_oauth_username_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_oauth_password_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          jQuery("#wc_settings_googleapi_wwe_small_packages_quotes").css(
            "background",
            "#fff"
          );
          jQuery(".class_success_message").remove();
          jQuery(".class_error_message").remove();
          jQuery(".wwex_connection_section_class .button-primary").attr(
            "disabled",
            false
          );
          jQuery(".savebtn").after(
            '<p class="test_conn_msg"><b> Success! The test resulted in a successful connection. </b></p>'
          );
        } else {
          jQuery(".updated").hide();
          jQuery(".test_conn_msg").hide();
          jQuery(".test_err_msg").remove();
          // jQuery("#wc_settings_account_number_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_username_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_password_wwe_small_packages").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery(
          //   "#wc_settings_plugin_licence_key_wwe_small_packages_quotes"
          // ).css("background", "#fff");
          // jQuery(
          //   "#wc_settings_authentication_key_wwe_small_packages_quotes"
          // ).css("background", "#fff");

          jQuery("#wc_settings_speedship_url_wwe_small_packages_quotes").css(
            "background",
            "#fff"
          );
          jQuery("#wc_settings_oauth_url_wwe_small_packages_quotes").css(
            "background",
            "#fff"
          );
          jQuery("#wc_settings_oauth_clientid_wwe_small_packages_quotes").css(
            "background",
            "#fff"
          );
          jQuery(
            "#wc_settings_oauth_client_secret_wwe_small_packages_quotes"
          ).css("background", "#fff");
          jQuery("#wc_settings_oauth_audience_wwe_small_packages_quotes").css(
            "background",
            "#fff"
          );
          // jQuery("#wc_settings_oauth_username_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          // jQuery("#wc_settings_oauth_password_wwe_small_packages_quotes").css(
          //   "background",
          //   "#fff"
          // );
          jQuery("#wc_settings_googleapi_wwe_small_packages_quotes").css(
            "background",
            "#fff"
          );
          jQuery(".class_success_message").remove();
          jQuery(".wwex_connection_section_class .button-primary").attr(
            "disabled",
            false
          );
          if (data.error_description) {
            jQuery(".warning-msg").before(
              '<p class="test_err_msg" ><b>Error! ' +
                data.error_description +
                " </b></p>"
            );
          } else {
            jQuery(".warning-msg").before(
              '<p class="test_err_msg" ><b>Error! The credentials entered did not result in a successful test. Confirm your credentials and try again. </b></p>'
            );
          }
        }
      },
    });
    e.preventDefault();
  });

  // jQuery(".ltl_connection_section_class .form-table").before(
  //   '<div class="warning-msg-ltl"><p> <b>Note!</b> You must have a Worldwide Express account to use this application. If you do not have one, click <a href="https://eniture.com/request-worldwide-express-account-number/" target="_blank">here</a> to access the new account request form. </p>'
  // );

  jQuery(".carrier_section_class .button-primary").on("click", function () {
    jQuery(".updated").hide();
    var num_of_checkboxes = jQuery(".carrier_check:checked").length;
    if (num_of_checkboxes < 1) {
      jQuery(".carrier_section_class:first-child").before(
        '<div id="message" class="error inline no_srvc_select"><p><strong>Please select at least one carrier service.</strong></p></div>'
      );

      jQuery("html, body").animate({
        scrollTop: jQuery(".no_srvc_select").position().top,
      });
      return false;
    }
  });

  jQuery(".quote_section_class_ltl .button-primary").on("click", function () {
    jQuery(".updated").hide();
    jQuery(".error").remove();
  });

  var all_checkboxes = jQuery(".carrier_check");
  if (all_checkboxes.length === all_checkboxes.filter(":checked").length) {
    jQuery(".include_all").prop("checked", true);
  }

  jQuery(".include_all").change(function () {
    if (this.checked) {
      jQuery(".carrier_check").each(function () {
        this.checked = true;
      });
    } else {
      jQuery(".carrier_check").each(function () {
        this.checked = false;
      });
    }
  });

  /*
   * Uncheck Select All Checkbox
   */

  jQuery(".carrier_check").on("change load", function () {
    var int_checkboxes = jQuery(".carrier_check:checked").length;
    var int_un_checkboxes = jQuery(".carrier_check").length;
    if (int_checkboxes === int_un_checkboxes) {
      jQuery(".include_all").prop("checked", true);
    } else {
      jQuery(".include_all").prop("checked", false);
    }
  });

  //        changed
  var wc_settings_wwe_rate_method = jQuery(
    "#wc_settings_wwe_rate_method"
  ).val();
  if (wc_settings_wwe_rate_method == "Cheapest") {
    jQuery("#wc_settings_wwe_Number_of_options")
      .closest("tr")
      .addClass("wwe_Number_of_options_class");
    jQuery("#wc_settings_wwe_Number_of_options")
      .closest("tr")
      .css("display", "none");
  }

  jQuery("#wc_settings_wwe_rate_method").change(function () {
    var rating_method = jQuery(this).val();
    if (rating_method == "Cheapest") {
      jQuery(".wwe_delivery_estimate").removeAttr("style");
      jQuery(".wwe_Number_of_label_as").removeAttr("style");
      jQuery(".wwe_Number_of_options_class").removeAttr("style");

      jQuery("#wc_settings_wwe_Number_of_options")
        .closest("tr")
        .addClass("wwe_Number_of_options_class");
      jQuery("#wc_settings_wwe_Number_of_options")
        .closest("tr")
        .css("display", "none");
      jQuery("#wc_settings_wwe_label_as")
        .closest("tr")
        .addClass("wwe_Number_of_label_as");
      jQuery("#wc_settings_wwe_delivery_estimate")
        .closest("tr")
        .addClass("wwe_delivery_estimate");
      jQuery("#wc_settings_wwe_rate_method")
        .closest("tr")
        .addClass("wwe_rate_mehod");

      jQuery(".wwe_rate_mehod td span").html(
        "Displays only the cheapest returned Rate."
      );
      jQuery(".wwe_Number_of_label_as td span").html(
        'What the user sees during checkout, e.g. "Freight". Leave blank to display the carrier name.'
      );
    }
    if (rating_method == "cheapest_options") {
      jQuery(".wwe_delivery_estimate").removeAttr("style");
      jQuery(".wwe_Number_of_label_as").removeAttr("style");
      jQuery(".wwe_Number_of_options_class").removeAttr("style");

      jQuery("#wc_settings_wwe_delivery_estimate")
        .closest("tr")
        .addClass("wwe_delivery_estimate");
      jQuery("#wc_settings_wwe_label_as")
        .closest("tr")
        .addClass("wwe_Number_of_label_as");
      jQuery("#wc_settings_wwe_label_as").closest("tr").css("display", "none");
      jQuery("#wc_settings_wwe_Number_of_options")
        .closest("tr")
        .addClass("wwe_Number_of_options_class");
      jQuery("#wc_settings_wwe_rate_method")
        .closest("tr")
        .addClass("wwe_rate_mehod");

      jQuery(".wwe_rate_mehod td span").html(
        "Displays a list of a specified number of least expensive options."
      );
      jQuery(".wwe_Number_of_options_class td span").html(
        "Number of options to display in the shopping cart."
      );
    }
    if (rating_method == "average_rate") {
      jQuery(".wwe_delivery_estimate").removeAttr("style");
      jQuery(".wwe_Number_of_label_as").removeAttr("style");
      jQuery(".wwe_Number_of_options_class").removeAttr("style");

      jQuery("#wc_settings_wwe_delivery_estimate")
        .closest("tr")
        .addClass("wwe_delivery_estimate");
      jQuery("#wc_settings_wwe_delivery_estimate")
        .closest("tr")
        .css("display", "none");
      jQuery("#wc_settings_wwe_label_as")
        .closest("tr")
        .addClass("wwe_Number_of_label_as");
      jQuery("#wc_settings_wwe_Number_of_options")
        .closest("tr")
        .addClass("wwe_Number_of_options_class");
      jQuery("#wc_settings_wwe_rate_method")
        .closest("tr")
        .addClass("wwe_rate_mehod");

      jQuery(".wwe_rate_mehod td span").html(
        "Displays a single rate based on an average of a specified number of least expensive options."
      );
      jQuery(".wwe_Number_of_options_class td span").html(
        "Number of options to include in the calculation of the average."
      );
      jQuery(".wwe_Number_of_label_as td span").html(
        'What the user sees during checkout, e.g. "Freight". If left blank will default to "Freight".'
      );
    }
  });

  jQuery('.ltl_connection_section_class input[type="text"]').each(function () {
    if (jQuery(this).parent().find(".err").length < 1) {
      jQuery(this).after('<span class="err"></span>');
    }
  });

  jQuery("#wc_settings_wwe_world_wide_express_account_number").attr(
    "title",
    "Account Number"
  );
  jQuery("#wc_settings_wwe_speed_freight_username").attr("title", "Username");
  jQuery("#wc_settings_wwe_speed_freight_password").attr("title", "Password");
  jQuery("#wc_settings_wwe_licence_key").attr("title", "Plugin License Key");
  jQuery("#wc_settings_wwe_authentication_key").attr(
    "title",
    "Authentication Key"
  );
  jQuery("#wc_settings_wwe_text_for_own_arrangment").attr(
    "title",
    "Text For Own Arrangement"
  );
  jQuery("#wc_settings_wwe_hand_free_mark_up").attr(
    "title",
    "Handling Fee / Markup"
  );
  jQuery("#wc_settings_wwe_label_as").attr("title", "Label As");

  jQuery(".quote_section_class_ltl .button-primary").on("click", function () {
    var Error = true;

    if (!speedfreight_label_validation()) {
      return false;
    } else if (!speedfreight_pallet_weight_validation()) {
      return false;
    } else if (!speedfreight_pallet_max_weight_validation()) {
      return false;
    } else if (!speedfreight_handling_fee_validation()) {
      return false;
    } else if (!speedfreight_hold_at_terminal_fee_validation()) {
      return false;
    } else if (!speedfreight_pallet_ship_class()) {
      return false;
    }
    /*Custom Error Message Validation*/
    var checkedValCustomMsg = jQuery(
      "input[name='wc_pervent_proceed_checkout_eniture']:checked"
    ).val();
    var allow_proceed_checkout_eniture = jQuery(
      "textarea[name=allow_proceed_checkout_eniture]"
    ).val();
    var prevent_proceed_checkout_eniture = jQuery(
      "textarea[name=prevent_proceed_checkout_eniture]"
    ).val();

    if (
      checkedValCustomMsg == "allow" &&
      allow_proceed_checkout_eniture == ""
    ) {
      jQuery("#mainform .quote_section_class_ltl").prepend(
        '<div id="message" class="error inline wwe_ltl_custom_error_message"><p><strong>Custom message field is empty.</strong></p></div>'
      );
      jQuery("html, body").animate({
        scrollTop: jQuery(".wwe_ltl_custom_error_message").position().top,
      });
      return false;
    } else if (
      checkedValCustomMsg == "prevent" &&
      prevent_proceed_checkout_eniture == ""
    ) {
      jQuery("#mainform .quote_section_class_ltl").prepend(
        '<div id="message" class="error inline wwe_ltl_custom_error_message"><p><strong>Custom message field is empty.</strong></p></div>'
      );
      jQuery("html, body").animate({
        scrollTop: jQuery(".wwe_ltl_custom_error_message").position().top,
      });
      return false;
    }

    var handling_weight = jQuery("#wwe_freight_handling_weight").val();
    var handling_weight_array = handling_weight.split(".");
    if (handling_weight != "" && handling_weight_array[1] == "") {
      jQuery("#mainform .quote_section_class_ltl").prepend(
        '<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>'
      );
      jQuery("html, body").animate({
        scrollTop: jQuery(".wwe_freight_weight_fee_error").position().top,
      });
      return false;
    }

    if (
      handling_weight != "" &&
      handling_weight_array[1] != undefined &&
      handling_weight_array[1].length > 2
    ) {
      jQuery("#mainform .quote_section_class_ltl").prepend(
        '<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>'
      );
      jQuery("html, body").animate({
        scrollTop: jQuery(".wwe_freight_weight_fee_error").position().top,
      });
      return false;
    }
    if (handling_weight == ".") {
      jQuery("#mainform .quote_section_class_ltl").prepend(
        '<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>'
      );
      jQuery("html, body").animate({
        scrollTop: jQuery(".wwe_freight_weight_fee_error").position().top,
      });
      return false;
    }
    var numberOnlyRegex = /^\d*\.?\d*$/;
    if (handling_weight != "" && !numberOnlyRegex.test(handling_weight)) {
      jQuery("#mainform .quote_section_class_ltl").prepend(
        '<div id="message" class="error inline wwe_freight_weight_fee_error"><p><strong>Weight of Handling Unit format should be 100.20.</strong></p></div>'
      );
      jQuery("html, body").animate({
        scrollTop: jQuery(".wwe_freight_weight_fee_error").position().top,
      });
      return false;
    }
    return Error;
  });

  jQuery(
    "#wwe_freight_handling_weight,#wwe_freight_maximum_handling_weight"
  ).keydown(function (e) {
    // Allow: backspace, delete, tab, escape, enter and .
    if (
      jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
      // Allow: Ctrl+A, Command+A
      (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
      // Allow: home, end, left, right, down, up
      (e.keyCode >= 35 && e.keyCode <= 40) ||
      (e.target.id == "wwe_freight_handling_weight" && e.keyCode == 109) ||
      (e.target.id == "wwe_freight_handling_weight" && e.keyCode == 189)
    ) {
      // let it happen, don't do anything
      return;
    }

    // Ensure that it is a number and stop the keypress
    if (
      (e.shiftKey || e.keyCode < 48 || e.keyCode > 57) &&
      (e.keyCode < 96 || e.keyCode > 105)
    ) {
      e.preventDefault();
    }

    if (
      jQuery(this).val().indexOf(".") != -1 &&
      jQuery(this)
        .val()
        .substring(
          jQuery(this).val().indexOf("."),
          jQuery(this).val().indexOf(".").length
        ).length > 2
    ) {
      if (e.keyCode !== 8 && e.keyCode !== 46) {
        //exception
        e.preventDefault();
      }
    }
  });

  jQuery("#wwe_freight_handling_weight").keyup(function (e) {
    var val = jQuery("#wwe_freight_handling_weight").val();

    if (val.split(".").length - 1 > 1) {
      var newval = val.substring(0, val.length - 1);
      var countDots = newval.substring(newval.indexOf(".") + 1).length;
      newval = newval.substring(0, val.length - countDots - 1);
      jQuery("#wwe_freight_handling_weight").val(newval);
    }

    if (val.split("%").length - 1 > 1) {
      var newval = val.substring(0, val.length - 1);
      var countPercentages = newval.substring(newval.indexOf("%") + 1).length;
      newval = newval.substring(0, val.length - countPercentages - 1);
      jQuery("#wwe_freight_handling_weight").val(newval);
    }
    if (val.split(">").length - 1 > 0) {
      var newval = val.substring(0, val.length - 1);
      var countGreaterThan = newval.substring(newval.indexOf(">") + 1).length;
      newval = newval.substring(newval, newval.length - countGreaterThan - 1);
      jQuery("#wwe_freight_handling_weight").val(newval);
    }
    if (val.split("_").length - 1 > 0) {
      var newval = val.substring(0, val.length - 1);
      var countUnderScore = newval.substring(newval.indexOf("_") + 1).length;
      newval = newval.substring(newval, newval.length - countUnderScore - 1);
      jQuery("#wwe_freight_handling_weight").val(newval);
    }
  });
});

// Weight threshold for LTL freight
if (typeof en_weight_threshold_limit != "function") {
  function en_weight_threshold_limit() {
    // Weight threshold for LTL freight
    jQuery("#en_weight_threshold_lfq").keypress(function (e) {
      if (
        String.fromCharCode(e.keyCode).match(/[^0-9]/g) ||
        !jQuery("#en_weight_threshold_lfq")
          .val()
          .match(/^\d{0,3}$/)
      )
        return false;
    });

    jQuery("#en_plugins_return_LTL_quotes").on("change", function () {
      if (jQuery("#en_plugins_return_LTL_quotes").prop("checked")) {
        jQuery("tr.en_weight_threshold_lfq").css("display", "contents");
      } else {
        jQuery("tr.en_weight_threshold_lfq").css("display", "none");
      }
    });

    jQuery("#en_plugins_return_LTL_quotes")
      .closest("tr")
      .addClass("en_plugins_return_LTL_quotes_tr");
    // Weight threshold for LTL freight
    var weight_threshold_class = jQuery("#en_weight_threshold_lfq").attr(
      "class"
    );
    jQuery("#en_weight_threshold_lfq")
      .closest("tr")
      .addClass("en_weight_threshold_lfq " + weight_threshold_class);

    // Weight threshold for LTL freight is empty
    if (
      jQuery("#en_weight_threshold_lfq").length &&
      !jQuery("#en_weight_threshold_lfq").val().length > 0
    ) {
      jQuery("#en_weight_threshold_lfq").val(150);
    }
  }
}

function isValidNumber(value, noNegative) {
  if (typeof noNegative === "undefined") noNegative = false;
  var isValidNumber = false;
  var validNumber = noNegative == true ? parseFloat(value) >= 0 : true;
  if ((value == parseInt(value) || value == parseFloat(value)) && validNumber) {
    if (value.indexOf(".") >= 0) {
      var n = value.split(".");
      if (n[n.length - 1].length <= 4) {
        isValidNumber = true;
      } else {
        isValidNumber = "decimal_point_err";
      }
    } else {
      isValidNumber = true;
    }
  }
  return isValidNumber;
}

// Update plan
if (typeof en_update_plan != "function") {
  function en_update_plan(input) {
    let action = jQuery(input).attr("data-action");
    jQuery.ajax({
      type: "POST",
      url: ajaxurl,
      data: { action: action },
      success: function (data_response) {
        window.location.reload(true);
      },
    });
  }
}

function speedfreight_pallet_ship_class() {
  var en_ship_class = jQuery(
    "#en_ignore_items_through_freight_classification"
  ).val();
  var en_ship_class_arr = en_ship_class.split(",");
  var en_ship_class_trim_arr = en_ship_class_arr.map(
    Function.prototype.call,
    String.prototype.trim
  );
  if (en_ship_class_trim_arr.indexOf("ltl_freight") != -1) {
    jQuery("#mainform .quote_section_class_ltl").prepend(
      '<div id="message" class="error inline wwe_freight_pallet_weight_error"><p><strong>Error! </strong>Shipping Slug of <b>ltl_freight</b> can not be ignored.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_freight_pallet_weight_error").position().top,
    });
    jQuery("#en_ignore_items_through_freight_classification").css({
      "border-color": "#e81123",
    });
    return false;
  } else {
    return true;
  }
}

function stopSpecialCharacters(e) {
  // Allow: backspace, delete, tab, escape, enter and .
  if (
    jQuery.inArray(e.keyCode, [46, 9, 27, 13, 110, 190, 189]) !== -1 ||
    // Allow: Ctrl+A, Command+A
    (e.keyCode === 65 && (e.ctrlKey === true || e.metaKey === true)) ||
    // Allow: home, end, left, right, down, up
    (e.keyCode >= 35 && e.keyCode <= 40)
  ) {
    // let it happen, don't do anything
    e.preventDefault();
    return;
  }
  // Ensure that it is a number and stop the keypress
  if (
    (e.shiftKey || e.keyCode < 48 || e.keyCode > 90) &&
    (e.keyCode < 96 || e.keyCode > 105) &&
    e.keyCode != 186 &&
    e.keyCode != 8
  ) {
    e.preventDefault();
  }
  if (
    e.keyCode == 186 ||
    e.keyCode == 190 ||
    e.keyCode == 189 ||
    (e.keyCode > 64 && e.keyCode < 91)
  ) {
    e.preventDefault();
    return;
  }
}

/**
 * Read a page's GET URL variables and return them as an associative array.
 */
function getUrlVarsWWELTL() {
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
}

function speedfreight_label_validation() {
  var label_value = jQuery("#wc_settings_wwe_label_as").val();
  var labelRegex = /^[a-zA-Z0-9\-\s]+$/;
  if (typeof label_value != "undefined" && label_value.length > 25) {
    jQuery("#mainform .quote_section_class_ltl").prepend(
      '<div id="message" class="error inline wwe_freight_label_error"><p><strong>Maximum 25 alpha characters are allowed for label field.</strong></p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_freight_label_error").position().top,
    });
    jQuery("#wc_settings_wwe_label_as").css({ "border-color": "#e81123" });
    return false;
  } else if (
    typeof label_value != "undefined" &&
    label_value != "" &&
    !labelRegex.test(label_value)
  ) {
    jQuery("#mainform .quote_section_class_ltl").prepend(
      '<div id="message" class="error inline wwe_freight_spec_label_error"><p><strong>No special characters allowed for label field.</strong></p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_freight_spec_label_error").position().top,
    });
    jQuery("#wc_settings_wwe_label_as").css({ "border-color": "#e81123" });
    return false;
  } else {
    return true;
  }
}

function speedfreight_pallet_weight_validation() {
  var weight_of_handling_unit = jQuery("#wwe_freight_handling_weight").val();
  if (
    typeof weight_of_handling_unit != "undefined" &&
    weight_of_handling_unit.length > 0
  ) {
    var validResponse = isValidDecimal(
      weight_of_handling_unit,
      "wwe_freight_handling_weight"
    );
  } else {
    validResponse = true;
  }
  if (validResponse) {
    return true;
  } else {
    jQuery("#mainform .quote_section_class_ltl").prepend(
      '<div id="message" class="error inline wwe_freight_pallet_weight_error"><p><strong>Error! </strong>Weight of Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_freight_pallet_weight_error").position().top,
    });
    jQuery("#wwe_freight_handling_weight").css({ "border-color": "#e81123" });
    return false;
  }
}

function speedfreight_pallet_max_weight_validation() {
  var max_weight_of_handling_unit = jQuery(
    "#wwe_freight_maximum_handling_weight"
  ).val();
  if (
    typeof max_weight_of_handling_unit != "undefined" &&
    max_weight_of_handling_unit.length > 0
  ) {
    var validResponse = isValidDecimal(
      max_weight_of_handling_unit,
      "wwe_freight_maximum_handling_weight"
    );
  } else {
    validResponse = true;
  }
  if (validResponse) {
    return true;
  } else {
    jQuery("#mainform .quote_section_class_ltl").prepend(
      '<div id="message" class="error inline wwe_freight_pallet_max_weight_error"><p><strong>Error! </strong>Maximum Weight per Handling Unit format should be like, e.g. 48.5 and only 3 digits are allowed after decimal point. The value can be up to 20,000.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_freight_pallet_max_weight_error").position().top,
    });
    jQuery("#wwe_freight_maximum_handling_weight").css({
      "border-color": "#e81123",
    });
    return false;
  }
}

/**
 * Check is valid number
 * @param num
 * @param selector
 * @param limit | LTL weight limit 20K
 * @returns {boolean}
 */
function isValidDecimal(num, selector, limit = 20000) {
  // validate the number:
  // positive and negative numbers allowed
  // just - sign is not allowed,
  // -0 is also not allowed.
  if (parseFloat(num) === 0) {
    // Change the value to zero
    return false;
  }

  const reg = /^(-?[0-9]{1,5}(\.\d{1,4})?|[0-9]{1,5}(\.\d{1,4})?)$/;
  let isValid = false;
  if (reg.test(num)) {
    isValid = inRange(parseFloat(num), -limit, limit);
  }
  if (isValid === true) {
    return true;
  }
  return isValid;
}

/**
 * Check is the number is in given range
 *
 * @param num
 * @param min
 * @param max
 * @returns {boolean}
 */
function inRange(num, min, max) {
  return (num - min) * (num - max) <= 0;
}

function speedfreight_hold_at_terminal_fee_validation() {
  var abf_hold_at_terminal_fee = jQuery("#wwe_ltl_hold_at_terminal_fee").val();
  var abf_hold_at_terminal_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
  if (
    (typeof abf_hold_at_terminal_fee_regex != "undefined" &&
      abf_hold_at_terminal_fee != "" &&
      !abf_hold_at_terminal_fee_regex.test(abf_hold_at_terminal_fee)) ||
    abf_hold_at_terminal_fee.split(".").length - 1 > 1
  ) {
    jQuery("#mainform .quote_section_class_ltl").prepend(
      '<div id="message" class="error inline wwe_freight_hold_at_terminal_fee_error"><p><strong>Hold at terminal fee format should be 100.20 or 10%.</strong></p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_freight_hold_at_terminal_fee_error").position()
        .top,
    });
    jQuery("#wwe_ltl_hold_at_terminal_fee").css({ "border-color": "#e81123" });
    return false;
  } else {
    return true;
  }
}

function speedfreight_handling_fee_validation() {
  var handling_fee = jQuery("#wc_settings_wwe_hand_free_mark_up").val();
  var handling_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
  if (
    (typeof handling_fee != "undefined" &&
      handling_fee != "" &&
      !handling_fee_regex.test(handling_fee)) ||
    handling_fee.split(".").length - 1 > 1
  ) {
    jQuery("#mainform .quote_section_class_ltl").prepend(
      '<div id="message" class="error inline wwe_freight_handlng_fee_error"><p><strong>Handling fee format should be 100.20 or 10%.</strong></p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_freight_handlng_fee_error").position().top,
    });
    jQuery("#wc_settings_wwe_hand_free_mark_up").css({
      "border-color": "#e81123",
    });
    return false;
  } else {
    return true;
  }
}

function validateInput(form_id) {
  var has_err = true;
  jQuery(form_id + " input[type='text']").each(function () {
    var input = jQuery(this).val();
    var response = validateString(input);

    var errorElement = jQuery(this).parent().find(".err");
    jQuery(errorElement).html("");
    var errorText = jQuery(this).attr("title");
    var optional = jQuery(this).data("optional");
    optional = optional === undefined ? 0 : 1;
    errorText = errorText != undefined ? errorText : "";
    if (optional == 0 && (response == false || response == "empty")) {
      errorText =
        response == "empty" ? errorText + " is required." : "Invalid input.";
      jQuery(errorElement).html(errorText);
    }
    has_err = response != true && optional == 0 ? false : has_err;
  });
  return has_err;
}

function validateString(string) {
  if (string == "") {
    return "empty";
  } else {
    return true;
  }
}
