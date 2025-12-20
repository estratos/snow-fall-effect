<?php
/**
 * Plugin Name: Snow Fall Effect
 * Plugin URI: https://yourwebsite.com/
 * Description: Adds a beautiful snowfall effect to your WordPress site
 * Version: 1.0.1
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: snow-fall-effect
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SnowFallEffect {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hook into WordPress
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('admin_menu', array($this, 'create_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
        
        // Add snow effect to footer
        add_action('wp_footer', array($this, 'render_snow_effect'), 100);
        
        // Add inline styles after theme styles
        add_action('wp_head', array($this, 'add_inline_styles'), 100);
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Only enqueue on front-end
        if (is_admin()) {
            return;
        }
        
        // Enqueue CSS with late priority
        wp_enqueue_style(
            'snow-fall-effect-css',
            plugin_dir_url(__FILE__) . 'css/snow-fall-effect.css',
            array(),
            '1.0.1'
        );
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'snow-fall-effect-js',
            plugin_dir_url(__FILE__) . 'js/snow-fall-effect.js',
            array(),
            '1.0.1',
            true
        );
        
        // Pass settings to JavaScript
        $settings = $this->get_settings();
        wp_localize_script('snow-fall-effect-js', 'snowSettings', $settings);
    }
    
    /**
     * Get all settings with defaults
     */
    private function get_settings() {
        return array(
            'snowflakeCount' => get_option('snow_flake_count', 80),
            'snowflakeSpeed' => get_option('snow_speed', 1),
            'snowflakeSize' => get_option('snow_size', 2),
            'snowflakeColor' => get_option('snow_color', '#ffffff'),
            'snowflakeCharacter' => get_option('snow_character', '❄'),
            'enableOnMobile' => get_option('snow_mobile_enable', 'no'),
            'onlyWinter' => get_option('snow_only_winter', 'no'),
            'zIndex' => get_option('snow_zindex', '12000'),
            'enableOnAllPages' => get_option('snow_enable_all_pages', 'yes'),
            'excludedPages' => get_option('snow_excluded_pages', ''),
            'snowEnabled' => $this->should_show_snow() ? 'yes' : 'no'
        );
    }
    
    /**
     * Add inline styles
     */
    public function add_inline_styles() {
        if (!$this->should_show_snow()) {
            return;
        }
        
        $zindex = get_option('snow_zindex', '12000');
        ?>
        <style id="snow-inline-styles">
            #snow-container {
                z-index: <?php echo esc_attr($zindex); ?> !important;
            }
            .snowflake {
                z-index: <?php echo esc_attr($zindex); ?> !important;
            }
        </style>
        <?php
    }
    
    /**
     * Render snow effect in footer
     */
    public function render_snow_effect() {
        // Check if we should show snow
        if (!$this->should_show_snow()) {
            return;
        }
        
        // Create snow container
        ?>
        <div id="snow-container" class="snow-container" style="z-index: <?php echo esc_attr(get_option('snow_zindex', '12000')); ?>;"></div>
        <?php
    }
    
    /**
     * Check if snow should be displayed
     */
    private function should_show_snow() {
        // Don't show in admin
        if (is_admin()) {
            return false;
        }
        
        // Check if user disabled via URL parameter
        if (isset($_GET['snow']) && $_GET['snow'] === 'off') {
            return false;
        }
        
        // Check mobile setting
        $enableOnMobile = get_option('snow_mobile_enable', 'no');
        if (wp_is_mobile() && $enableOnMobile !== 'yes') {
            return false;
        }
        
        // Check winter months setting
        $onlyWinter = get_option('snow_only_winter', 'no');
        if ($onlyWinter === 'yes') {
            $currentMonth = date('n');
            // Only show in December, January, February
            if (!in_array($currentMonth, [12, 1, 2])) {
                return false;
            }
        }
        
        // Check excluded pages
        $enableOnAllPages = get_option('snow_enable_all_pages', 'yes');
        $excludedPages = get_option('snow_excluded_pages', '');
        
        if ($enableOnAllPages === 'no') {
            // Only show on specific pages
            $current_page_id = get_queried_object_id();
            $allowed_pages = array_map('trim', explode(',', get_option('snow_allowed_pages', '')));
            
            if (!in_array($current_page_id, $allowed_pages)) {
                return false;
            }
        } elseif (!empty($excludedPages)) {
            // Check if current page is excluded
            $excluded_array = array_map('trim', explode(',', $excludedPages));
            $current_page_id = get_queried_object_id();
            
            if (in_array($current_page_id, $excluded_array)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Create settings page in admin
     */
    public function create_settings_page() {
        add_options_page(
            'Snow Fall Effect Settings',
            'Snow Fall Effect',
            'manage_options',
            'snow-fall-effect',
            array($this, 'settings_page_html')
        );
    }
    
    /**
     * Settings page HTML
     */
    public function settings_page_html() {
        // Check user capabilities
        if (!current_user_can('manage_options')) {
            return;
        }
        
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            
            <?php if (isset($_GET['settings-updated'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p>Settings saved successfully!</p>
                </div>
            <?php endif; ?>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('snow_fall_effect');
                do_settings_sections('snow-fall-effect');
                submit_button('Save Settings');
                ?>
            </form>
            
            <div class="snow-preview" style="margin-top: 30px; padding: 20px; background: #1e1e1e; border-radius: 5px;">
                <h3>Troubleshooting</h3>
                <ul>
                    <li>If snow doesn't appear, check if your theme has a high z-index on elements.</li>
                    <li>Try increasing the z-index value above 12000.</li>
                    <li>Check browser console for JavaScript errors (F12 → Console).</li>
                    <li>Make sure no other plugins are conflicting.</li>
                </ul>
                
                <h3>Quick Test</h3>
                <p>Visit your site with these URLs to test:</p>
                <ul>
                    <li><a href="<?php echo home_url('/?snow=off'); ?>" target="_blank"><?php echo home_url('/?snow=off'); ?></a> - Disable snow</li>
                    <li><a href="<?php echo home_url('/'); ?>" target="_blank"><?php echo home_url('/'); ?></a> - Normal view</li>
                </ul>
            </div>
        </div>
        <?php
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Register settings
        register_setting('snow_fall_effect', 'snow_flake_count', array(
            'sanitize_callback' => 'absint',
            'default' => 80
        ));
        register_setting('snow_fall_effect', 'snow_speed', array(
            'sanitize_callback' => 'floatval',
            'default' => 1
        ));
        register_setting('snow_fall_effect', 'snow_size', array(
            'sanitize_callback' => 'absint',
            'default' => 2
        ));
        register_setting('snow_fall_effect', 'snow_color', array(
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#ffffff'
        ));
        register_setting('snow_fall_effect', 'snow_character', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '❄'
        ));
        register_setting('snow_fall_effect', 'snow_mobile_enable', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => 'no'
        ));
        register_setting('snow_fall_effect', 'snow_only_winter', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => 'no'
        ));
        register_setting('snow_fall_effect', 'snow_zindex', array(
            'sanitize_callback' => 'absint',
            'default' => 12000
        ));
        register_setting('snow_fall_effect', 'snow_enable_all_pages', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => 'yes'
        ));
        register_setting('snow_fall_effect', 'snow_excluded_pages', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        register_setting('snow_fall_effect', 'snow_allowed_pages', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        
        // Add settings section
        add_settings_section(
            'snow_fall_effect_section',
            'Snow Effect Configuration',
            array($this, 'section_callback'),
            'snow-fall-effect'
        );
        
        // Add settings fields
        $this->add_settings_field('snow_flake_count', 'Number of Snowflakes', 'number', '80', 'Number of snowflakes to display (1-500)');
        $this->add_settings_field('snow_speed', 'Snowfall Speed', 'range', '1', 'Speed of snowfall (0.1 - 5)', 0.1, 5, 0.1);
        $this->add_settings_field('snow_size', 'Snowflake Size', 'range', '2', 'Size of snowflakes in pixels (1-10)', 1, 10, 1);
        $this->add_settings_field('snow_color', 'Snowflake Color', 'color', '#ffffff', 'Color of snowflakes');
        $this->add_settings_field('snow_character', 'Snowflake Character', 'text', '❄', 'Character to use as snowflake (emoji or text)');
        $this->add_settings_field('snow_mobile_enable', 'Enable on Mobile', 'checkbox', 'no', 'Show snow effect on mobile devices');
        $this->add_settings_field('snow_only_winter', 'Winter Months Only', 'checkbox', 'no', 'Show snow only in December, January, February');
        $this->add_settings_field('snow_zindex', 'Z-Index', 'number', '12000', 'CSS z-index value (higher values appear on top) - Start with 12000');
        $this->add_settings_field('snow_enable_all_pages', 'Enable on All Pages', 'checkbox', 'yes', 'Show snow on all pages by default');
        $this->add_settings_field('snow_excluded_pages', 'Excluded Page IDs', 'text', '', 'Comma-separated page IDs to exclude snow effect');
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>Configure the snowfall effect. The default z-index is 12000. If snow doesn\'t appear, try increasing this value.</p>';
    }
    
    /**
     * Helper to add settings fields
     */
    private function add_settings_field($id, $title, $type, $default, $description = '', $min = null, $max = null, $step = null) {
        add_settings_field(
            $id,
            $title,
            function() use ($id, $type, $default, $description, $min, $max, $step) {
                $value = get_option($id, $default);
                
                switch ($type) {
                    case 'number':
                        echo '<input type="number" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text"';
                        if ($min !== null) echo ' min="' . esc_attr($min) . '"';
                        if ($max !== null) echo ' max="' . esc_attr($max) . '"';
                        if ($step !== null) echo ' step="' . esc_attr($step) . '"';
                        echo '>';
                        break;
                        
                    case 'range':
                        echo '<input type="range" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value) . '"';
                        if ($min !== null) echo ' min="' . esc_attr($min) . '"';
                        if ($max !== null) echo ' max="' . esc_attr($max) . '"';
                        if ($step !== null) echo ' step="' . esc_attr($step) . '"';
                        echo ' style="width: 300px;">';
                        echo ' <span id="' . esc_attr($id) . '_value">' . esc_html($value) . '</span>';
                        echo '<script>document.getElementById("' . esc_js($id) . '").addEventListener("input", function(e) { document.getElementById("' . esc_js($id) . '_value").textContent = e.target.value; });</script>';
                        break;
                        
                    case 'color':
                        echo '<input type="color" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value) . '">';
                        break;
                        
                    case 'checkbox':
                        echo '<input type="checkbox" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="yes"' . checked($value, 'yes', false) . '>';
                        break;
                        
                    case 'text':
                    default:
                        echo '<input type="text" id="' . esc_attr($id) . '" name="' . esc_attr($id) . '" value="' . esc_attr($value) . '" class="regular-text">';
                        break;
                }
                
                if ($description) {
                    echo '<p class="description">' . esc_html($description) . '</p>';
                }
            },
            'snow-fall-effect',
            'snow_fall_effect_section'
        );
    }
    
    /**
     * Sanitize checkbox
     */
    public function sanitize_checkbox($input) {
        return $input === 'yes' ? 'yes' : 'no';
    }
    
    /**
     * Add settings link on plugins page
     */
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=snow-fall-effect') . '">' . __('Settings') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

// Initialize the plugin
SnowFallEffect::get_instance();
