/**
 * Author: Laravel
 * Project: Laravel Breeze Starter Kit used in Fitstagram (ITU/IIS)
 */

import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
