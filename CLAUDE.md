# CLAUDE.md

Guidance for Claude Code when working in this repository.

## Project

Website for Baumschrat, a one-person tree care business (Daniel Lindner) in Graz, Austria. German-language (de-AT) content. Static site built with Astro 7 + Tailwind CSS 4, deployed via FTP to a PHP-capable host. Replaces an earlier Hugo version and a React/Vite prototype.

## Commands

```bash
npm run dev        # dev server, port 4321
npm run build      # static build to dist/
npm run check      # astro check (types)
SITE_BASE=/test npm run build   # build as served on the test environment
```

Astro is pinned to 7.4.0 because newer 7.4.x requires Node >= 22.23.4 and the local machine has 22.23.3.

## Architecture

- `src/content.config.ts` – three collections: `leistungen` (services), `ratgeber` (blog), `regionen` (service-area pages). Schemas are strict; build fails on missing fields.
- `src/data/site.ts` – company data, nav, and `u()` helper. **Every internal link must go through `u()`** so it works under the `/test/` base path. Markdown links are rewritten by `src/lib/rehype-base-links.mjs` (requires `@astrojs/markdown-remark`, since Astro 7's default Markdown processor doesn't run rehype plugins).
- `src/layouts/Base.astro` – head/SEO, LocalBusiness JSON-LD on every page, header, footer, mobile action bar. Pages pass extra JSON-LD (Service, BlogPosting, FAQPage, BreadcrumbList) via `jsonLd`.
- `src/components/ContactForm.astro` – multipart POST with photo upload; photos are downscaled client-side to 1800px before upload. Backend: `public/send_email.php` (honeypot, min fill time, per-IP rate limit, header-injection safe, MIME attachments).
- Images live in `src/assets/` and are optimised by `astro:assets`. Always pass `width` (and `height` + `fit="cover"` for cropped hero images) together with `widths`, otherwise the src fallback is the full-size original.
- Tailwind 4 theme tokens (`wald`, `moos`, `rinde`, `papier`, `tinte`) and custom utilities (`btn-*`, `card`, `eyebrow`, `container-page`, `prose-baum`) are in `src/styles/global.css`. Custom classes must be `@utility`, not `@layer components`, to be usable with `@apply`.

## Environments

`PUBLIC_NOINDEX=true` adds `noindex` and a disallow-all robots.txt. CI sets it and `SITE_BASE=/test` for every branch except `main`.

## Content rules

- Legal statements (Grazer Baumschutzverordnung, Stmk. Baumschutzgesetz) must match the source texts on ris.bka.gv.at; cite sources at the end of Ratgeber articles.
- Don't invent business facts (certifications, insurance, years of experience) – ask the owner.
