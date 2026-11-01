// @ts-check
import { defineConfig } from 'astro/config';
import sitemap from '@astrojs/sitemap';
import tailwindcss from '@tailwindcss/vite';
import rehypeBaseLinks from './src/lib/rehype-base-links.mjs';

// Testumgebung wird unter einem Unterpfad ausgeliefert (SITE_BASE=/test im Workflow).
const base = process.env.SITE_BASE || '/';

export default defineConfig({
  site: 'https://www.baumschrat.at',
  base,
  trailingSlash: 'always',
  integrations: [sitemap()],
  // Alte URLs aus der Hugo-Version weiterleiten, damit bestehende Links und Google-Einträge funktionieren.
  redirects: {
    '/leistungen/baumschnitt/': `${base.replace(/\/$/, '')}/leistungen/baumpflege/`,
  },
  markdown: {
    rehypePlugins: [[rehypeBaseLinks, { base }]],
  },
  vite: {
    plugins: [tailwindcss()],
  },
});
