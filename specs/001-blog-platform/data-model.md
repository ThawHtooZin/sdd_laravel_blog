# Data Model: Blog Platform

## Overview

The blog uses a small Eloquent model set: one `User` model for the authenticated administrator and one `Post` model for blog content. The `posts` table stores the core content, publication state, and slug used by public routes.

## Entity: User

**Purpose**: Represents the single authenticated administrator account created through a seeder.

**Fields**:
- `id`: primary key
- `name`: administrator display name
- `email`: unique login identifier
- `email_verified_at`: optional timestamp for verification workflow
- `password`: hashed password
- `remember_token`: session token
- `created_at`, `updated_at`: Laravel timestamps

**Relationships**:
- `hasMany(Post::class)`

**Validation rules**:
- `name`: required, string, moderate length
- `email`: required, unique, valid email format
- `password`: required for new admin user, hashed before storage by Laravel

## Entity: Post

**Purpose**: Represents a blog entry that may exist as a draft or a published article.

**Fields**:
- `id`: primary key
- `user_id`: foreign key to the administrator user
- `title`: post title as entered by the admin
- `slug`: unique URL-safe identifier derived from the title
- `body`: article content
- `is_published`: boolean indicating whether the post is publicly visible
- `published_at`: nullable timestamp for the publish date
- `created_at`, `updated_at`: Laravel timestamps

**Relationships**:
- `belongsTo(User::class)`

**Validation rules**:
- `title`: required, string, trimmed
- `body`: required, string, non-empty content
- `slug`: required, unique, generated from title and normalized for URL use
- `is_published`: boolean
- `published_at`: required when the post is published; null for drafts

**State transitions**:
- Draft -> published: set `is_published = true` and `published_at = now()`
- Published -> draft: set `is_published = false` and `published_at = null`
- Delete: permanent deletion from the system; no soft-deletes or version history

## Derived rules

- Public route queries MUST filter to `is_published = true`.
- Private admin route queries MAY include both published and draft posts.
- Slug generation should normalize the title, remove non-word characters, and convert to kebab-case; if the slug already exists, append a numeric suffix such as `-2`.
- A unique index on `slug` ensures that duplicate slugs cannot be created through different request paths.
