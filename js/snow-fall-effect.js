(function($) {
    'use strict';
    
    document.addEventListener('DOMContentLoaded', function() {
        // Get settings from PHP
        const settings = window.snowSettings || {};
        
        // Create snow container if it doesn't exist
        let snowContainer = document.getElementById('snow-container');
        if (!snowContainer) {
            snowContainer = document.createElement('div');
            snowContainer.id = 'snow-container';
            snowContainer.className = 'snow-container';
            document.body.appendChild(snowContainer);
        }
        
        // Set CSS variables
        snowContainer.style.setProperty('--snow-z-index', settings.zIndex || '9998');
        
        // Create snowflakes
        createSnowflakes(settings);
        
        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Remove old snowflakes
                const snowflakes = document.querySelectorAll('.snowflake');
                snowflakes.forEach(flake => flake.remove());
                // Create new snowflakes
                createSnowflakes(settings);
            }, 250);
        });
    });
    
    function createSnowflakes(settings) {
        const snowContainer = document.getElementById('snow-container');
        if (!snowContainer) return;
        
        const flakeCount = Math.min(parseInt(settings.snowflakeCount) || 100, 500);
        const snowSpeed = parseFloat(settings.snowflakeSpeed) || 1;
        const snowSize = parseInt(settings.snowflakeSize) || 2;
        const snowColor = settings.snowflakeColor || '#ffffff';
        const snowCharacter = settings.snowflakeCharacter || '❄';
        
        for (let i = 0; i < flakeCount; i++) {
            createSnowflake(snowContainer, i, {
                speed: snowSpeed,
                size: snowSize,
                color: snowColor,
                character: snowCharacter
            });
        }
    }
    
    function createSnowflake(container, index, options) {
        const flake = document.createElement('div');
        flake.className = 'snowflake';
        flake.innerHTML = options.character;
        
        // Random position
        const left = Math.random() * 100;
        
        // Random size variation
        const sizeVariation = 0.5 + Math.random() * 0.5;
        const fontSize = options.size * sizeVariation;
        
        // Random animation duration (speed)
        const duration = (3 + Math.random() * 7) / options.speed;
        
        // Random opacity
        const opacity = 0.3 + Math.random() * 0.7;
        
        // Random sway (horizontal movement)
        const sway = 50 + Math.random() * 100;
        const swayDirection = Math.random() > 0.5 ? 1 : -1;
        
        // Apply styles
        flake.style.cssText = `
            left: ${left}vw;
            font-size: ${fontSize}px;
            color: ${options.color};
            opacity: ${opacity};
            animation-name: fall, sway;
            animation-duration: ${duration}s, ${duration * 2}s;
            animation-delay: ${Math.random() * 5}s;
            z-index: ${Math.floor(opacity * 10)};
        `;
        
        // Add custom sway animation
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sway-${index} {
                0%, 100% { transform: translateX(0); }
                50% { transform: translateX(${sway * swayDirection}px); }
            }
        `;
        document.head.appendChild(style);
        
        flake.style.animationName = `fall, sway-${index}`;
        
        // Add to container
        container.appendChild(flake);
        
        // Remove and recreate when animation completes (for continuous snowfall)
        setTimeout(() => {
            if (flake.parentNode) {
                flake.remove();
                createSnowflake(container, index, options);
            }
        }, duration * 1000);
    }
    
})(jQuery);
