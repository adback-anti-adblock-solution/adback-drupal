(function( $ ) {
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
	 * Although scripts in the WordPress core, Plugins and Themes may be
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

	function saveSlug() {
		if($("#ab-select-slug-field").val() == "") return;

		var data = {
			'action': 'saveSlug',
			'slug' :  $("#ab-select-slug-field").val()
		}

		$.post(ajaxurl, data, function(response) {
			var obj = JSON.parse(response);
			if(obj.done === true) {
				window.location.reload();
			} else {
				sweetAlert(trans_arr.oops, trans_arr.error, "error");
			}
		});
	}

	function saveMessage() {
		if($("#ab-settings-header-text").val() == "" || $("#ab-settings-close-text").val() == "" || $("#ab-settings-message").val() == "") {
			sweetAlert(trans_arr.oops, trans_arr.all_the_fields_should_be_fill, "error");
			return;
		}

		$("#ab-settings-submit").attr('disabled', true);
		var data = {
			'action': 'saveMessage',
			'header-text' :  $("#ab-settings-header-text").val(),
			'close-text' : $("#ab-settings-close-text").val(),
			'message' : $("#ab-settings-message").val(),
			'display' : $("#ab-settings-display").is(":checked")
		}

		$.post(ajaxurl, data, function(response) {
			var obj = JSON.parse(response);
			$("#ab-settings-submit").attr('disabled', false);
			if(obj.done === true) {
				window.location.reload();
			} else {
				sweetAlert(trans_arr.oops, trans_arr.error, "error");
			}
		});
	}

	function _logout() {
		var data = {
			'action': 'ab_logout'
		}

		$.post('/admin/config/adback/logout', data, function(response) {
			if(response.done === true) {
				window.location.reload();
			} else {
				sweetAlert(trans_arr.oops, trans_arr.error, "error");
			}
		});
	}

	$(document).ready(function() {
		if($("[data-ab-type]").length>0) {
			$("#ab-logout").click(_logout);
		}

		if($("#ab-login").length>0) {
			$("#ab-login-adback").click(loginAdback);
			$("#ab-register-adback").click(registerAdback);


			$("#ab-username,#ab-password").keyup(function(e) {
				var code = e.which; // recommended to use e.which, it's normalized across browsers
				if(code==13) {
					e.preventDefault();
					loginAdback();
				}
			});
		}

		if($("#ab-select-slug").length>0) {
			$("#ab-select-slug-save").click(saveSlug);
		}

		if($("#ab-settings").length>0) {
			$("#ab-settings-submit").click(saveMessage);
		}
	});


})( jQuery );
