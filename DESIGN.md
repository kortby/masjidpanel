---
tokens:
  colors:
    primary:
      text: "emerald-950"
      active: "emerald-900"
      hover: "emerald-700"
    background:
      default: "stone-50"
      card: "white"
      highlight: "emerald-50"
    accent:
      default: "amber-500"
      hover: "amber-400"
  typography:
    family:
      sans: "Inter, sans-serif"
    weights:
      normal: 400
      medium: 500
      semibold: 600
      bold: 700
  radii:
    card: "3xl"
    button: "full"
  spacing:
    container: "mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl"
---

# MasjidPanel Design System

This document outlines the visual identity and structural UI guidelines for MasjidPanel's public-facing application. Our goal is to provide a serene, welcoming, and highly modern aesthetic tailored specifically to the Muslim community. 

## Rationale

- **Colors:** We utilize deep Emerald greens (`emerald-950`, `emerald-900`) for primary text and structural elements to symbolize vitality and Islamic heritage. Amber accents (`amber-500`) provide an elegant "gold" contrast for primary calls to action. The background defaults to warm `stone-50` to soften the overall presentation and eliminate harsh, purely white backgrounds.
- **Typography:** The clean `Inter` sans-serif typeface is applied uniformly to ensure high legibility and a modern software feel.
- **Shapes & Radii:** We heavily rely on generously rounded corners (`rounded-3xl` for main cards and containers) to evoke a friendly, approachable, and smooth user experience, moving away from sharp, generic tech dashboard templates. 
- **Decoupling:** The public-facing site operates entirely on standard HTML with Tailwind classes defined by this document. Shadcn UI components remain exclusively within the administrative dashboard.
