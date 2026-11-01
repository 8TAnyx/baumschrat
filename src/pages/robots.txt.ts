import type { APIRoute } from 'astro';

// Testumgebung (PUBLIC_NOINDEX=true) komplett für Suchmaschinen sperren.
export const GET: APIRoute = ({ site }) => {
  const body =
    import.meta.env.PUBLIC_NOINDEX === 'true'
      ? 'User-agent: *\nDisallow: /\n'
      : `User-agent: *\nAllow: /\n\nSitemap: ${new URL('sitemap-index.xml', site).href}\n`;
  return new Response(body, { headers: { 'Content-Type': 'text/plain; charset=utf-8' } });
};
