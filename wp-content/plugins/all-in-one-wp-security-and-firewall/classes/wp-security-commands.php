<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('AIOWPSecurity_Commands')) return;

class AIOWPSecurity_Commands {

	/**
	 * Get IP address of given method.
	 *
	 * @param array $data - the request data
	 *
	 * @return array|WP_Error - an array response or a WP_Error if there was an error
	 */
	public function get_ip_address_of_given_method($data) {
		$ip_method_id = $data['ip_retrieve_method'];
		$ip_retrieve_methods = AIOS_Abstracted_Ids::get_ip_retrieve_methods();
		if (isset($ip_retrieve_methods[$ip_method_id])) {
			return array(
				'ip_address' => isset($_SERVER[$ip_retrieve_methods[$ip_method_id]]) ? $_SERVER[$ip_retrieve_methods[$ip_method_id]] : '',
			);
		} else {
			return new WP_Error('aios-invalid-ip-retrieve-method', __('Invalid IP retrieve method.', 'all-in-one-wp-security-and-firewall'));
		}
		die;
	}
	
	/**
	 * Get User IPv4 and IPv6 addresses
	 *
	 * @return array|WP_Error - an array response or a WP_Error if there was an error
	 */
	public function get_user_ip_all_version() {
		$ip_v4 = AIOWPSecurity_Utility_IP::get_user_ipv4();
		$ip_v6 = AIOWPSecurity_Utility_IP::get_user_ipv6();
		
		$user_ip_all = array();
		if (!empty($ip_v4)) $user_ip_all['ip_v4'] = $ip_v4;
		if (!empty($ip_v6) && $ip_v6 != $ip_v4) $user_ip_all['ip_v6'] = $ip_v6; // Sometimes get_user_ipv6 - api64.ipify.org returns the IPv4 address so check with $ip_v4 requried.
		
		if (!empty($user_ip_all)) {
			return $user_ip_all;
		} else {
			return new WP_Error('aios-error-user-ip-request', __('Error in getting user IPv4 and IPv6 address(es).', 'all-in-one-wp-security-and-firewall'));
		}
		die;
	}

	/**
	 * Dismiss a notice
	 *
	 * @param array $data - the request data contains the notice to dismiss
	 *
	 * @return array
	 */
	public function dismiss_notice($data) {
		global $aio_wp_security;

		$time_now = $aio_wp_security->notices->get_time_now();
		
		if (in_array($data['notice'], array('dismissdashnotice', 'dismiss_season'))) {
			$aio_wp_security->configs->set_value($data['notice'], $time_now + (366 * 86400));
		} elseif (in_array($data['notice'], array('dismiss_page_notice_until', 'dismiss_notice'))) {
			$aio_wp_security->configs->set_value($data['notice'], $time_now + (84 * 86400));
		} elseif ('dismiss_review_notice' == $data['notice']) {
			if (empty($data['dismiss_forever'])) {
				$aio_wp_security->configs->set_value($data['notice'], $time_now + (84 * 86400));
			} else {
				$aio_wp_security->configs->set_value($data['notice'], $time_now + (100 * 365.25 * 86400));
			}
		} elseif ('dismiss_automated_database_backup_notice' == $data['notice']) {
			$aio_wp_security->delete_automated_backup_configs();
		} elseif ('dismiss_ip_retrieval_settings_notice' == $data['notice']) {
			$aio_wp_security->configs->set_value($data['notice'], 1);
		} elseif ('dismiss_ip_retrieval_settings_notice' == $data['notice']) {
			$aio_wp_security->configs->set_value('aiowps_is_login_whitelist_disabled_on_upgrade', 1);
		} elseif ('dismiss_login_whitelist_disabled_on_upgrade_notice' == $data['notice']) {
			if (isset($data['turn_it_back_on']) && '1' == $data['turn_it_back_on']) {
				$aio_wp_security->configs->set_value('aiowps_enable_whitelisting', '1');
			}
			$aio_wp_security->configs->delete_value('aiowps_is_login_whitelist_disabled_on_upgrade');
		} elseif ('dismiss_ip_blacklist_notice' == $data['notice']) {
			if (isset($data['turn_it_back_on']) && '1' == $data['turn_it_back_on']) {
				$aio_wp_security->configs->set_value('aiowps_enable_blacklisting', '1');
				AIOWPSecurity_Configure_Settings::set_blacklist_ip_firewall_configs();
				AIOWPSecurity_Configure_Settings::set_user_agent_firewall_configs();
			}
			$aio_wp_security->configs->delete_value('aiowps_is_ip_blacklist_settings_notice_on_upgrade');
		} elseif ('dismiss_firewall_settings_disabled_on_upgrade_notice' == $data['notice']) {
			$is_reactivated = (isset($data['turn_it_back_on']) && '1' == $data['turn_it_back_on']);
				if ($is_reactivated) {
					global $aiowps_firewall_config;
					$active_settings = $aio_wp_security->configs->get_value('aiowps_firewall_active_upgrade');
	
					if (!empty($active_settings)) {
						$active_settings = json_decode($active_settings);
						if (!empty($active_settings)) {
							foreach ($active_settings as $setting) {
								$aiowps_firewall_config->set_value($setting, true);
							}
						}
					}
				}

				$aio_wp_security->configs->delete_value('aiowps_firewall_active_upgrade');
		}
		

		$aio_wp_security->configs->save_config();
		
		return array();
	}
}
