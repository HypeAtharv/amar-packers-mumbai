# Project: Amar Packers and Movers Homepage CRO Redesign

## Architecture
- **Single-File Standalone Architecture**: All HTML markup, embedded CSS `<style>`, client-side JavaScript `<script>`, and inline SVG icons reside strictly inside `d:\Atharv Shinde\Scratch\Domains\index.html`. External assets are restricted to standard, reliable CDN endpoints (Google Fonts: Montserrat & Inter, FontAwesome 6 icons).
- **Conversion-Rate-Optimized (CRO) Funnel**:
  1. *Immediate Conversion Layer*: High-visibility top alert bar (guarantee & IBA trust), sticky header with phone CTA (`+91 82868 64002`), hero section with immediate above-the-fold Instant Quote Calculator.
  2. *Social Proof & Trust Layer*: 4.9★ Google Rating badge (91+ reviews), IBA approval standards for bank/PSU employees, GST-registered invoices, ISO 9001:2015 certification, 100% transit insurance.
  3. *Core Services Grid*: 8 genuine services (Household, Office, Car & Bike, Warehousing, Mumbai Local, Domestic Intercity, Packing/Unpacking, Loading/Unloading).
  4. *Operational Rigor*: Authentic 5-stage moving process (`Inspection / Survey -> 4-Layer Packing -> Safe Loading -> GPS Transit -> Unloading & Unpacking`), Mumbai CHS society guidelines (9 AM - 6 PM, NOCs, elevator padding), Island City vehicle restrictions.
  5. *Pricing & Rates Matrix*: Transparent, realistic Mumbai pricing (1BHK ₹4,500–₹9,500, 2BHK ₹7,000–₹15,000, 3BHK ₹11,000–₹24,000, 4BHK ₹16,000–₹38,000, Vehicles ₹1,500–₹18,000).
  6. *Persistent Mobile CTAs*: 3-action fixed bottom contact bar (`Call Now`, `WhatsApp Now`, `Get Free Quote`) and floating WhatsApp action button with zero touch overlap.

## Feature Inventory
| # | Feature | Description | Milestone | Source |
|---|---------|-------------|-----------|--------|
| 1 | Complete R1 Elimination | Cleanly remove "15% Google Ads Discount Included", "Flat 15% OFF", and discount messages from markup and scripts without visual collapse | M1 (Implementation) | Survey & ORIGINAL_REQUEST |
| 2 | Standalone File Architecture | All styles, semantic tags, and scripts strictly in `index.html`; no local external CSS/JS files | M1 (Implementation) | Survey & ORIGINAL_REQUEST |
| 3 | Above-the-Fold Quote Calculator | 6-field instant quote calculator: Name, Phone (`^[6-9]\d{9}$`), Moving From, Moving To, Move Size chips (1BHK/2BHK/3BHK/Vehicle/Office), Shifting Date, with instant price engine | M1 (Implementation) | Survey & CRO UX |
| 4 | Multi-Channel High-Impact CTAs | Sticky header call button, 3-action mobile bottom bar (`Call Now`, `WhatsApp Now`, `Get Free Quote`), floating WhatsApp button (`+91 82868 64002`) | M1 (Implementation) | Survey & CRO UX |
| 5 | Authentic Mumbai Identity & Visuals | Transport Red `#ee1d23`, Deep Crimson `#c2030a`, Slate `#0b192c`, Powai hub address, *"House Shifting Hai? Ho Jayega!"*, zero AI neon gradients | M1 (Implementation) | Survey & Spec Miner |
| 6 | Comprehensive Trust & Services | 8 authentic services, 4.9★ Google rating (91+ reviews), IBA/GST/ISO trust cues, 5-stage moving process, Mumbai CHS shifting guide | M1 (Implementation) | Survey & Spec Miner |
| 7 | Full Cross-Device Responsiveness | 375px–430px mobile and 1280px–1920px desktop viewports with zero horizontal overflow, 48px+ touch targets, 16px input font size | M1 (Implementation) | Survey & CRO UX |
| 8 | Automated E2E Test Suite | 4-tier automated test suite covering R1-R4, DOM assertions, form validation, calculation engine, viewport responsiveness, and `TEST_READY.md` | M_E2E (Testing Track) | Survey & Project Pattern |

## Milestones
| # | Name | Scope | Dependencies | Status |
|---|------|-------|-------------|--------|
| M_E2E | Automated E2E Testing Suite | Build standalone test script testing all 4 tiers (R1-R4), verify test execution, publish `TEST_READY.md` | none | IN_PROGRESS |
| M1 | Homepage Redesign & Implementation | Complete overhaul of `index.html` meeting all R1-R4 requirements, CRO architecture, Mumbai identity, and form validation | none | IN_PROGRESS |
| M_VERIFY | Multi-Agent Gate Verification | Independent review (2 Reviewers), empirical testing (2 Challengers), and Forensic Integrity Audit (1 Auditor) | M_E2E, M1 | PLANNED |

## Code Layout
- `d:\Atharv Shinde\Scratch\Domains\index.html` — The single production homepage file (owned by Worker for M1)
- `d:\Atharv Shinde\Scratch\Domains\tests\` — E2E test scripts and runner (owned by Test Writer for M_E2E)
- `d:\Atharv Shinde\Scratch\Domains\TEST_INFRA.md` — E2E Test Infrastructure documentation
- `d:\Atharv Shinde\Scratch\Domains\TEST_READY.md` — E2E Test Suite completion readiness signal

## Interface Contracts
### Form ↔ WhatsApp / Lead Generation
- Phone click-to-call: `tel:+918286864002` (sanitized, directly dialable)
- WhatsApp click: `https://wa.me/918286864002?text=...` (URI-encoded, respectful professional inquiry, NO discount text)
- Form Validation:
  - Phone: `^[6-9]\d{9}$` (10 digits starting with 6, 7, 8, 9)
  - Name: non-empty string (min 2 chars)
  - From & To: non-empty strings
  - Move Size: one of `1BHK`, `2BHK`, `3BHK`, `Vehicle`, `Office` (or `1RK`, `4+BHK`)
  - Date: valid future date (default to today or tomorrow)
- Calculation Engine:
  - Local Mumbai moves (within Mumbai/Thane/Navi Mumbai):
    - 1BHK: ₹4,500 – ₹7,500
    - 2BHK: ₹7,000 – ₹12,000
    - 3BHK: ₹11,000 – ₹18,000
    - 4BHK/Villa: ₹16,000 – ₹28,000
    - Vehicle: ₹1,500 – ₹6,500
    - Office: ₹8,000 – ₹25,000
  - Intercity / Domestic moves:
    - Distance-adjusted dynamic pricing with transparent transport rate card
