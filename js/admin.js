// Custom Confirm Modal
function showConfirm(message, onConfirm, onCancel) {
    return new Promise((resolve) => {
        // Remove existing modal if any
        const existingModal = document.querySelector('.custom-modal-overlay');
        if (existingModal) existingModal.remove();

        // Create modal overlay
        const overlay = document.createElement('div');
        overlay.className = 'custom-modal-overlay';
        overlay.innerHTML = `
            <div class="custom-modal">
                <div class="custom-modal-message">${message}</div>
                <div class="custom-modal-buttons">
                    <button class="custom-modal-btn custom-modal-btn-cancel" data-action="cancel">Tidak</button>
                    <button class="custom-modal-btn custom-modal-btn-confirm" data-action="confirm">Ya</button>
                </div>
            </div>
        `;

        // Add to body
        document.body.appendChild(overlay);

        // Trigger animation
        setTimeout(() => overlay.classList.add('show'), 10);

        // Handle button clicks
        const handleClick = (e) => {
            const action = e.target.dataset.action;
            if (!action) return;

            // Close animation
            overlay.classList.remove('show');
            setTimeout(() => overlay.remove(), 300);

            if (action === 'confirm') {
                if (onConfirm) onConfirm();
                resolve(true);
            } else {
                if (onCancel) onCancel();
                resolve(false);
            }
        };

        overlay.addEventListener('click', (e) => {
            if (e.target === overlay) { // Click outside modal
                overlay.classList.remove('show');
                setTimeout(() => overlay.remove(), 300);
                if (onCancel) onCancel();
                resolve(false);
            } else {
                handleClick(e);
            }
        });
    });
}

// Custom Toast Notification
function showToast(message, type = 'info', duration = 4000) {
    // Get or create toast container
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    
    // Set icon based on type
    let icon = 'bi-info-circle-fill';
    if (type === 'success') icon = 'bi-check-circle-fill';
    else if (type === 'error') icon = 'bi-x-circle-fill';
    else if (type === 'warning') icon = 'bi-exclamation-triangle-fill';

    toast.innerHTML = `
        <i class="bi ${icon}"></i>
        <span>${message}</span>
        <button class="toast-close"><i class="bi bi-x"></i></button>
    `;

    // Add to container
    container.appendChild(toast);

    // Trigger show animation
    setTimeout(() => toast.classList.add('show'), 10);

    // Close button handler
    const closeBtn = toast.querySelector('.toast-close');
    const closeToast = () => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    };
    closeBtn.addEventListener('click', closeToast);

    // Auto close
    setTimeout(closeToast, duration);
}
