// Weiche Trennstellen (U+00AD) in langen deutschen Komposita, damit Überschriften am Handy
// sauber mit Bindestrich umbrechen statt mitten im Wort abgeschnitten zu werden.
// Nur für die Anzeige verwenden – nicht für <title>, Meta-Beschreibungen oder JSON-LD.
const fugen = [
  'Seilkletter|technik',
  'Schadholz|entfernung',
  'Baumschutz|verordnung',
  'Drohnen|technologie',
  'Borkenkäfer|befall',
  'Kastanien|minier|motte',
  'Grundstücks|eigentümer',
  'Ersatz|pflanzung',
  'Verkehrs|sicherungs|pflicht',
];

const ersetzungen = fugen
  .filter((f) => f.includes('|'))
  .map((f) => [f.replaceAll('|', ''), f.replaceAll('|', '­')] as const);

export const trennen = (text: string) =>
  ersetzungen.reduce((t, [wort, getrennt]) => t.replaceAll(wort, getrennt), text);
