document.addEventListener('DOMContentLoaded', function () {

    // =========================================
    // 1. IMAGE SLIDER LOGIC
    // =========================================
    (function initSlider() {
        if (!window.pdpData || !window.pdpData.sliderImages) return;

        const images = window.pdpData.sliderImages;
        const mainImage = document.getElementById('mainImage');
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        const dots = document.querySelectorAll('.dot');
        let currentIndex = 0;

        function showImage(index) {
            if (index < 0) index = images.length - 1;
            if (index >= images.length) index = 0;

            currentIndex = index;

            if (mainImage) mainImage.src = images[currentIndex];

            dots.forEach(dot => dot.classList.remove('active'));
            if (dots[currentIndex]) dots[currentIndex].classList.add('active');
        }

        if (prevBtn) prevBtn.addEventListener('click', () => showImage(currentIndex - 1));
        if (nextBtn) nextBtn.addEventListener('click', () => showImage(currentIndex + 1));

        dots.forEach(dot => {
            dot.addEventListener('click', function () {
                showImage(parseInt(this.getAttribute('data-index')));
            });
        });
    })();

    // =========================================
    // 2. QUANTITY & CART LOGIC
    // =========================================
    (function initCart() {
        const addToCartForm = document.getElementById('addToCartForm');
        const decrementBtn = document.getElementById('decrementBtn');
        const incrementBtn = document.getElementById('incrementBtn');
        const quantityValue = document.getElementById('quantityValue');
        const quantityInput = document.getElementById('quantityInput');
        const sessionQuantityEl = document.getElementById('sessionQuantity');

        if (!addToCartForm) return;

        // Internal State
        let currentQuantity = 1;
        const minQuantity = 1;

        // --- Helper: Update UI ---
        function updateQuantityUI(qty) {
            currentQuantity = qty;
            if (quantityValue) quantityValue.textContent = currentQuantity;
            if (quantityInput) quantityInput.value = currentQuantity;

            // Toggle Decrement Button
            if (decrementBtn) {
                if (currentQuantity <= minQuantity) {
                    decrementBtn.disabled = true;
                    decrementBtn.style.opacity = '0.5';
                    decrementBtn.style.cursor = 'not-allowed';
                } else {
                    decrementBtn.disabled = false;
                    decrementBtn.style.opacity = '1';
                    decrementBtn.style.cursor = 'pointer';
                }
            }
        }

        // --- Event Listeners: Quantity ---
        if (decrementBtn) {
            decrementBtn.addEventListener('click', () => {
                if (currentQuantity > minQuantity) updateQuantityUI(currentQuantity - 1);
            });
        }

        if (incrementBtn) {
            incrementBtn.addEventListener('click', () => {
                updateQuantityUI(currentQuantity + 1);
            });
        }

        // Initialize state
        updateQuantityUI(1);

        // --- Event Listener: Add to Cart (AJAX) ---
        addToCartForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const submitBtn = this.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.innerText : 'Add to Cart';

            // Loading State
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerText = 'Adding...';
                submitBtn.style.opacity = '0.7';
            }

            const formData = new FormData(this);

            fetch('ajax/add_to_cart_ajax.php', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (response.redirected) {
                        window.location.href = response.url;
                        return;
                    }
                    return response.json();
                })
                .then(data => {
                    if (!data) return;

                    if (data.success) {
                        // 1. Update Global Header Cart Badge
                        const badge = document.querySelector('.cart-badge');
                        if (badge) {
                            badge.textContent = data.cartCount;
                        } else {
                            // Create badge if missing (first item)
                            const cartLink = document.querySelector('.cart-icon-link');
                            if (cartLink) {
                                const newBadge = document.createElement('span');
                                newBadge.className = 'cart-badge';
                                newBadge.textContent = data.cartCount;
                                cartLink.appendChild(newBadge);
                            }
                        }

                        // 2. Update "Current in Cart" on PDP
                        if (sessionQuantityEl) {
                            // Parse current value (handle if it's 0 or empty)
                            let currentInCart = parseInt(sessionQuantityEl.textContent) || 0;
                            const addedQty = parseInt(formData.get('quantity')) || 0;

                            // Update DOM
                            sessionQuantityEl.textContent = currentInCart + addedQty;

                            // Optional: Highlight effect
                            sessionQuantityEl.parentElement.style.backgroundColor = '#d1e7dd';
                            setTimeout(() => {
                                sessionQuantityEl.parentElement.style.backgroundColor = '';
                            }, 500);
                        }

                        // 3. Reset Quantity Selector
                        updateQuantityUI(1);

                        // 4. Success Feedback
                        showToast('Item added to cart!');

                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(err => {
                    console.error('Cart Error:', err);
                    alert('Something went wrong. Please try again.');
                })
                .finally(() => {
                    // Reset Button State
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        submitBtn.innerText = originalBtnText;
                        submitBtn.style.opacity = '1';
                    }
                });
        });
    })();

    // Helper: Toast Message (reused pattern)
    function showToast(message) {
        let toast = document.createElement('div');
        toast.className = 'cart-toast';
        toast.textContent = message;
        Object.assign(toast.style, {
            position: 'fixed',
            bottom: '20px',
            right: '20px',
            backgroundColor: '#198754',
            color: '#fff',
            padding: '12px 24px',
            borderRadius: '6px',
            zIndex: '9999',
            boxShadow: '0 4px 12px rgba(0,0,0,0.15)',
            opacity: '0',
            transition: 'opacity 0.3s ease',
            fontWeight: '500'
        });

        document.body.appendChild(toast);

        requestAnimationFrame(() => toast.style.opacity = '1');

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
});
