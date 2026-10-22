/**
 * FlexiTag Public Profile Interactivity
 * Copy Link & Lost Item Modal Handler
 */

export function initPublicProfile() {
    // Copy Profile Link
    const copyBtn = document.getElementById('ft-copy-link-btn');
    const toast = document.getElementById('ft-toast');

    if (copyBtn) {
        copyBtn.addEventListener('click', async () => {
            try {
                await navigator.clipboard.writeText(window.location.href);
                showToast('Tautan profil berhasil disalin ke clipboard!');
            } catch (err) {
                // Fallback for browsers without clipboard API
                const input = document.createElement('input');
                input.value = window.location.href;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('Tautan profil berhasil disalin!');
            }
        });
    }

    function showToast(message) {
        if (!toast) return;
        toast.textContent = message;
        toast.style.display = 'block';
        toast.style.opacity = '1';
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => {
                toast.style.display = 'none';
            }, 300);
        }, 3000);
    }

    // Lost Item Message Form Toggle
    const toggleLostFormBtn = document.getElementById('ft-open-lost-form-btn');
    const lostFormModal = document.getElementById('ft-lost-modal');
    const lostFormBackdrop = document.getElementById('ft-lost-modal-backdrop');
    const closeLostFormBtn = document.getElementById('ft-close-lost-modal-btn');

    if (toggleLostFormBtn && lostFormModal && lostFormBackdrop) {
        function openLostModal() {
            lostFormModal.classList.add('is-open');
            lostFormBackdrop.classList.add('is-open');
            document.body.style.overflow = 'hidden';
        }

        function closeLostModal() {
            lostFormModal.classList.remove('is-open');
            lostFormBackdrop.classList.remove('is-open');
            document.body.style.overflow = '';
        }

        toggleLostFormBtn.addEventListener('click', openLostModal);
        lostFormBackdrop.addEventListener('click', closeLostModal);

        if (closeLostFormBtn) {
            closeLostFormBtn.addEventListener('click', closeLostModal);
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lostFormModal.classList.contains('is-open')) {
                closeLostModal();
            }
        });
    }
}
