import './bootstrap';
import Alpine from 'alpinejs';
import apiClient from './api';
import './stores';

window.apiClient = apiClient;
window.Alpine = Alpine;

Alpine.start();
