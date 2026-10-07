document.querySelectorAll('[data-quantity-action]').forEach(function (button) {
    button.addEventListener('click', function () {
        var input = button.parentElement.querySelector('input[type="number"]');
        var min = Number(input.min) || 1;
        var max = Number(input.max) || 20;
        var nextValue = Number(input.value) + (button.dataset.quantityAction === 'increase' ? 1 : -1);

        input.value = Math.min(max, Math.max(min, nextValue));
        input.dispatchEvent(new Event('change', { bubbles: true }));
    });
});
