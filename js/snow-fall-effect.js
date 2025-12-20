(function() {
    'use strict';
    
    // Wait for DOM to be fully loaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSnow);
    } else {
        initSnow();
    }
    
    function initSnow() {
        // Check if snow should be enabled
        if (typeof snowSettings !== 'undefined' && snowSettings.snowEnabled === 'no') {
            return;
        }
        
        // Get settings
        const settings = window.snowSettings || {
            snowflakeCount: 80,
            snowflakeSpeed: 1,
            snowflakeSize: 2,
            snowflakeColor: '#ffffff',
            snowflakeCharacter: '❄',
            zIndex: '12000'
        };
        
        // Create snow container
        let snowContainer = document.getElementById('snow-container');
        if (!snowContainer) {
            snowContainer = document.createElement('div');
            snowContainer.id = 'snow-container';
            snowContainer.className = 'snow-container';
            snowContainer.style.cssText = `
                position: fixed !important;
                top: 0 !important;
                left: 0 !important;
                width: 100% !important;
                height: 100% !important;
                pointer-events: none !important;
                overflow: hidden !important;
                z-index: ${settings.zIndex || '12000'} !important;
            `;
            document.body.appendChild(snowContainer);
        }
        
        // Create snowflakes
        createSnowflakes(snowContainer, settings);
        
        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(function() {
                // Remove old snowflakes
                const snowflakes = document.querySelectorAll('.snowflake');
                snowflakes.forEach(flake => {
                    if (flake.parentNode === snowContainer) {
                        flake.remove();
                    }
                });
                // Create new snowflakes
                createSnowflakes(snowContainer, settings);
            }, 250);
        });
        
        // Handle visibility change (tab switching)
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                // Tab became visible again, refresh snow
                const snowflakes = document.querySelectorAll('.snowflake');
                snowflakes.forEach(flake => {
                    if (flake.parentNode === snowContainer) {
                        flake.remove();
                    }
                });
                createSnowflakes(snowContainer, settings);
            }
        });
    }
    
    function createSnowflakes(container, settings) {
        const flakeCount = Math.min(parseInt(settings.snowflakeCount) || 80, 500);
        const snowSpeed = parseFloat(settings.snowflakeSpeed) || 1;
        const baseSize = parseInt(settings.snowflakeSize) || 2;
        const snowColor = settings.snowflakeColor || '#ffffff';
        const snowCharacter = settings.snowflakeCharacter || '❄';
        const containerWidth = container.clientWidth || window.innerWidth;
        
        for (let i = 0; i < flakeCount; i++) {
            createSnowflake(container, i, {
                count: flakeCount,
                speed: snowSpeed,
                size: baseSize,
                color: snowColor,
                character: snowCharacter,
                containerWidth: containerWidth
            });
        }
    }
    
    function createSnowflake(container, index, options) {
        // Create snowflake element
        const flake = document.createElement('div');
        flake.className = 'snowflake';
        flake.setAttribute('aria-hidden', 'true');
        flake.textContent = options.character;
        
        // Random values for natural look
        const left = Math.random() * 100; // 0-100%
        const sizeVariation = 0.5 + Math.random() * 1.5; // 0.5x to 2x size
        const fontSize = Math.max(1, options.size * sizeVariation);
        const duration = (3 + Math.random() * 7) / options.speed; // 3-10 seconds adjusted by speed
        const opacity = 0.3 + Math.random() * 0.7;
        const swayAmount = (20 + Math.random() * 80) * (Math.random() > 0.5 ? 1 : -1);
        const swayDistance = (10 + Math.random() * 40) * (Math.random() > 0.5 ? 1 : -1);
        const rotation = Math.random() * 360;
        
        // Apply styles with CSS variables for animations
        flake.style.cssText = `
            position: absolute !important;
            top: -30px !important;
            left: ${left}vw !important;
            color: ${options.color} !important;
            font-size: ${fontSize}px !important;
            line-height: 1 !important;
            opacity: ${opacity} !important;
            user-select: none !important;
            pointer-events: none !important;
            text-shadow: 0 0 5px rgba(255, 255, 255, 0.5) !important;
            z-index: ${window.snowSettings?.zIndex || '12000'} !important;
            --sway-amount: ${swayAmount}px;
            --sway-distance: ${swayDistance}px;
            animation: 
                snow-fall ${duration}s linear infinite,
                snow-sway ${duration * 2}s ease-in-out infinite;
            animation-delay: ${Math.random() * 5}s;
            transform: translateY(-20px) translateX(0) rotate(${rotation}deg);
        `;
        
        // Add to container
        container.appendChild(flake);
        
        // Remove flake when animation completes and create new one
        setTimeout(function() {
            if (flake.parentNode === container) {
                flake.remove();
                createSnowflake(container, index, options);
            }
        }, (duration + Math.random() * 2) * 1000);
    }
    
    // Debug helper
    window.debugSnow = function() {
        console.log('Snow Settings:', window.snowSettings);
        const container = document.getElementById('snow-container');
        console.log('Snow Container:', container);
        const flakes = document.querySelectorAll('.snowflake');
        console.log('Snowflakes count:', flakes.length);
        
        // Force show snow
        if (!container) {
            initSnow();
        }
    };
    
})();
