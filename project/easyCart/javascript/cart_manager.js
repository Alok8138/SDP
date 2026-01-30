document.addEventListener('DOMContentLoaded', function () {

    // Select container to use event delegation (better for dynamic content)
    const cartContainer = document.querySelector('.cart-items');

    // Elements to update
    const subtotalEl = document.getElementById('cart-subtotal');
    const taxEl = document.getElementById('cart-tax');
    const totalEl = document.getElementById('cart-total');
    const badgeEl = document.querySelector('.cart-badge');

    if (!cartContainer) return;

    cartContainer.addEventListener('click', function (e) {

        // Handle Quantity Buttons
        if (e.target.closest('.qty-btn')) {
            e.preventDefault();
            const btn = e.target.closest('.qty-btn');
            const action = btn.dataset.action; // 'increase' or 'decrease'
            const id = btn.dataset.id;
            const row = btn.closest('tr');
            const qtyInput = row.querySelector('.qty-value');
            console.log(row)
            let currentQty = parseInt(qtyInput.textContent);
            let newQty = currentQty;

            if (action === 'increase') {
                newQty++;
            } else if (action === 'decrease') {
                if (currentQty > 1) {
                    newQty--;
                } else {
                    return; // Don't do anything if 1 and clicking minus
                }
            }

            updateCart(id, newQty, 'update', row);
        }

        // Handle Remove Link
        if (e.target.closest('.remove-link')) {
            e.preventDefault();
            const btn = e.target.closest('.remove-link');
            const id = btn.dataset.id;
            const row = btn.closest('tr');

            updateCart(id, 0, 'remove', row);
        }
    });

    function updateCart(id, qty, action, rowElement) {
        const formData = new FormData();
        formData.append('id', id);
        formData.append('qty', qty);
        formData.append('action', action);

        fetch('update_cart_ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.isEmpty) {
                        // Refresh if empty to show "Empty Cart" message properly (easiest way)
                        location.reload();
                        return;
                    }

                    if (action === 'remove') {
                        rowElement.remove();
                    } else {
                        // Update Row
                        rowElement.querySelector('.qty-value').textContent = qty;
                        // Find total cell - need to ensure class exists
                        const totalCell = rowElement.querySelector('.cart-total');
                        if (totalCell) totalCell.textContent = '$' + data.itemTotal;
                    }

                    // Update Summary
                    if (subtotalEl) subtotalEl.textContent = '$' + data.subtotal;
                    if (taxEl) taxEl.textContent = '$' + data.tax;
                    if (totalEl) totalEl.textContent = '$' + data.grandTotal;

                    // Update Header Badge
                    if (badgeEl) badgeEl.textContent = data.cartCount;

                } else {
                    alert('Error updating cart: ' + data.message);
                }
            })
            .catch(err => {
                console.error(err);
            });
    }
});
