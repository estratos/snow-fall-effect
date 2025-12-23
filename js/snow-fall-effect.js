(function() {
    'use strict';
    
    // Esperar a que el DOM esté listo
    document.addEventListener('DOMContentLoaded', function() {
        // Pequeño delay para asegurar que todo cargó
        setTimeout(initSnowEffect, 500);
    });
    
    function initSnowEffect() {
        console.log('❄️ Inicializando efecto de nieve...');
        
        // Verificar si snowSettings existe
        if (typeof snowSettings === 'undefined') {
            console.warn('snowSettings no definido. Usando valores por defecto.');
            window.snowSettings = {
                snowflakeCount: 80,
                snowflakeSpeed: 1,
                snowflakeSize: 5,
                snowflakeColor: '#ffffff',
                snowflakeCharacter: '❄',
                enableOnMobile: 'no',
                onlyWinter: 'no',
                zIndex: '12000',
                enableOnAllPages: 'yes',
                excludedPages: '',
                snowEnabled: 'yes'
            };
        }
        
        const settings = window.snowSettings;
        
        // Verificar si la nieve está habilitada
        if (settings.snowEnabled !== 'yes') {
            console.log('Nieve deshabilitada por configuración');
            return;
        }
        
        // Verificar si es móvil y está deshabilitado
        if (window.innerWidth <= 768 && settings.enableOnMobile !== 'yes') {
            console.log('Nieve deshabilitada en móvil');
            return;
        }
        
        // Crear contenedor si no existe
        let snowContainer = document.getElementById('snow-container');
        if (!snowContainer) {
            snowContainer = createSnowContainer(settings);
        }
        
        // Limpiar nieve anterior si existe
        clearExistingSnowflakes();
        
        // Crear copos de nieve
        createSnowflakes(snowContainer, settings);
        
        // Manejar redimensionamiento
        setupResizeHandler(snowContainer, settings);
        
        console.log('✅ Efecto de nieve inicializado correctamente');
    }
    
    function createSnowContainer(settings) {
        const container = document.createElement('div');
        container.id = 'snow-container';
        container.className = 'snow-container';
        
        // Aplicar estilos CRÍTICOS con !important
        container.style.cssText = `
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            width: 100% !important;
            height: 100% !important;
            pointer-events: none !important;
            z-index: ${settings.zIndex || '12000'} !important;
            overflow: hidden !important;
        `;
        
        document.body.appendChild(container);
        return container;
    }
    
    function clearExistingSnowflakes() {
        const existingFlakes = document.querySelectorAll('.snowflake');
        existingFlakes.forEach(flake => {
            if (flake.parentNode) {
                flake.parentNode.removeChild(flake);
            }
        });
    }
    
    function createSnowflakes(container, settings) {
        const flakeCount = parseInt(settings.snowflakeCount) || 80;
        const maxFlakes = Math.min(flakeCount, 200); // Límite por rendimiento
        
        console.log(`Creando ${maxFlakes} copos de nieve...`);
        
        for (let i = 0; i < maxFlakes; i++) {
            setTimeout(() => {
                createSingleSnowflake(container, i, settings);
            }, i * 10); // Espaciar la creación para mejor rendimiento
        }
    }
    
    function createSingleSnowflake(container, index, settings) {
        const flake = document.createElement('div');
        flake.className = 'snowflake';
        flake.setAttribute('aria-hidden', 'true');
        
        // Usar el carácter configurado o por defecto
        const snowChar = settings.snowflakeCharacter || '❄';
        flake.textContent = snowChar;
        
        // Configuración de estilos
        const snowSpeed = parseFloat(settings.snowflakeSpeed) || 1;
        const baseSize = parseInt(settings.snowflakeSize) || 2;
        const snowColor = settings.snowflakeColor || '#ffffff';
        
        // Valores aleatorios para naturalidad
        const leftPosition = Math.random() * 100;
        const sizeVariation = 0.5 + Math.random() * 1.5;
        const fontSize = Math.max(5, baseSize * sizeVariation);
        const duration = (5000 + Math.random() * 10000) / snowSpeed;
        const opacity = 0.4 + Math.random() * 0.6;
        const swayAmount = 50 + Math.random() * 100;
        const swayDirection = Math.random() > 0.5 ? 1 : -1;
        const rotation = Math.random() * 360;
        const delay = Math.random() * 5;
        
        // Aplicar estilos INLINE para mayor prioridad
        flake.style.cssText = `
            /* Estilos críticos con !important */
            position: absolute !important;
            top: -50px !important;
            left: ${leftPosition}vw !important;
            color: ${snowColor} !important;
            font-size: ${fontSize}px !important;
            opacity: ${opacity} !important;
            user-select: none !important;
            pointer-events: none !important;
            z-index: ${settings.zIndex || '12000'} !important;
            display: block !important;
            visibility: visible !important;
            
            /* Animaciones */
            animation-duration: ${duration}ms !important;
            animation-delay: ${delay}s !important;
            animation-timing-function: linear !important;
            animation-iteration-count: infinite !important;
            animation-name: snowFall, snowSway !important;
            
            /* Transform inicial */
            transform: translateY(-50px) rotate(${rotation}deg) !important;
        `;
        
        // Crear keyframes dinámicos para el movimiento
        createSnowKeyframes(index, swayAmount * swayDirection);
        
        container.appendChild(flake);
        
        // Reciclar el copo cuando termine su animación
        setTimeout(() => {
            if (flake.parentNode === container) {
                flake.remove();
                createSingleSnowflake(container, index, settings);
            }
        }, duration + (delay * 1000) + 1000);
    }
    
    function createSnowKeyframes(index, swayAmount) {
        // Crear keyframes únicos para cada copo
        const styleId = `snow-keyframes-${index}`;
        
        // Eliminar si ya existe
        const existingStyle = document.getElementById(styleId);
        if (existingStyle) {
            existingStyle.remove();
        }
        
        const style = document.createElement('style');
        style.id = styleId;
        
        // Keyframe para caída
        style.textContent = `
            @keyframes snowFall-${index} {
                0% {
                    transform: translateY(-50px) rotate(0deg);
                    opacity: 0;
                }
                10% {
                    opacity: ${0.3 + Math.random() * 0.7};
                }
                90% {
                    opacity: ${0.3 + Math.random() * 0.7};
                }
                100% {
                    transform: translateY(calc(100vh + 50px)) rotate(360deg);
                    opacity: 0;
                }
            }
            
            @keyframes snowSway-${index} {
                0%, 100% {
                    transform: translateX(0);
                }
                50% {
                    transform: translateX(${swayAmount}px);
                }
            }
        `;
        
        document.head.appendChild(style);
    }
    
    function setupResizeHandler(container, settings) {
        let resizeTimeout;
        
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            
            resizeTimeout = setTimeout(function() {
                console.log('Redimensionando efecto de nieve...');
                
                // Limpiar copos existentes
                clearExistingSnowflakes();
                
                // Volver a crear los copos
                createSnowflakes(container, settings);
            }, 250);
        });
    }
    
    // Exponer funciones para debug
    window.snowDebug = {
        getInfo: function() {
            const container = document.getElementById('snow-container');
            const flakes = document.querySelectorAll('.snowflake');
            
            return {
                containerExists: !!container,
                flakeCount: flakes.length,
                settings: window.snowSettings || {},
                containerStyles: container ? window.getComputedStyle(container) : null,
                sampleFlake: flakes.length > 0 ? window.getComputedStyle(flakes[0]) : null
            };
        },
        
        showFlakes: function() {
            document.querySelectorAll('.snowflake').forEach(flake => {
                flake.style.cssText += `
                    background: rgba(255,0,0,0.2) !important;
                    border: 1px solid red !important;
                    outline: 2px solid yellow !important;
                `;
            });
            console.log('Copos resaltados en rojo/amarillo');
        },
        
        toggleSnow: function() {
            const container = document.getElementById('snow-container');
            if (container) {
                if (container.style.display === 'none') {
                    container.style.display = 'block';
                    console.log('Nieve mostrada');
                } else {
                    container.style.display = 'none';
                    console.log('Nieve ocultada');
                }
            }
        }
    };
    
    // Inicialización de emergencia si algo falla
    setTimeout(function() {
        if (!document.getElementById('snow-container')) {
            console.log('Reintentando inicialización de nieve...');
            initSnowEffect();
        }
    }, 3000);
    
})();
