document.addEventListener("DOMContentLoaded", function () {

  const subtotal = parseFloat(window.checkoutData.subtotal);

  const deliveryRadios = document.querySelectorAll("input[name='delivery']");
  const deliveryChargeEl = document.getElementById("delivery-charge");
  const taxEl = document.getElementById("tax-amount");
  const finalTotalEl = document.getElementById("final-total");
  const deliveryTypeInput = document.getElementById("delivery-type");

  function calculateShipping(type) {
    switch (type) {
      case "standard": return 40;
      case "express": return Math.min(80, subtotal * 0.10);
      case "white_glove": return Math.min(150, subtotal * 0.05);
      case "freight": return Math.max(subtotal * 0.03, 200);
      default: return 40;
    }
  }

  function updateTotals(type) {
    const shipping = calculateShipping(type);
    const tax = (subtotal + shipping) * 0.18;
    const finalTotal = subtotal + shipping + tax;

    deliveryChargeEl.textContent = "$" + shipping.toFixed(2);
    taxEl.textContent = "$" + tax.toFixed(2);
    finalTotalEl.textContent = "$" + finalTotal.toFixed(2);

    deliveryTypeInput.value = type;
  }

  deliveryRadios.forEach(radio => {
    radio.addEventListener("change", () => updateTotals(radio.value));
  });

  updateTotals("standard");
});
