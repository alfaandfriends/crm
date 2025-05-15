// Session timeout handling
const SESSION_TIMEOUT = 120 * 60 * 1000; // 120 minutes in milliseconds
let idleTimer = null;

function resetIdleTimer() {
    if (idleTimer) clearTimeout(idleTimer);
    idleTimer = setTimeout(handleSessionTimeout, SESSION_TIMEOUT);
}

function handleSessionTimeout() {
    // Clear any existing timers
    if (idleTimer) clearTimeout(idleTimer);
    
    // Redirect to login page
    window.location.href = '/login';
}

// Reset timer on user activity
document.addEventListener('mousemove', resetIdleTimer);
document.addEventListener('mousedown', resetIdleTimer);
document.addEventListener('keypress', resetIdleTimer);
document.addEventListener('touchmove', resetIdleTimer);
document.addEventListener('scroll', resetIdleTimer);

// Start the timer
resetIdleTimer();

// Handle AJAX session expiration for jQuery
$.ajaxSetup({
    statusCode: {
        401: function() {
            window.location.href = '/login';
        }
    }
});

// Handle Axios session expiration
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
); 