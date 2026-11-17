# Web Route Contracts: Blog Platform

## Public routes

### GET /
**Purpose**: Show the public homepage with all published posts.

**Behavior**:
- Returns the homepage view.
- Loads only `Post` records where `is_published = true`.
- Sorts records in a consistent order such as newest first.

### GET /posts/{slug}
**Purpose**: Show a single published post.

**Behavior**:
- Accepts a URL slug derived from the title.
- Returns the post if it exists and is published.
- Returns HTTP 404 if the slug is missing or points to an unpublished post.

## Admin routes

### GET /admin/login
**Purpose**: Display the administrator login form.

### POST /admin/login
**Purpose**: Authenticate the seeded administrator account.

**Behavior**:
- Validates email and password.
- Redirects authenticated users to the admin posts overview.
- Rejects invalid credentials without public registration.

### POST /admin/logout
**Purpose**: End the administrator session.

### GET /admin/posts
**Purpose**: Display all posts for the administrator, including drafts and published articles.

### GET /admin/posts/create
**Purpose**: Display the create-post form.

### POST /admin/posts
**Purpose**: Create a new post.

**Validation**:
- Title required
- Body required
- Slug generated automatically and checked for uniqueness
- Draft or published state selected by the admin

### GET /admin/posts/{post}/edit
**Purpose**: Display the edit form for an existing post.

### PUT|PATCH /admin/posts/{post}
**Purpose**: Update an existing post record.

**Behavior**:
- Updates the current record in place.
- Regenerates the slug if the title changes and the new slug is already in use.
- Does not create revision history.

### DELETE /admin/posts/{post}
**Purpose**: Permanently delete a post.

**Behavior**:
- Removes the post from storage regardless of publication state.
- Redirects to the admin overview after deletion.

## Security expectations

- Public routes must not expose draft or unpublished posts.
- Admin routes must require authenticated session-based access.
- Slug generation and uniqueness must happen on create and update.
- Invalid or missing post URLs must return HTTP 404 rather than revealing private content.
