/**
 * FlexiTag User Edit Lost Mode JavaScript
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Character Counter for Emergency Message Textarea
    const messageInput = document.getElementById('lostMessage');
    const charCounter = document.getElementById('messageCharCount');

    function updateCharCount() {
        if (!messageInput || !charCounter) return;
        const count = messageInput.value.length;
        charCounter.textContent = `${count}/250 karakter`;

        if (count > 250) {
            charCounter.style.color = '#DC2626';
            charCounter.style.fontWeight = '700';
        } else {
            charCounter.style.color = '#78716C';
            charCounter.style.fontWeight = '600';
        }
    }

    if (messageInput) {
        messageInput.addEventListener('input', updateCharCount);
        updateCharCount(); // Initialize with existing value
    }

    // 2. Client-side Form Validation Check before submission
    const lostForm = document.getElementById('lostModeForm');
    if (lostForm) {
        lostForm.addEventListener('submit', (e) => {
            if (messageInput && messageInput.value.trim().length === 0) {
                e.preventDefault();
                alert('Pesan penemuan barang wajib diisi!');
                messageInput.focus();
            }
        });
    }
});

// 3. Mark Single Message as Read (FR-LST-03)
window.markMessageRead = async function(messageId) {
    const btn = document.getElementById(`btn-read-${messageId}`);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<span>Memproses...</span>';
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch(`/mode-hilang/pesan/${messageId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });

        const data = await res.json();
        if (data.success) {
            const card = document.getElementById(`msg-card-${messageId}`);
            if (card) {
                card.classList.remove('is-unread');
            }

            const badge = document.getElementById(`msg-badge-${messageId}`);
            if (badge) {
                badge.className = 'msg-status-pill-read';
                badge.textContent = '✓ Telah Dibaca';
            }

            if (btn) {
                btn.outerHTML = `<span class="msg-read-timestamp">✓ Dibaca baru saja</span>`;
            }

            const countPill = document.getElementById('unreadCountBadge');
            if (countPill) {
                let current = parseInt(countPill.getAttribute('data-count') || '0', 10);
                current = Math.max(0, current - 1);
                countPill.setAttribute('data-count', current);
                if (current > 0) {
                    countPill.innerHTML = `<span class="unread-dot">●</span><span>${current} Baru</span>`;
                } else {
                    countPill.style.display = 'none';
                    const markAllBtn = document.getElementById('btnMarkAllRead');
                    if (markAllBtn) markAllBtn.style.display = 'none';
                }
            }
        }
    } catch (err) {
        console.error('Gagal menandai pesan telah dibaca:', err);
        if (btn) {
            btn.disabled = false;
            btn.innerHTML = `<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg><span>Tandai Dibaca</span>`;
        }
    }
};

// 4. Mark All Lost Messages as Read (FR-LST-03)
window.markAllLostMessagesRead = async function() {
    const btn = document.getElementById('btnMarkAllRead');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Memproses...';
    }

    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        const res = await fetch('/mode-hilang/pesan/read-all', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
        });

        const data = await res.json();
        if (data.success) {
            document.querySelectorAll('.inbox-msg-item.is-unread').forEach(item => {
                item.classList.remove('is-unread');
            });

            document.querySelectorAll('.msg-status-pill-unread').forEach(badge => {
                badge.className = 'msg-status-pill-read';
                badge.textContent = '✓ Telah Dibaca';
            });

            document.querySelectorAll('.btn-action-read').forEach(btnAction => {
                btnAction.outerHTML = `<span class="msg-read-timestamp">✓ Dibaca baru saja</span>`;
            });

            const countPill = document.getElementById('unreadCountBadge');
            if (countPill) {
                countPill.style.display = 'none';
            }

            if (btn) {
                btn.style.display = 'none';
            }
        }
    } catch (err) {
        console.error('Gagal menandai semua dibaca:', err);
        if (btn) {
            btn.disabled = false;
            btn.textContent = 'Tandai Semua Dibaca';
        }
    }
};
