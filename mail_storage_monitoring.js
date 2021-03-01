function update_ui(params) {
  if (params.mail_storage_online && $("a.mail").hasClass("disabled")) {
    if (rcmail.env.task === "mail" && rcmail.env.action !== "compose")
      rcmail.refresh_list();

    $("input, textarea, select, button, ul").prop("disabled", false);
    $("a").not(".about, .logout").removeClass("disabled");
    $("table tr").removeClass("disabled");
    $("table tr, ul li").css({"pointer-events": "auto"});
    var iframe = window.frames["preferences-frame"];
    if (typeof iframe !== "undefined") {
      $("input, textarea, select, button, ul", iframe.document).prop("disabled", false);
      $("a, table tr", iframe.document).removeClass("disabled");
      $("table tr, ul li", iframe.document).css({"pointer-events": "auto"});
    }
  } else if (!params.mail_storage_online && !$("a.mail").hasClass("disabled")) {
    /* Run the disabling a bit later so we can override Roundcube's internals */
    setTimeout(function() {
      $("input, textarea, select, button, ul").prop("disabled", true);
      $("a").not(".about, .logout").addClass("disabled");
      $("table tr").addClass("disabled");
      $("table tr, ul li").css({"pointer-events": "none"});
      var iframe = window.frames["preferences-frame"];
      if (typeof iframe !== "undefined") {
        $("input, textarea, select, button, ul", iframe.document).prop("disabled", true);
        $("a, table tr", iframe.document).addClass("disabled");
        $("table tr, ul li", iframe.document).css({"pointer-events": "none"});
      }
    }, 500);
  }
}

rcmail.addEventListener('plugin.mail_storage_monitoring.update_ui', update_ui);
