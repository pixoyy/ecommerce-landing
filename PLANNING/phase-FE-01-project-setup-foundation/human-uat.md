# Human UAT — Phase FE 01

## Scenario 1: Development Mode
1. Run `npm run dev`
2. Open browser to `http://127.0.0.1:8000`
3. Modify `resources/css/app.css` (add a test class)
4. **Expected**: Browser auto-refreshes with changes

## Scenario 2: Production Build
1. Run `npm run build`
2. **Expected**: Exit code 0, assets in `public/build/`
3. Verify `public/build/manifest.json` exists

## Scenario 3: First-Time Developer Setup
1. Clone repo fresh
2. `cp .env.example .env`
3. `composer install`
4. `npm install`
5. `npm run build`
6. **Expected**: All steps complete without errors
