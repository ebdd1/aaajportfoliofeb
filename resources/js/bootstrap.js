import axios from 'axios';
import Nprogress from 'nprogress';
import { router } from '@inertiajs/vue3';

window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

// Set baseURL untuk tunnel - gunakan APP_URL dari env atau current origin
const appUrl = import.meta.env.VITE_APP_URL || window.location.origin;
axios.defaults.baseURL = appUrl;

// Nprogress configuration
Nprogress.configure({ showSpinner: false });

router.on('start', () => Nprogress.start());
router.on('finish', () => Nprogress.done());
