jQuery(document).ready(function () {
  hhul_bind_events();
});

/**
 * Bind events to the roles list, to the sortable columns and to the pagination.
 */
function hhul_bind_events() {
  jQuery("#hhul_sort").on("change", function () {
    let orderby = "";
    let order = "";
    if ( jQuery(".hhul_sortable .hhul_arrow.asc").length > 0) {
      orderby = jQuery(".hhul_sortable .hhul_arrow.asc").closest(".hhul_sortable").data("value");
      order = "asc";
    }
    else if ( jQuery(".hhul_sortable .hhul_arrow.desc").length > 0 ) {
      orderby = jQuery(".hhul_sortable .hhul_arrow.desc").closest(".hhul_sortable").data("value");
      order = "desc";
    }
    let page = 1;
    hhul_sort(orderby, order, page);
  });
  jQuery(".hhul_sortable").on("click", function () {
    let orderby = jQuery(this).data("value");
    let order = jQuery(this).find(".hhul_arrow").hasClass("asc") ? "desc" : "asc";
    let page = jQuery(".hhul_pagination button").hasClass("active") ? jQuery(".hhul_pagination button.active").data("value") : 1;

    hhul_sort(orderby, order, page);
  });
  jQuery(".hhul_pagination button").on("click", function () {
    let orderby = "";
    let order = "";
    if ( jQuery(".hhul_sortable .hhul_arrow.asc").length > 0) {
      orderby = jQuery(".hhul_sortable .hhul_arrow.asc").closest(".hhul_sortable").data("value");
      order = "asc";
    }
    else if ( jQuery(".hhul_sortable .hhul_arrow.desc").length > 0 ) {
      orderby = jQuery(".hhul_sortable .hhul_arrow.desc").closest(".hhul_sortable").data("value");
      order = "desc";
    }
    let page = jQuery(this).data("value");

    hhul_sort(orderby, order, page);
  });
}

/**
 * Ajax call for all actions.
 *
 * @param orderby
 * @param order
 * @param page
 */
function hhul_sort(orderby, order, page) {
  jQuery.ajax({
    type: "POST",
    url: jQuery(".wrap").data("ajax-url"),
    data: {
      "action": "hhul_ajax",
      "sort": jQuery("#hhul_sort").val(),
      "orderby": orderby,
      "order": order,
      "page": page,
    },
    success: function (data) {
      jQuery("#hhul_container").html(jQuery(data).html());
      /* Bind events after the ajax call.*/
      hhul_bind_events();
      /* Update order arrows.*/
      hhul_update_arrows(orderby, order);
    },
    error: function (xhr) {},
  });
}

/**
 * Update arrows.
 *
 * @param sortBy
 * @param order
 */
function hhul_update_arrows(sortBy, order) {
  const usernameArrow = jQuery('#hhul_name_arrow');
  const emailArrow = jQuery('#hhul_username_arrow');
  if (sortBy === 'name') {
    usernameArrow.toggleClass('asc', order === 'asc');
    usernameArrow.toggleClass('desc', order === 'desc');
    emailArrow.removeClass('asc desc');
  }
  else if (sortBy === 'username') {
    emailArrow.toggleClass('asc', order === 'asc');
    emailArrow.toggleClass('desc', order === 'desc');
    usernameArrow.removeClass('asc desc');
  }
}