import json

with open('scripts/elementor_data_no_pricing.json', 'r', encoding='utf-8') as f:
    data = json.load(f)

# 1. Section 5: Packaging
sec5_html = data[5]['elements'][0]['settings']['html']
old_pack_css = """.amar-pack-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
}"""
new_pack_css = """.amar-pack-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 20px;
  align-items: stretch;
}
.amar-pack-card {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}"""
if old_pack_css in sec5_html:
    sec5_html = sec5_html.replace(old_pack_css, new_pack_css, 1)
    data[5]['elements'][0]['settings']['html'] = sec5_html
    print("Updated Section 5 Packaging CSS with align-items: stretch & equal height")

# 2. Section 8: Reviews
sec8_html = data[8]['elements'][0]['settings']['html']
old_rev_css = """.amar-reviews-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 22px;
  margin-bottom: 24px;
}"""
new_rev_css = """.amar-reviews-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 22px;
  margin-bottom: 24px;
  align-items: stretch;
}
.amar-review-card {
  height: 100%;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
}"""
if old_rev_css in sec8_html:
    sec8_html = sec8_html.replace(old_rev_css, new_rev_css, 1)
    data[8]['elements'][0]['settings']['html'] = sec8_html
    print("Updated Section 8 Reviews CSS with align-items: stretch & equal height")

# 3. Add mobile footer & duplicate bar fix to Hero HTML widget 5a8ae667
mobile_fix_css = """
<style id="amar-mobile-footer-fix">
/* Disable duplicate XTRA mobile bottom bar */
@media (max-width: 768px) {
  .xtra-fixed-mobile-nav,
  .cz_fixed_mobile_nav,
  .cz_fixed_bottom_border {
    display: none !important;
    visibility: hidden !important;
    pointer-events: none !important;
  }
  body {
    padding-bottom: 90px !important;
    padding-bottom: calc(90px + env(safe-area-inset-bottom, 0px)) !important;
  }
  footer,
  .site-footer,
  #footer,
  .cz_footer {
    padding-bottom: 45px !important;
  }
  .cz_footer_bottom, .site-info, .footer-bottom {
    margin-bottom: 15px !important;
  }
}
</style>
"""

hero_widget = data[0]['elements'][0]['elements'][1]['elements'][0]
print("Hero widget id:", hero_widget.get('id'))
hero_html = hero_widget['settings']['html']
if 'amar-mobile-footer-fix' not in hero_html:
    hero_widget['settings']['html'] = hero_html + mobile_fix_css
    print("Appended mobile footer fix CSS to Hero widget")

with open('scripts/elementor_data_no_pricing.json', 'w', encoding='utf-8') as f:
    json.dump(data, f, ensure_ascii=False)

print("Saved updated scripts/elementor_data_no_pricing.json successfully!")
