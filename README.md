# Inertia Live Demo

A Laravel 13 app that shows [Inertia Live](https://github.com/freepeace13/inertia-live) in action: **two frontends over one backend**, Vue 3 at `/documents` and React at `/react/documents`.

Open one document in two browser tabs. Renaming it or adding a comment in one tab updates the other, with no page-level WebSocket code.

## Related repositories

| Repository | Role |
| --- | --- |
| [`inertia-live`](https://github.com/freepeace13/inertia-live) | Client packages (`core`, `vue`, `react`) |
| [`inertia-live-laravel`](https://github.com/freepeace13/inertia-live-laravel) | Server package, installed here through Composer |
| [`inertia-live-docs`](https://github.com/freepeace13/inertia-live-docs) | Documentation |

## Run it

**Prerequisites:** PHP 8.3+, Composer, Node 22+.

The server package comes from GitHub (`dev-main`) until it is on Packagist. The client packages are linked from a sibling checkout, so clone and build `inertia-live` first.

```bash
# 1. Client packages, next to this repo
git clone https://github.com/freepeace13/inertia-live.git ../inertia-live
(cd ../inertia-live && pnpm install && pnpm build)

# 2. This app
cp .env.example .env && php artisan key:generate
touch database/database.sqlite
composer install && npm install
php artisan migrate

# 3. Three terminals
php artisan reverb:start --port=8080   # WebSocket server
php artisan serve --port=8000          # app
npm run dev                            # Vite
```

Open <http://localhost:8000> (Vue) or <http://localhost:8000/react/documents> (React), create a document, then open it in a second tab.

To develop the server package alongside, clone it next to this repo and point Composer at it:

```bash
composer config repositories.inertia-live-laravel path ../inertia-live-laravel
```

## Where to look

The domain and server side are shared by both frontends.

| Path | What it shows |
| --- | --- |
| `app/Domain/Documents` | Stored events with `#[LiveTopic]`, and the projector using `EmitsLiveChanges` |
| `app/Http/Controllers/DocumentController.php` | `InertiaLive::render()` with `live("documents.{uuid}", ...)` props |
| `app/Providers/AppServiceProvider.php` | `Live::authorize(...)` |

Each frontend has its own Vite entry and root view. `HandleInertiaRequests::rootView()` picks one by route name, and each app resolves the same component names from its own `Pages` folder.

| Path | What it shows |
| --- | --- |
| `resources/js/vue/app.js` | `app.use(InertiaLive, { echo })` |
| `resources/js/vue/Pages/Documents/Show.vue` | `useLive()` status, `pause()` / `resume()` while editing |
| `resources/js/react/app.jsx`, `AppLayout.jsx` | `InertiaLiveProvider` in a persistent layout (it reads `usePage()`, so it must render inside the Inertia tree) |
| `resources/js/react/Pages/Documents/Show.jsx` | The same page with the `useLive()` hook |

> The app signs everyone in as one demo user (`LoginDemoUser`) so private channels work without a login screen. Never do that in a real app.

## Verify

With `reverb:start` and `serve` running as above:

```bash
php artisan test                         # server flow with Live::fake()
npm run e2e                              # real Reverb round trip: two Echo clients, sender exclusion, auth refusal

npx playwright install chromium          # once
npm run build && npm run e2e:browser     # two real browser tabs, Vue then React
```

The browser test also checks that `pause()` holds updates while an input is focused, and that an unsent draft survives a live reload.
