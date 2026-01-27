// Slider Logic
(function () {
    // Ensure data exists
    if (!window.pdpData || !window.pdpData.sliderImages) return;

    const images = window.pdpData.sliderImages;
    const mainImage = document.getElementById('mainImage');
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const dots = document.querySelectorAll('.dot');

    let currentIndex = 0;

    function showImage(index) {
        // Loop navigation
        if (index < 0) {
            currentIndex = images.length - 1;
        } else if (index >= images.length) {
            currentIndex = 0;
        } else {
            currentIndex = index;
        }

        // Update image source
        if (mainImage) {
            mainImage.src = images[currentIndex];
        }

        // Update dots
        dots.forEach(dot => dot.classList.remove('active'));
        if (dots[currentIndex]) {
            dots[currentIndex].classList.add('active');
        }
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            showImage(currentIndex - 1);
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            showImage(currentIndex + 1);
        });
    }

    dots.forEach(dot => {
        dot.addEventListener('click', function () {
            const index = parseInt(this.getAttribute('data-index'));
            showImage(index);
        });
    });
})();

// Quantity control - prevent going below 1
(function () {
    const decrementBtn = document.getElementById('decrementBtn');
    const incrementBtn = document.getElementById('incrementBtn');
    const quantityValue = document.getElementById('quantityValue');
    const quantityInput = document.getElementById('quantityInput');

    if (!decrementBtn || !incrementBtn || !quantityValue || !quantityInput) return;

    let quantity = 1;
    const minQuantity = 1;

    function updateQuantity(newQty) {
        if (newQty < minQuantity) {
            newQty = minQuantity;
        }
        quantity = newQty;
        quantityValue.textContent = quantity;
        quantityInput.value = quantity;

        // Disable decrement button if at minimum
        if (quantity <= minQuantity) {
            decrementBtn.disabled = true;
            decrementBtn.style.opacity = '0.5';
            decrementBtn.style.cursor = 'not-allowed';
        } else {
            decrementBtn.disabled = false;
            decrementBtn.style.opacity = '1';
            decrementBtn.style.cursor = 'pointer';
        }
    }

    decrementBtn.addEventListener('click', function () {
        if (quantity > minQuantity) {
            updateQuantity(quantity - 1);
        }
    });

    incrementBtn.addEventListener('click', function () {
        updateQuantity(quantity + 1);
    });

    // Initialize
    updateQuantity(1);
})();
