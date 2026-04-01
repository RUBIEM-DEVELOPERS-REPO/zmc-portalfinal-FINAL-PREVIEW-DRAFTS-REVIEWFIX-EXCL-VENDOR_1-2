/**
 * ZMC Session Timeout Manager
 * Handles session timeout warnings and automatic logout
 */
class SessionTimeoutManager {
    constructor(options = {}) {
        this.options = {
            sessionLifetime: options.sessionLifetime || 7200, // 2 hours in seconds
            warningTime: options.warningTime || 300, // 5 minutes warning
            checkInterval: options.checkInterval || 30, // Check every 30 seconds
            logoutUrl: options.logoutUrl || '/logout',
            loginUrl: options.loginUrl || '/login',
            extendUrl: options.extendUrl || '/session/extend',
            ...options
        };
        
        this.warningShown = false;
        this.countdownInterval = null;
        this.checkInterval = null;
        this.lastActivity = Date.now();
        
        this.init();
    }
    
    init() {
        this.createWarningModal();
        this.bindActivityEvents();
        this.startSessionCheck();
        
        // Check for existing session data
        this.checkSessionStatus();
    }
    
    createWarningModal() {
        const modalHtml = `
            <div id="session-timeout-modal" class="session-modal" style="display: none;">
                <div class="session-modal-overlay"></div>
                <div class="session-modal-content">
                    <div class="session-modal-header">
                        <i class="ri-time-line"></i>
                        <h3>Session Timeout Warning</h3>
                    </div>
                    <div class="session-modal-body">
                        <p>Your session will expire in <strong id="session-countdown">5:00</strong> due to inactivity.</p>
                        <p>Click "Stay Logged In" to extend your session, or you will be automatically logged out.</p>
                    </div>
                    <div class="session-modal-actions">
                        <button id="session-logout-btn" class="btn btn-secondary">
                            <i class="ri-logout-circle-line"></i> Logout Now
                        </button>
                        <button id="session-extend-btn" class="btn btn-primary">
                            <i class="ri-refresh-line"></i> Stay Logged In
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Add styles
        const styles = `
            <style>
                .session-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 10000;
                }
                
                .session-modal-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0, 0, 0, 0.7);
                    backdrop-filter: blur(5px);
                }
                
                .session-modal-content {
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
                    max-width: 450px;
                    width: 90%;
                    animation: sessionModalSlideIn 0.3s ease-out;
                }
                
                @keyframes sessionModalSlideIn {
                    from {
                        opacity: 0;
                        transform: translate(-50%, -60%);
                    }
                    to {
                        opacity: 1;
                        transform: translate(-50%, -50%);
                    }
                }
                
                .session-modal-header {
                    padding: 24px 24px 16px;
                    border-bottom: 1px solid #e5e7eb;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }
                
                .session-modal-header i {
                    font-size: 24px;
                    color: #f59e0b;
                }
                
                .session-modal-header h3 {
                    margin: 0;
                    font-size: 18px;
                    font-weight: 600;
                    color: #1f2937;
                }
                
                .session-modal-body {
                    padding: 16px 24px 24px;
                    color: #4b5563;
                    line-height: 1.6;
                }
                
                .session-modal-body strong {
                    color: #dc2626;
                    font-weight: 600;
                    font-family: 'Courier New', monospace;
                }
                
                .session-modal-actions {
                    padding: 0 24px 24px;
                    display: flex;
                    gap: 12px;
                    justify-content: flex-end;
                }
                
                .session-modal .btn {
                    padding: 10px 20px;
                    border-radius: 6px;
                    font-weight: 500;
                    text-decoration: none;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    cursor: pointer;
                    border: none;
                    font-size: 14px;
                    transition: all 0.2s ease;
                }
                
                .session-modal .btn-primary {
                    background: #2563eb;
                    color: white;
                }
                
                .session-modal .btn-primary:hover {
                    background: #1d4ed8;
                }
                
                .session-modal .btn-secondary {
                    background: #6b7280;
                    color: white;
                }
                
                .session-modal .btn-secondary:hover {
                    background: #4b5563;
                }
            </style>
        `;
        
        document.head.insertAdjacentHTML('beforeend', styles);
        
        // Bind modal events
        document.getElementById('session-extend-btn').addEventListener('click', () => {
            this.extendSession();
        });
        
        document.getElementById('session-logout-btn').addEventListener('click', () => {
            this.logout();
        });
    }
    
    bindActivityEvents() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.updateActivity();
            }, { passive: true });
        });
    }
    
    updateActivity() {
        this.lastActivity = Date.now();
        
        if (this.warningShown) {
            this.hideWarning();
        }
    }
    
    startSessionCheck() {
        this.checkInterval = setInterval(() => {
            this.checkSessionStatus();
        }, this.options.checkInterval * 1000);
    }
    
    checkSessionStatus() {
        const now = Date.now();
        const timeSinceActivity = (now - this.lastActivity) / 1000;
        const timeUntilExpiry = this.options.sessionLifetime - timeSinceActivity;
        
        if (timeUntilExpiry <= 0) {
            this.logout();
        } else if (timeUntilExpiry <= this.options.warningTime && !this.warningShown) {
            this.showWarning(timeUntilExpiry);
        }
    }
    
    showWarning(timeRemaining) {
        this.warningShown = true;
        document.getElementById('session-timeout-modal').style.display = 'block';
        
        this.startCountdown(timeRemaining);
    }
    
    hideWarning() {
        this.warningShown = false;
        document.getElementById('session-timeout-modal').style.display = 'none';
        
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
            this.countdownInterval = null;
        }
    }
    
    startCountdown(timeRemaining) {
        let remaining = Math.floor(timeRemaining);
        
        const updateCountdown = () => {
            const minutes = Math.floor(remaining / 60);
            const seconds = remaining % 60;
            const display = `${minutes}:${seconds.toString().padStart(2, '0')}`;
            
            document.getElementById('session-countdown').textContent = display;
            
            if (remaining <= 0) {
                this.logout();
                return;
            }
            
            remaining--;
        };
        
        updateCountdown();
        this.countdownInterval = setInterval(updateCountdown, 1000);
    }
    
    async extendSession() {
        try {
            const response = await fetch(this.options.extendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            if (response.ok) {
                this.lastActivity = Date.now();
                this.hideWarning();
                this.showNotification('Session extended successfully', 'success');
            } else {
                throw new Error('Failed to extend session');
            }
        } catch (error) {
            console.error('Session extension failed:', error);
            this.showNotification('Failed to extend session. Please log in again.', 'error');
            setTimeout(() => this.logout(), 2000);
        }
    }
    
    logout() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
        
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
        
        // Clear any stored session data
        if (typeof(Storage) !== "undefined") {
            localStorage.removeItem('session_data');
            sessionStorage.clear();
        }
        
        // Redirect to logout
        window.location.href = this.options.logoutUrl;
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `session-notification session-notification-${type}`;
        notification.innerHTML = `
            <div class="session-notification-content">
                <i class="ri-${type === 'success' ? 'check-circle' : 'error-warning'}-line"></i>
                <span>${message}</span>
            </div>
        `;
        
        const styles = `
            <style>
                .session-notification {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 10001;
                    padding: 12px 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
                    animation: slideInRight 0.3s ease-out;
                }
                
                .session-notification-success {
                    background: #10b981;
                    color: white;
                }
                
                .session-notification-error {
                    background: #ef4444;
                    color: white;
                }
                
                .session-notification-content {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }
                
                @keyframes slideInRight {
                    from {
                        opacity: 0;
                        transform: translateX(100%);
                    }
                    to {
                        opacity: 1;
                        transform: translateX(0);
                    }
                }
            </style>
        `;
        
        if (!document.querySelector('.session-notification-styles')) {
            const styleElement = document.createElement('div');
            styleElement.className = 'session-notification-styles';
            styleElement.innerHTML = styles;
            document.head.appendChild(styleElement);
        }
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
    
    destroy() {
        if (this.checkInterval) {
            clearInterval(this.checkInterval);
        }
        
        if (this.countdownInterval) {
            clearInterval(this.countdownInterval);
        }
        
        const modal = document.getElementById('session-timeout-modal');
        if (modal) {
            modal.remove();
        }
    }
}

// Auto-initialize for authenticated users
document.addEventListener('DOMContentLoaded', function() {
    // Only initialize if user is authenticated (check for common auth indicators)
    const isAuthenticated = document.querySelector('meta[name="user-authenticated"]')?.getAttribute('content') === 'true' ||
                           document.body.classList.contains('authenticated') ||
                           window.location.pathname.includes('/portal') ||
                           window.location.pathname.includes('/staff') ||
                           window.location.pathname.includes('/admin');
    
    if (isAuthenticated) {
        window.sessionTimeout = new SessionTimeoutManager({
            sessionLifetime: parseInt(document.querySelector('meta[name="session-lifetime"]')?.getAttribute('content')) || 7200,
            warningTime: 300, // 5 minutes warning
            checkInterval: 30, // Check every 30 seconds
            logoutUrl: '/logout',
            extendUrl: '/session/extend'
        });
    }
});