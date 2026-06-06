# Verification — Phase FE 01

## Automated Checks

| # | Check | Command | Expected | Status |
|---|-------|---------|----------|--------|
| 1 | NPM packages installed | `npm list alpinejs axios --depth=0` | Both listed | ❓ |
| 2 | Tailwind config exists | `test -f vite.config.js` | EXISTS | ✅ |
| 3 | JS files exist | `test -f resources/js/app.js && test -f resources/js/api.js && test -f resources/js/stores.js && test -f resources/js/bootstrap.js` | All exist | ✅ |
| 4 | CSS file exists | `test -f resources/css/app.css` | EXISTS | ✅ |
| 5 | API config exists | `test -f config/api.php` | EXISTS | ✅ |
| 6 | Build compiles | `npm run build` | Exit code 0 | ✅ |
| 7 | Folder structure | Verify directories exist | All present | ✅ |

## Manual Checks

| # | Check | How to Verify |
|---|-------|---------------|
| 1 | Tailwind renders | Create test view with `class="text-red-500"` — red text visible |
| 2 | Alpine.js works | Add `<div x-data="{ count: 0 }"><button @click="count++">+</button><span x-text="count"></span></div>` — counter works |
| 3 | Axios loads | Browser console: `console.log(window.apiClient)` → Axios instance |
| 4 | api.php accessible | `php artisan config:get api.base_url` → returns URL |
