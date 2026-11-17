# Quickstart Guide: Blog Platform

## Prerequisites

- PHP 8.3+
- Composer
- SQLite support enabled in PHP
- Node.js and npm for Tailwind/Vite asset building
- Laravel project dependencies installed

## Setup

1. Ensure the project has Composer dependencies installed.
2. Confirm the SQLite database file is present in `database/database.sqlite`.
3. Generate the application key and configure the `.env` file for SQLite if needed.
4. Set `ADMIN_EMAIL` and a unique `ADMIN_PASSWORD` of at least 12 characters in `.env`; do not commit the populated `.env` file.
5. Run the database migrations and seed the administrator user.
6. Install or build frontend assets with Vite / Tailwind.

## Validation scenarios

### 1. Admin login and logout

- Navigate to the admin login page.
- Sign in using the seeded administrator credentials.
- Confirm the administrator can access the post management page.
- Log out and confirm the session ends.

**Expected result**: Admin routes are protected and only authenticated users can access them.

### 2. Create, draft, publish, unpublish, and delete posts

- Use the admin area to create a post and save it as a draft.
- Confirm the post is not visible on the public homepage.
- Publish the post and confirm it appears on the public homepage.
- Unpublish it and confirm it is hidden again.
- Delete the post and confirm it is permanently removed.

**Expected result**: Full admin content lifecycle works without any public visibility until publication.

### 3. Public browsing and 404 behavior

- Open the homepage and confirm only published posts are listed.
- Click a published post and confirm the article loads.
- Request a draft or unpublished post directly by URL.
- Request a nonexistent post slug.

**Expected result**: Public users see only published content; unpublished or missing posts return HTTP 404.

### 4. Duplicate title handling

- Create two posts with the same title.
- Confirm both are accepted and each gets a unique slug.

**Expected result**: Slugs remain unique and are resolved by generated suffixes when needed.

## Optional verification commands

- `php artisan migrate --seed`
- `php artisan test`
- `npm run build`

These commands validate the migration state, test coverage, and frontend asset compilation without implementing the feature.
