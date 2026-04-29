# UI Foundation - Headless UI And Emotion

## Summary

This project now has a React island layer for incremental UI modernization inside the existing Laravel Blade application.

The first island uses:

- `@headlessui/react` for accessible, unstyled UI behavior.
- `@emotion/react` for component-scoped runtime styling.
- `react` and `react-dom` for mounting interactive islands into Blade pages.

This avoids a full frontend rewrite while allowing newer UI components to be introduced gradually.

## Current Implementation

### Build entry

The island entry lives at:

- `resources/js/ui-islands.jsx`

Laravel Mix compiles it to:

- `public/js/ui-islands.js`

The bundle is loaded from the shared script partials:

- `resources/views/layouts/script.blade.php`
- `resources/views/frontend/layouts/script.blade.php`
- `resources/views/super_admin/layouts/script.blade.php`

### Login credential helper

The login page now exposes the demo credential helper as a React island:

- `resources/views/auth/login.blade.php`

When `LOGIN_HELP=active`, Blade passes the available demo credentials into a `data-ui-island="login-credential-menu"` mount node. The React island renders a Headless UI menu and fills the login form when a credential is selected.

The old jQuery click handlers for the credential helper were removed from the login view. This keeps the behavior scoped to the new island bundle.

## Development Pattern

To add another island:

1. Add a React component in `resources/js/ui-islands.jsx` or split it into a dedicated file under `resources/js/ui/`.
2. Register it in the `islands` map.
3. Add a Blade mount node with `data-ui-island="your-island-name"`.
4. Pass server-side props through `data-*` attributes as JSON.
5. Run `npm run production`.

Example Blade mount:

```blade
<div
    data-ui-island="login-credential-menu"
    data-credentials='@json($credentials)'
></div>
```

## Dependency Notes

Installed frontend packages:

- `@headlessui/react`
- `@emotion/react`
- `react`
- `react-dom`

Build tooling additions:

- `@babel/preset-react`
- `webpack` pinned to a Laravel Mix compatible version that still builds under the current toolchain.

## Current Audit Status

- `npm audit --audit-level=high` passes after upgrading Axios.
- `npm audit` still reports low/moderate findings in the Laravel Mix/Webpack development toolchain.
- `composer audit` still reports a Laravel framework advisory that requires a larger Laravel upgrade path.

## Next UI Steps

- Extract shared Headless UI primitives for menus, dialogs, tabs, toggles, and buttons.
- Migrate one admin modal/dropdown workflow to Headless UI.
- Add browser smoke tests for login and one authenticated admin/alumni flow.
- Plan a future Vite migration if the app continues adopting React islands.
