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
