jQuery(document).ready(function () {
  // estimated delivery options
  jQuery(".wwe_small_dont_show_estimate_option")
    .closest("tr")
    .addClass("wwe_small_dont_show_estimate_option_tr");
  jQuery("#service_small_estimates_title")
    .closest("tr")
    .addClass("service_small_estimates_title_tr");
  jQuery("input[name=wwe_small_delivery_estimates]")
    .closest("tr")
    .addClass("wwe_small_delivery_estimates_tr");
  jQuery("#service_wwe_small_estimates_title")
    .closest("tr")
    .addClass("service_wwe_small_estimates_title_tr");
  jQuery(".wwe_small_shipment_day")
    .closest("tr")
    .addClass("wwe_small_shipment_day_tr");
  jQuery("#wwe_small_cutOffTime_shipDateOffset")
    .closest("tr")
    .addClass("wwe_small_cutOffTime_shipDateOffset_required_label");
  jQuery("#wwe_small_orderCutoffTime")
    .closest("tr")
    .addClass("wwe_small_cutOffTime_shipDateOffset");
  jQuery("#wwe_small_shipmentOffsetDays")
    .closest("tr")
    .addClass("wwe_small_cutOffTime_shipDateOffset");
  jQuery("#wwe_small_timeformate")
    .closest("tr")
    .addClass("wwe_small_timeformate");

  jQuery("#wwe_small_shipmentOffsetDays").attr("min", 1);
  var wweSmallCurrentTime =
    en_speedship_admin_script.wwe_small_order_cutoff_time;
  if (wweSmallCurrentTime != "") {
    jQuery("#wwe_small_orderCutoffTime").wickedpicker({
      now: wweSmallCurrentTime,
      title: "Cut Off Time",
    });
  } else {
    jQuery("#wwe_small_orderCutoffTime").wickedpicker({
      now: "",
      title: "Cut Off Time",
    });
  }

  // estimated delivery options js
  jQuery("input[name=wwe_small_delivery_estimates]").change(function () {
    var delivery_estimate_val = jQuery(
      "input[name=wwe_small_delivery_estimates]:checked"
    ).val();
    if (delivery_estimate_val == "dont_show_estimates") {
      jQuery("#wwe_small_orderCutoffTime").prop("disabled", true);
      jQuery("#wwe_small_shipmentOffsetDays").prop("disabled", true);
    } else {
      jQuery("#wwe_small_orderCutoffTime").prop("disabled", false);
      jQuery("#wwe_small_shipmentOffsetDays").prop("disabled", false);
    }
  });

  //** Start: Validat Shipment Offset Days
  jQuery("#wwe_small_shipmentOffsetDays").keydown(function (e) {
    if (e.keyCode == 8) return;

    var val = jQuery("#wwe_small_shipmentOffsetDays").val();
    if (val.length > 1 || e.keyCode == 190) {
      e.preventDefault();
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
  // Allow: only positive numbers
  jQuery("#wwe_small_shipmentOffsetDays").keyup(function (e) {
    if (e.keyCode == 189) {
      e.preventDefault();
      jQuery("#wwe_small_shipmentOffsetDays").val("");
    }
  });

  // End estimated delivery options

  jQuery(".quote_section_class_smpkg .wwe_small_markup").on(
    "click",
    function (event) {
      jQuery(".quote_section_class_smpkg .wwe_small_markup").css("border", "");
    }
  );

  jQuery(
    "#wc_settings_hand_free_mark_up_wwe_small_packages, #air_hazardous_material_fee, #ground_hazardous_material_fee,#ground_transit_wwe_small_packages "
  ).focus(function (e) {
    jQuery("#" + this.id).css({ "border-color": "#ddd" });
  });
  jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages").attr(
    "maxlength",
    7
  );

  var prevent_text_box = jQuery(".prevent_text_box").length;
  if (!prevent_text_box > 0) {
    jQuery("input[name*='wc_pervent_proceed_checkout_eniture']")
      .closest("tr")
      .addClass("wc_pervent_proceed_checkout_eniture");
    jQuery(".wc_pervent_proceed_checkout_eniture input[value*='allow']").after(
      '<span class="wwe_small_custom_message">Allow user to continue to check out and display this message<br><textarea  name="allow_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' +
        en_speedship_admin_script.allow_proceed_checkout_eniture +
        '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>'
    );
    jQuery(
      ".wc_pervent_proceed_checkout_eniture input[value*='prevent']"
    ).after(
      '<span class="wwe_small_custom_message">Prevent user from checking out and display this message<br><textarea name="prevent_proceed_checkout_eniture" class="prevent_text_box" title="Message" maxlength="250">' +
        en_speedship_admin_script.prevent_proceed_checkout_eniture +
        '</textarea></br><span class="description"> Enter a maximum of 250 characters.</span>'
    );
  }
  jQuery(".wwe_small_markup").closest("tr").addClass("wwe_small_markup_tr");
  jQuery(".wwe_small_markup_label")
    .closest("tr")
    .addClass("wwe_small_markup_label_tr");
  jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages")
    .closest("tr")
    .addClass("wc_settings_hand_free_mark_up_wwe_small_packages_tr");
  jQuery("#avaibility_box_sizing")
    .closest("tr")
    .addClass("avaibility_box_sizing_tr");
  jQuery("#wc_settings_wwe_small_allow_other_plugins")
    .closest("tr")
    .addClass("wc_settings_wwe_small_allow_other_plugins_tr");
  jQuery(
    "#ground_transit_wwe_small_packages , #ground_hazardous_material_fee , #air_hazardous_material_fee"
  ).keydown(function (e) {
    // Allow one decimal in integers values
    if (e.keyCode === 190 && this.value.split(".").length === 2) {
      e.preventDefault();
    }

    // Allow: backspace, delete, tab, escape, enter and .
    if (
      jQuery.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
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

  //** Start: Validation for domestic service level markup

  jQuery(".wwe_small_markup").keydown(function (e) {
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
  jQuery(".wwe_small_markup").keyup(function (e) {
    var selected_domestic_id = jQuery(this).attr("id");
    jQuery("#" + selected_domestic_id).css({ border: "1px solid #ddd" });

    var val = jQuery("#" + selected_domestic_id).val();
    if (val.split(".").length - 1 > 1) {
      var newval = val.substring(0, val.length - 1);
      var countDots = newval.substring(newval.indexOf(".") + 1).length;
      newval = newval.substring(0, val.length - countDots - 1);
      jQuery("#" + selected_domestic_id).val(newval);
    }

    if (val.split("%").length - 1 > 1) {
      var newval = val.substring(0, val.length - 1);
      var countPercentages = newval.substring(newval.indexOf("%") + 1).length;
      newval = newval.substring(0, val.length - countPercentages - 1);
      jQuery("#" + selected_domestic_id).val(newval);
    }
    if (val.split(">").length - 1 > 0) {
      var newval = val.substring(0, val.length - 1);
      var countGreaterThan = newval.substring(newval.indexOf(">") + 1).length;
      newval = newval.substring(newval, newval.length - countGreaterThan - 1);
      jQuery("#" + selected_domestic_id).val(newval);
    }
    if (val.split("_").length - 1 > 0) {
      var newval = val.substring(0, val.length - 1);
      var countUnderScore = newval.substring(newval.indexOf("_") + 1).length;
      newval = newval.substring(newval, newval.length - countUnderScore - 1);
      jQuery("#" + selected_domestic_id).val(newval);
    }
    if (val.split("-").length - 1 > 1) {
      var newval = val.substring(0, val.length - 1);
      var countPercentages = newval.substring(newval.indexOf("-") + 1).length;
      newval = newval.substring(0, val.length - countPercentages - 1);
      jQuery("#" + selected_domestic_id).val(newval);
    }
  });

  //** END: Validation for domestic service level markup

  jQuery(".wwex_connection_section_class .button-primary").click(function () {
    var input = validateInput(".wwex_connection_section_class");
    if (input === false) {
      return false;
    }
  });
  jQuery(".wwex_connection_section_class .woocommerce-save-button").addClass(
    "savebtn"
  );
  jQuery(".wwex_connection_section_class .savebtn").before(
    '<a href="javascript:void(0)" class="button-primary sm_test_connection sptest">Test Connection</a>'
  );
  if (jQuery(".ltltest").length) {
    jQuery(".ltltest").hide();
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
            '<p class="test_conn_msg"><b> Success!!! The test resulted in a successful connection. </b></p>'
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
          jQuery(".wwex_connection_section_class .button-primary").attr(
            "disabled",
            false
          );
          if (data.error_description) {
            jQuery(".savebtn").after(
              '<p class="test_err_msg" ><b>Error! ' +
                data.error_description +
                " The credentials entered did not result in a successful test. Confirm your credentials and try again.</b></p>"
            );
          } else {
            jQuery(".savebtn").after(
              '<p class="test_err_msg" ><b>Error! The credentials entered did not result in a successful test. Confirm your credentials and try again. </b></p>'
            );
          }
        }
      },
    });
    e.preventDefault();
  });

  jQuery("#wc_settings_quest_as_residential_delivery_wwe_small_packages")
    .closest("tr")
    .addClass("wc_settings_quest_as_residential_delivery_wwe_small_packages");
  jQuery("#avaibility_auto_residential")
    .closest("tr")
    .addClass("avaibility_auto_residential");

  var url = getUrlVarsWWESmall()["tab"];
  if (url === "wwe_small_packages_quotes") {
    jQuery("#footer-left").attr("id", "wc-footer-left");
  }

  // jQuery(".wwex_connection_section_class .form-table").before(
  //   '<div class="warning-msg"><p> <b>Note!</b> You must have a Worldwide Express account to use this application. If you do not have one, click <a href="https://eniture.com/request-worldwide-express-account-number/" target="_blank">here</a> to access the new account request form. </p>'
  // );
  jQuery(".warning-msg").first().show();

  // Ignore shipping calculator
  function speedship_ignore_items() {
    var en_ship_class = jQuery(
      "#en_ignore_items_through_freight_classification"
    ).val();
    var en_ship_class_arr = en_ship_class.split(",");
    var en_ship_class_trim_arr = en_ship_class_arr.map(
      Function.prototype.call,
      String.prototype.trim
    );
    if (en_ship_class_trim_arr.indexOf("ltl_freight") != -1) {
      jQuery("#mainform .quote_section_class_smpkg").prepend(
        '<div id="message" class="error inline wwe_small_pallet_weight_error"><p><strong>Error! </strong>Shipping Slug of <b>ltl_freight</b> can not be ignored.</p></div>'
      );
      jQuery("html, body").animate({
        scrollTop: jQuery(".wwe_small_pallet_weight_error").position().top,
      });
      jQuery("#en_ignore_items_through_freight_classification").css({
        "border-color": "#e81123",
      });
      return false;
    } else {
      return true;
    }
  }

  jQuery(".quote_section_class_smpkg .button-primary").on(
    "click",
    function (e) {
      jQuery(".error").remove();

      if (!speedship_ignore_items()) {
        return false;
      } else if (!speedship_handling_fee_validation()) {
        return false;
      } else if (!speedship_air_hazardous_material_fee_validation()) {
        return false;
      } else if (!speedship_ground_hazardous_material_fee_validation()) {
        return false;
      } else if (!speedship_ground_transit_validation()) {
        return false;
      }

      var wwe_small_shipmentOffsetDays = jQuery(
        "#wwe_small_shipmentOffsetDays"
      ).val();
      if (
        wwe_small_shipmentOffsetDays != "" &&
        wwe_small_shipmentOffsetDays < 1
      ) {
        jQuery("#mainform .quote_section_class_smpkg").prepend(
          '<div id="message" class="error inline wwe_small_orderCutoffTime_error"><p><strong>Error! </strong>Days should not be less than 1.</p></div>'
        );
        jQuery("html, body").animate({
          scrollTop: jQuery(".wwe_small_orderCutoffTime_error").position().top,
        });
        jQuery("#wwe_small_shipmentOffsetDays").css({
          "border-color": "#e81123",
        });
        return false;
      }
      if (
        wwe_small_shipmentOffsetDays != "" &&
        wwe_small_shipmentOffsetDays > 8
      ) {
        jQuery("#mainform .quote_section_class_smpkg").prepend(
          '<div id="message" class="error inline wwe_small_orderCutoffTime_error"><p><strong>Error! </strong>Days should be less than or equal to 8.</p></div>'
        );
        jQuery("html, body").animate({
          scrollTop: jQuery(".wwe_small_orderCutoffTime_error").position().top,
        });
        jQuery("#wwe_small_shipmentOffsetDays").css({
          "border-color": "#e81123",
        });
        return false;
      }

      var numberOnlyRegex = /^[0-9]+$/;

      if (
        wwe_small_shipmentOffsetDays != "" &&
        !numberOnlyRegex.test(wwe_small_shipmentOffsetDays)
      ) {
        jQuery("#mainform .quote_section_class_smpkg").prepend(
          '<div id="message" class="error inline wwe_small_orderCutoffTime_error"><p><strong>Error! </strong>Entered Days are not valid.</p></div>'
        );
        jQuery("html, body").animate({
          scrollTop: jQuery(".wwe_small_orderCutoffTime_error").position().top,
        });
        jQuery("#wwe_small_shipmentOffsetDays").css({
          "border-color": "#e81123",
        });
        return false;
      }

      let wwe_small_markup = jQuery(".wwe_small_markup");
      jQuery(wwe_small_markup).each(function () {
        if (jQuery("#" + this.id).val() != "" && !markup_service(this.id)) {
          e.preventDefault();
          return false;
        }
      });

      // var handling_fee = jQuery('#wc_settings_hand_free_mark_up_wwe_small_packages').val();
      var num_of_checkboxes = jQuery(".quotes_services:checked").length;
      if (num_of_checkboxes < 1) {
        no_service_selected(num_of_checkboxes);
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
        jQuery("#mainform .quote_section_class_smpkg").prepend(
          '<div id="message" class="error inline wwe_small_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>'
        );
        jQuery("html, body").animate({
          scrollTop: jQuery(".wwe_small_custom_error_message").position().top,
        });
        return false;
      } else if (
        checkedValCustomMsg == "prevent" &&
        prevent_proceed_checkout_eniture == ""
      ) {
        jQuery("#mainform .quote_section_class_smpkg").prepend(
          '<div id="message" class="error inline wwe_small_custom_error_message"><p><strong>Error! </strong>Custom message field is empty.</p></div>'
        );
        jQuery("html, body").animate({
          scrollTop: jQuery(".wwe_small_custom_error_message").position().top,
        });
        return false;
      }
    }
  );

  var sm_all_checkboxes = jQuery(".quotes_services");
  if (
    sm_all_checkboxes.length === sm_all_checkboxes.filter(":checked").length
  ) {
    jQuery(".sm_all_services").prop("checked", true);
  }

  jQuery(".sm_all_services").change(function () {
    if (this.checked) {
      jQuery(".quotes_services").each(function () {
        this.checked = true;
      });
    } else {
      jQuery(".quotes_services").each(function () {
        this.checked = false;
      });
    }
  });

  jQuery('.wwex_connection_section_class input[type="text"]').each(function () {
    if (jQuery(this).parent().find(".err").length < 1) {
      jQuery(this).after('<span class="err"></span>');
    }
  });

  /*
   * Uncheck Select All Checkbox
   */

  jQuery(".quotes_services").on("change load", function () {
    var checkboxes = jQuery(".quotes_services:checked").length;
    var un_checkboxes = jQuery(".quotes_services").length;
    if (checkboxes === un_checkboxes) {
      jQuery(".sm_all_services").prop("checked", true);
    } else {
      jQuery(".sm_all_services").prop("checked", false);
    }
  });

  // jQuery("#wc_settings_account_number_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Account Number"
  // );
  // jQuery("#wc_settings_username_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Username"
  // );
  // jQuery("#wc_settings_password_wwe_small_packages").attr("title", "Password");
  // jQuery("#wc_settings_plugin_licence_key_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Plugin License Key"
  // );
  // jQuery("#wc_settings_authentication_key_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Authentication Key"
  // );

  // jQuery("#wc_settings_speedship_url_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Speedship URL"
  // );
  // jQuery("#wc_settings_oauth_url_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Oauth URL"
  // );
  jQuery("#wc_settings_oauth_clientid_wwe_small_packages_quotes").attr(
    "title",
    "Oauth ClientID"
  );
  jQuery("#wc_settings_oauth_client_secret_wwe_small_packages_quotes").attr(
    "title",
    "Oauth Client Secret"
  );
  // jQuery("#wc_settings_oauth_audience_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Oauth Audience"
  // );
  // jQuery("#wc_settings_oauth_username_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Oauth Username"
  // );
  // jQuery("#wc_settings_oauth_password_wwe_small_packages_quotes").attr(
  //   "title",
  //   "Oauth Password"
  // );
  jQuery("#wc_settings_googleapi_wwe_small_packages_quotes").attr(
    "title",
    "Google Api"
  );
  jQuery("#wc_settings_googleapi_wwe_small_packages_quotes").attr(
    "data-optional",
    "true"
  );
  jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages").attr(
    "title",
    "Handling Fee / Markup"
  );
  jQuery(".prevent_text_box").attr("title", "Message");

  jQuery(".quotes_services").closest("tr").addClass("quotes_services_tr");
  jQuery(".quotes_services").closest("td").addClass("quotes_services_td");

  jQuery("#ground_transit_label")
    .closest("tr")
    .addClass("ground_transit_label");
  jQuery("#hazardous_material_settings")
    .closest("tr")
    .addClass("hazardous_material_settings");
  jQuery("#ground_transit_wwe_small_packages")
    .closest("tr")
    .addClass("ground_transit_wwe_small_packages");
  jQuery("input[name*='restrict_calendar_transit_wwe_small_packages']")
    .closest("tr")
    .addClass("restrict_calendar_transit_wwe_small_packages");
  jQuery(
    "input[name*='only_quote_ground_service_for_hazardous_materials_shipments']"
  )
    .closest("tr")
    .addClass("only_quote_ground_service_for_hazardous_materials_shipments");
  jQuery("input[name*='ground_hazardous_material_fee']")
    .closest("tr")
    .addClass("ground_hazardous_material_fee");
  jQuery("input[name*='air_hazardous_material_fee']")
    .closest("tr")
    .addClass("air_hazardous_material_fee");

  // Nested Material
  // JS for edit product nested fields
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

  // insertion in ready function
  // Nested fields validation on product details
  jQuery("._nestedPercentage").keydown(function (eve) {
    wwe_stopSpecialCharacters(eve);
    var nestedPercentage = jQuery("._nestedPercentage").val();
    if (nestedPercentage.length == 2) {
      var newValue = nestedPercentage + "" + eve.key;
      if (newValue > 100) {
        return false;
      }
    }
  });

  jQuery("._maxNestedItems").keydown(function (eve) {
    wwe_stopSpecialCharacters(eve);
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
});

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

function wwe_stopSpecialCharacters(e) {
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

/**
 * Read a page's GET URL variables and return them as an associative array.
 */
function getUrlVarsWWESmall() {
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

function speedship_handling_fee_validation() {
  var handling_fee = jQuery(
    "#wc_settings_hand_free_mark_up_wwe_small_packages"
  ).val();
  var handling_fee_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
  var numeric_values_regex = /^[0-9]{1,7}$/;
  if (handling_fee != "" && numeric_values_regex.test(handling_fee)) {
    return true;
  } else if (
    (handling_fee != "" && !handling_fee_regex.test(handling_fee)) ||
    handling_fee.split(".").length - 1 > 1
  ) {
    jQuery("#mainform .quote_section_class_smpkg").prepend(
      '<div id="message" class="error inline wwe_handlng_fee_error"><p><strong>Error! </strong>Handling fee format should be 100.20 or 10%.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_handlng_fee_error").position().top,
    });
    jQuery("#wc_settings_hand_free_mark_up_wwe_small_packages").css({
      "border-color": "#e81123",
    });
    return false;
  } else {
    return true;
  }
}

function speedship_air_hazardous_material_fee_validation() {
  var air_hazardous_fee = jQuery("#air_hazardous_material_fee").val();
  var air_hazardous_fee_regex = /^([0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
  if (
    (air_hazardous_fee != "" &&
      !air_hazardous_fee_regex.test(air_hazardous_fee)) ||
    air_hazardous_fee.split(".").length - 1 > 1
  ) {
    jQuery("#mainform .quote_section_class_smpkg").prepend(
      '<div id="message" class="error inline wwe_small_air_hazardous_fee_error"><p><strong>Error! </strong>Air hazardous material fee format should be 100.20 or 10%.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".wwe_small_air_hazardous_fee_error").position().top,
    });
    jQuery("#air_hazardous_material_fee").css({ "border-color": "#e81123" });
    return false;
  } else {
    return true;
  }
}

function speedship_ground_hazardous_material_fee_validation() {
  var ground_hazardous_fee = jQuery("#ground_hazardous_material_fee").val();
  var ground_hazardous_regex = /^([0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;
  if (
    (ground_hazardous_fee != "" &&
      !ground_hazardous_regex.test(ground_hazardous_fee)) ||
    ground_hazardous_fee.split(".").length - 1 > 1
  ) {
    jQuery("#mainform .quote_section_class_smpkg").prepend(
      '<div id="message" class="error inline ground_ground_hazardous_fee_error"><p><strong>Error! </strong>Ground  hazardous material  fee format should be 100.20 or 10%.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".ground_ground_hazardous_fee_error").position().top,
    });
    jQuery("#ground_hazardous_material_fee").css({ "border-color": "#e81123" });
    return false;
  } else {
    return true;
  }
}

function speedship_ground_transit_validation() {
  var ground_transit_value = jQuery("#ground_transit_wwe_small_packages").val();
  var ground_transit_regex = /^[0-9]{1,2}$/;
  if (
    ground_transit_value != "" &&
    !ground_transit_regex.test(ground_transit_value)
  ) {
    jQuery("#mainform .quote_section_class_smpkg").prepend(
      '<div id="message" class="error inline ground_transit_error"><p><strong>Error! </strong>Maximum 2 numeric characters are allowed for transit day field.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".ground_transit_error").position().top,
    });
    jQuery("#ground_transit_wwe_small_packages").css({
      "border-color": "#e81123",
    });
    return false;
  } else {
    return true;
  }
}

function markup_service(id) {
  var wwe_small_markup = jQuery("#" + id).val();
  var wwe_small_markup_service_regex = /^(-?[0-9]{1,4}%?)$|(\.[0-9]{1,2})%?$/;

  if (!wwe_small_markup_service_regex.test(wwe_small_markup)) {
    jQuery("#mainform .quote_section_class_smpkg").prepend(
      '<div id="message" class="error inline smpkg_small_dom_markup_service_error"><p><strong>Error! </strong>Service Level Markup fee format should be 100.20 or 10%.</p></div>'
    );
    jQuery("html, body").animate({
      scrollTop: jQuery(".smpkg_small_dom_markup_service_error").position().top,
    });
    jQuery("#" + id).css({ "border-color": "#e81123" });
    return false;
  } else {
    return true;
  }
}

/**
 * validation
 * @param form_id
 * @returns {Boolean}             */
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

/**
 * validate string
 * @param string
 * @returns {String|Boolean}         */
function validateString(string) {
  if (string == "") {
    return "empty";
  } else {
    return true;
  }
}

/**
 * if No Service selected
 * @param num_of_checkboxes
 * @returns {Boolean}             */
function no_service_selected(num_of_checkboxes) {
  jQuery(".updated").hide();
  jQuery(".quote_section_class_smpkg h2:first-child").after(
    '<div id="message" class="error inline no_srvc_select"><p><strong>Error! </strong>Please select at least one quote service.</p></div>'
  );
  jQuery("html, body").animate({
    scrollTop: jQuery(".no_srvc_select").position().top,
  });
  return false;
}
