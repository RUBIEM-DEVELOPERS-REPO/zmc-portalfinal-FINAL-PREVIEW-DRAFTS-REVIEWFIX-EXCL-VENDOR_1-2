# ZMC System Timeout Management

## Overview
The ZMC portal includes a comprehensive session timeout system to prevent unauthorized access due to user inactivity and maintain system security.

## Features

### 🔒 **Automatic Session Management**
- **Session Lifetime**: 2 hours (120 minutes) by default
- **Warning Period**: 5 minutes before expiration
- **Activity Tracking**: Monitors mouse, keyboard, and touch interactions
- **Automatic Logout**: Forces logout when session expires

### ⚠️ **User Warnings**
- **Modal Warning**: Appears 5 minutes before session expiry
- **Countdown Timer**: Shows exact time remaining
- **Session Extension**: Users can extend their session with one click
- **Visual Indicator**: Bottom-right corner shows remaining session time

### 🛡️ **Security Features**
- **Server-side Validation**: All timeout logic verified on backend
- **CSRF Protection**: All session requests include CSRF tokens
- **Activity Logging**: Session extensions and timeouts are logged
- **Secure Cleanup**: Expired sessions are automatically removed

## Configuration

### Session Settings (`.env`)
```env
SESSION_LIFETIME=120          # Session duration in minutes
SESSION_EXPIRE_ON_CLOSE=false # Keep session when browser closes
SESSION_ENCRYPT=true          # Encrypt session data
```

### Timeout Settings (JavaScript)
```javascript
{
    sessionLifetime: 7200,    // 2 hours in seconds
    warningTime: 300,         // 5 minutes warning
    checkInterval: 30,        // Check every 30 seconds
}
```

## User Experience

### 1. **Normal Operation**
- Session indicator shows remaining time in bottom-right corner
- Green indicator when time > 10 minutes
- Red pulsing indicator when time < 10 minutes

### 2. **Warning Phase**
- Modal appears 5 minutes before expiry
- Countdown timer shows exact remaining time
- Two options: "Stay Logged In" or "Logout Now"

### 3. **Session Extension**
- Click "Stay Logged In" to extend session
- Resets session timer to full duration
- Success notification confirms extension

### 4. **Automatic Logout**
- Occurs when countdown reaches zero
- Redirects to login page with timeout message
- All session data is cleared

## Administrative Features

### Session Cleanup Command
```bash
# Manual cleanup
php artisan sessions:cleanup

# Force cleanup without confirmation
php artisan sessions:cleanup --force

# Keep sessions for specific days
php artisan sessions:cleanup --days=7
```

### Scheduled Cleanup
- Runs automatically daily at 2:00 AM
- Removes sessions older than 7 days
- Cleans up related activity logs

### Monitoring
- Session extensions logged in activity logs
- Timeout logouts tracked for security auditing
- Failed extension attempts recorded

## Technical Implementation

### Middleware
- `SessionTimeoutMiddleware`: Server-side session validation
- Applied to all authenticated routes
- Checks activity timestamps on each request

### JavaScript Components
- `SessionTimeoutManager`: Handles warnings and logout
- `SessionIndicator`: Shows remaining time
- Auto-initializes for authenticated users

### API Endpoints
- `POST /session/extend`: Extend current session
- `GET /session/status`: Get session information
- `POST /session/timeout-logout`: Force timeout logout

## Security Considerations

### 1. **Activity Detection**
- Monitors: mouse movement, clicks, keyboard input, scrolling, touch
- Updates server timestamp on any activity
- Prevents false timeouts during active use

### 2. **Server Validation**
- All timeout logic verified server-side
- Client-side warnings are advisory only
- Cannot be bypassed by disabling JavaScript

### 3. **Data Protection**
- Session data encrypted when stored
- Automatic cleanup prevents data accumulation
- CSRF protection on all session operations

## Troubleshooting

### Common Issues

**1. Session expires too quickly**
- Check `SESSION_LIFETIME` in `.env`
- Verify user activity is being detected
- Check server clock synchronization

**2. Warning modal not appearing**
- Ensure JavaScript files are loaded
- Check browser console for errors
- Verify CSRF token is present

**3. Extension requests failing**
- Check network connectivity
- Verify CSRF token validity
- Check server logs for errors

### Debug Mode
Add to `.env` for debugging:
```env
APP_DEBUG=true
LOG_LEVEL=debug
```

## Best Practices

### For Users
- Save work frequently
- Use "Stay Logged In" when actively working
- Don't leave sessions unattended in public areas

### For Administrators
- Monitor session cleanup logs
- Adjust timeout based on user feedback
- Review activity logs for security patterns
- Test timeout functionality after updates

## Browser Compatibility
- Modern browsers (Chrome 80+, Firefox 75+, Safari 13+)
- Mobile browsers supported
- Graceful degradation for older browsers
- No external dependencies required

---

**Note**: This system is designed to balance security with user experience. The 2-hour default timeout provides adequate security while minimizing user disruption during normal work sessions.