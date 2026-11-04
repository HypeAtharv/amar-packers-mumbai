with open('scripts/elementor_data_no_pricing.json', 'r', encoding='utf-8') as f:
    text = f.read()

# 1. Update Pillar Card description
t1_old = "All-risk comprehensive coverage up to ₹1.50 Crore with instant claims support and official IBA-approved paperwork."
t1_new = "All-risk comprehensive coverage at up to 3% of total declared value with instant claims support and official IBA-approved paperwork."

# 2. Update Pillar Card badge
t2_old = "Cover to ₹1.5 Cr"
t2_new = "Up to 3% Total Value"

# 3. Update FAQ content
t3_old = "Yes. We offer complete transit and all-risk insurance coverage up to ₹1.5 Crore. In the rare event of transit damage, our claims coordinator assists you with instant paperwork."
t3_new = "Yes. We offer comprehensive transit and all-risk insurance coverage at up to 3% of the total declared consignment value. In the rare event of transit damage, our claims coordinator assists you with instant paperwork and claim settlement."

assert t1_old in text, "t1_old not found"
assert t2_old in text, "t2_old not found"
assert t3_old in text, "t3_old not found"

text = text.replace(t1_old, t1_new, 1)
text = text.replace(t2_old, t2_new, 1)
text = text.replace(t3_old, t3_new, 1)

with open('scripts/elementor_data_no_pricing.json', 'w', encoding='utf-8') as f:
    f.write(text)

print("Successfully updated insurance copy in scripts/elementor_data_no_pricing.json!")
