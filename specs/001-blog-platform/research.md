# Research: Laravel Blog Platform

## Decision: Use Laravel's built-in session authentication and a single administrator role

**Rationale**: The project requirement specifies a single authenticated administrator and a public visitor flow. Laravel's built-in authentication system fits this model without adding a separate identity platform or custom auth layer. A seeded administrator account keeps the user story simple and aligns with the requirement that public registration is not allowed.

**Alternatives considered**:
- Custom auth implementation: rejected because it adds complexity and duplicates Laravel's security and session handling.
- Role package or multi-user system: rejected because the scope is intentionally limited to one admin role and no public registration.

## Decision: Use SQLite for the development database

**Rationale**: The project constraint explicitly requires SQLite, and the Laravel installation already supports it through the default `database.php` configuration. This keeps local setup simple and matches the lightweight project scope.

**Alternatives considered**:
- MySQL/PostgreSQL: rejected because SQLite is required by the technical constraints and has no additional setup cost for a small application.

## Decision: Use Blade + Tailwind CSS with no separate frontend

**Rationale**: The project requires a Laravel Blade and Tailwind stack, and the repository already includes Tailwind and Vite configuration. A single Laravel app with Blade templates preserves the simple architecture and avoids adding an API or SPA frontend that is outside the scope.

**Alternatives considered**:
- React/Vue/Inertia: rejected because they are explicitly disallowed.
- Separate admin + public frontend apps: rejected because the project is intentionally simple and should remain a single Laravel MVC application.

## Decision: Use a single `posts` table with publication state, unique slug, and admin ownership

**Rationale**: The feature requires a simple blog with admin management, drafts, publishing, and public reads. A single `posts` table with `user_id`, `title`, `slug`, `body`, `is_published`, and timestamps is sufficient and keeps the model clear; no extra tables or polymorphism are needed.

**Alternatives considered**:
- Separate tables for published and draft records: rejected because it adds complexity and duplicates the same content model.
- Revision history tables: rejected because the scope explicitly states that revision tracking is not required.

## Decision: Enforce public access rules in route-level authorization and policy-style checks

**Rationale**: Public and admin routes can be separated cleanly in Laravel routes and guarded by auth middleware for the admin side, while public routes should filter by `is_published = true` and return 404 when the post is missing or unpublished. This keeps authorization logic easy to reason about and consistent with Laravel conventions.

**Alternatives considered**:
- Purely view-layer checks: rejected because route and controller-level validation is more reliable and easier to test.
- Custom policy layer for a single admin role: rejected because the scope is too small to justify a dedicated policy abstraction.

## Decision: Use slug generation with a unique fallback strategy on duplicate titles

**Rationale**: The specification requires unique URL slugs, and duplicate titles must be handled gracefully. Best practice is to generate a normalized slug from the title and append a numeric suffix when the base slug already exists.

**Alternatives considered**:
- Rejecting duplicate titles entirely: rejected because the specification requires the system to handle duplicates without blocking content creation.
- Manual slug entry: rejected because the requirement explicitly calls for slug generation from title.

## Decision: Use Laravel HTTP 404 behavior for private and missing content

**Rationale**: Public visitors should not be able to determine whether unpublished content exists, so route handlers should treat both missing and unpublished posts the same way and return 404. This matches secure-by-default principles and avoids accidental content leakage.

**Alternatives considered**:
- Returning 403 or redirecting to home: rejected because the requirement explicitly calls for 404 responses for both unpublished and nonexistent posts.
