# Gravitas Coaching

## Theme Options live-output fix — 1.6.3 / Core 1.3.1

Theme Options now use their saved database values as the single source of truth across native templates, generated CSS, headers, footers, blog layouts, and imported Elementor homepage widgets. Saving options also refreshes Elementor's generated CSS cache. Existing user-created Elementor elements are preserved.

## Bundled plugin installer fix — 1.6.4

Gravitas Core and Gravitas Video are prepackaged inside the theme (`inc/plugins/`). The theme installs and activates both automatically on the first admin load after activation, so no separate ZIP upload is required. They remain available from the Plugin Center if a manual reinstall is ever needed.

## Installer packaging fix — 1.6.1

The distribution is rebuilt as a theme-only WordPress installer archive with exactly one top-level `gravitas-coaching` directory and its required `style.css` file directly inside that directory.

## Complete Elementor editing and title controls — 1.6.0

- Pages, posts, and every wp-admin content type with editor support are enabled for Elementor without removing existing Elementor preferences.
- “Edit with Elementor” is guaranteed on content-list rows, classic edit screens, and the front-end admin toolbar for eligible content.
- Imported Home content is a genuine Elementor document containing editable hero, introduction, programs, team, and journal sections.
- Gravitas Core 1.3.0 adds independent native page/post title-bar height and title-size controls under **Gravitas → Theme Options → Page Titles**.
- Eight account-owned Magnific images are packaged locally and distributed across the homepage, services, trainer portfolios, and nine journal articles. The demo has no remote image dependency.

## Elementor rendering fix — 1.5.1

Elementor-built pages and custom posts now bypass the native Gravitas title, article, profile, service, and video wrappers. They render through one unconstrained full-width document path, while Elementor Pro Theme Builder locations continue to take priority. The Elementor path also removes the theme shell's clipping so responsive containers, sticky elements, and overlays remain interactive.

## Theme controls and demo cleanup — 1.5.0

- The Gravitas Core Theme Options screen now has a dedicated **Hero** tab for the background image, eyebrow, two-line heading, description, both calls to action, minimum height, overlay strength, and content alignment.
- **Appearance → Import Gravitas Demo** now includes an **Uninstall Demo** action. Imported posts and custom posts are moved to Trash; tracked demo media and the Gravitas demo menu are deleted after confirmation.
- Elementor editing is enabled dynamically for posts, pages, trainers, coaching services, testimonials, videos, playlists, channels, series, people, and other Gravitas content types that support the editor.
- Elementor Theme Builder single and archive locations are honored by both the theme and Gravitas Video templates.

## Blog and Elementor update — 1.4.0

Gravitas Coaching now includes six original, responsive blog designs: Editorial Feature, Modern Magazine, Creative Masonry, Classic Grid, Elegant List, and Minimal Journal. Open **Appearance → Import Gravitas Demo** to import the complete coaching site or any blog-only starter. Each blog starter creates a Journal page, nine categorized articles, local featured images, and the selected layout without duplicating content on later runs.

Change the design later under **Appearance → Customize → Gravitas Blog**. The same panel controls the journal eyebrow, description, and sidebar. Existing sites default to Editorial Feature and retain their content.

Elementor support now includes:

- Theme Builder locations for the header, footer, single posts/pages, trainer and service profiles, archives, directories, and search results.
- **Gravitas Full Width** and **Gravitas Canvas** page templates.
- Automatic editor support for pages, posts, trainers, and coaching services.
- Gravitas Trainer Directory, Services, and Blog Posts widgets in the **Gravitas Coaching** Elementor category when Gravitas Core is active.
- An `[elevora_posts]` shortcode for adding any of the six blog layouts to Elementor or the block editor.

All demo designs and implementation code are original to Gravitas Coaching. The supplied reference theme was used only to understand the breadth of layout choices; no reference-theme templates, assets, or proprietary importer data are redistributed.

## Previous design update — 1.3.0

The consulting design uses a navy header, ivory backgrounds, gold accents, a compact photographic hero, overlapping service cards, and a four-step coaching process. Existing directories, forms, service records, and lower homepage content remain available.

- Edit the main headline and its gold second line in **Appearance → Customize → Gravitas Homepage**. Clear the gold headline setting if your existing headline should stand alone.
- The four compact cards use the first four published services sorted by menu order and date. Starter cards appear when no services have been published.
- Existing saved colors and homepage text are retained. On an existing site, set the accent to `#c6a454` in the Customizer. If Gravitas Core is active, set Accent to `#c6a454`, Text and Dark Background to `#0c1d30`, and Page Background to `#f7f3eb` in its visual identity settings; the plugin's saved settings take precedence.
- Gravitas Core includes matching defaults for new installations and is prepackaged inside the theme.
- The sibling `design-preview/index.html` is a static layout preview with sample navigation and starter cards, not a WordPress installation. Browser checks cover widths 320, 390, 768, 1024, and 1440, mobile menu open/Escape, and dark-mode interaction. PHP execution and database-backed flows need validation on a WordPress staging site.

Install `gravitas-coaching.zip`. Gravitas Core and Gravitas Video are prepackaged in the theme, but are installed only when you start the optional guided setup or request them from the Plugin Center; their standalone ZIPs remain available for independent installs.

Gravitas Coaching is an original WordPress theme for soft skills coaching, image consulting, executive presence, personal branding, and professional development practices. Its editorial presentation is inspired by the strong card hierarchy and immersive media treatment of the supplied reference theme, while the layout, visual identity, code, assets, and consulting workflows are newly created.

## Installation

1. Upload `gravitas-coaching.zip` in **Appearance → Themes → Add New → Upload Theme**.
2. Activate **Gravitas Coaching**.
3. Open **Appearance → Import Gravitas Demo**.
4. Choose **Import Complete Demo**.
5. Visit **Settings → Permalinks** and save once if your server does not automatically refresh rewrite rules.

The importer is idempotent. Running it again adds missing demo items without duplicating or overwriting existing demo content.

Activating the theme does not install plugins or import starter content. To build the starter site, open **Appearance → Gravitas Setup** and start the optional guided setup; it installs and activates the included **Gravitas Core** and **Gravitas Video** plugins before running the demo importer. You can also manage either plugin manually from **Appearance → Gravitas Plugins**.

## Companion plugins

- **Gravitas Core** provides the branded admin dashboard, logo, color, typography, header, layout, footer, social, custom CSS, Elementor, and system-status controls.
- **Gravitas Video** provides videos, channels, playlists, series, people, reports, scheduled feeds, topics, levels, player sources, subtitles, chapters, downloads, ads, membership access, analytics, reactions, watch later, history, subscriptions, front-end submissions, and import tools.

Both plugins are original clean-room implementations. Public integrations are installed from the official WordPress plugin repository and are not redistributed by the theme.

## Core features

- Responsive editorial homepage focused on soft skills, image consulting, executive presence, personal branding, and career readiness.
- Trainer directory with keyword search, taxonomy filters, sorting, grid/list views, progressive AJAX enhancement, and shareable non-JavaScript URLs.
- Dedicated trainer profiles with services, specializations, formats, languages, credentials, booking details, embedded video, comments/reviews, portfolio gallery, schema markup, and related trainers.
- Coaching service post type with categories, delivery methods, audiences, pricing, duration, related trainers, and schema markup.
- Native fields that work without a plugin plus automatically registered ACF field groups when Advanced Custom Fields is active.
- Gutenberg `theme.json`, wide/full alignment, editor styling, custom logo, menus, widget areas, RTL styling, accessibility controls, and reduced-motion support.
- Sticky responsive header, mobile navigation, search overlay, dark mode with saved preference, mega-menu-ready submenu styling, and social sharing.
- WooCommerce product gallery support and wrappers, plus presentational compatibility for Elementor Theme Builder, bbPress, BuddyPress, and Paid Memberships Pro.
- One-click local demo importer with no required importer plugin and no remote image dependency.
- Moderated front-end trainer applications and reusable `[elevora_trainers]`, `[elevora_services]`, and `[elevora_trainer_submission]` shortcodes.
- Video library shortcodes: `[gravitas_video_player]`, `[gravitas_video_grid]`, `[gravitas_playlist]`, `[gravitas_channel]`, `[gravitas_series]`, `[gravitas_watch_later]`, `[gravitas_history]`, `[gravitas_video_submit]`, and `[gravitas_my_videos]`.

## Custom content model

### Trainers (`trainer`)

Taxonomies:

- `trainer_specialization`
- `trainer_location`
- `coaching_format`
- `trainer_language`

Fields:

- `elevora_role`
- `elevora_tagline`
- `elevora_experience`
- `elevora_hourly_rate`
- `elevora_rating`
- `elevora_sessions`
- `elevora_availability`
- `elevora_email`
- `elevora_phone`
- `elevora_website`
- `elevora_booking_url`
- `elevora_video_url`
- `elevora_credentials`
- `elevora_trainer_services`
- `elevora_portfolio`

### Services (`coaching_service`)

Taxonomies:

- `service_category`
- `service_delivery`
- `service_audience`

Fields:

- `elevora_service_summary`
- `elevora_service_duration`
- `elevora_service_price`
- `elevora_service_icon`
- `elevora_service_featured`
- `elevora_booking_url`
- `elevora_service_trainers`

### Testimonials (`testimonial`)

- `elevora_client_role`
- `elevora_client_company`
- `elevora_testimonial_rating`

## Optional plugins

The theme works on its own. Optional integrations include Advanced Custom Fields, WooCommerce, Elementor, bbPress, BuddyPress, Paid Memberships Pro, Contact Form 7, and an SEO plugin of your choice.

## Notes for production

- Replace the demo contact details and booking links in **Appearance → Customize** and in each trainer/service record.
- The generated demo photography is included locally and may be replaced through the Media Library.
- For long-term portability across theme changes, a production deployment may move custom post type registration into a site-specific companion plugin while preserving the same slugs and meta keys.
