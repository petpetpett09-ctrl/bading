import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set CSRF token from meta tag
const csrfTokenElement = document.querySelector('meta[name="csrf-token"]');
if (csrfTokenElement) {
    const token = csrfTokenElement.getAttribute('content');
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
    // Also set it as a default for form data
    window.axios.defaults.headers.post['X-CSRF-TOKEN'] = token;
}

// Add request interceptor to always include CSRF token
window.axios.interceptors.request.use(config => {
    const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    if (token) {
        config.headers['X-CSRF-TOKEN'] = token;
    }
    return config;
});


