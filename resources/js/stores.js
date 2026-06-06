import Alpine from 'alpinejs';
import apiClient from './api';

document.addEventListener('alpine:init', () => {
    Alpine.store('auth', {
        user: null,
        token: localStorage.getItem('auth_token') || null,

        get isAuthenticated() {
            return !!this.token;
        },

        setAuth(user, token) {
            this.user = user;
            this.token = token;
            localStorage.setItem('auth_token', token);
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            localStorage.removeItem('auth_token');
        },

        async hydrate() {
            if (!this.token) return;
            try {
                const res = await apiClient.get('/me');
                this.user = res.data.data.user;
            } catch {
                this.clearAuth();
            }
        },
    });

    Alpine.store('cart', {
        items: [],
        isLoading: false,

        get totalItemCount() {
            return this.items.reduce((sum, item) => sum + item.quantity, 0);
        },

        get subtotal() {
            return this.items.reduce((sum, item) => sum + item.subtotal, 0);
        },

        setItems(items) {
            this.items = items;
        },

        addItem(item) {
            const existing = this.items.find(i => i.product_variant_id === item.product_variant_id);
            if (existing) {
                existing.quantity = item.quantity;
                existing.subtotal = item.subtotal;
            } else {
                this.items.push(item);
            }
        },

        updateItem(itemId, quantity, subtotal) {
            const item = this.items.find(i => i.id === itemId);
            if (item) {
                item.quantity = quantity;
                item.subtotal = subtotal;
            }
        },

        removeItem(itemId) {
            this.items = this.items.filter(i => i.id !== itemId);
        },

        clear() {
            this.items = [];
        },
    });

    Alpine.store('ui', {
        isMobileMenuOpen: false,
        isLoading: false,

        toggleMenu() {
            this.isMobileMenuOpen = !this.isMobileMenuOpen;
        },

        startLoading() {
            this.isLoading = true;
        },

        stopLoading() {
            this.isLoading = false;
        },
    });
});
