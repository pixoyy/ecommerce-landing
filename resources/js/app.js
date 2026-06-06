import './bootstrap';
import Alpine from 'alpinejs';
import apiClient from './api';
import './stores';

window.apiClient = apiClient;
window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
    setTimeout(() => {
        Alpine.store('auth').hydrate().then(() => {
            Alpine.store('cart').hydrate();
        });
    }, 0);
});

Alpine.start();
