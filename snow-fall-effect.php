<?php
/**
 * Plugin Name: Snow Fall Effect - Nuclear Version
 * Plugin URI: https://yourwebsite.com/
 * Description: Adds a snowfall effect that works with ANY theme including Elementor
 * Version: 3.0.0
 * Author: Your Name
 * License: GPL v2 or later
 * Text Domain: snow-fall-effect-nuclear
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

class SnowFallEffectNuclear {
    
    private static $instance = null;
    
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        // Hook into WordPress - ALTA PRIORIDAD
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts_nuclear'), 999999);
        add_action('admin_menu', array($this, 'create_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), array($this, 'add_settings_link'));
        
        // Add snow effect - AL FINAL DE TODO
        add_action('wp_footer', array($this, 'render_snow_effect_nuclear'), 999999);
        
        // Add NUCLEAR CSS inline - AL PRINCIPIO DE TODO
        add_action('wp_head', array($this, 'add_nuclear_css'), 1);
        
        // Add NUCLEAR JS inline - después del body
        add_action('wp_body_open', array($this, 'add_nuclear_js'), 1);
    }
    
    /**
     * Enqueue scripts and styles - NUCLEAR VERSION
     */
    public function enqueue_scripts_nuclear() {
        // Only enqueue on front-end
        if (is_admin() || !$this->should_show_snow()) {
            return;
        }
        
        // NO usaremos CSS externo - TODO va inline
        // NO usaremos JS externo - TODO va inline
        
        // Solo pasar configuración
        wp_localize_script('jquery', 'snowSettingsNuclear', $this->get_settings_nuclear());
    }
    
    /**
     * Add NUCLEAR CSS inline - IMPOSIBLE DE ANULAR
     */
    public function add_nuclear_css() {
        if (!$this->should_show_snow()) {
            return;
        }
        
        $zindex = get_option('snow_zindex', '999999');
        $color = get_option('snow_color', '#ff0000');
        $size = get_option('snow_size', '20');
        
        ?>
        <style id="snow-nuclear-css" data-snow-nuclear="true">
/* === SNOW NUCLEAR CSS - IMPOSIBLE DE ANULAR === */
/* RESET ABSOLUTO */
#snow-container-nuclear,
#snow-container-nuclear * {
    all: unset !important;
    box-sizing: border-box !important;
}

/* CONTENEDOR NUCLEAR */
#snow-container-nuclear {
    /* POSICIÓN Y TAMAÑO */
    position: fixed !important;
    top: 0px !important;
    left: 0px !important;
    width: 100vw !important;
    height: 100vh !important;
    margin: 0 !important;
    padding: 0 !important;
    border: none !important;
    
    /* CAPAS */
    z-index: <?php echo (int)$zindex; ?> !important;
    pointer-events: none !important;
    
    /* VISIBILIDAD */
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    
    /* OVERFLOW */
    overflow: visible !important;
    clip: auto !important;
    clip-path: none !important;
    
    /* TRANSFORM */
    transform: none !important;
    transform-style: flat !important;
    
    /* BACKGROUND */
    background: transparent !important;
    background-color: transparent !important;
    
    /* ANIMATION */
    animation: none !important;
    
    /* FILTERS */
    filter: none !important;
    backdrop-filter: none !important;
    
    /* MISC */
    will-change: unset !important;
    contain: none !important;
    isolation: auto !important;
}

/* COPOS DE NIEVE NUCLEARES */
.snowflake-nuclear {
    /* POSICIÓN */
    position: absolute !important;
    
    /* TEXTO */
    color: <?php echo esc_attr($color); ?> !important;
    font-size: <?php echo (int)$size; ?>px !important;
    font-weight: normal !important;
    font-family: Arial, sans-serif !important;
    line-height: 1 !important;
    text-align: center !important;
    text-decoration: none !important;
    text-shadow: 0 0 10px rgba(255,255,255,0.8) !important;
    text-transform: none !important;
    
    /* VISIBILIDAD */
    display: block !important;
    visibility: visible !important;
    opacity: 0.9 !important;
    
    /* CAPAS */
    z-index: <?php echo (int)$zindex; ?> !important;
    
    /* INTERACCIÓN */
    user-select: none !important;
    pointer-events: none !important;
    
    /* TAMAÑO */
    width: auto !important;
    height: auto !important;
    min-width: 0 !important;
    min-height: 0 !important;
    max-width: none !important;
    max-height: none !important;
    
    /* MARGEN Y PADDING */
    margin: 0 !important;
    padding: 0 !important;
    
    /* BORDE Y FONDO */
    border: none !important;
    border-radius: 0 !important;
    outline: none !important;
    background: transparent !important;
    background-color: transparent !important;
    
    /* TRANSFORM */
    transform-origin: center !important;
    transform-style: flat !important;
    
    /* ANIMACIONES BASE */
    animation-timing-function: linear !important;
    animation-iteration-count: infinite !important;
    animation-play-state: running !important;
    
    /* FILTERS */
    filter: none !important;
    
    /* MISC */
    will-change: transform, opacity !important;
    contain: none !important;
    content: "❄" !important;
}

/* KEYFRAMES NUCLEARES - NO DEPENDEN DE CLASES EXTERNAS */
@keyframes snowFallNuclear {
    0% {
        transform: translateY(-100px) translateX(0px) rotate(0deg) !important;
        opacity: 0 !important;
    }
    10% {
        opacity: 1 !important;
    }
    90% {
        opacity: 1 !important;
    }
    100% {
        transform: translateY(calc(100vh + 100px)) translateX(var(--sway-x, 100px)) rotate(360deg) !important;
        opacity: 0 !important;
    }
}

@keyframes snowSwayNuclear {
    0%, 100% {
        transform: translateX(0px) !important;
    }
    50% {
        transform: translateX(var(--sway-amount, 100px)) !important;
    }
}

/* ANULAR CUALQUIER ESTILO DE TEMA/PLUGIN QUE AFECTE A NUESTRA NIEVE */
*:not(#snow-container-nuclear):not(.snowflake-nuclear) {
    overflow: visible !important;
    position: relative !important;
    z-index: auto !important;
}

body, html {
    overflow-x: visible !important;
    overflow-y: visible !important;
    position: static !important;
}

/* MEDIA QUERIES NUCLEARES */
@media (max-width: 768px) {
    .snowflake-nuclear {
        font-size: <?php echo max(10, (int)$size - 5); ?>px !important;
    }
}

@media print {
    #snow-container-nuclear,
    .snowflake-nuclear {
        display: none !important;
        visibility: hidden !important;
        opacity: 0 !important;
    }
}
        </style>
        <?php
    }
    
    /**
     * Add NUCLEAR JS inline
     */
    public function add_nuclear_js() {
        if (!$this->should_show_snow() || is_admin()) {
            return;
        }
        
        $settings = $this->get_settings_nuclear();
        ?>
        <script id="snow-nuclear-js" data-snow-nuclear="true">
        // === SNOW NUCLEAR JAVASCRIPT - AUTO-EJECUTABLE ===
        (function() {
            'use strict';
            
            console.log('❄️ SNOW NUCLEAR ACTIVADO - Funciona con ANY theme');
            
            // CONFIGURACIÓN
            const config = <?php echo json_encode($settings); ?>;
            
            // ESPERAR A QUE EL DOM ESTÉ LISTO
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initSnowNuclear);
            } else {
                initSnowNuclear();
            }
            
            function initSnowNuclear() {
                console.log('🚀 Inicializando Nieve Nuclear...');
                
                // 1. ELIMINAR CUALQUIER NIEVE ANTERIOR
                const oldSnow = document.getElementById('snow-container-nuclear');
                if (oldSnow) oldSnow.remove();
                
                // 2. ELIMINAR COPOS VIEJOS
                document.querySelectorAll('.snowflake-nuclear').forEach(f => f.remove());
                
                // 3. CREAR CONTENEDOR NUCLEAR
                const container = document.createElement('div');
                container.id = 'snow-container-nuclear';
                container.setAttribute('data-snow-nuclear', 'true');
                
                // Estilos DIRECTOS en el elemento
                container.style.cssText = `
                    position: fixed !important;
                    top: 0 !important;
                    left: 0 !important;
                    width: 100vw !important;
                    height: 100vh !important;
                    pointer-events: none !important;
                    z-index: ${config.zIndex} !important;
                    overflow: visible !important;
                    display: block !important;
                    visibility: visible !important;
                    opacity: 1 !important;
                    background: transparent !important;
                    margin: 0 !important;
                    padding: 0 !important;
                    border: none !important;
                    transform: none !important;
                `;
                
                // 4. INSERTAR AL PRINCIPIO DEL BODY
                document.body.insertBefore(container, document.body.firstChild);
                console.log('✅ Contenedor Nuclear creado');
                
                // 5. CREAR COPOS NUCLEARES
                createSnowflakesNuclear(container, config);
                
                // 6. MANEJAR REDIMENSIONAMIENTO
                let resizeTimer;
                window.addEventListener('resize', () => {
                    clearTimeout(resizeTimer);
                    resizeTimer = setTimeout(() => {
                        document.querySelectorAll('.snowflake-nuclear').forEach(f => f.remove());
                        createSnowflakesNuclear(container, config);
                    }, 250);
                });
                
                // 7. DEBUG
                setTimeout(() => {
                    const flakes = document.querySelectorAll('.snowflake-nuclear');
                    console.log(`✅ ${flakes.length} copos nucleares creados`);
                    console.log('🎯 z-index:', config.zIndex);
                    console.log('🎨 color:', config.snowflakeColor);
                    console.log('📏 tamaño:', config.snowflakeSize);
                    
                    // Hacer el primer copo VERDE para verificación
                    if (flakes.length > 0) {
                        flakes[0].style.cssText += 'border: 3px solid #00ff00 !important; background: rgba(0,255,0,0.2) !important;';
                        console.log('🔍 Primer copo marcado en VERDE para verificación');
                    }
                }, 1000);
            }
            
            function createSnowflakesNuclear(container, config) {
                const count = Math.min(config.snowflakeCount, 100);
                const char = config.snowflakeCharacter || '❄';
                const color = config.snowflakeColor || '#ff0000';
                const baseSize = parseInt(config.snowflakeSize) || 20;
                const speed = parseFloat(config.snowflakeSpeed) || 1;
                const zIndex = parseInt(config.zIndex) || 999999;
                
                console.log(`Creando ${count} copos nucleares...`);
                
                for (let i = 0; i < count; i++) {
                    createSingleSnowflakeNuclear(container, i, {
                        char: char,
                        color: color,
                        baseSize: baseSize,
                        speed: speed,
                        zIndex: zIndex,
                        totalCount: count
                    });
                }
            }
            
            function createSingleSnowflakeNuclear(container, index, options) {
                const flake = document.createElement('div');
                flake.className = 'snowflake-nuclear';
                flake.setAttribute('data-snow-id', index);
                flake.textContent = options.char;
                
                // VALORES ALEATORIOS
                const left = Math.random() * 100;
                const sizeVar = 0.5 + Math.random() * 1.5;
                const fontSize = Math.max(5, options.baseSize * sizeVar);
                const duration = (4000 + Math.random() * 8000) / options.speed;
                const opacity = 0.5 + Math.random() * 0.5;
                const sway = 50 + Math.random() * 100;
                const swayDir = Math.random() > 0.5 ? 1 : -1;
                const rotation = Math.random() * 360;
                const delay = Math.random() * 5;
                
                // ESTILOS INLINE NUCLEARES - PROPIEDAD POR PROPIEDAD
                flake.style.position = 'absolute';
                flake.style.top = '-100px';
                flake.style.left = left + 'vw';
                flake.style.color = options.color;
                flake.style.fontSize = fontSize + 'px';
                flake.style.opacity = opacity;
                flake.style.zIndex = options.zIndex;
                flake.style.pointerEvents = 'none';
                flake.style.userSelect = 'none';
                flake.style.display = 'block';
                flake.style.visibility = 'visible';
                flake.style.textShadow = '0 0 10px rgba(255,255,255,0.8)';
                
                // VARIABLES CSS PARA ANIMACIONES
                flake.style.setProperty('--sway-amount', (sway * swayDir) + 'px');
                flake.style.setProperty('--sway-x', (sway * swayDir * 0.5) + 'px');
                
                // ANIMACIONES
                flake.style.animation = `
                    snowFallNuclear ${duration}ms linear ${delay}s infinite,
                    snowSwayNuclear ${duration * 2}ms ease-in-out ${delay}s infinite
                `;
                
                // TRANSFORM INICIAL
                flake.style.transform = `translateY(-100px) translateX(0) rotate(${rotation}deg)`;
                
                // AÑADIR AL CONTENEDOR
                container.appendChild(flake);
                
                // RECICLAR
                setTimeout(() => {
                    if (flake.parentNode === container) {
                        flake.remove();
                        createSingleSnowflakeNuclear(container, index, options);
                    }
                }, duration + (delay * 1000) + 1000);
            }
            
            // INICIALIZACIÓN DE EMERGENCIA
            setTimeout(() => {
                if (!document.getElementById('snow-container-nuclear')) {
                    console.warn('⚠️ Reintentando inicialización nuclear...');
                    initSnowNuclear();
                }
            }, 3000);
            
            // EXPONER PARA DEBUG
            window.SNOW_NUCLEAR = {
                version: '3.0.0',
                config: config,
                restart: initSnowNuclear,
                getInfo: () => {
                    const container = document.getElementById('snow-container-nuclear');
                    const flakes = document.querySelectorAll('.snowflake-nuclear');
                    return {
                        container: !!container,
                        flakes: flakes.length,
                        containerStyle: container ? window.getComputedStyle(container) : null,
                        sampleFlake: flakes.length > 0 ? window.getComputedStyle(flakes[0]) : null
                    };
                },
                highlight: () => {
                    document.querySelectorAll('.snowflake-nuclear').forEach(f => {
                        f.style.cssText += 'border: 2px solid #ff00ff !important; background: rgba(255,0,255,0.1) !important;';
                    });
                    console.log('🔦 Copos resaltados en magenta');
                }
            };
            
        })();
        </script>
        <?php
    }
    
    /**
     * Get settings - NUCLEAR VERSION
     */
    private function get_settings_nuclear() {
        return array(
            'snowflakeCount' => (int) get_option('snow_flake_count', 50),
            'snowflakeSpeed' => (float) get_option('snow_speed', 1),
            'snowflakeSize'  => (int) get_option('snow_size', 20), // MUY GRANDE por defecto
            'snowflakeColor' => sanitize_hex_color(get_option('snow_color', '#ff0000')), // ROJO por defecto
            'snowflakeCharacter' => sanitize_text_field(get_option('snow_character', '❄')),
            'enableOnMobile' => get_option('snow_mobile_enable', 'yes'), // Activado en móvil
            'onlyWinter' => get_option('snow_only_winter', 'no'),
            'zIndex' => (int) get_option('snow_zindex', '999999'), // Z-INDEX MÁXIMO
            'enableOnAllPages' => get_option('snow_enable_all_pages', 'yes'),
            'excludedPages' => get_option('snow_excluded_pages', ''),
            'snowEnabled' => $this->should_show_snow() ? 'yes' : 'no'
        );
    }
    
    /**
     * Render snow effect - NUCLEAR VERSION
     */
    public function render_snow_effect_nuclear() {
        // Backup container in case JavaScript fails
        if (!$this->should_show_snow()) {
            return;
        }
        
        $zindex = (int) get_option('snow_zindex', '999999');
        ?>
        <!-- Snow Nuclear Backup Container -->
        <div id="snow-backup-container" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:<?php echo $zindex; ?>;"></div>
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
        
        // URL override has highest priority
        if (isset($_GET['snow'])) {
            return $_GET['snow'] === 'on' || $_GET['snow'] === '1';
        }
        
        // Check mobile setting
        $enableOnMobile = get_option('snow_mobile_enable', 'yes');
        if (wp_is_mobile() && $enableOnMobile !== 'yes') {
            return false;
        }
        
        // Check winter months
        $onlyWinter = get_option('snow_only_winter', 'no');
        if ($onlyWinter === 'yes') {
            $currentMonth = (int) date('n');
            if (!in_array($currentMonth, [12, 1, 2])) {
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
            'Snow Fall Effect - Nuclear',
            'Snow Nuclear',
            'manage_options',
            'snow-fall-effect-nuclear',
            array($this, 'settings_page_html_nuclear')
        );
    }
    
    /**
     * Settings page HTML - NUCLEAR VERSION
     */
    public function settings_page_html_nuclear() {
        if (!current_user_can('manage_options')) {
            return;
        }
        ?>
        <div class="wrap">
            <h1>❄️ Snow Fall Effect - NUCLEAR VERSION</h1>
            
            <?php if (isset($_GET['settings-updated'])): ?>
                <div class="notice notice-success is-dismissible">
                    <p>Settings saved! The snow effect should now work on ANY theme.</p>
                </div>
            <?php endif; ?>
            
            <div class="notice notice-info">
                <p><strong>⚠️ NUCLEAR MODE ACTIVATED:</strong> This version uses aggressive CSS that overrides ALL theme styles. It should work with Elementor, Divi, Avada, and any other theme.</p>
            </div>
            
            <form action="options.php" method="post">
                <?php
                settings_fields('snow_fall_effect_nuclear');
                do_settings_sections('snow-fall-effect-nuclear');
                submit_button('Save Nuclear Settings');
                ?>
            </form>
            
            <div style="margin-top: 40px; padding: 20px; background: #1a1a1a; color: white; border-radius: 5px;">
                <h3 style="color: #ff6b6b;">🚀 NUCLEAR SETTINGS GUIDE</h3>
                
                <h4>For MAXIMUM VISIBILITY (Recommended):</h4>
                <ul>
                    <li>✅ <strong>Snowflake Size:</strong> 20-30 pixels</li>
                    <li>✅ <strong>Snowflake Color:</strong> #ff0000 (RED) or #0000ff (BLUE)</li>
                    <li>✅ <strong>Number of Snowflakes:</strong> 30-60</li>
                    <li>✅ <strong>Z-Index:</strong> 999999 (MAXIMUM)</li>
                    <li>✅ <strong>Snowflake Character:</strong> ❄ or ★ or •</li>
                </ul>
                
                <h4>Quick Test Links:</h4>
                <p>
                    <a href="<?php echo home_url('/?snow=on'); ?>" target="_blank" style="color: #4ecdc4; font-weight: bold;">🔗 FORCE ENABLE SNOW ON HOME PAGE</a> |
                    <a href="<?php echo home_url('/?snow=off'); ?>" target="_blank" style="color: #ff6b6b;">🔗 DISABLE SNOW</a>
                </p>
                
                <h4>Debug Commands (Open browser console F12):</h4>
                <pre style="background: #2d2d2d; padding: 10px; border-radius: 3px;">
// Check if snow is working
SNOW_NUCLEAR.getInfo()

// Restart snow effect
SNOW_NUCLEAR.restart()

// Highlight all snowflakes
SNOW_NUCLEAR.highlight()

// See configuration
console.log(SNOW_NUCLEAR.config)</pre>
                
                <h4>If STILL not working:</h4>
                <ol>
                    <li>Clear browser cache (Ctrl+F5)</li>
                    <li>Try in Private/Incognito mode</li>
                    <li>Disable ad-blockers temporarily</li>
                    <li>Check browser console for errors</li>
                </ol>
            </div>
            
            <div style="margin-top: 30px; border: 2px solid #4ecdc4; padding: 20px; border-radius: 5px;">
                <h3 style="color: #4ecdc4;">🎯 Live Preview Simulation</h3>
                <p>Below is how your snowflakes should look with current settings:</p>
                <div style="height: 200px; background: #2d2d2d; position: relative; overflow: hidden; border-radius: 5px;">
                    <?php
                    $color = get_option('snow_color', '#ff0000');
                    $size = get_option('snow_size', 20);
                    $char = get_option('snow_character', '❄');
                    ?>
                    <div style="position: absolute; top: 20px; left: 20%; color: <?php echo $color; ?>; font-size: <?php echo $size; ?>px; opacity: 0.9; text-shadow: 0 0 10px white;"><?php echo $char; ?></div>
                    <div style="position: absolute; top: 80px; left: 50%; color: <?php echo $color; ?>; font-size: <?php echo $size * 0.7; ?>px; opacity: 0.7; text-shadow: 0 0 8px white;"><?php echo $char; ?></div>
                    <div style="position: absolute; top: 140px; left: 70%; color: <?php echo $color; ?>; font-size: <?php echo $size * 1.3; ?>px; opacity: 0.8; text-shadow: 0 0 12px white;"><?php echo $char; ?></div>
                    <div style="position: absolute; top: 50px; left: 10%; color: <?php echo $color; ?>; font-size: <?php echo $size * 0.5; ?>px; opacity: 0.6; text-shadow: 0 0 6px white;"><?php echo $char; ?></div>
                </div>
                <p><small>Actual effect includes animation and many more snowflakes.</small></p>
            </div>
        </div>
        <?php
    }
    
    /**
     * Register settings - NUCLEAR VERSION
     */
    public function register_settings() {
        register_setting('snow_fall_effect_nuclear', 'snow_flake_count', array(
            'sanitize_callback' => 'absint',
            'default' => 50
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_speed', array(
            'sanitize_callback' => 'floatval',
            'default' => 1.0
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_size', array(
            'sanitize_callback' => 'absint',
            'default' => 20
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_color', array(
            'sanitize_callback' => 'sanitize_hex_color',
            'default' => '#ff0000'
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_character', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => '❄'
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_mobile_enable', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => 'yes'
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_only_winter', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => 'no'
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_zindex', array(
            'sanitize_callback' => 'absint',
            'default' => 999999
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_enable_all_pages', array(
            'sanitize_callback' => array($this, 'sanitize_checkbox'),
            'default' => 'yes'
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_excluded_pages', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        register_setting('snow_fall_effect_nuclear', 'snow_allowed_pages', array(
            'sanitize_callback' => 'sanitize_text_field',
            'default' => ''
        ));
        
        add_settings_section(
            'snow_fall_effect_nuclear_section',
            'Nuclear Settings (Works with ANY Theme)',
            array($this, 'section_callback_nuclear'),
            'snow-fall-effect-nuclear'
        );
        
        $this->add_settings_field_nuclear('snow_flake_count', 'Number of Snowflakes', 'number', '50', 'How many snowflakes (1-100). Fewer = better performance.');
        $this->add_settings_field_nuclear('snow_speed', 'Snowfall Speed', 'range', '1', 'Speed multiplier (0.1 = slow, 3 = fast)', 0.1, 3, 0.1);
        $this->add_settings_field_nuclear('snow_size', 'Snowflake Size', 'range', '20', 'Size in pixels (5-50). Larger = more visible!', 5, 50, 1);
        $this->add_settings_field_nuclear('snow_color', 'Snowflake Color', 'color', '#ff0000', 'Color. Use bright colors for visibility.');
        $this->add_settings_field_nuclear('snow_character', 'Snowflake Character', 'text', '❄', 'Emoji or character (try: ❄ ★ • * ❅ ❆)');
        $this->add_settings_field_nuclear('snow_mobile_enable', 'Enable on Mobile', 'checkbox', 'yes', 'Show on mobile devices');
        $this->add_settings_field_nuclear('snow_only_winter', 'Winter Months Only', 'checkbox', 'no', 'Only show in Dec, Jan, Feb');
        $this->add_settings_field_nuclear('snow_zindex', 'Z-Index', 'number', '999999', 'Layer priority (999999 = maximum)', 1, 999999);
        $this->add_settings_field_nuclear('snow_enable_all_pages', 'Enable on All Pages', 'checkbox', 'yes', 'Show on all pages');
        $this->add_settings_field_nuclear('snow_excluded_pages', 'Excluded Page IDs', 'text', '', 'Page IDs to exclude (comma-separated)');
    }
    
    public function section_callback_nuclear() {
        echo '<p>These settings use <strong>aggressive CSS</strong> that overrides ALL theme styles. Should work with Elementor, Divi, and any theme.</p>';
    }
    
    private function add_settings_field_nuclear($id, $title, $type, $default, $description = '', $min = null, $max = null, $step = null) {
        add_settings_field(
            $id,
            $title,
            function() use ($id, $type, $default, $description, $min, $max, $step) {
                $value = get_option($id, $default);
                
                $input_html = '';
                switch ($type) {
                    case 'number':
                        $input_html = sprintf(
                            '<input type="number" id="%1$s" name="%1$s" value="%2$s" class="regular-text" min="%3$s" max="%4$s" style="border: 2px solid #4ecdc4;">',
                            esc_attr($id),
                            esc_attr($value),
                            esc_attr($min ?: '1'),
                            esc_attr($max ?: '999999')
                        );
                        break;
                    case 'range':
                        $input_html = sprintf(
                            '<input type="range" id="%1$s" name="%1$s" value="%2$s" min="%3$s" max="%4$s" step="%5$s" style="width: 300px; accent-color: #ff6b6b;" 
                             oninput="document.getElementById(\'%1$s-value\').textContent = this.value">',
                            esc_attr($id),
                            esc_attr($value),
                            esc_attr($min ?: '0.1'),
                            esc_attr($max ?: '3'),
                            esc_attr($step ?: '0.1')
                        );
                        $input_html .= ' <span id="' . esc_attr($id) . '-value" style="font-weight: bold; color: #ff6b6b;">' . esc_html($value) . '</span>';
                        break;
                    case 'color':
                        $input_html = sprintf(
                            '<input type="color" id="%1$s" name="%1$s" value="%2$s" style="height: 40px; width: 60px; border: 2px solid #4ecdc4; border-radius: 4px;">',
                            esc_attr($id),
                            esc_attr($value)
                        );
                        break;
                    case 'checkbox':
                        $input_html = sprintf(
                            '<input type="checkbox" id="%1$s" name="%1$s" value="yes" %2$s style="transform: scale(1.3);">',
                            esc_attr($id),
                            checked($value, 'yes', false)
                        );
                        break;
                    case 'text':
                    default:
                        $input_html = sprintf(
                            '<input type="text" id="%1$s" name="%1$s" value="%2$s" class="regular-text" style="border: 2px solid #4ecdc4;">',
                            esc_attr($id),
                            esc_attr($value)
                        );
                        break;
                }
                
                echo $input_html;
                
                if ($description) {
                    echo '<p class="description" style="color: #666;">' . esc_html($description) . '</p>';
                }
            },
            'snow-fall-effect-nuclear',
            'snow_fall_effect_nuclear_section'
        );
    }
    
    public function sanitize_checkbox($input) {
        return $input === 'yes' ? 'yes' : 'no';
    }
    
    public function add_settings_link($links) {
        $settings_link = '<a href="' . admin_url('options-general.php?page=snow-fall-effect-nuclear') . '" style="font-weight: bold; color: #ff6b6b;">' . __('Nuclear Settings') . '</a>';
        array_unshift($links, $settings_link);
        return $links;
    }
}

// Initialize the NUCLEAR plugin
SnowFallEffectNuclear::get_instance();

// Set default options on activation
register_activation_hook(__FILE__, function() {
    $defaults = array(
        'snow_flake_count' => 50,
        'snow_speed' => 1.0,
        'snow_size' => 20,
        'snow_color' => '#ff0000',
        'snow_character' => '❄',
        'snow_mobile_enable' => 'yes',
        'snow_only_winter' => 'no',
        'snow_zindex' => 999999,
        'snow_enable_all_pages' => 'yes',
        'snow_excluded_pages' => '',
        'snow_allowed_pages' => ''
    );
    
    foreach ($defaults as $option => $value) {
        if (get_option($option) === false) {
            add_option($option, $value);
        }
    }
    
    // Show activation notice
    add_action('admin_notices', function() {
        ?>
        <div class="notice notice-success is-dismissible">
            <h3>❄️ Snow Fall Effect - Nuclear Version Activated!</h3>
            <p>This version uses <strong>aggressive CSS</strong> that works with ANY theme including Elementor.</p>
            <p>Configure it at <a href="<?php echo admin_url('options-general.php?page=snow-fall-effect-nuclear'); ?>">Settings → Snow Nuclear</a></p>
            <p><strong>Recommended settings for testing:</strong> Size: 20px, Color: #ff0000 (red), Z-Index: 999999</p>
        </div>
        <?php
    });
});
