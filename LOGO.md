# ReserHotel — Brand Logo Guide

> Site: **ReserHotel** — a curated collection of luxury hotels & resorts.
> Framework context: Laravel 12 · Blade · Alpine.js · Tailwind CSS v4 (shadcn-style tokens)

---

## 1. Overview

The ReserHotel logo communicates **two-color luxury hospitality**: warmth and trust
(red) meeting calm, curated comfort (green). It pairs a geometric emblem with the
wordmark for a premium, editorial feel.

- Emblem: an **MD monogram** — the brand initials set in Fraunces Black on the
  signature red tile. A monogram reads clearly at small sizes (avatar, favicon)
  and pairs cleanly with the wordmark.
- Wordmark: **ReserHotel**.
- Baseline (optional): "Premium Hotels & Resorts".

### Design intent
The **MD** monogram doubles as the brand mark. Depending on context it can stand
alone (favicon, avatars, app icons) or pair with the full wordmark. The strict
red→dark-red tile gives the monogram a gem-like, hospitality feel without a
dated icon.

---

## 2. Color palette

Two harmonious colors. Red is the action/brand primary; green is the accent/trust.

| Token        | HEX (Light) | HEX (Dark)   | Role                                |
| ------------ | ----------- | ------------ | ----------------------------------- |
| `--primary`  | `#B3261E`   | `#E0564A`    | Emblem gradient start, accents, CTAs |
| `--primary-foreground` | `#FFFFFF` | `#FFFFFF` | On-primary text               |
| `--green`    | `#0E8A5D`   | `#34D399`    | Secondary accent, availability, "on" |
| `--gold`     | `#B3261E`   | `#E0564A`    | Legacy alias of primary (red)       |
| `--gold-dark`| `#7F1610`   | —            | Emblem gradient end / depth shadow   |

> Rule: **red** leads (button/links/emblem); **green** supports (badges, trust,
> avail signals). Never invert — green must not compete with red for the CTA.

### Gradients
- Primary CTA & emblem: `linear-gradient(135deg, #B3261E, #7F1610)`.
- Full-bleed bands (Stats / CTA sections): `linear-gradient(135deg, #B3261E, #7F1610, #0E8A5D)`.

---

## 3. Typography

| Use        | Family   | Weight           |
|------------|----------|------------------|
| Logo wordmark | **Fraunces** (serif) | 700–900 |
| Headings   | Fraunces / Manrope | 700–800 |
| Body / UI  | Manrope / Cairo (ar) | 400–600 |
| Arabic     | Cairo / Amiri     | —               |

- Wordmark casing: **Title Case** (`ReserHotel`, never all-caps; the "S" is capital for the compound word).
- Letter-spacing on wordmark: `tracking-tight`; optional `tracking-widest` only on the tagline.

---

## 4. Construction & clear space

- The emblem tile carries the **MD** monogram in Fraunces Black 900, centered,
  matching the header brand box (`h-9 w-9`).
- **Clear space:** keep a margin of at least one emblem height on all sides.
- **Minimum sizes:** wordmark ≥ 96px wide; emblem tile ≥ 28px.
- **Lockup:** horizontal (emblem + wordmark), center or start aligned; keep a
  `gap` of `0.625rem` (10px) between emblem and wordmark.

```
[ ◆ ]  ReserHotel
```

---

## 5. Usage guidelines

**Do:**
- Use the provided SVG/PNG lockups and color values above.
- Use the monochrome white version on photo/dark hero backgrounds.

**Don't:**
- Recolor to gold, blue, or black-on-color.
- Stretch, skew, or rotate beyond ±5°.
- Place on busy imagery without the white-on-dark version or an overlay.
- Add drop shadows to the emblem tile (use the flat 2D/3D depth presets only).

---

## 6. Current implementation

The logo is rendered inline (no image asset) as:

```blade
{{-- navbar / footer brand box --}}
<span class="flex h-9 w-9 items-center justify-center rounded-lg bg-gradient-to-br from-primary to-primary/80 font-serif text-sm font-black leading-none text-white shadow-sm shadow-primary/25">MD</span>
<span class="text-lg font-extrabold tracking-tight text-foreground">ReserHotel</span>
```

- Monogram: **MD** rendered in `font-serif` (Fraunces) Black, inherited theme colors.
- Colors come from the CSS theme tokens (`--primary`, `--green`).
- This is the single source of truth — changes here update logo everywhere it's used.

---

## 7. Asset files

Generated SVG masters live in `public/images/logo/`:

| File            | Contents                                      |
| --------------- | --------------------------------------------- |
| `logo-mark.svg` | Emblem tile (red gradient + MD monogram)       |
| `logo-wide.svg` | Emblem + wordmark lockup (dark, on light)      |
| `logo-white.svg`| White MD + wordmark (for dark/imagery)         |
| `favicon.svg`   | Emblem tile, `rx=12`, wired into `<head>`      |

The favicon is already linked in `components/layouts/frontend.blade.php`:

```html
<link rel="icon" type="image/svg+xml" href="{{ asset('images/logo/favicon.svg') }}">
```

> The in-page navbar/footer brand still renders the inline **MD** monogram on the
> red-gradient tile so it inherits live theme tokens. Use the SVG files for
> external/social/favicon assets where token-driven markup isn't possible.