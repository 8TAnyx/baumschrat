// Helfer für strukturierte Daten (schema.org) – werden von den Seiten an das Base-Layout übergeben.
export type Crumb = { label: string; href: string };

export function breadcrumbLd(crumbs: Crumb[], site: URL) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: crumbs.map((c, i) => ({
      '@type': 'ListItem',
      position: i + 1,
      name: c.label,
      item: new URL(c.href, site).href,
    })),
  };
}

export function faqLd(items: { frage: string; antwort: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: items.map((f) => ({
      '@type': 'Question',
      name: f.frage,
      acceptedAnswer: { '@type': 'Answer', text: f.antwort },
    })),
  };
}

export const formatDate = (d: Date) =>
  d.toLocaleDateString('de-AT', { day: '2-digit', month: 'long', year: 'numeric' });
