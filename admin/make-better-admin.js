var strict;

jQuery(document).ready(function ($) {
    /**
     * DEACTIVATION FEEDBACK FORM
     */
    // show overlay when clicked on "deactivate"
    adsforwp_deactivate_link = $('.wp-admin.plugins-php tr[data-slug="ads-for-wp"] .row-actions .deactivate a');
    adsforwp_deactivate_link_url = adsforwp_deactivate_link.attr('href');

    adsforwp_deactivate_link.click(function (e) {
        e.preventDefault();
        
        // only show feedback form once per 30 days
        var c_value = adsforwp_admin_get_cookie("adsforwp_hide_deactivate_feedback");

        if (c_value === undefined) {
            $('#ads-for-wp-reloaded-feedback-overlay').show();
        } else {
            // click on the link
            window.location.href = adsforwp_deactivate_link_url;
        }
    });
    // show text fields
    $('#ads-for-wp-reloaded-feedback-content input[type="radio"]').click(function () {
        // show text field if there is one
        var inputValue = $(this).attr("value");
        var targetBox = $("." + inputValue);
        $(".mb-box").not(targetBox).hide();
        $(targetBox).show();
    });
    // send form or close it
    $('#ads-for-wp-reloaded-feedback-content .button').click(function (e) {
        e.preventDefault();
        // set cookie for 30 days
        var exdate = new Date();
        exdate.setSeconds(exdate.getSeconds() + 2592000);
        document.cookie = "adsforwp_hide_deactivate_feedback=1; expires=" + exdate.toUTCString() + "; path=/";

        $('#ads-for-wp-reloaded-feedback-overlay').hide();
        if ('ads-for-wp-reloaded-feedback-submit' === this.id) {
            // Send form data
            $.ajax({
                type: 'POST',
                url: ajaxurl,
                dataType: 'json',
                data: {
                    action: 'adsforwp_send_feedback',
                    data: $('#ads-for-wp-reloaded-feedback-content form').serialize()
                },
                complete: function (MLHttpRequest, textStatus, errorThrown) {
                    // deactivate the plugin and close the popup
                    $('#ads-for-wp-reloaded-feedback-overlay').remove();
                    window.location.href = adsforwp_deactivate_link_url;

                }
            });
        } else {
            $('#ads-for-wp-reloaded-feedback-overlay').remove();
            window.location.href = adsforwp_deactivate_link_url;
        }
    });
    // close form without doing anything
    $('.ads-for-wp-feedback-not-deactivate').click(function (e) {
        $('#ads-for-wp-reloaded-feedback-overlay').hide();
    });
    
    function adsforwp_admin_get_cookie (name) {
	var i, x, y, adsforwp_cookies = document.cookie.split( ";" );
	for (i = 0; i < adsforwp_cookies.length; i++)
	{
		x = adsforwp_cookies[i].substr( 0, adsforwp_cookies[i].indexOf( "=" ) );
		y = adsforwp_cookies[i].substr( adsforwp_cookies[i].indexOf( "=" ) + 1 );
		x = x.replace( /^\s+|\s+$/g, "" );
		if (x === name)
		{
			return unescape( y );
		}
	}
}

}); // document ready