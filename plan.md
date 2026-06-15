## Plan: Customer-Scoped Blog CMS + Automation

Status: in progress — Phases 1–11 complete; Phase 12 (tests/CI) remaining

Legend: ✅ done · ⏳ pending · 🔲 not started

Create a Laravel app with customer-scoped Blog and Todo Kanban modules using policy-first authorization, shared requests and queries across admin and API, and OpenAPI docs. Build admin pages with filament/filament. Add customer-configurable social todo templates so creating a blog item queues a job that auto-creates platform todos with mandatory due dates. For assets, enforce customer-based ownership and storage end-to-end, using customer-specific remote storage only (no local image storage), with selectable backends per customer: S3, FTP, or Cloudinary via cloudinary-labs/cloudinary-laravel.

### Steps
1. ✅ Phase 1 - Bootstrap and foundations.
2. ✅ Scaffold Laravel, configure MariaDB, add Sanctum baseline, and verify app health.
3. ✅ Configure Sanctum for dual-mode auth: bearer token API access and stateful cookie session login.
4. ✅ Define trusted-domain onboarding for stateful cookie auth (stateful domains, CORS, CSRF, and session alignment) so approved domains can use cookie login safely.
5. ✅ Install Spatie Permission and Spatie Query Builder.
6. ✅ Install filament/filament and configure the admin panel shell and navigation.
7. ✅ Install graham-campbell/markdown and wire a shared markdown rendering service for content fields.
8. ✅ Install OpenAPI attribute tooling with zircote/swagger-php and add docs generation script.
9. ✅ Phase 2 - Customer scope and identity.
10. ✅ Create customers and customer_user tables.
11. ✅ Add User::customers and Customer::users relations.
12. ✅ Seed roles admin, customer, user and permissions for Blog and Todo actions.
13. ✅ Phase 3 - Localization strategy.
14. ✅ Use a single JSON language file for labels and UI copy.
15. ✅ Enforce translation key format domain.section.label_slug.
16. ✅ Apply translation keys consistently across Filament labels, validation messages, action text, and API-visible labels.
17. ✅ Phase 4 - Routes, requests, and policies.
18. ✅ Build admin CRUD/list/move experiences with Filament resources/pages/actions for Blog and Todo using customer route context.
19. ✅ Enable markdown authoring for Blog and Todo content fields in Filament forms with preview-friendly UX.
20. ✅ Use Filament tenancy (`->tenant(Customer::class, slugAttribute: 'slug')`) for customer-scoped admin resources — do NOT add `getEloquentQuery()` overrides.
21. ✅ Keep route files authorization-free (only wiring and middleware).
22. ✅ Reuse the same FormRequest classes between admin and API.
23. ✅ Validate markdown-backed content fields for Blog and Todo consistently across admin and API.
24. ✅ Keep only markdown in persistence; render HTML in API Resource classes at response time.
25. ✅ Implement Blog and Todo policies with shared rules.
26. ✅ Admin bypass across all customers (via `before()` in each policy).
27. ✅ Customer role requires customer membership plus permission.
28. ✅ Record-level customer match enforcement.
29. ✅ Phase 5 - Blog asset storage (customer remote disk).
30. ✅ Create customer-scoped asset storage configuration model(s) with selectable driver type per customer: s3, ftp, or cloudinary.
31. ✅ Install and configure cloudinary-labs/cloudinary-laravel for the Cloudinary option.
32. ✅ Build an asset service that resolves the correct storage connection per customer and never writes Blog images to local disk.
33. ✅ Add Filament admin pages/actions for per-customer asset connection setup and validation (credentials, bucket/path/folder, connection test).
34. ✅ Add customer-scoped asset upload flow that stores metadata and remote URL/public id only, scoped to customer context.
35. ✅ Add customer-scoped asset listing/selection flows so Blog records can attach only assets belonging to the same customer.
36. ✅ Phase 6 - Shared query layer.
37. ✅ Create a Queries folder with Blog, Todo, and Asset query classes based on Spatie Query Builder.
38. ✅ Add reusable customer-scope trait so query constraints are applied consistently across domains and surfaces.
39. ✅ Use shared query classes in admin and API controllers.
40. ✅ Phase 7 - Todo Kanban core.
41. ✅ Build customer-scoped todo kanban page with columns todo, planned, in_progress, blocked, done.
42. ✅ Install spatie/eloquent-sortable; add `position` (unsignedInteger) column to todos; scope sort by customer_id + status via `buildSortQuery()`.
43. ✅ SortableJS drag-and-drop in blade view; dispatch `status-changed` and `sort-changed` Livewire events; persist via `setNewOrder()`.
44. ✅ Header actions on kanban: New Todo (create modal); cards link to edit modal via `wire:click` + `editTodo` action.
45. ✅ Phase 8 - Customer social automation setup.
46. ✅ Add customer automation toggle (enabled/disabled) and platform template relations.
47. ✅ Create platform model/table (global platforms, customer relations) and customer todo template table for social automation defaults.
48. ✅ Template fields include customer_id, platform_id, title/body template fields, default status/column, due offset (ISO-8601 duration), and active flag.
49. ✅ Enforce template data validity so due offset is present and parseable.
50. ✅ Phase 9 - Blog-created automation flow.
51. ✅ On Blog created, dispatch async job to generate customer social todos when customer automation is enabled.
52. ✅ Job resolves active templates for the blog customer and creates one todo per template.
53. ✅ Due-date rule: primary base datetime is blog.publish_at, fallback is blog.created_at, final due date is base datetime plus template ISO-8601 offset.
54. ✅ Always require due date on created todos; if computation fails, job fails with logged reason.
55. ✅ Add idempotency strategy (for example unique key on blog_id + customer_id + template_id) to prevent duplicate todos on retries.
56. ✅ Phase 10 - Admin UI polish.
57. ✅ All 8 Filament resources use Heroicons for navigation icons.
58. ✅ ID columns removed from all resource tables.
59. ✅ Slug removed from all forms and tables; generated automatically via Observers (CustomerObserver, PlatformObserver, BlogObserver) on creating/saving.
60. ✅ `customer_id` removed from all tenant-scoped forms (BlogForm, TodoForm, CustomerAssetForm, CustomerTodoTemplateForm) — tenancy injects it automatically.
61. ✅ Admin-only resources (Users, Platforms, CustomerAssetConnections, Customers) set `$isScopedToTenant = false` with dedicated policies returning false for non-admins.
62. ✅ Tailwind v4 + @tailwindcss/vite wired; Filament custom theme at `resources/css/filament/admin/theme.css` compiled via Vite.
63. ✅ Dashboard widgets: LatestBlogsWidget (50% width, links to edit) and UpcomingTodosWidget (50% width, overdue + due ≤7 days, links to edit).
64. ✅ Phase 11 - OpenAPI docs.
65. ✅ Annotate API endpoints and schemas with attributes.
66. ✅ Expose public Swagger UI at api/docs backed by generated OpenAPI JSON.
67. ✅ Document Query Builder contracts (filter, sort, include, pagination) for Blog and Todo endpoints.
68. ✅ Document /me and /customers endpoints (extracted closures to MeController and CustomerListController); automation templates and assets are Filament-admin-only — no public API routes.
69. ✅ Document Blog asset fields in BlogSchema (assets array, public_url, provider_asset_id); asset upload/management is Filament-admin-only — no public API routes.
70. 🔲 Phase 12 - Verification and CI.
71. 🔲 Add Pest tests for role/membership policy matrix across Blog and Todo.
72. 🔲 Add parity tests proving admin/API share FormRequests and query behavior.
73. 🔲 Add Filament feature tests for key admin resources/pages/actions under customer context.
74. 🔲 Add asset storage tests for Blog: per-customer driver resolution, successful upload for s3/ftp/cloudinary adapters, and explicit failure if local disk is selected.
75. 🔲 Add automation tests: job dispatch, expected todo creation, due-date population, idempotent retries, and disabled automation no-op.
76. 🔲 Add localization tests/lints to enforce key naming and detect missing translation keys.
77. 🔲 Add CI checks for tests, lint/static analysis, OpenAPI generation consistency, and translation consistency.

### Database Structure

The following is the proposed baseline schema for implementation.

1. customers
- id uuid primary key.
- name string.
- slug string unique.
- automation_enabled boolean default false.
- created_at, updated_at.

2. customer_user
- id bigint primary key (pivot, no public exposure).
- customer_id uuid foreign key to customers.
- user_id uuid foreign key to users.
- created_at, updated_at.
- Unique index on (customer_id, user_id).

3. platforms
- id uuid primary key.
- name string unique (for example tiktok, facebook, instagram, x).
- slug string unique.
- is_active boolean default true.
- created_at, updated_at.

4. customer_platforms
- id bigint primary key (pivot, no public exposure).
- customer_id uuid foreign key to customers.
- platform_id uuid foreign key to platforms.
- is_enabled boolean default true.
- created_at, updated_at.
- Unique index on (customer_id, platform_id).

5. blogs
- id uuid primary key.
- customer_id uuid foreign key to customers.
- title string.
- slug string.
- excerpt text nullable.
- content_markdown longText.
- status string indexed.
- publish_at timestamp nullable indexed.
- created_by uuid foreign key to users nullable.
- updated_by uuid foreign key to users nullable.
- created_at, updated_at.
- Unique index on (customer_id, slug).

6. todos
- id uuid primary key.
- customer_id uuid foreign key to customers.
- blog_id uuid foreign key to blogs nullable.
- platform_id uuid foreign key to platforms nullable.
- title string.
- content_markdown text nullable.
- status string indexed (todo, planned, in_progress, blocked, done).
- position string indexed (per-column order key).
- due_at timestamp indexed.
- created_by uuid foreign key to users nullable.
- updated_by uuid foreign key to users nullable.
- created_at, updated_at.

7. customer_todo_templates
- id uuid primary key.
- customer_id uuid foreign key to customers.
- platform_id uuid foreign key to platforms.
- title_template string.
- body_template text nullable.
- default_status string.
- due_offset_iso8601 string.
- is_active boolean default true.
- created_at, updated_at.

8. customer_asset_connections
- id uuid primary key.
- customer_id uuid foreign key to customers unique.
- driver enum/string (s3, ftp, cloudinary).
- config_encrypted json/text (encrypted credentials and driver config).
- is_active boolean default true.
- last_validated_at timestamp nullable.
- created_at, updated_at.

9. customer_assets
- id uuid primary key.
- customer_id uuid foreign key to customers.
- uploaded_by uuid foreign key to users nullable.
- connection_id uuid foreign key to customer_asset_connections.
- disk_driver string.
- path string nullable.
- public_url text nullable.
- provider_asset_id string nullable (for example Cloudinary public id).
- filename string.
- mime_type string.
- size_bytes bigint.
- meta json nullable.
- created_at, updated_at.

10. blog_assets
- id bigint primary key (pivot, no public exposure).
- blog_id uuid foreign key to blogs.
- customer_asset_id uuid foreign key to customer_assets.
- sort_order integer default 0.
- created_at, updated_at.
- Unique index on (blog_id, customer_asset_id).

11. Spatie + Sanctum framework tables
- Spatie: roles, permissions, model_has_roles, model_has_permissions, role_has_permissions.
- Sanctum: personal_access_tokens.

Core relationship rules:
- Every domain record (blogs, todos, customer_assets, templates) belongs to exactly one customer.
- Cross-customer links are forbidden by both foreign key design and policy checks.
- due_at on todos is required and never nullable.
- Local filesystem is not used for Blog assets.
- Markdown is the persisted source of truth; HTML is rendered in API Resources and is not stored in database columns.
- Domain models use UUIDs (via Laravel HasUuids trait); pure pivot tables use bigint for internal join efficiency.
- users table uses uuid primary key to align with all created_by/updated_by references.

### Relevant Files
- Laravel scaffold/app config files.
- Customer and membership migrations/models.
- Blog/Todo migrations, models, controllers, routes, policies, and Filament resources/pages/actions.
- Customer asset storage configuration table/model(s), customer-owned asset records, and asset service (driver adapters for s3, ftp, cloudinary).
- Shared FormRequest classes and shared query classes in Queries.
- Markdown package config and shared markdown rendering service used by Blog and Todo content rendering.
- Cloudinary integration config using cloudinary-labs/cloudinary-laravel.
- Customer platform/template migrations/models for automation config.
- Job/listener/event classes for Blog-created todo generation.
- OpenAPI attributes plus generation script plus docs endpoint/page.
- Pest test suites for auth scope, query parity, automation behavior, and localization checks.

### Verification
1. Schema includes customers, memberships, platforms, customer todo templates, blogs, and todos.
2. Policy tests pass for all role + membership combinations.
3. Query parity tests pass for admin/API with same filters/sorts/includes.
4. Filament admin resources/pages/actions enforce expected customer-scoped behavior.
5. Markdown content for Blog and Todo validates and renders consistently between admin and API using shared markdown service.
6. Markdown is persisted without HTML columns, and API Resources return rendered HTML from markdown when needed.
7. Blog-created job creates customer-scoped social todos with non-null due dates.
8. Idempotency constraints prevent duplicates on retries.
9. api/docs renders and matches implemented API/query behavior.
10. Translation keys follow domain.section.label_slug format and resolve correctly from the JSON language file.
11. Sanctum bearer and cookie flows both authenticate correctly, and non-trusted domains are blocked from stateful cookie auth.
12. Assets are owned and scoped per customer for upload, list, attach, and read access, and cross-customer asset access is denied.
13. Assets are stored remotely per customer using the configured driver (s3, ftp, or cloudinary) and never persisted on local disk.
14. Full CI checks pass.

### Decisions
- Customer template storage: separate table customer_todo_templates.
- Platform modeling: dedicated platforms table with customer relations.
- Trigger: on every blog create, gated by customer automation enabled flag.
- Due date base: publish_at, fallback to created_at.
- Offset format: ISO-8601 duration string.
- Duplicate handling: idempotent (no duplicates).
- Admin pages stack: filament/filament.
- Markdown stack for Blog and Todo content: graham-campbell/markdown.
- Content persistence strategy: markdown only in database; HTML rendered in API Resource classes.
- Localization strategy: single JSON language file with keys in domain.section.label_slug format.
- Sanctum strategy: support both bearer token API auth and cookie-based stateful auth with explicit trusted-domain onboarding.
- Asset storage strategy: per-customer remote disk configuration with s3, ftp, or cloudinary via cloudinary-labs/cloudinary-laravel; local image storage disabled; asset ownership and access are customer-scoped.

### Rules & Conventions

Rules learned during implementation that apply to all future work on this project.

#### Filament
- Always scaffold panels and resources via Artisan — never create them by hand.
- Install/reinstall panel: `php artisan filament:install --panels --no-interaction`
- Add a new panel: `php artisan make:filament-panel <name> --no-interaction`
- Add a resource: `php artisan make:filament-resource <Model> --panel=admin --generate --no-interaction`
- After any composer change run: `php artisan filament:upgrade`
- **Tenancy**: the admin panel uses `->tenant(Customer::class, slugAttribute: 'slug')`. Filament automatically scopes all resource queries to the current customer. Do NOT add manual `getEloquentQuery()` overrides for customer scoping.
- `User` implements `HasTenants`: admins get all customers via `Customer::all()`; others get `$this->customers`.
- Resources that are NOT scoped to a tenant (Customer itself, User) must set `protected static bool $isScopedToTenant = false`.
- `->authorizeWithPolicies()` is enabled on the panel. Filament calls policies for all CRUD actions. `viewAny` and `create` policy methods that take a `Customer` parameter must make it optional (`?Customer $customer = null`); when `null`, return `true` — tenancy already scopes the query.
- **Filament v5 action imports**: actions are imported from `Filament\Actions\*` (not `Filament\Tables\Actions\*`). Table row actions use `->recordActions([])` and toolbar/bulk actions use `->toolbarActions([])`.
- **Relation managers**: `form()` method signature is `form(Schema $schema): Schema` using `Filament\Schemas\Schema` — not `Filament\Forms\Form`. Share form fields via a static `MyForm::components()` array method reused across the resource and its relation managers.
- **Page full width**: override `protected string|Width|null $maxContentWidth = Width::Full` using `Filament\Support\Enums\Width` — not a plain string.
- **DateTimePicker**: always use `->native(false)` for a consistent custom UI across all form schemas.
- **Status Select fields**: always use `->selectablePlaceholder(false)` with an explicit `->default()` so the field is never blank.
- **Table column callbacks**: use `?string $state` closures instead of `$record` closures for `->url()`, `->visible()`, and `->color()` — accessing `$record` in these callbacks throws when the record is null during column header rendering.
- **Badge colors**: always use Filament semantic tokens (`gray`, `info`, `warning`, `danger`, `success`), never raw Tailwind color names (`blue`, `red`, etc.).
- **Status column ordering**: place the status badge column first in all resource tables and dashboard widgets.
- **ToggleColumn**: use `Filament\Tables\Columns\ToggleColumn` for boolean fields that should be togglable inline — replaces `IconColumn::make()->boolean()`.

#### Enums
- `App\Enums\StatusColor` is the single source of truth for status/badge/kanban color tokens and their corresponding Tailwind classes.
- Each domain status enum (e.g. `BlogStatus`, `TodoStatus`) must implement `color(): StatusColor` and `static colorFor(string $value): string` for use in Filament badge color closures.
- `TodoStatus::kanbanColumns()` returns `header_color_classes` from `StatusColor::kanbanHeaderClasses()` — the Kanban Blade view must not hardcode a color map; it reads `$status['header_color_classes']` directly.
- Validation rules should use `Rule::enum(StatusEnum::class)` rather than `Rule::in([...])` so adding a new case automatically extends validation.

#### Migrations
- Always implement both `up()` and `down()` with actual schema changes **before** running `php artisan migrate`. Never run a migration whose body is still the empty scaffold.
- Verify the migration file content with `cat` or a file read before executing to catch empty scaffolds early.

#### Code style
- Run `./vendor/bin/pint` after every batch of changes.

#### Eloquent best practices
- Prefer `whereBelongsTo($model)` over manual foreign-key `where()` clauses when filtering by model relationships.
- When creating connected models, prefer relation methods with `associate()` (for example `->blog()->associate($blog)`) instead of writing raw foreign key values directly.

#### Frontend / Vite
- Third-party JS libraries (e.g. FullCalendar) must be installed via npm and bundled through Vite — never use CDN `<script>` or `<link>` tags in production Blade views.
- Add a dedicated entry file (e.g. `resources/js/fullcalendar.js`) that imports the library and assigns it to `window.*`, then register it in `vite.config.js`'s `input` array.
- Import accompanying CSS inside the JS entry file (`import '../css/fullcalendar.css'`) so the stylesheet is bundled and injected automatically.

#### FullCalendar
- FullCalendar `saade/filament-fullcalendar` only supports Filament v2/v3 — do not install it; build pages manually with the npm packages `@fullcalendar/core`, `@fullcalendar/daygrid`, `@fullcalendar/timegrid`, `@fullcalendar/list`.
- Dark mode: override FullCalendar's CSS custom properties (`--fc-*`) under `html.dark {}` to match Filament's Tailwind gray palette.
- Week start: use `locale: FullCalendar.enGbLocale` (import `@fullcalendar/core/locales/en-gb`) instead of `firstDay: 1` — locale-driven first-day is reliable; `firstDay` alone can be ignored.
- Width fix: always call `setTimeout(() => calendar.updateSize(), 50)` after `calendar.render()` and on `livewire:navigated` to force a correct size calculation after page layout paints.
- Livewire integration: only `public` methods can be called from the frontend via `$wire.call()`. Separate data-fetch methods (`refreshEvents(): array`) from Filament's protected `getViewData()`.

#### Observers / Automation
- The `GenerateSocialTodosJob` is triggered in `BlogObserver::updated()`, not `created()`.
- Dispatch conditions: blog must be `published`, `publish_at` must be set, and `publish_at` must not be older than one week.
- The job itself also guards: skips if the blog already has any connected todos; idempotency per template is enforced via a unique check on `(blog_id, customer_id, generated_from_template_id)`.

#### Authorization
- Policy checks: controllers call `$this->authorize()` for the API; Filament calls policies automatically via `->authorizeWithPolicies()`.
- FormRequest `authorize()` must always return `true` — validation only, no policy logic.

#### Schema
- `users` table uses the default Laravel bigint auto-increment `id`. All `created_by` / `updated_by` / `uploaded_by` / pivot `user_id` columns are `foreignId` (bigint), not `foreignUuid`.
- All other domain models (customers, blogs, todos, platforms, templates, assets, connections) use UUID primary keys via `HasUuids`.
- Pure pivot tables use bigint `id` for internal join efficiency.

#### Code style
- Run `./vendor/bin/pint` after every batch of changes.

#### Eloquent best practices
- Prefer `whereBelongsTo($model)` over manual foreign-key `where()` clauses when filtering by model relationships.
- When creating connected models, prefer relation methods with `associate()` (for example `->blog()->associate($blog)`) instead of writing raw foreign key values directly.

#### OpenAPI
- Use `zircote/swagger-php ^6` with PHP 8 Attributes style (`OpenApi\Attributes as OA`).
- Generate docs via the CLI binary, not the PHP `Generator` class: `./vendor/bin/openapi app/OpenApi app/Http/Controllers/Api --output public/api-docs.json --format json`
- A composer script `composer docs` runs the above command. The Artisan command `php artisan openapi:generate` shells out to the same binary.
- `app/OpenApi/OpenApi.php` holds only `OA\Info`, `OA\Server`, `OA\SecurityScheme`, `OA\Tag`, and `OA\Parameter` component definitions (all as attributes on the holder class).
- **Each schema must be its own class** in `app/OpenApi/Schemas/`. Stacking multiple `#[OA\Schema]` attributes on a single class causes swagger-php v6 to silently drop all but the first schema — they will not appear in `components.schemas`.
- `ApiDocsController::json()` serves the pre-generated `public/api-docs.json` file. Regenerate with `composer docs` before deploying or in CI.
- Scan paths: `app/OpenApi` and `app/Http/Controllers/Api`.

#### Impersonation
- Use `stechstudio/filament-impersonate` for admin impersonation actions in Filament resources/pages.
- Do not use direct `lab404/laravel-impersonate` manager calls in application code.

#### Seeders
- `DatabaseSeeder` is the single entry point: `RoleSeeder → PlatformSeeder → AdminUserSeeder`.
- Seeders use `firstOrCreate` / `updateOrCreate` so they are safe to re-run.
- `TestCase` sets `$seed = true` and `$seeder = DatabaseSeeder::class` so baseline roles, platforms, and admin user are always available in tests.

### Further Considerations
1. Confirm whether templates should support per-platform time-of-day normalization.
2. Decide whether failed automation jobs should be auto-retried with backoff or moved quickly to manual review.
3. Decide whether template-generated todo content should snapshot platform/template metadata at creation time.
4. Decide whether to reserve top-level key namespaces (blog, todo, customer, common) now to prevent collisions.
5. Decide how customer storage credentials are encrypted, rotated, and revalidated (including Cloudinary keys and FTP passwords).
6. Decide whether to enable `->authorizeWithPolicies()` on the Filament panel. Currently policies are API-only; Filament uses tenancy for scoping. Enabling it would require making the `Customer $customer` parameter optional on `viewAny` and `create` policy methods (Filament calls those without a customer argument).
