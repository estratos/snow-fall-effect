(function() {
    'use strict';
    
    console.log('❄️ SNOW PLUGIN - VERSIÓN FORZADA');
    
    // Esperar a que la página cargue completamente
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSnowForced);
    } else {
        initSnowForced();
    }
    
    function initSnowForced() {
        console.log('🚀 Iniciando nieve FORZADA');
        
        // 1. Eliminar cualquier nieve anterior
        const oldContainer = document.getElementById('snow-container');
        if (oldContainer) {
            console.log('Eliminando contenedor viejo');
            oldContainer.remove();
        }
        
        // 2. Crear contenedor CON ESTILOS INLINE FORZADOS
        const container = document.createElement('div');
        container.id = 'snow-container-force';
        container.setAttribute('data-snow-system', 'forced-v1');
        
        // ESTILOS INLINE - IMPOSIBLES DE ANULAR
        container.style.position = 'fixed';
        container.style.top = '0';
        container.style.left = '0';
        container.style.width = '100%';
        container.style.height = '100%';
        container.style.pointerEvents = 'none';
        container.style.zIndex = '2147483647'; // Máximo posible
        container.style.overflow = 'visible';
        container.style.display = 'block';
        container.style.visibility = 'visible';
        container.style.opacity = '1';
        container.style.background = 'transparent';
        container.style.transform = 'none';
        container.style.all = 'initial'; // Reset total
        container.style.position = 'fixed !important';
        container.style.zIndex = '2147483647 !important';
        
        // 3. Añadir al INICIO del body
        document.body.insertBefore(container, document.body.firstChild);
        console.log('Contenedor creado al inicio del body');
        
        // 4. Configuración
        const settings = window.snowSettings || {
            snowflakeCount: 80,
            snowflakeSpeed: 1,
            snowflakeSize: 10, // Tamaño grande por defecto
            snowflakeColor: '#ff0000', // Rojo por defecto para debug
            snowflakeCharacter: '❄',
            zIndex: '2147483647'
        };
        
        // 5. Crear copos con estilos INLINE
        console.log(`Creando ${settings.snowflakeCount} copos`);
        
        for (let i = 0; i < Math.min(settings.snowflakeCount, 100); i++) {
            createSnowflakeForced(container, i, settings);
        }
        
        // 6. Función para crear cada copo
        function createSnowflakeForced(container, index, settings) {
            const flake = document.createElement('div');
            flake.className = 'snowflake-forced';
            flake.setAttribute('data-snow-id', index);
            flake.textContent = settings.snowflakeCharacter || '❄';
            
            // Estilos INLINE para cada propiedad
            flake.style.position = 'absolute';
            flake.style.top = '-50px';
            flake.style.left = (Math.random() * 100) + 'vw';
            flake.style.color = settings.snowflakeColor || '#ff0000';
            flake.style.fontSize = (parseInt(settings.snowflakeSize) * (0.5 + Math.random())) + 'px';
            flake.style.opacity = (0.3 + Math.random() * 0.7).toString();
            flake.style.zIndex = '2147483647';
            flake.style.pointerEvents = 'none';
            flake.style.userSelect = 'none';
            flake.style.display = 'block';
            flake.style.visibility = 'visible';
            flake.style.textShadow = '0 0 5px rgba(255,255,255,0.5)';
            flake.style.transform = 'none';
            flake.style.willChange = 'transform, opacity';
            
            // Forzar !important en propiedades críticas
            flake.style.cssText += 'position: absolute !important;';
            flake.style.cssText += 'z-index: 2147483647 !important;';
            flake.style.cssText += 'display: block !important;';
            flake.style.cssText += 'visibility: visible !important;';
            
            // Animación con Web Animations API (no CSS externo)
            const duration = (3000 + Math.random() * 7000) / (settings.snowflakeSpeed || 1);
            const delay = Math.random() * 5000;
            
            const animation = flake.animate([
                { 
                    transform: 'translateY(0px) translateX(0px) rotate(0deg)',
                    opacity: 0 
                },
                { 
                    transform: 'translateY(100px) translateX(' + (Math.random() * 100 - 50) + 'px) rotate(180deg)',
                    opacity: flake.style.opacity 
                },
                { 
                    transform: 'translateY(calc(100vh + 100px)) translateX(' + (Math.random() * 100 - 50) + 'px) rotate(360deg)',
                    opacity: 0 
                }
            ], {
                duration: duration,
                delay: delay,
                iterations: Infinity,
                easing: 'linear'
            });
            
            container.appendChild(flake);
            
            // Reciclar copo cuando termine (en teoría nunca, pero por si acaso)
            setTimeout(() => {
                if (flake.parentNode === container) {
                    flake.remove();
                    createSnowflakeForced(container, index, settings);
                }
            }, duration + delay + 1000);
        }
        
        // 7. Debug info
        console.log('✅ Sistema de nieve FORZADO activado');
        console.log('🎯 z-index máximo (2147483647)');
        console.log('🔴 Copos en color: ' + (settings.snowflakeColor || 'rojo (debug)'));
        
        // 8. Exponer función para debug
        window.debugSnowForced = function() {
            const flakes = document.querySelectorAll('.snowflake-forced');
            console.log('Copos forzados:', flakes.length);
            flakes.forEach((f, i) => {
                if (i < 3) {
                    const style = window.getComputedStyle(f);
                    console.log(`Copo ${i}:`, {
                        color: style.color,
                        fontSize: style.fontSize,
                        opacity: style.opacity,
                        display: style.display,
                        visibility: style.visibility
                    });
                }
            });
        };
        
        // Llamar a debug después de 2 segundos
        setTimeout(() => {
            window.debugSnowForced();
            console.log('🔍 Verifica:');
            console.log('1. Busca elementos con clase .snowflake-forced');
            console.log('2. Deberían tener color ROJO y tamaño grande');
            console.log('3. z-index máximo: 2147483647');
        }, 2000);
    }
    
    // Forzar inicialización incluso si hay errores
    setTimeout(() => {
        if (!document.getElementById('snow-container-force')) {
            console.warn('Reintentando inicialización...');
            initSnowForced();
        }
    }, 3000);
    
})();
