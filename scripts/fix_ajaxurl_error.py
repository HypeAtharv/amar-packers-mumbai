with open('plugins/hype-marketer.php', 'r', encoding='utf-8') as f:
    code = f.read()

# Replace const ajaxurl and const ajaxNonce
old_defs = """    <script>
    const ajaxurl = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    let hypeLeadsStore = {};
    try {
        const storeEl = document.getElementById('hypeLeadsStore');
        if (storeEl && storeEl.textContent) {
            hypeLeadsStore = JSON.parse(storeEl.textContent);
        }
    } catch(err) {
        console.warn('Failed to parse leads store:', err);
    }

    function hypeHandleRowClick(e, leadId) {
        if (!e) return;
        const target = e.target;
        if (target.closest('input') || target.closest('select') || target.closest('button') || target.closest('a')) {
            return;
        }
        hypeOpenDetailModalById(leadId);
    }

    function hypeOpenDetailModalById(leadId) {
        const lead = hypeLeadsStore[leadId];
        if (lead) {
            hypeOpenDetailModal(lead);
        }
    }
    const ajaxNonce = '<?php echo esc_js($ajax_nonce); ?>';
    let currentModalLeadId = 0;"""

new_defs = """    <script>
    var hypeAjaxUrl = window.ajaxurl || '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
    var ajaxNonce = '<?php echo esc_js($ajax_nonce); ?>';
    var currentModalLeadId = 0;
    var hypeLeadsStore = {};

    try {
        var storeEl = document.getElementById('hypeLeadsStore');
        if (storeEl && storeEl.textContent) {
            hypeLeadsStore = JSON.parse(storeEl.textContent);
        }
    } catch(err) {
        console.warn('Failed to parse leads store:', err);
    }

    window.hypeHandleRowClick = function(e, leadId) {
        if (!e) return;
        var target = e.target;
        if (target.closest('input') || target.closest('select') || target.closest('button') || target.closest('a')) {
            return;
        }
        window.hypeOpenDetailModalById(leadId);
    };

    window.hypeOpenDetailModalById = function(leadId) {
        var lead = hypeLeadsStore[leadId];
        if (lead) {
            window.hypeOpenDetailModal(lead);
        } else {
            console.warn('Lead #' + leadId + ' not found in store');
        }
    };"""

if old_defs in code:
    code = code.replace(old_defs, new_defs, 1)
    print("Replaced old_defs successfully!")
else:
    print("WARNING: old_defs not found, checking variations...")

# Replace fetch(ajaxurl, with fetch(hypeAjaxUrl,
code = code.replace('fetch(ajaxurl,', 'fetch(hypeAjaxUrl,')

# Attach functions to window
replacements = [
    ('function showHypeToast(', 'window.showHypeToast = function('),
    ('function hypeClientSearch(', 'window.hypeClientSearch = function('),
    ('function hypeChangeStatus(', 'window.hypeChangeStatus = function('),
    ('function hypeChangePriority(', 'window.hypeChangePriority = function('),
    ('function hypeMoveToTrash(', 'window.hypeMoveToTrash = function('),
    ('function hypeRestoreTrash(', 'window.hypeRestoreTrash = function('),
    ('function hypeDeletePermanent(', 'window.hypeDeletePermanent = function('),
    ('function hypeToggleSpam(', 'window.hypeToggleSpam = function('),
    ('function hypeExecuteBulkAction(', 'window.hypeExecuteBulkAction = function('),
    ('function hypeOpenDetailModal(', 'window.hypeOpenDetailModal = function('),
    ('function hypeCloseModal(', 'window.hypeCloseModal = function('),
    ('function hypeSaveNotes(', 'window.hypeSaveNotes = function('),
    ('function hypeOpenManualModal(', 'window.hypeOpenManualModal = function('),
    ('function hypeCloseManualModal(', 'window.hypeCloseManualModal = function('),
    ('function hypeSubmitManualLead(', 'window.hypeSubmitManualLead = function(')
]

for old_fn, new_fn in replacements:
    if old_fn in code:
        code = code.replace(old_fn, new_fn, 1)
        print(f"Attached {old_fn} to window")

with open('plugins/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(code)

with open('scripts/hype-marketer/hype-marketer.php', 'w', encoding='utf-8') as f:
    f.write(code)

print("Saved fixed hype-marketer.php files!")
