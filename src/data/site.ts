// Zentrale Firmendaten – werden in Kopf, Fußzeile, Impressum und strukturierten Daten verwendet.
export const site = {
  name: 'Baumschrat',
  owner: 'Daniel Lindner',
  tagline: 'Baumpflege, Baumkontrolle & Spezialfällungen in Graz',
  description:
    'Baumschrat – Baumpflege nach ÖNORM L 1122, Baumkontrolle, Drohnen-Baumprüfung, Spezialfällungen und Borkenkäfer-Hilfe in Graz, Graz-Umgebung und im Mürztal.',
  phone: '+43 650 9999539',
  phoneHref: 'tel:+436509999539',
  email: 'daniel@baumschrat.at',
  street: 'Janzgasse 14',
  zip: '8020',
  city: 'Graz',
  region: 'Steiermark',
  country: 'AT',
  geo: { lat: 47.0667, lng: 15.4206 },
  areas: ['Graz', 'Graz-Umgebung', 'Mürztal'],
  wkoUrl:
    'https://firmen.wko.at/daniel-lindner/steiermark/?firmaid=a3580f35-b993-4cd0-9f3b-1cc9965627b8',
};

export const nav = [
  { href: '/leistungen/', label: 'Leistungen' },
  { href: '/einsatzgebiet/', label: 'Einsatzgebiet' },
  { href: '/ratgeber/', label: 'Ratgeber' },
  { href: '/galerie/', label: 'Galerie' },
  { href: '/faq/', label: 'FAQ' },
  { href: '/kontakt/', label: 'Kontakt' },
];

/** Interne URL mit Basis-Pfad versehen (Testumgebung läuft unter /test/). */
export const u = (path: string) => import.meta.env.BASE_URL.replace(/\/$/, '') + path;
