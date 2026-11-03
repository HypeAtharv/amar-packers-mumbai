# Test Infrastructure Documentation: Amar Packers and Movers CRO Redesign

## 1. Overview & Architecture

The E2E test suite for Amar Packers and Movers provides a 100% automated, opaque-box testing framework built strictly using Node.js built-ins (`node:test`, `node:assert/strict`, `fetch`, `WebSocket`, `child_process`) and Chrome DevTools Protocol (CDP) communicating with real headless Google Chrome (version 156+).

Zero external npm package dependencies are required to run the entire test suite.

```
d:\Atharv Shinde\Scratch\Domains\
├── index.html                            # Standalone production homepage
├── TEST_INFRA.md                         # This documentation
├── TEST_READY.md                         # Readiness signal for testing track
└── tests\
    ├── e2e-runner.js                     # Master test suite runner with ANSI dashboard & JSON reporter
    ├── tier1-feature-coverage.test.js    # Tier 1: Feature Coverage (26 tests)
    ├── tier2-boundary-corner.test.js     # Tier 2: Boundary & Corner Cases (15 tests)
    ├── tier3-cross-feature.test.js       # Tier 3: Cross-Feature Interactions (6 tests)
    ├── tier4-real-world-scenarios.test.js# Tier 4: Real-World Scenarios (3 tests)
    ├── test-results.json                 # Exported test results artifact
    └── helpers\
        ├── chrome-cdp.js                 # Headless Chrome CDP automation harness
        ├── static-server.js              # Ephemeral embedded HTTP server for index.html
        └── dom-analyzer.js               # Static DOM, AST token, and contract parser
```

---

## 2. Test Execution Commands

### Run Full E2E Test Suite
```bash
node tests/e2e-runner.js
```

### Run Full Suite with JSON Report Export
```bash
node tests/e2e-runner.js --report-json
```

### Run Specific Test Tier
```bash
node tests/e2e-runner.js --tier=1
node tests/e2e-runner.js --tier=2
node tests/e2e-runner.js --tier=3
node tests/e2e-runner.js --tier=4
```

### Run via Node.js Native Test Runner
```bash
node --test tests/tier1-feature-coverage.test.js
node --test tests/tier2-boundary-corner.test.js
node --test tests/tier3-cross-feature.test.js
node --test tests/tier4-real-world-scenarios.test.js
```

---

## 3. Test Tier Breakdown & Authoritative Criteria

### Tier 1: Feature Coverage (`tests/tier1-feature-coverage.test.js` — 26 tests)
- **1.1 Requirement R1 (Discount Removal)**:
  - Verifies visible page text, labels, badges do not contain `"15% Google Ads Discount Included"`, `"Flat 15% OFF"`, or `"15% discount"`.
  - Verifies document `<title>` and `<meta name="description">` do not mention discounts.
  - Verifies client-side scripts and WhatsApp templates do not reference discount quotes.
- **1.2 Requirement R2 (Standalone Single-File Architecture)**:
  - Verifies all styles reside strictly in embedded `<style>` tags.
  - Verifies all client-side logic resides strictly in embedded `<script>` tags.
  - Verifies no local external `.css` or `.js` file links exist in HTML or workspace root.
  - Verifies external CDN resources strictly point to approved endpoints (Google Fonts, cdnjs Font Awesome).
- **1.3 Requirement R3 (Sticky Phone & WhatsApp CTAs)**:
  - Verifies clickable `tel:+918286864002` direct dialer links.
  - Verifies clickable `https://wa.me/918286864002` direct WhatsApp triggers.
  - Verifies desktop sticky header call action, mobile sticky bottom bar, and floating WhatsApp action button.
- **1.4 Requirement R3 (Quote Request Form Fields)**:
  - Verifies presence of Customer Name field (min 2 chars).
  - Verifies Phone field with Indian mobile 10-digit validation pattern `[6-9][0-9]{9}`.
  - Verifies Moving From and Moving To location inputs.
  - Verifies Move Size / Inventory options: `1BHK`, `2BHK`, `3BHK`, `Vehicle`, `Office`.
  - Verifies Shifting Date input.
- **1.5 Requirement R4 (Authentic Mumbai Branding & Trust Badges)**:
  - Verifies brand name "Amar Packers and Movers".
  - Verifies colloquial reassurance slogan: *"House Shifting Hai? Ho Jayega!"*.
  - Verifies official physical hub address in Powai (`Shop No. 25, Morarji Nagar, Near L&T Gate No. 1, Aarey Colony Road, Powai, Mumbai - 400087`).
  - Verifies primary hotline `+91 82868 64002`.
  - Verifies authentic transport color palette (`#ee1d23`/`#d32f2f`, `#0b192c`, `#ffffff`) and absence of AI neon purple gradients.
  - Verifies institutional trust credentials: 4.9★ Google rating (91+ reviews), IBA approval standards, GST registered tax invoices (SAC 9965/9967), ISO 9001:2015, 100% transit insurance.

---

### Tier 2: Boundary & Corner Cases (`tests/tier2-boundary-corner.test.js` — 15 tests)
- **2.1 Phone Number Boundary & Format Validation**:
  - Rejects numbers < 10 digits (`""`, `"9"`, `"9820"`, `"982016400"`).
  - Rejects numbers starting with invalid digits `0, 1, 2, 3, 4, 5`.
  - Accepts valid 10-digit Indian mobile numbers starting with `6, 7, 8, 9` (`9820164002`, `8286864002`, `7021345678`, `6201234567`).
  - Rejects non-numeric/alphabetic phone inputs.
- **2.2 Extreme Dates & Scheduling Boundaries**:
  - Rejects past shifting dates (`2020-01-01` or yesterday) via `min` attribute constraint.
  - Accepts tomorrow or upcoming dates.
  - Accepts distant future dates (e.g. 6 months ahead).
- **2.3 Viewport Responsiveness & Layout Overflow Checks**:
  - Empirical overflow check (`scrollWidth <= innerWidth` and element bounding box verification) across 6 viewports:
    - Mobile 375px × 667px (iPhone SE)
    - Mobile 390px × 844px (iPhone 12/13/14)
    - Mobile 430px × 932px (iPhone 14/15/16 Pro Max)
    - Desktop 1280px × 800px (Standard Laptop)
    - Desktop 1440px × 900px (High-Res Laptop / Mac)
    - Desktop 1920px × 1080px (Full HD Display)
  - Mobile touch target minimum height: CTAs must be >= 44px (preferably >= 48px).
  - Mobile form inputs font-size: Must be >= 16px to prevent iOS Safari auto-zoom.

---

### Tier 3: Cross-Feature Interactions (`tests/tier3-cross-feature.test.js` — 6 tests)
- **3.1 CTA Wiring & Navigation Consistency**:
  - Verifies all `tel:` links route to `+918286864002` or `8286864002`.
  - Verifies all `wa.me` links route to `918286864002`.
  - Verifies quote buttons across header and bottom bar scroll to `#estimate-form`.
- **3.2 WhatsApp URL Generation & R1 Discount-Free Messaging**:
  - Verifies form submission generates a URI-encoded WhatsApp link containing user parameters (Pickup, Drop, Date, Phone, Name).
  - **CRITICAL R1 ASSERTION**: Confirms the generated text contains **ZERO** discount terms (`15%`, `discount`, `google ads`, `special offer`).
- **3.3 Quote Calculation Engine Logic**:
  - Verifies realistic baseline price ranges for 1BHK (₹4,500–₹7,500), 2BHK (₹7,000–₹12,000), 3BHK (₹11,000–₹18,000), Vehicle, and Office.
  - Verifies dynamic UI price update when move size or floor/lift selection changes.

---

### Tier 4: Real-World Scenarios (`tests/tier4-real-world-scenarios.test.js` — 3 tests)
- **4.1 Scenario 1: End-to-End User Booking Flow**:
  - User lands on homepage, inputs relocation data (Name, Phone, Move Size, Pickup, Drop, Date), reviews estimated calculation, submits form, triggers success state, and obtains WhatsApp inquiry without JavaScript exceptions.
- **4.2 Scenario 2: Mobile Emergency Calling Flow**:
  - Urgent mobile search user lands on 390px viewport, immediately accesses persistent call button with valid `tel:+918286864002`, meets >= 44px touch target, and triggers direct dialer.
- **4.3 Scenario 3: Bank / PSU Employee Relocation Trust Flow**:
  - Bank employee seeking relocation reimbursement confirms page displays: IBA approval standards, GST registered tax invoices (SAC 9965/9967), ISO 9001:2015, transit insurance, Powai hub address, 4.9★ Google rating, and Mumbai CHS society shifting regulations.

---

## 4. Current Test Results & Discovered Implementation Bugs

A baseline execution of `node tests/e2e-runner.js` against the unmigrated `index.html` was performed.

### Baseline Summary
- Total Tests: **50**
- Passed: **40** (80.0%)
- Failed: **10** (20.0%)
- Duration: **5.06s**

### Discovered Implementation Bugs (Escalated to Worker M1):
1. **[R1 Violation] Customer-Facing Discount Banner**: Line 2261 displays `<span>🎉 Special Offer: Flat 15% OFF + Free Moving Insurance</span>`.
2. **[R1 Violation] Discount Text in WhatsApp Lead Generator**: Script line 4216 generates `Please provide best 15% discount quote.`.
3. **[Form Defect] Missing Customer Name Field**: The quote calculator form lacks an input field for Customer Name (`userName` / `customerName`).
4. **[Form Defect] Missing Dedicated Rates for Vehicle and Office**: `rateMatrix` only contains 1RK, 1BHK, 2BHK, 3BHK, 4+BHK, missing rates for Vehicle transport and Office relocation.
5. **[Mobile UX Defect] Touch Target Height Below 44px**: Mobile sticky bar action buttons have height of 42.25px / 43.17px, failing the 44px/48px touch target standard.
6. **[Mobile UX Defect] Input Font Size Below 16px**: Mobile form inputs currently have `font-size: 14px`, which triggers iOS Safari auto-zoom on focus.
7. **[Trust Signal Defect] GST Invoice Compliance Text Missing**: Page text lacks explicit GST registered invoice compliance (SAC 9965/9967).
