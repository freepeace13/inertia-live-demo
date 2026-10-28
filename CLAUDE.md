# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## What this is

A Laravel 13 demo app (Inertia 3, Spatie Event Sourcing, Reverb) showing Inertia Live with two frontends over one backend: Vue at `/documents`, React at `/react/documents`. The documents-and-comments example is a starting point only; the real-world scenario is not chosen (D-016, D-018). The controller uses the response factory (`InertiaLive::render()` with `live()` props, D-019). The owner may still rework the scenario (D-016, D-018); do not add features around the current example.

README.md has the setup and the "where to look" tables; do not duplicate them here.

## Commands

```sh
composer setup / composer dev / composer test   # see composer.json scripts
php artisan test                                # server flow with Live::fake()
php artisan test --filter <name>                # single test (only tests/Feature/LiveDocumentTest.php exists)
npm run build                                   # vite build
npm run e2e                                     # needs reverb:start --port=8080 and serve --port=8000 running
npm run e2e:browser                             # needs a build first and `npx playwright install chromium`
```

## Dependencies on sibling repos

This repo is checked out inside the workspace folder next to the others, and depends on them by path:

- **Client packages** (`@freepeace13/inertia-live-{core,vue,react}`) are `file:../inertia-live/packages/*` links, so `../inertia-live` must be the client repo, **built** (`dist/` is what gets consumed). The client repo is a **pnpm** workspace: use `pnpm install && pnpm build` there, never npm. Rebuild after changing client source.
- **Server package** comes from GitHub at `dev-main` via the `repositories` entry in `composer.json`. To develop it alongside: `composer config repositories.inertia-live-laravel path ../inertia-live-laravel` (do not commit that change).
- `.npmrc` sets `ignore-scripts=true`; composer's `setup` script installs with `--ignore-scripts` too.

Because both links track unreleased code, a wire-protocol or API change in the client or server can break this app silently. After such a change, run `php artisan test` and `npm run e2e` here.

## Rules for this repo

- The app signs everyone in as one demo user (`LoginDemoUser`) so private channels work without a login screen. Keep it demo-only.
- `HandleInertiaRequests::rootView()` picks the Vue or React entry by route name; both resolve the same component names from their own `Pages` folder. Keep Vue and React pages in step when changing one.
- Release program, roadmap and decisions live in `../inertia-live-docs` (`roadmap.md`, `decision-log.md`); docs win over the tracker. The tracker is one artifact: https://claude.ai/artifact/PjKwna6Tty3ZnZFKneBjhT. Update docs first, then the tracker, in the same piece of work; never create a second tracker.
- Never tag or publish from here. Do not commit or push unless the owner asks.
- **No AI attribution** anywhere: no `Co-Authored-By` trailers, no "Generated with" footers, no mentions in commits, PRs, docs or code comments.
