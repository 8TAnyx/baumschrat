import { defineCollection, reference } from 'astro:content';
import { glob } from 'astro/loaders';
import { z } from 'astro/zod';

const faq = z.array(z.object({ frage: z.string(), antwort: z.string() })).default([]);

const leistungen = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/leistungen' }),
  schema: ({ image }) =>
    z.object({
      title: z.string(),
      // Kurzer Name für Navigation und Karten, z. B. "Baumpflege"
      kurz: z.string(),
      // Seitentitel für Google (ca. 50–60 Zeichen)
      seoTitle: z.string(),
      description: z.string(),
      teaser: z.string(),
      icon: z.string(),
      order: z.number(),
      image: image(),
      imageAlt: z.string(),
      faq,
    }),
});

const ratgeber = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/ratgeber' }),
  schema: ({ image }) =>
    z.object({
      title: z.string(),
      date: z.coerce.date(),
      updated: z.coerce.date().optional(),
      description: z.string(),
      image: image(),
      imageAlt: z.string(),
      thema: z.string().default('Baumpflege'),
      leistungen: z.array(reference('leistungen')).default([]),
      draft: z.boolean().default(false),
    }),
});

const regionen = defineCollection({
  loader: glob({ pattern: '**/*.md', base: './src/content/regionen' }),
  schema: ({ image }) =>
    z.object({
      title: z.string(),
      name: z.string(),
      seoTitle: z.string(),
      description: z.string(),
      teaser: z.string(),
      order: z.number(),
      image: image(),
      imageAlt: z.string(),
      orte: z.array(z.string()).default([]),
    }),
});

export const collections = { leistungen, ratgeber, regionen };
