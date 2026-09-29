# AGENTS.md — Curtain & Interior E-commerce Platform (CurtainLux - Laravel)

Copy this file to the project's root as `AGENTS.md`. This document establishes the architectural standards, database conventions, and design system guidelines for the **CurtainLux** curtain and interior e-commerce platform.

---

## 1. Project Overview

CurtainLux is a specialized e-commerce platform designed for custom curtains, blinds, and smart motorized drapery:
- **Storefront**:
  - Catalogue: Fabric curtains (2 layers), Korean rainbow/combi blinds, roller blinds, natural wooden blinds, and smart motors.
  - **Interactive Dimension Calculator**: Real-time pricing based on window dimensions ($W \times H$ in cm), calculation unit ($m^2$ or linear meters), and rounding rules (e.g. min 1.0 $m^2$).
  - **Customization Options**: Sewing header styles (S-fold/wave, grommet/ore, pleats), track systems (standard aluminum, silent glider, wooden rod), smart motors (Tuya, Aqara, Somfy), and sheer/voile linings.
  - **Home Survey & Consultation**: Free home measurement booking with fabric swatch samples brought to customer addresses.
  - Cart & Checkout: Storing custom window dimensions, room labels ("Cửa phòng khách", "Phòng ngủ Master"), and calculated subtotal.
- **Admin Panel**:
  - Product & Catalog management (with curtain-specific specs: `price_unit`, `min_area`, `min_width`, `max_width`, `blackout_rate`).
  - Home survey coordination & consultation pipeline (`pending` → `assigned` → `surveying` → `quoted` → `completed`).
  - Staff assignment and quotation recording.
  - Dashboard analytics with real-time consultation leads and product inventory.

---

## 2. Tech Stack & Environment

| Layer | Technology |
|---|---|
| **Backend** | PHP 8.2+ / Laravel 12.x |
| **Database** | SQLite (Dev) / MySQL 8.x (Staging & Prod) |
| **Storefront & Admin UI** | Blade Templates + Vanilla CSS (No heavy framework dependencies) |
| **Icons & Typography** | FontAwesome 6.5+; Plus Jakarta Sans (Google Fonts) |
| **Design System** | **Japandi & Warm Neutral Palette (Tone 1)** |
| **Caching** | Redis / Database cache via Laravel Cache driver |
| **Validation** | Laravel Form Requests + Client-side JavaScript calculators |

---

## 3. Design System & Aesthetics (Japandi Warm Neutral - Tone 1)

All public pages and storefront elements must strictly conform to the **Warm Neutral / Japandi interior aesthetic**:

```css
:root {
    --bg-body: #FAF7F2;          /* Warm Cream / Off-white (60% background) */
    --bg-card: #FFFFFF;          /* Pure White card containers */
    --bg-subtle: #F5EFE6;        /* Light Beige accents */
    --primary: #8C7A6B;          /* Warm Sand / Linen Brown (30% primary) */
    --primary-hover: #756557;
    --primary-light: #F2ECE4;
    --accent-cta: #B86B53;       /* Terracotta / Earthy Clay (10% CTA highlight) */
    --accent-cta-hover: #9E5640;
    --text-main: #2C2724;        /* Soft Charcoal / Deep warm brown (No harsh #000000) */
    --text-muted: #7A726D;       /* Warm Gray */
    --border-line: #E8E2D9;      /* Subtle Linen border */
}
```

### Visual & UX Principles:
1. **Never use pure pitch black (`#000000`) for text or dark neon backgrounds**: Keep text in soft charcoal (`#2C2724`) to maintain the tactile feel of fabric and interior warmth.
2. **Neutral backgrounds to showcase fabric textures**: Curtains come in diverse colors (blue, green, beige, gray); neutral backgrounds prevent color clashing with photography.
3. **Terracotta (`#B86B53`) for Primary Actions**: Use for high-intent buttons ("Thêm Vào Giỏ Hàng", "Đặt Lịch Khảo Sát Miễn Phí").
4. **Monochrome Functional Icons**: Use single-color icons inheriting `currentColor` or brand tokens. Do not use multicolor/rainbow decorative icons.

---

## 4. Curtain Domain Business Logic & Pricing Engine

### 4.1 Units of Measurement (`price_unit`)
- **`sqm` (Mét vuông - $m^2$)**: Applied to Roller Blinds, Korean Rainbow Blinds, and Wooden Blinds.
  $$\text{Units} = \max\left(\frac{W_{\text{cm}}}{100} \times \frac{H_{\text{cm}}}{100},\, \text{min\_area}\right)$$
  *(Standard industry rule: areas under 1.0 $m^2$ are rounded up to 1.0 $m^2$)*.
- **`meter` (Mét ngang hoàn thiện)**: Applied to fabric curtains (already includes 2.5x fabric gathering factor for fullness).
  $$\text{Units} = \max\left(\frac{W_{\text{cm}}}{100},\, 1.0\right)$$
- **`piece` (Bộ / Cái cố định)**: Applied to Smart Motors and standalone accessories.

### 4.2 Customization Options (`curtain_options`)
Option values apply surcharges via `price_impact_type`:
- `fixed`: Flat surcharge added to the window set (e.g. Smart Motor +1.500.000 ₫).
- `per_meter`: Multiplied by the width in meters (e.g. Silent glider track +50.000 ₫ / meter; Voile layer +220.000 ₫ / meter).
- `per_sqm`: Multiplied by the total calculated square meters.

$$\text{Subtotal} = (\text{Units} \times \text{Base Price} + \text{Options Surcharges}) \times \text{Quantity}$$

---

## 5. Database Conventions & Schema Standards

### 5.1 Tables
- `products`: Base catalog items with curtain specifications (`price_unit`, `min_area`, `min_width`, `max_width`, `min_height`, `max_height`, `blackout_rate`, `fabric_material`, `origin`).
- `curtain_option_groups`: Option groupings (`header_style`, `track_type`, `motor_type`, `sheer_layer`).
- `curtain_option_values`: Individual selectable choices with `price_impact_type` and `extra_price`.
- `cart_items` & `order_items`: Must record `room_label`, `width`, `height`, `mount_type`, `calculated_units`, `unit_price`, and `selected_options` (JSON snapshot).
- `consultations`: Leads for free home measurement with customer contact, address, preferred date/slot, assigned `staff_id`, and status pipeline.

### 5.2 Schema Integrity Rules
- Migrations are timestamped executable files in `database/migrations`. Never edit migrations that have already run.
- Keep foreign keys properly constrained with explicit `onDelete('cascade')` or `nullOnDelete()`.
- Financial amounts use `DECIMAL(12, 0)` for VNĐ. Do not use float values for money.

---

## 6. Backend Conventions (Laravel)

### 6.1 Naming Conventions
- Models: Singular PascalCase (`Product`, `Consultation`, `CartItem`, `CurtainOptionGroup`).
- Tables: Plural snake_case (`products`, `consultations`, `cart_items`, `curtain_option_groups`).
- Controllers: Singular/Plural PascalCase + Controller (`ShopController`, `CartController`, `ConsultationController`).
- Routes: kebab-case in Vietnamese/English (`/san-pham/{slug}`, `/dat-lich-khao-sat`, `/gio-hang`).

### 6.2 Code Style & Separation of Concerns
- Controllers validate request parameters, delegate to Eloquent models/services, and return views or JSON responses.
- Always use eager loading (`with(['category', 'values'])`) to eliminate N+1 query bottlenecks.
- Never mass-assign `$request->all()`. Explicitly define `$fillable` on all models and use `$request->validate([...])`.

---

## 7. UTF-8 & Vietnamese Text Integrity (Mandatory)

- All source files (`.php`, `.blade.php`, `.css`, `.js`, `.json`, `.sql`, `.md`) must be saved in **UTF-8 without BOM**.
- Preserve full Vietnamese diacritics (dấu tiếng Việt chuẩn) across all views, seeders, and notifications. Never strip accents to mask encoding defects.
- Use explicit UTF-8 encodings in PowerShell or shell commands (`[System.Text.Encoding]::UTF8`).

---

## 8. Current Roadmap & Priorities

1. **Phase 1 (Completed)**:
   - Established curtain database schema (`products` curtain specs, `curtain_options`, `consultations`, `cart_items`).
   - Implemented Japandi Warm Neutral (Tone 1) design system across Storefront and Admin.
   - Built Interactive Real-Time Dimension Calculator on product detail page.
   - Implemented Home Survey / Consultation booking module for Storefront & Admin.
   - Implemented Custom Curtain Cart with window labels and dimensions.
2. **Phase 2 (Upcoming)**:
   - Online Payment Integration (MoMo, VNPay, Bank QR transfer).
   - Order confirmation and invoice printing with window cutting sheets for tailors.
   - Customer account portal to track consultation progress and custom order manufacturing status.
