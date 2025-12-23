<?php
/**
 * Plugin Name: Snow Fall Effect
 * Plugin URI: https://yourwebsite.com/
 * Description: Adds a beautiful snowfall effect to your WordPress site
 * Version: 2.0.0
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
        
        // Add inline styles for critical properties
        add_action('wp_head', array($this, 'add_critical_styles'), 99);
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Only enqueue on front-end
        if (is_admin() || !$this->should_show_snow()) {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style(
            'snow-fall-effect-css',
            plugin_dir_url(__FILE__) . 'css/snow-fall-effect.css',
            array(),
            '2.0.0'
        );
        
        // Enqueue JavaScript - NO jQuery dependency
        wp_enqueue_script(
            'snow-fall-effect-js',
            plugin_dir_url(__FILE__) . 'js/snow-fall-effect.js',
            array(), // No dependencies
            '2.0.0',
            array(
                'strategy'  => 'defer',
                'in_footer' => true
            )
        );
        
        // Pass settings to JavaScript
        $settings = $this->get_settings();
        wp_localize_script('snow-fall-effect-js', 'snowSettings', $settings);
    }
    
    /**
     * Add critical inline styles
     */
    public function add_critical_styles() {
        if (!$this->should_show_snow()) {
            return;
        }
        
        $zindex = get_option('snow_zindex', '20000');
        ?>
        <style id="snow-critical-styles">
            /* Critical styles that must load first */
            #snow-container {
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                pointer-events: none !important;
                z-index: <?php echo (int) $zindex; ?> !important;
                overflow: hidden !important;
            }
            
            .snowflake {
                position: absolute !important;
                user-select: none !important;
                pointer-events: none !important;
                z-index: <?php echo (int) $zindex; ?> !important;
                display: block !important;
                visibility: visible !important;
            }
        </style>
        <?php
    }
    
    /**
     * Get all settings with defaults
     */
    private function get_settings() {
        return array(
            'snowflakeCount' => (int) get_option('snow_flake_count', 60),
            'snowflakeSpeed' => (float) get_option('snow_speed', 1),
            'snowflakeSize'  => (int) get_option('snow_size', 8), // Increased default size
            'snowflakeColor' => sanitize_hex_color(get_option('snow_color', '#ffffff')),
            'snowflakeCharacter' => sanitize_text_field(get_option('snow_character', '❄')),
            'enableOnMobile' => get_option('snow_mobile_enable', 'no'),
            'onlyWinter' => get_option('snow_only_winter', 'no'),
            'zIndex' => (int) get_option('snow_zindex', '20000'),
            'enableOnAllPages' => get_option('snow_enable_all_pages', 'yes'),
            'excludedPages' => get_option('snow_excluded_pages', ''),
            'snowEnabled' => $this->should_show_snow() ? 'yes' : 'no'
        );
    }
    
    /**
     * Render snow effect in footer
     */
    public function render_snow_effect() {
        // Check if we should show snow
        if (!$this->should_show_snow()) {
            return;
        }
        
        // Create snow container with inline styles as backup
        $zindex = (int) get_option('snow_zindex', '20000');
        ?>
        <div id="snow-container" class="snow-container" 
             style="position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:<?php echo $zindex; ?>;overflow:hidden;"></div>
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
        
        // Check if explicitly enabled via URL
        if (isset($_GET['snow']) && $_GET['snow'] === 'on') {
            return true;
        }
        
        // Check mobile setting
        $enableOnMobile = get_option('snow_mobile_enable', 'no');
        if (wp_is_mobile() && $enableOnMobile !== 'yes') {
            return false;
        }
        
        // Check winter months setting
        $onlyWinter = get_option('snow_only_winter', 'no');
        if ($onlyWinter === 'yes') {
            $currentMonth = (int) date('n');
            // Only show in December, January, February
            if (!in_array($currentMonth, [12, 1, 2])) {
                return false;
            }
        }
        
        // Check excluded pages
        $excludedPages = get_option('snow_excluded_pages', '');
        if (!empty($excludedPages)) {
            $excluded_array = array_map('trim', explode(',', $excludedPages));
            $current_page_id = get_queried_object_id();
            
            if (in_array($current_page_id, $excluded_array)) {
                return false;
            }
        }
        
        // Check specific pages setting
        $enableOnAllPages = get_option('snow_enable_all_pages', 'yes');
        if ($enableOnAllPages === 'no') {
            $allowed_pages = array_map('trim', explode(',', get_option('snow_allowed_pages', '')));
            $current_page_id = get_queried_object_id();
            
            if (!in_array($current_page_id, $allowed_pages)) {
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
        
        // Show success message
        if (isset($_GET['settings-updated'])) {
            add_settings_error(
                'snow_fall_effect_messages',
                'snow_fall_effect_message',
                'Settings Saved!',
                'updated'
            );
        }
        
        // Show error messages
        settings_errors('snow_fall_effect_messages');
        ?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <p>Configure the snowfall effect on your website. For best visibility, use larger sizes and contrasting colors.</p>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('snow_fall_effect');
                do_settings_sections('snow-fall-effect');
                submit_button('Save Settings');
                ?>
            </form>
            
            <div class="snow-debug-info" style="margin-top: 40px; padding: 20px; background: #f5f5f5; border-radius: 5px; border-left: 4px solid #2271b1;">
                <h3>Troubleshooting & Testing</h3>
                
                <h4>Quick Tests:</h4>
                <ul>
                    <li><a href="<?php echo home_url('/?snow=on'); ?>" target="_blank">Force enable snow on current page</a></li>
                    <li><a href="<?php echo home_url('/?snow=off'); ?>" target="_blank">Force disable snow on current page</a></li>
                    <li><a href="<?php echo home_url('/'); ?>" target="_blank">View home page with normal settings</a></li>
                </ul>
                
                <h4>Recommended Settings for Visibility:</h4>
                <ol>
                    <li><strong>Snowflake Size:</strong> 8-15 pixels</li>
                    <li><strong>Snowflake Color:</strong> #000000 (black) or #0055ff (blue) for light backgrounds</li>
                    <li><strong>Number of Snowflakes:</strong> 30-60 (fewer = better performance)</li>
                    <li><strong>Z-Index:</strong> 20000 or higher</li>
                </ol>
                
                <h4>If snow doesn't appear:</h4>
                <ol>
                    <li>Check browser console (F12 → Console) for errors</li>
                    <li>Try a different browser</li>
                    <li>Temporarily disable other plugins to check for conflicts</li>
                    <li>Use the test links above to verify the plugin is working</li>
                </ol>
                
                <h4>Current Configuration:</h4>
                <pre style="background: white; padding: 10px; border: 1px solid #ddd; overflow: auto;">
<?php 
$settings = $this->get_settings();
foreach ($settings as $key => $value) {
    echo htmlspecialchars("$key: " . print_r($value, true)) . "\n";
}
?>
                </pre>
            </div>
            
            <div class="snow-preview-area" style="margin-top: 30px;">
                <h3>Live Preview</h3>
                <p>The snow effect will be visible on your site's front-end. Below is a static preview of how snowflakes might look:</p>
                <div style="height: 200px; border: 1px solid #ddd; background: #1a1a1a; position: relative; overflow: hidden; border-radius: 5px;">
                    <div style="position: absolute; top: 10px; left: 20%; color: white; font-size: 24px; opacity: 0.8;">❄</div>
                    <div style="position: absolute; top: 50px; left: 60%; color: white; font-size: 18px; opacity: 0.6;">❄</div>
                    <div style="position: absolute; top: 100px; left: 40%; color: white; font-size: 32px; opacity: 0.9;">❄</div>
                    <div style="position: absolute; top: 150px; left: 80%; color: white; font-size: 14px; opacity: 0.5;">❄</div>
                    <div style="position: absolute; top: 30px; left: 10%; color: white; font-size: 20px; opacity: 0.7;">❄</div>
                </div>
                <p><small>Note: This is a static preview. The actual effect includes animation and many more snowflakes.</small></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Register settings
     */
    public function register_settings() {
        // Register settings with sanitization
        $settings = array(
            'snow_flake_count' => array('absint', 60),
            'snow_speed' => array('floatval', 1.0),
            'snow_size' => array('absint', 8),
            'snow_color' => array('sanitize_hex_color', '#ffffff'),
            'snow_character' => array('sanitize_text_field', '❄'),
            'snow_mobile_enable' => array(array($this, 'sanitize_checkbox'), 'no'),
            'snow_only_winter' => array(array($this, 'sanitize_checkbox'), 'no'),
            'snow_zindex' => array('absint', 20000),
            'snow_enable_all_pages' => array(array($this, 'sanitize_checkbox'), 'yes'),
            'snow_excluded_pages' => array('sanitize_text_field', ''),
            'snow_allowed_pages' => array('sanitize_text_field', '')
        );
        
        foreach ($settings as $option => $sanitize) {
            register_setting('snow_fall_effect', $option, array(
                'sanitize_callback' => $sanitize[0],
                'default' => $sanitize[1]
            ));
        }
        
        // Add settings section
        add_settings_section(
            'snow_fall_effect_section',
            'Snow Effect Configuration',
            array($this, 'section_callback'),
            'snow-fall-effect'
        );
        
        // Add settings fields
        $this->add_settings_field('snow_flake_count', 'Number of Snowflakes', 'number', '60', 'Number of snowflakes to display (1-200). Fewer = better performance.');
        $this->add_settings_field('snow_speed', 'Snowfall Speed', 'range', '1', 'Speed of snowfall (0.1 = slow, 5 = very fast)', 0.1, 5, 0.1);
        $this->add_settings_field('snow_size', 'Snowflake Size', 'range', '8', 'Size of snowflakes in pixels (1-30). Larger = more visible.', 1, 30, 1);
        $this->add_settings_field('snow_color', 'Snowflake Color', 'color', '#ffffff', 'Color of snowflakes. Use dark colors for light backgrounds.');
        $this->add_settings_field('snow_character', 'Snowflake Character', 'text', '❄', 'Character to use as snowflake (emoji or text like * or •)');
        $this->add_settings_field('snow_mobile_enable', 'Enable on Mobile', 'checkbox', 'no', 'Show snow effect on mobile devices (may affect performance)');
        $this->add_settings_field('snow_only_winter', 'Winter Months Only', 'checkbox', 'no', 'Show snow only in December, January, February');
        $this->add_settings_field('snow_zindex', 'Z-Index', 'number', '20000', 'CSS z-index value (higher values appear on top). Start with 20000.', 1, 999999);
        $this->add_settings_field('snow_enable_all_pages', 'Enable on All Pages', 'checkbox', 'yes', 'Show snow on all pages by default');
        $this->add_settings_field('snow_excluded_pages', 'Excluded Page IDs', 'text', '', 'Comma-separated page/post IDs to exclude snow effect');
        $this->add_settings_field('snow_allowed_pages', 'Allowed Page IDs', 'text', '', 'If "Enable on All Pages" is NO, list allowed page IDs here (comma-separated)');
    }
    
    /**
     * Section callback
     */
    public function section_callback() {
        echo '<p>Configure the snowfall effect below. Changes will be visible immediately on your site.</p>';
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
                        printf(
                            '<input type="number" id="%1$s" name="%1$s" value="%2$s" class="regular-text" min="%3$s" max="%4$s">',
                            esc_attr($id),
                            esc_attr($value),
                            esc_attr($min ?: '1'),
                            esc_attr($max ?: '999999')
                        );
                        break;
                        
                    case 'range':
                        printf(
                            '<input type="range" id="%1$s" name="%1$s" value="%2$s" min="%3$s" max="%4$s" step="%5$s" style="width: 300px;" oninput="document.getElementById(\'%1$s-value\').textContent = this.value">',
                            esc_attr($id),
                            esc_attr($value),
                            esc_attr($min ?: '0.1'),
                            esc_attr($max ?: '5'),
                            esc_attr($step ?: '0.1')
                        );
                        echo ' <span id="' . esc_attr($id) . '-value">' . esc_html($value) . '</span>';
                        break;
                        
                    case 'color':
                        printf(
                            '<input type="color" id="%1$s" name="%1$s" value="%2$s">',
                            esc_attr($id),
                            esc_attr($value)
                        );
                        break;
                        
                    case 'checkbox':
                        printf(
                            '<input type="checkbox" id="%1$s" name="%1$s" value="yes" %2$s>',
                            esc_attr($id),
                            checked($value, 'yes', false)
                        );
                        break;
                        
                    case 'text':
                    default:
                        printf(
                            '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text">',
                            esc_attr($id),
                            esc_attr($value)
                        );
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

// Activation hook
register_activation_hook(__FILE__, function() {
    // Set default options if not exists
    $defaults = array(
        'snow_flake_count' => 60,
        'snow_speed' => 1.0,
        'snow_size' => 8,
        'snow_color' => '#ffffff',
        'snow_character' => '❄',
        'snow_mobile_enable' => 'no',
        'snow_only_winter' => 'no',
        'snow_zindex' => 20000,
        'snow_enable_all_pages' => 'yes',
        'snow_excluded_pages' => '',
        'snow_allowed_pages' => ''
    );
    
    foreach ($defaults as $option => $value) {
        if (get_option($option) === false) {
            add_option($option, $value);
        }
    }
});

// Deactivation hook
register_deactivation_hook(__FILE__, function() {
    // Clean up if needed
});
