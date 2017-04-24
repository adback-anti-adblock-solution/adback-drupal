/**
 * @file
 */

(function ($) {
  'use strict';

  /**
   * All of the code for your admin-facing JavaScript source
   * should reside in this file.
   *
   * Note: It has been assumed you will write jQuery code here, so the
   * $ function reference has been prepared for usage within the scope
   * of this function.
   *
   * This enables you to define handlers, for when the DOM is ready:
   *
   * $(function() {
     *
     * });
   *
   * When the window is loaded:
   *
   * $( window ).load(function() {
     *
     * });
   *
   * ...and/or other possibilities.
   *
   * Ideally, it is not considered best practise to attach more than a
   * single DOM-ready or window-load handler for a particular page.
   * Although scripts in the Drupal core, Plugins and Themes may be
   * practising this, we should strive to set a better example in our own work.
   */

  function loginAdback() {
    $('#ab-login-adback').attr('disabled', true);
    var callback = encodeURI(window.location.href);
    window.location.href = 'https://www.adback.co/tokenoauth/site?redirect_url=' + callback;
  }

  function registerAdback(event) {
    $('#ab-register-adback').attr('disabled', true);
    var callback = encodeURI(window.location.href);
    window.location.href = 'https://www.adback.co/en/register/?redirect_url='
        + callback
        + '&email=' + $(event.target).data('email')
        + '&website=' + $(event.target).data('site-url');
  }

  function _logout() {
    var data = {
      action: 'ab_logout'
    };

    $.post('/admin/config/adback/logout', data, function (response) {
      if (response.done === true) {
        window.location.reload();
      }
    });
  }

  $(document).ready(function () {
    if ($('#ab-logout').length > 0) {
      $('#ab-logout').click(_logout);
    }

    if ($('#ab-login').length > 0) {
      $('#ab-login-adback').click(loginAdback);
      $('#ab-register-adback').click(registerAdback);

      $('#ab-username,#ab-password').keyup(function (e) {
        var code = e.which; // Recommended to use e.which, it's normalized across browsers.
        if (code === 13) {
          e.preventDefault();
          loginAdback();
        }
      });
    }

    if ($('#ab-settings').length > 0) {
      $('#ab-settings-submit').click(saveMessage);
    }
  });

})(jQuery);
