(function () {
    // Ensure data exists
    if (!window.checkoutData || typeof window.checkoutData.subtotal === 'undefined') return;

    const subtotal = window.checkoutData.subtotal;
    const deliveryRadios = document.querySelectorAll('input[name="delivery"]');
    const deliveryChargeEl = document.getElementById('delivery-charge');
    const finalTotalEl = document.getElementById('final-total');
    const deliveryTypeInput = document.getElementById('delivery-type');

    function updateTotals() {
        const checkedRadio = document.querySelector('input[name="delivery"]:checked');
        if (!checkedRadio) return;

        const selectedDelivery = checkedRadio.value;
        let deliveryCharge = 0;

        if (selectedDelivery === 'express') {
            deliveryCharge = subtotal * 0.1;
        }

        const finalTotal = subtotal + deliveryCharge;

        if (deliveryChargeEl) {
            deliveryChargeEl.textContent = '$' + deliveryCharge.toFixed(2);
        }

        if (finalTotalEl) {
            finalTotalEl.textContent = '$' + finalTotal.toFixed(2);
        }

        if (deliveryTypeInput) {
            deliveryTypeInput.value = selectedDelivery;
        }
    }

    deliveryRadios.forEach(radio => {
        radio.addEventListener('change', updateTotals);
    });

    updateTotals();
})();
