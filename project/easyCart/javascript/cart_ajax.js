document.addEventListener('DOMContentLoaded', function () {
    // Select all forms with class 'ajax-cart-form'
    const cartForms = document.querySelectorAll('.ajax-cart-form');

    cartForms.forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault(); // Stop normal submission

            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');

            // Optional: Visual feedback on button (loading state)
            const originalBtnContent = submitBtn.innerHTML;
            // Don't fully replace if it's an image, but maybe change opacity
            submitBtn.style.opacity = '0.7';
            submitBtn.disabled = true;

            fetch('add_to_cart_ajax.php', {
                method: 'POST',
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update cart badge
                        const badge = document.querySelector('.cart-badge');
                        if (badge) {
                            badge.textContent = data.cartCount;
                        } else {
                            // If badge doesn't exist yet (empty cart), append it to the link if possible
                            // This depends on the header structure, but usually .cart-badge exists if count > 0
                            // If it completely disappears when empty, we might need to recreate it.
                            // Checking header.php: it only echoes if session cart is set.
                            // So we might need to find the cart link and append.
                            const cartLink = document.querySelector('.cart-icon-link');
                            if (cartLink) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'cart-badge';
                                newBadge.textContent = data.cartCount;
                                cartLink.appendChild(newBadge);
                            }
                        }

                        // Optional: Show success message (Alert for now as per requirements)
                        // Or a small toast if we want to be fancy, but let's stick to requirements "Optionally show small success message"
                        // A simple alert is annoying, let's do a console log or a temporary text change on the button?
                        // The user said "Optionally show a small success message".
                        // Let's create a temporary floating element.
                        showToast("Item added to cart!");

                    } else {
                        alert('Error adding to cart: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Something went wrong. Please try again.');
                })
                .finally(() => {
                    // Restore button state
                    submitBtn.style.opacity = '1';
                    submitBtn.disabled = false;
                });
        });
    });

    // Helper to show a simple toast message
    function showToast(message) {
        let toast = document.createElement('div');
        toast.className = 'cart-toast';
        toast.textContent = message;
        toast.style.position = 'fixed';
        toast.style.bottom = '20px';
        toast.style.right = '20px';
        toast.style.backgroundColor = '#28a745';
        toast.style.color = 'white';
        toast.style.padding = '10px 20px';
        toast.style.borderRadius = '5px';
        toast.style.zIndex = '1000';
        toast.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        toast.style.opacity = '0';
        toast.style.transition = 'opacity 0.3s ease';

        document.body.appendChild(toast);

        // Trigger animation
        setTimeout(() => toast.style.opacity = '1', 10);

        // Remove after 3 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }
});
