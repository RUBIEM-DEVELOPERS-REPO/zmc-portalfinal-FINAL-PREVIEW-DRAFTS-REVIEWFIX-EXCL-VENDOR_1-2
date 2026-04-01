/**
 * Session Status Indicator
 * Shows remaining session time in the UI
 */
class SessionIndicator {
    constructor(options = {}) {
        this.options = {
            containerId: options.containerId || 'session-indicator',
            updateInterval: options.updateInterval || 60, // Update every minute
            showWarningAt: options.showWarningAt || 600, // Show warning at 10 minutes
            ...options
        };
        
        this.remainingTime = 0;
        this.updateInterval = null;
        
        this.init();
    }
    
    init() {
        this.createIndicator();
        this.startUpdating();
        this.fetchSessionStatus();
    }
    
    createIndicator() {
        const indicator = document.createElement('div');
        indicator.id = this.options.containerId;
        indicator.className = 'session-indicator';
        indicator.innerHTML = `
            <div class="session-indicator-content">
                <i class="ri-time-line"></i>
                <span class="session-time">--:--</span>
                <span class="session-label">Session</span>
            </div>
        `;
        
        // Add styles
        const styles = `
            <style>
                .session-indicator {
                    position: fixed;
                    bottom: 20px;
                    right: 20px;
                    z-index: 1000;
                    background: rgba(255, 255, 255, 0.95);
                    border: 1px solid rgba(0, 0, 0, 0.1);
                    border-radius: 8px;
                    padding: 8px 12px;
                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
                    backdrop-filter: blur(10px);
                    font-size: 12px;
                    transition: all 0.3s ease;
                }
                
                .session-indicator.warning {
                    background: rgba(239, 68, 68, 0.95);
                    color: white;
                    animation: sessionPulse 2s infinite;
                }
                
                @keyframes sessionPulse {
                    0%, 100% { opacity: 0.95; }
                    50% { opacity: 0.7; }
                }
                
                .session-indicator-content {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                }
                
                .session-indicator i {
                    font-size: 14px;
                }
                
                .session-time {
                    font-weight: 600;
                    font-family: 'Courier New', monospace;
                }
                
                .session-label {
                    font-size: 10px;
                    opacity: 0.8;
                }
                
                @media (max-width: 768px) {
                    .session-indicator {
                        bottom: 10px;
                        right: 10px;
                        font-size: 11px;
                    }
                }
            </style>
        `;
        
        if (!document.querySelector('.session-indicator-styles')) {
            const styleElement = document.createElement('div');
            styleElement.className = 'session-indicator-styles';
            styleElement.innerHTML = styles;
            document.head.appendChild(styleElement);
        }
        
        // Find a good place to insert the indicator
        let container = document.querySelector('.main-content') || 
                       document.querySelector('main') || 
                       document.body;
        
        container.appendChild(indicator);
    }
    
    startUpdating() {
        this.updateInterval = setInterval(() => {
            this.updateDisplay();
            
            // Fetch fresh status every 5 minutes
            if (Date.now() % (5 * 60 * 1000) < this.options.updateInterval * 1000) {
                this.fetchSessionStatus();
            }
        }, this.options.updateInterval * 1000);
    }
    
    async fetchSessionStatus() {
        try {
            const response = await fetch('/session/status', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                }
            });
            
            if (response.ok) {
                const data = await response.json();
                this.remainingTime = data.remaining_time;
                this.updateDisplay();
            }
        } catch (error) {
            console.warn('Failed to fetch session status:', error);
        }
    }
    
    updateDisplay() {
        const indicator = document.getElementById(this.options.containerId);
        if (!indicator) return;
        
        const timeElement = indicator.querySelector('.session-time');
        if (!timeElement) return;
        
        // Decrease remaining time
        this.remainingTime = Math.max(0, this.remainingTime - this.options.updateInterval);
        
        // Format time
        const hours = Math.floor(this.remainingTime / 3600);
        const minutes = Math.floor((this.remainingTime % 3600) / 60);
        
        let display;
        if (hours > 0) {
            display = `${hours}h ${minutes}m`;
        } else {
            display = `${minutes}m`;
        }
        
        timeElement.textContent = display;
        
        // Add warning class if time is low
        if (this.remainingTime <= this.options.showWarningAt) {
            indicator.classList.add('warning');
        } else {
            indicator.classList.remove('warning');
        }
        
        // Hide if session expired
        if (this.remainingTime <= 0) {
            indicator.style.display = 'none';
        }
    }
    
    destroy() {
        if (this.updateInterval) {
            clearInterval(this.updateInterval);
        }
        
        const indicator = document.getElementById(this.options.containerId);
        if (indicator) {
            indicator.remove();
        }
    }
}

// Auto-initialize for authenticated users
document.addEventListener('DOMContentLoaded', function() {
    const isAuthenticated = document.querySelector('meta[name="user-authenticated"]')?.getAttribute('content') === 'true';
    
    if (isAuthenticated) {
        window.sessionIndicator = new SessionIndicator();
    }
});