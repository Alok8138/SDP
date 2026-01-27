// Price filter validation - prevent negative values
(function() {
  const priceInput = document.getElementById('maxPriceInput');

  if (priceInput) {
    // Prevent negative values on input
    priceInput.addEventListener('input', function() {
      if (this.value < 0) {
        this.value = 0;
      }
    });

    // Prevent negative values on blur (when user leaves field)
    priceInput.addEventListener('blur', function() {
      if (this.value < 0 || this.value === '') {
        this.value = '';
      }
    });

    // Prevent negative values on form submit
    const form = priceInput.closest('form');
    if (form) {
      form.addEventListener('submit', function(e) {
        if (priceInput.value < 0) {
          priceInput.value = 0;
        }
      });
    }
  }
})();
