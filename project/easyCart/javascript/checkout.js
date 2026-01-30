document.addEventListener("DOMContentLoaded", function () {

  const deliveryRadios = document.querySelectorAll("input[name='delivery']");
  const deliveryChargeEl = document.getElementById("delivery-charge");
  const taxEl = document.getElementById("tax-amount");
  const finalTotalEl = document.getElementById("final-total");
  const deliveryTypeInput = document.getElementById("delivery-type");
  const subtotalEl = document.getElementById("subtotal");

  function updateTotals(type) {
    // Phase 5: AJAX update
    fetch('ajax_checkout_totals.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({ type: type })
    })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          // Update UI
          if (subtotalEl) subtotalEl.textContent = "$" + data.subtotal;
          deliveryChargeEl.textContent = "$" + data.shipping;
          taxEl.textContent = "$" + data.tax;
          finalTotalEl.textContent = "$" + data.total;

          // Allow form submission with correct type
          deliveryTypeInput.value = type;
        } else {
          console.error("Error updating totals:", data.message);
        }
      })
      .catch(error => console.error('Error:', error));
  }

  // Event Listeners
  deliveryRadios.forEach(radio => {
    radio.addEventListener("change", () => updateTotals(radio.value));
  });

  // Sync hidden input with initially checked radio on load
  const checkedRadio = document.querySelector("input[name='delivery']:checked");
  if (checkedRadio) {
    deliveryTypeInput.value = checkedRadio.value;
  }
});
