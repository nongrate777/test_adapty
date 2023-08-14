<?php
if (!defined('ASSETS_VERSION')) {
    define('ASSETS_VERSION', time());
}

class General
{
    protected static $instance = null;

    public static function instance(): ?General
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    function __construct()
    {
        add_action('init', array($this, 'lowercase_url'));
        add_action('init', array($this, 'clear_header'));
        add_action('init', array($this, 'blog_public_disable'));
        add_filter('acf/settings/save_json', array($this, 'my_acf_json_save_point'));
        add_filter('acf/settings/load_json', array($this, 'my_acf_json_load_point'));
        add_filter('upload_mimes', array($this, 'cc_mime_types'));
        add_filter('as3cf_gzip_mime_types', array($this, 'disable_as3cf_gzip_mime_types'), 10, 2);
        add_filter('wp_update_attachment_metadata', array($this, 'as3cf_update_attachment_metadata'), 10, 2);
        add_action('init', array($this, 'theme_support'));

        /* Disable Search */
        add_action('parse_query', array($this, 'theme_filter_query'));
        add_filter('get_search_form', array($this, 'search_form'));
        add_action('widgets_init', array($this, 'theme_remove_search_widget'));
        /* Disable Search */

        add_action('wpcf7_init', array($this, 'wpcf7_remove_assets'));
        add_filter('shortcode_atts_wpcf7', array($this, 'wpcf7_add_assets'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_enqueue_scripts', array($this, 'dequeue_assets'));
        add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
        add_action('aiowps_before_wp_die_renamed_login', array($this, 'wp_admin_redirect'));
        add_filter( 'xmlrpc_enabled', '__return_false' );
        add_filter( 'wp_mail_smtp_reports_emails_summary_is_disabled', '__return_true' );
        add_filter('xmlrpc_methods', array($this, 'rxpp_remove_xmlrpc_pingback_ping'));
        add_filter( 'rest_pre_dispatch', array($this, 'close_rest_api_routes'), 10, 3 );
        add_action('init', array($this, 'update_db'));
    }

    public static function lowercase_url(): void
    {
        if (!is_admin()) {
            $url = $_SERVER['REQUEST_URI'];
            $params = ($_SERVER['QUERY_STRING'] ?? '');

            if (preg_match('/[.]/', $url)) {
                return;
            }

            if (preg_match('/[A-Z]/', $url)) {

                $lc_url = empty($params)
                    ? strtolower($url)
                    : strtolower(substr($url, 0, strrpos($url, '?'))) . '?' . $params;

                if ($lc_url !== $url) {
                    header('Location: ' . $lc_url, TRUE, 301);
                    exit();
                }
            }
        }
    }

    public static function clear_header(): void
    {
        remove_action('wp_head', 'wp_generator');
        remove_action('wp_head', 'rsd_link');
        remove_action('wp_head', 'feed_links', 2);
        remove_action('wp_head', 'wlwmanifest_link');
        remove_action('wp_head', 'index_rel_link');
        remove_action('wp_head', 'wp_shortlink_wp_head');
        remove_action('wp_head', 'rest_output_link_wp_head', 10);
        remove_action('template_redirect', 'rest_output_link_header', 11, 0);
        remove_action('rest_api_init', 'wp_oembed_register_route');
        add_filter('embed_oembed_discover', '__return_false');
        remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10);
        remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);
        remove_action('wp_head', 'wp_oembed_add_host_js');
        remove_filter('pre_oembed_result', 'wp_filter_pre_oembed_result', 10);
        remove_action('wp_head', 'wp_resource_hints', 2);
        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_filter('the_content_feed', 'wp_staticize_emoji');
        remove_filter('comment_text_rss', 'wp_staticize_emoji');
        remove_filter('wp_mail', 'wp_staticize_emoji_for_email');
        add_filter('wpseo_debug_markers', '__return_false');
        add_filter('tiny_mce_plugins', 'disable_emojis_tinymce');
        add_filter('wp_resource_hints', 'disable_emojis_remove_dns_prefetch', 10, 2);

        function disable_emojis_tinymce($plugins)
        {
            if (is_array($plugins)) {
                return array_diff($plugins, array('wpemoji'));
            } else {
                return array();
            }
        }

        function disable_emojis_remove_dns_prefetch($urls, $relation_type)
        {
            if ('dns-prefetch' == $relation_type) {
                $emoji_svg_url = apply_filters('emoji_svg_url', 'https://s.w.org/images/core/emoji/2/svg/');
                $urls = array_diff($urls, array($emoji_svg_url));
            }
            return $urls;
        }
    }

    public static function blog_public_disable(): void
    {
        if (wp_get_environment_type() !== 'production')
            update_option('blog_public', '0');
        else
            update_option('blog_public', '1');
    }

    public static function my_acf_json_save_point(): string
    {
        return get_stylesheet_directory() . '/acf';
    }

    public static function my_acf_json_load_point($paths)
    {
        add_filter('acf/settings/current_language', 'wp_acf_set_language', 100);
        unset($paths[0]);
        $paths[] = get_stylesheet_directory() . '/acf';
        remove_filter('acf/settings/current_language', 'wp_acf_set_language', 100);
        return $paths;
    }

    public static function cc_mime_types($mimes)
    {
        $mimes['svg'] = 'image/svg+xml';
        $mimes['ico'] = 'image/x-icon';
        return $mimes;
    }

    public static function disable_as3cf_gzip_mime_types($mime_types, $media_library): array
    {
        return array();
    }

    public static function as3cf_update_attachment_metadata($data, $id)
    {
        if (get_post_mime_type($id) != 'image/svg+xml') return $data;
        $data['sizes'] = [
            'large' => [
                'width' => 0,
                'height' => 0,
                'file' => basename(get_attached_file($id))
            ]
        ];
        return $data;
    }

    public static function theme_support(): void
    {
        add_theme_support('post-thumbnails');
        add_theme_support('title-tag');
        add_theme_support('menus');
    }

    public static function theme_filter_query($query, $error = true): void
    {
        if (is_search()) {
            $query->is_search = false;
            $query->query_vars['s'] = false;
            $query->query['s'] = false;
            if (true == $error) {
                $query->is_404 = true;
            }
        }
    }

    public static function search_form($a)
    {
        return null;
    }

    public static function theme_remove_search_widget(): void
    {
        unregister_widget('WP_Widget_Search');
    }

    public static function wpcf7_remove_assets(): void
    {
        add_filter('wpcf7_load_js', '__return_false');
        add_filter('wpcf7_load_css', '__return_false');

        add_filter('wpcf7cf_load_js', '__return_false');
        add_filter('wpcf7cf_load_css', '__return_false');
    }

    public static function wpcf7_add_assets($atts)
    {
        wpcf7_enqueue_styles();
        wpcf7_enqueue_scripts();

        return $atts;
    }

    public static function enqueue_assets(): void
    {
        wp_enqueue_style('main-styles', get_template_directory_uri() . '/assets/styles/styles.min.css', array(), ASSETS_VERSION);
        wp_enqueue_script('main-scripts', get_template_directory_uri() . '/assets/js/scripts.min.js', array(), ASSETS_VERSION, true);

        if(defined('API_PATH')) {
            wp_localize_script('main-scripts', 'API', array(
                'path' => API_PATH
            ));
        }
    }

    public static function dequeue_assets(): void
    {
        wp_dequeue_style('wp-block-library');
        wp_dequeue_style('wpml-legacy-dropdown-0');
        wp_dequeue_style('wpml-tm-admin-bar');
        wp_dequeue_style('global-styles');
    }

    public static function admin_assets(): void
    {
        wp_enqueue_style('admin-styles', get_template_directory_uri() . '/assets/styles/admin.min.css', array(), ASSETS_VERSION);
    }

    public static function wp_admin_redirect(): void
    {
        status_header(404);
        nocache_headers();
        include(get_query_template('404'));
        die();
    }

    public static function rxpp_remove_xmlrpc_pingback_ping($methods)
    {
        unset($methods['pingback.ping']);
        $rxpp_blocked_methods_count = get_option('rxpp_blocked_methods_count', 0);
        $rxpp_blocked_methods_count++;
        update_option('rxpp_blocked_methods_count', $rxpp_blocked_methods_count, false);
        return $methods;
    }

    public static function close_rest_api_routes($result, $rest_server, $request)
    {
        if (!is_null($result)) {
            return $result;
        }

        if ('/wp/v2' === substr($request->get_route(), 0, 6) && !current_user_can('edit_others_posts')) {
            return new WP_Error('rest_not_logged_in', 'Your capability is low.', ['status' => 401]);
        }

        return $result;
    }

    public static function update_db(): void
    {
        if (is_user_logged_in()) {
            global $wpdb;

            $character_set_database = $wpdb->get_results('SHOW VARIABLES LIKE "character\_set\_database"', OBJECT);
            $collation_database = $wpdb->get_results('SHOW VARIABLES LIKE "collation\_database"', OBJECT);
            if ($character_set_database[0]->Value != 'utf8mb4' || $collation_database[0]->Value != 'utf8mb4_unicode_ci')
                $wpdb->query('ALTER DATABASE `' . $wpdb->dbname . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;', OBJECT);

            $table_status = $wpdb->get_results('SHOW TABLE STATUS', OBJECT);
            foreach ($table_status as $table) {
                if ($table->Collation != 'utf8mb4_unicode_ci') {
                    $wpdb->query('ALTER TABLE `' . $table->Name . '` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;', OBJECT);
                }
            }

            if (wp_get_environment_type() === 'local') {
                $last_update_db = get_option('last_update_db');
                if ($last_update_db) {
                    if (time() - $last_update_db > WEEK_IN_SECONDS) {
                        add_action('admin_notices', 'show_notice_update_db');
                        function show_notice_update_db()
                        {
                            echo '<div class="error" style="padding:15px"><b>База данных с production обновлялась более 7 дней назад</b></div>';
                        }
                    }
                } else {
                    add_option('last_update_db', time());
                }

                $clean_tables = array(
                    $wpdb->prefix . 'aiowps_failed_logins',
                    $wpdb->prefix . 'aiowps_login_lockdown',
                    $wpdb->prefix . 'wsal_metadata',
                    $wpdb->prefix . 'wsal_occurrences'
                );

                foreach ($table_status as $table) {
                    if(in_array($table->Name, $clean_tables))
                        $wpdb->query('TRUNCATE `' . $table->Name . '`');
                }
            } else {
                delete_option('last_update_db');
            }
        }
    }
}

General::instance();
