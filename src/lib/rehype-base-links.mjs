// Interne Links in Markdown (z. B. "/leistungen/baumpflege/") mit dem Basis-Pfad versehen,
// damit sie auch auf der Testumgebung unter /test/ funktionieren.
export default function rehypeBaseLinks({ base }) {
  const prefix = base.replace(/\/$/, '');
  const visit = (node) => {
    if (node.type === 'element' && node.tagName === 'a') {
      const href = node.properties?.href;
      if (prefix && typeof href === 'string' && href.startsWith('/') && !href.startsWith('//')) {
        node.properties.href = prefix + href;
      }
    }
    node.children?.forEach(visit);
  };
  return (tree) => visit(tree);
}
