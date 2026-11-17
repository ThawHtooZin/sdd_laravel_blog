# Tasks: Blog Platform

**Input**: Design documents from `/specs/001-blog-platform/`

**Prerequisites**: plan.md (required), spec.md (required for user stories), research.md, data-model.md, contracts/

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Confirm the base Laravel application and project layout match the approved architecture.

- [X] T001 Create the Laravel project structure for the blog platform in app/, database/, resources/, routes/, and tests/ to match the approved MVC layout.
- [X] T002 Configure SQLite as the default database connection in config/database.php and confirm database/database.sqlite is the active development database.
- [X] T003 Configure Tailwind and Vite integration in package.json, resources/css/, and the Laravel Vite setup so Blade views can use Tailwind styling.
- [X] T004 [P] Review and align the existing Laravel auth scaffolding with the seeded single-admin requirement in config/auth.php and routes/web.php.

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core infrastructure that must be complete before any user story can be implemented.

- [X] T005 Create the database migration for the posts table in database/migrations/ with title, slug, body, is_published, published_at, user_id, and timestamps, including a unique index on slug.
- [X] T006 Create the User model and Post model in app/Models/ with the required Eloquent relationships and mass-assignment or guarded behavior consistent with Laravel conventions.
- [X] T007 Seed the initial administrator account in database/seeders/ using Laravel's database seeding workflow and ensure public registration is not added.
- [X] T008 Configure the Laravel admin authentication flow using built-in session auth, with login/logout routes and the protected admin-session middleware flow.
- [X] T009 [P] Add request validation rules and error handling for admin create/edit forms, including title/body requirement checks and slug-generation validation.
- [X] T010 [P] Add a shared admin layout and base Blade styling shell in resources/views/layouts/app.blade.php and the matching stylesheet foundation for the blog admin area.
- [X] T011 Establish the public and admin route structure in routes/web.php covering homepage, post detail, admin login, admin overview, create, edit, and delete actions.

**Checkpoint**: Foundation ready - user story implementation can now begin in parallel.

---

## Phase 3: User Story 1 - Administrator manages post content (Priority: P1) 🎯 MVP

**Goal**: Deliver a working admin experience for creating, editing, publishing, and deleting posts.

**Independent Test**: An authenticated administrator can create a draft, publish it, unpublish it, edit it, and delete it without exposing content publicly.

### Tests for User Story 1

- [X] T012 [P] [US1] Add admin feature tests in tests/Feature/AdminPostTest.php covering login, create draft, publish, unpublish, edit, and delete flows.
- [X] T013 [P] [US1] Add a feature test in tests/Feature/PostSlugTest.php to verify slug generation and duplicate-title handling.

### Implementation for User Story 1

- [X] T014 [P] [US1] Create the admin PostController in app/Http/Controllers/Admin/PostController.php for index, create, store, edit, update, publish/unpublish, and destroy actions.
- [X] T015 [US1] Implement the admin post index and create/edit views in resources/views/posts/index.blade.php, create.blade.php, and edit.blade.php with form handling for title, body, draft/publish state, and delete actions.
- [X] T016 [US1] Implement slug-generation and uniqueness logic for title-based URLs in the Post model or controller layer so duplicate titles receive a unique fallback slug and the stored slug remains unique.
- [X] T017 [US1] Add validation and authorization in the admin controller path to ensure only the authenticated administrator can manage posts and that draft/published transitions follow the approved lifecycle.
- [X] T018 [US1] Add logic to persist published/draft state transitions in the database without introducing revision history or version tracking.
- [X] T019 [US1] Add admin success/error feedback in Blade views for create, edit, publish/unpublish, and delete actions.

**Checkpoint**: At this point, User Story 1 should be fully functional and testable independently.

---

## Phase 4: User Story 2 - Public visitors browse published content (Priority: P1)

**Goal**: Deliver a read-only homepage and public article view that exposes only published content.

**Independent Test**: A public visitor can browse the homepage, open a published article, and receive HTTP 404 for unpublished or nonexistent posts.

### Tests for User Story 2

- [X] T020 [P] [US2] Add public-access feature tests in tests/Feature/PublicPostAccessTest.php covering homepage visibility, published read access, draft access denial, and nonexistent-post 404 behavior.

### Implementation for User Story 2

- [X] T021 [P] [US2] Create the public home and post detail controller flow in app/Http/Controllers/HomeController.php or a public controller to load published posts and handle 404 behavior.
- [X] T022 [US2] Implement the public homepage Blade view in resources/views/welcome.blade.php or a dedicated public index page to list only is_published posts in a readable layout with Tailwind styling.
- [X] T023 [US2] Implement the public post detail view in resources/views/posts/show.blade.php showing a single published post and returning 404 for unpublished or missing records.
- [X] T024 [US2] Add route-level and controller-level checks so public visitors cannot access draft or unpublished posts and requests for missing posts resolve to HTTP 404.
- [X] T025 [US2] Add public UX feedback for empty homepage states and not-found behavior without exposing private information.

**Checkpoint**: At this point, User Stories 1 and 2 should both work independently.

---

## Phase 5: User Story 3 - Drafts remain private and publish visibility is enforced (Priority: P2)

**Goal**: Confirm the privacy boundary and duplicate-slug safety are enforced across the app.

**Independent Test**: Draft posts are never visible publicly, and duplicate titles still produce unique page URLs without leaking private content.

### Tests for User Story 3

- [X] T026 [P] [US3] Add edge-case feature tests for duplicate-titles, unpublished direct-access denial, and 404 behavior in tests/Feature/PublicPostAccessTest.php or a dedicated blog privacy test.

### Implementation for User Story 3

- [X] T027 [P] [US3] Add final privacy checks in the public post lookup workflow to ensure only published posts are displayed and unpublished or missing items return 404 in app/Http/Controllers/ and routes/web.php.
- [X] T028 [US3] Finalize slug uniqueness logic for duplicate titles by checking against existing slugs before save and by inserting a numeric suffix when necessary in the Post model/controller flow.
- [X] T029 [US3] Add final admin feedback and validation messaging for duplicate-title handling and visibility states in resources/views/posts/create.blade.php and edit.blade.php.

**Checkpoint**: All user stories should now be independently functional.

---

## Phase 6: Polish & Cross-Cutting Concerns

**Purpose**: Final quality checks across the entire feature.

- [X] T030 [P] Review all Blade views in resources/views/ for consistent Tailwind styling, empty states, and admin/public separation.
- [X] T031 [P] Review all routing and middleware logic in routes/web.php and app/Http/Middleware/ to confirm admin routes require authentication and public routes enforce visibility rules.
- [X] T032 [P] Run the Laravel test suite for auth, admin management, public access, and slug behavior to confirm the feature passes the approved quality gates.
- [X] T033 [P] Validate the quickstart workflow in specs/001-blog-platform/quickstart.md and ensure the final project matches the approved specification and technical plan.

---

## Dependencies & Execution Order

### Phase Dependencies

- Setup (Phase 1): No dependencies - can start immediately.
- Foundational (Phase 2): Depends on Phase 1 completion and blocks all user story work.
- User Story 1 (Phase 3): Depends on Phase 2 completion.
- User Story 2 (Phase 4): Depends on Phase 2 completion; can proceed after US1 or in parallel with US1 if staffing allows.
- User Story 3 (Phase 5): Depends on Phase 2 completion; validates privacy and duplicate-slug edge cases.
- Polish (Phase 6): Depends on all desired user stories being complete.

### User Story Dependencies

- User Story 1 (P1): No dependency on other stories; it is the MVP path.
- User Story 2 (P1): Independent of US1 from a public-access perspective, but shares the same post model and route conventions.
- User Story 3 (P2): Independent of US1 and US2 as an edge-case validation pass, but relies on the same model and access rules.

### Parallel Opportunities

- T003 and T004 can run in parallel during Setup.
- T009 and T010 can run in parallel during Foundational work.
- T012 and T013 can run in parallel for the User Story 1 test pass.
- T014 and T020 can run in parallel across US1 and US2 after foundational work completes.
- T026 and T027 can run in parallel during the privacy and slug edge-case pass.

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup.
2. Complete Phase 2: Foundational.
3. Complete Phase 3: User Story 1.
4. Validate the admin workflow independently.
5. Stop and confirm the MVP is functional before continuing to public access work.

### Incremental Delivery

1. Setup + Foundational: establish the Laravel app, database, and auth foundation.
2. Add User Story 1: admin post lifecycle.
3. Add User Story 2: public browsing and 404 access control.
4. Add User Story 3: privacy boundary and duplicate slug edge cases.
5. Complete polish, verify cross-cutting concerns, and run the full validation set.

---

## Notes

- [P] tasks are parallelizable and should be executed in separate files or independent paths.
- Each task includes the exact file path or directory affected to avoid ambiguity.
- Tasks avoid unnecessary architecture, repositories, and service layers.
- No implementation code is included; this file is ready for execution by the implementation phase.
