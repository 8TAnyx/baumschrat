# Baumschrat Website

Website von Baumschrat (Daniel Lindner) – Baumpflege, Baumkontrolle und Spezialfällungen in Graz, Graz-Umgebung und im Mürztal.

Statische Seite mit [Astro](https://astro.build) und Tailwind CSS. Keine Cookies, kein Tracking, keine externen Ressourcen.

## Entwicklung

Voraussetzung: Node.js 22 (siehe `.nvmrc`).

```bash
npm install
npm run dev       # Entwicklungsserver auf http://localhost:4321
npm run build     # Produktions-Build nach dist/
npm run preview   # Build lokal ansehen
npm run check     # Typprüfung
```

## Inhalte pflegen

| Was | Wo |
|---|---|
| Firmendaten (Telefon, Adresse, …) | `src/data/site.ts` |
| Leistungen | `src/content/leistungen/*.md` |
| Ratgeber-Artikel | `src/content/ratgeber/*.md` |
| Regionsseiten | `src/content/regionen/*.md` |
| Allgemeine FAQ | `src/data/faq.ts` |
| Galerie | Foto nach `src/assets/fotos/`, dann in `src/data/galerie.ts` eintragen |

Neuer Ratgeber-Artikel: Markdown-Datei in `src/content/ratgeber/` anlegen, Frontmatter wie in den bestehenden Artikeln (`title`, `date`, `description`, `image`, `imageAlt`, `thema`, `leistungen`). Mit `draft: true` wird ein Artikel nicht veröffentlicht.

## Deployment

GitHub Actions baut bei jedem Push und lädt `dist/` per FTP hoch:

- `main` → Produktion (`www.baumschrat.at`)
- `test`, `relaunch-astro` → Testumgebung unter `/test/` (mit `noindex`)

Das Kontaktformular sendet an `public/send_email.php` – der Webserver braucht PHP mit `mail()`.
