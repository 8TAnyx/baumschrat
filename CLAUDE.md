# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Hugo static site for Baumschrat, a professional tree services company (Daniel Lindner) in Graz, Austria. Converted from a React/Vite SPA at `../www/`. Custom theme named `baumschrat`.

**Tech stack:** Hugo v0.161.1+, Tailwind CSS v3 via CDN, vanilla JS for gallery carousel and contact form.

## Common Commands

```bash
# Development server (live reload)
hugo server --port 1314

# Production build (outputs to public/)
hugo build

# Build with drafts
hugo server --buildDrafts
```

## Architecture

### Theme: `themes/baumschrat/`
Single-page layout — `layouts/index.html` composes all section partials in order:
`nav → hero → services → gallery → about → contact → footer`

All sections are in `themes/baumschrat/layouts/partials/`. Each partial is self-contained (HTML + inline `<script>` if needed).

### Content & Data
- `content/_index.md` — home page (front matter only; display content lives in partials)
- `content/datenschutz.md` — privacy policy (rendered by `_default/single.html`)
- `content/leistungen/` — **extensible services section** (see below)
- `content/galerie/` — **extensible gallery** (see below)

Site-wide params (phone, email, city, address, WKO URL) live in `hugo.toml [params]`.

### Adding a New Service
Create a markdown file in `content/leistungen/`:

```markdown
---
title: "Neue Leistung"
weight: 40          # controls display order (lower = first)
params:
  icon: "shield"    # see icons/service.html for available names
---

Beschreibung der Leistung in ein bis zwei Sätzen.
```

Available icons: `scissors`, `layers`, `pruning`, `shield`, `tree`, `truck`. To add a new icon, add a branch to `themes/baumschrat/layouts/partials/icons/service.html`.

The section heading, subtitle, and network box text come from `content/leistungen/_index.md` params — edit there to change those texts without touching templates.

### Adding a Gallery Image
`content/galerie/` is a Hugo leaf bundle. Drop any `.jpg` into that directory and it appears in the carousel automatically (sorted last). To set alt text and display order, add a `resources` entry in `content/galerie/index.md`:

```yaml
resources:
  - src: "new-photo.jpg"
    title: "Beschreibung für Alt-Text"
    params:
      weight: 8    # display position; images without weight sort by filename
```

Hugo generates two optimised variants at build time: a 1200×600 crop for the carousel and a 128×128 crop for thumbnails. The standalone gallery page at `/galerie/` shows a 600×600 grid. Title and subtitle text come from `content/galerie/index.md` params.

### Contact Form
`partials/contact.html` POSTs JSON to `/send_email.php` (copied to `static/`). Fields: `firstName`, `lastName`, `email`, `phone`, `location`, `message`. Success/error state is handled inline without page reload.

### Images
- `static/images/logo.png` — inverted white in hero, normal color in nav/footer
- `static/images/hero-background.jpg` — full-screen hero background
- `static/images/work-8.jpg`, `work-9.jpg` — about section stacked images (not in gallery)
- `content/galerie/*.jpg` — gallery images (processed by Hugo at build time)

## Deployment
The `public/` output directory is the deployable artifact. Copy `public/` contents alongside `static/send_email.php` (already included via `static/`) to a PHP-capable web server.
