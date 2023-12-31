/**
 * Send an action over AJAX. A wrapper around jQuery.ajax. In future, all consumers can be reviewed to simplify some of the options, where there is historical cruft.
 *
 * @param {string}   action   - the action to send
 * @param {*}        data     - data to send
 * @param {Function} callback - will be called with the results
 * @param {object}   options  -further options. Relevant properties include:
 * - [json_parse=true] - whether to JSON parse the results
 * - [alert_on_error=true] - whether to show an alert box if there was a problem (otherwise, suppress it)
 * - [action='aios_ajax'] - what to send as the action parameter on the AJAX request (N.B. action parameter to this function goes as the 'subaction' parameter on the AJAX request)
 * - [nonce=aios_ajax_nonce] - the nonce value to send.
 * - [nonce_key='nonce'] - the key value for the nonce field
 * - [timeout=null] - set a timeout after this number of seconds (or if null, none is set)
 * - [async=true] - control whether the request is asynchronous (almost always wanted) or blocking (would need to have a specific reason)
 * - [type='POST'] - GET or POST
 */
function aios_send_command(action, data, callback, options) {

	default_options = {
		json_parse: true,
		alert_on_error: true,
		action: 'aios_ajax',
		nonce: aios_data.ajax_nonce,
		nonce_key: 'nonce',
		timeout: null,
		async: true,
		type: 'POST'
	};

	if ('undefined' === typeof options) options = {};

	for (var opt in default_options) {
		if (!options.hasOwnProperty(opt)) { options[opt] = default_options[opt]; }
	}

	var ajax_data = {
		action: options.action,
		subaction: action,
	};

	ajax_data[options.nonce_key] = options.nonce;
	ajax_data.data = data;

	var ajax_opts = {
		type: options.type,
		url: ajaxurl,
		data: ajax_data,
		success: function(response, status) {
			if (options.json_parse) {
				try {
					var resp = aios_parse_json(response);
				} catch (e) {
					if ('function' == typeof options.error_callback) {
						return options.error_callback(response, e, 502, resp);
					} else {
						console.log(e);
						console.log(response);
						if (options.alert_on_error) { alert(aios_trans.unexpected_response+' '+response); }
						return;
					}
				}
				if (resp.hasOwnProperty('fatal_error')) {
					if ('function' == typeof options.error_callback) {
						// 500 is internal server error code
						return options.error_callback(response, status, 500, resp);
					} else {
						console.error(resp.fatal_error_message);
						if (options.alert_on_error) { alert(resp.fatal_error_message); }
						return false;
					}
				}
				if ('function' == typeof callback) callback(resp, status, response);
			} else {
				if ('function' == typeof callback) callback(response, status);
			}
		},
		error: function(response, status, error_code) {
			if ('function' == typeof options.error_callback) {
				options.error_callback(response, status, error_code);
			} else {
				console.log("aios_send_command: error: "+status+" ("+error_code+")");
				console.log(response);
			}
		},
		dataType: 'text',
		async: options.async
	};

	if (null != options.timeout) { ajax_opts.timeout = options.timeout; }

	jQuery.ajax(ajax_opts);

}

/**
 * Parse JSON string, including automatically detecting unwanted extra input and skipping it
 *
 * @param {string}  json_mix_str - JSON string which need to parse and convert to object
 * @param {boolean} analyse		 - if true, then the return format will contain information on the parsing, and parsing will skip attempting to JSON.parse() the entire string (will begin with trying to locate the actual JSON)
 *
 * @throws SyntaxError|String (including passing on what JSON.parse may throw) if a parsing error occurs.
 *
 * @returns Mixed parsed JSON object. Will only return if parsing is successful (otherwise, will throw). If analyse is true, then will rather return an object with properties (mixed)parsed, (integer)json_start_pos and (integer)json_end_pos
 */
function aios_parse_json(json_mix_str, analyse) {

	analyse = ('undefined' === typeof analyse) ? false : true;

	// Just try it - i.e. the 'default' case where things work (which can include extra whitespace/line-feeds, and simple strings, etc.).
	if (!analyse) {
		try {
			var result = JSON.parse(json_mix_str);
			return result;
		} catch (e) {
			console.log('AIOS: Exception when trying to parse JSON (1) - will attempt to fix/re-parse based upon first/last curly brackets');
			console.log(json_mix_str);
		}
	}

	var json_start_pos = json_mix_str.indexOf('{');
	var json_last_pos = json_mix_str.lastIndexOf('}');

	// Case where some php notice may be added after or before json string
	if (json_start_pos > -1 && json_last_pos > -1) {
		var json_str = json_mix_str.slice(json_start_pos, json_last_pos + 1);
		try {
			var parsed = JSON.parse(json_str);
			if (!analyse) { console.log('AIOS: JSON re-parse successful'); }
			return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: json_last_pos + 1 } : parsed;
		} catch (e) {
			console.log('AIOS: Exception when trying to parse JSON (2) - will attempt to fix/re-parse based upon bracket counting');

			var cursor = json_start_pos;
			var open_count = 0;
			var last_character = '';
			var inside_string = false;

			// Don't mistake this for a real JSON parser. Its aim is to improve the odds in real-world cases seen, not to arrive at universal perfection.
			while ((open_count > 0 || cursor == json_start_pos) && cursor <= json_last_pos) {

				var current_character = json_mix_str.charAt(cursor);

				if (!inside_string && '{' == current_character) {
					open_count++;
				} else if (!inside_string && '}' == current_character) {
					open_count--;
				} else if ('"' == current_character && '\\' != last_character) {
					inside_string = inside_string ? false : true;
				}

				last_character = current_character;
				cursor++;
			}
			console.log("Started at cursor="+json_start_pos+", ended at cursor="+cursor+" with result following:");
			console.log(json_mix_str.substring(json_start_pos, cursor));

			try {
				var parsed = JSON.parse(json_mix_str.substring(json_start_pos, cursor));
				console.log('AIOS: JSON re-parse successful');
				return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: cursor } : parsed;
			} catch (e) {
				// Throw it again, so that our function works just like JSON.parse() in its behaviour.
				throw e;
			}
		}
	}

	throw "AIOS: could not parse the JSON";

}

jQuery(function($) {
	//Add Generic Admin Dashboard JS Code in this file

	//Media Uploader - start
	jQuery("#aiowps_restore_htaccess_form").submit(function(e) {
		e.preventDefault();
		aios_read_restore_file(this, 'htaccess');
	});

	jQuery("#aiowps_restore_wp_config_form").submit(function(e) {
		e.preventDefault();
		aios_read_restore_file(this, 'wp_config');
	});

	jQuery("#aiowps_restore_settings_form").submit(function(e) {
		e.preventDefault();
		aios_read_restore_file(this, 'import_settings');
	});

	function aios_read_restore_file(form, file) {
		var aios_import_file_input = document.getElementById('aiowps_' + file + '_file');
		if (aios_import_file_input.files.length == 0) {
			alert(aios_trans.no_import_file);
			return;
		}
		var aios_import_file_file = aios_import_file_input.files[0];
		var aios_import_file_reader = new FileReader();
		aios_import_file_reader.onload = function() {
			jQuery('#aiowps_' + file + '_file_contents').val(this.result);
			form.submit();
		};
		aios_import_file_reader.readAsText(aios_import_file_file);
	}
	//End of Media Uploader
	
	// Triggers the more info toggle link
	$(".aiowps_more_info_body").hide();//hide the more info on page load
	$('.aiowps_more_info_anchor').on('click', function() {
		$(this).next(".aiowps_more_info_body").animate({ "height": "toggle"});
		var toogle_char_ref = $(this).find(".aiowps_more_info_toggle_char");
		var toggle_char_value = toogle_char_ref.text();
		if(toggle_char_value === "+"){
			toogle_char_ref.text("-");
		}
		else{
			 toogle_char_ref.text("+");
		}
	});
	//End of more info toggle

	//This function uses javascript to retrieve a query arg from the current page URL
	function getParameterByName(name) {
		var url = window.location.href;
		name = name.replace(/[\[\]]/g, "\\$&");
		var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
			results = regex.exec(url);
		if (!results) return null;
		if (!results[2]) return '';
		return decodeURIComponent(results[2].replace(/\+/g, " "));
	}

	// Start of brute force attack prevention toggle handling
	jQuery('input[name=aiowps_enable_brute_force_attack_prevention]').on('click', function() {
		jQuery('input[name=aiowps_brute_force_secret_word]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_cookie_based_brute_force_redirect_url]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_brute_force_attack_prevention_pw_protected_exception]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_brute_force_attack_prevention_ajax_exception]').prop('disabled', !jQuery(this).prop('checked'));
	});
	// End of brute force attack prevention toggle handling

	// Start of CAPTCHA handling
	jQuery('.wrap').on('change', '#aiowps_default_captcha', function () {
		var selected_captcha = $(this).val();
		jQuery('.captcha_settings').hide();
		jQuery('#aios-'+ selected_captcha).show();
		
		if ('none' === selected_captcha) {
			jQuery('#aios-captcha-options').hide()
		} else {
			jQuery('#aios-captcha-options').show();
		}
	});
	// End of CAPTCHA handling

	/**
	 * Take a backup with UpdraftPlus if possible.
	 *
	 * @param {String}   file_entities
	 *
	 * @return void
	 */
	function take_a_backup_with_updraftplus(file_entities) {
		// Set default for file_entities to empty string
		if ('undefined' == typeof file_entities) file_entities = '';
		var exclude_files = file_entities ? 0 : 1;

		if (typeof updraft_backupnow_inpage_go === 'function') {
			updraft_backupnow_inpage_go(function () {
				// Close the backup dialogue.
				$('#updraft-backupnow-inpage-modal').dialog('close');
			}, file_entities, 'autobackup', 0, exclude_files, 0);
		}
	}
	if (jQuery('#aios-manual-db-backup-now').length) {
		jQuery('#aios-manual-db-backup-now').on('click', function (e) {
			e.preventDefault();
			take_a_backup_with_updraftplus();
		});
	}

	// Hide 2FA premium section (advertisements) for free.
	if (jQuery('.tfa-premium').length && 0 == jQuery('#tfa_trusted_for').length) {
		jQuery('.tfa-premium').parent().find('hr').first().hide();
		jQuery('.tfa-premium').hide();
	}


	// Start of trash spam comments toggle handling
	jQuery('input[name=aiowps_enable_trash_spam_comments]').on('click', function() {
		jQuery('input[name=aiowps_trash_spam_comments_after_days]').prop('disabled', !jQuery(this).prop('checked'));
	});
	// End of trash spam comments toggle handling

	// Copies text using the deprecated document.execCommand method
	function deprecated_copy(text) {
		var $temp = $('<input>');
		$('body').append($temp);
		$temp.val(text).select();
		if (document.execCommand('copy')) {
			alert(aios_trans.copied);
		}
		$temp.remove()
	}

	// Start of copy-to-clipboard click handling
	jQuery('.copy-to-clipboard').on('click', function(event) {
		if (navigator.clipboard) {
			navigator.clipboard.writeText(event.target.value).then(function() {
				alert(aios_trans.copied);
			}, function() {
				deprecated_copy(event.target.value);
			});
		} else {
			deprecated_copy(event.target.value);
		}
	});
	// End of copy-to-clipboard click handling

	// Start of database table prefix handling
	jQuery('#aiowps_enable_random_prefix').on('click', function() {
		jQuery('#aiowps_new_manual_db_prefix').prop('disabled', jQuery(this).prop('checked'));
	});

	jQuery('#aiowps_new_manual_db_prefix').on('input', function() {
		if (jQuery(this).prop('value')) {
			jQuery('#aiowps_enable_random_prefix').prop('disabled', true);
		} else {
			jQuery('#aiowps_enable_random_prefix').prop('disabled', false);
		}
	});
	// End of database table prefix handling
	
	//Login white list page - fetch user's IPv6/IPv4 address as another type IPv4/IPv6 only considered.
	if (jQuery('#aios_user_ip_v4').length) {
		var selector = '#aios-ipify-ip-address';
		var ipv4_field = '#aios_user_ip_v4';
		var ipv6_field = '#aios_user_ip_v6';
		jQuery(selector).removeClass('aio_hidden');
		aios_send_command('get_user_ip_all_version', {
		}, function (resp) {
			if (resp.hasOwnProperty('ip_v4')) {
				jQuery(ipv4_field).val(resp.ip_v4);
				jQuery(ipv4_field).removeClass('aio_hidden');
			}
			if (resp.hasOwnProperty('ip_v6')) {
				jQuery(ipv6_field).val(resp.ip_v6);
				jQuery(ipv6_field).removeClass('aio_hidden');
			}
			if (!resp.hasOwnProperty('ip_v4') && !resp.hasOwnProperty('ip_v6')) { // if ip_v4 and ip_v6 both not returned show error_message
				console.log(resp);
				jQuery(selector).html(resp.error_message); // show error message instead getting...
			} else {
				jQuery(selector).addClass('aio_hidden'); // hide getting... message
			}
		}, {
			error_callback: function (response, status, error_code, resp) {
				if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
					console.error(resp.fatal_error);
				} else {
					var error_message = "Error: " + status + " (" + error_code + ")";
					jQuery(selector).html(error_message); // show error message instead getting...
					console.log(response);
				}
			}
		});
	};
});
