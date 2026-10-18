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

var menuToggle = document.querySelector('.menu-toggle');
var navigation = document.querySelector('#main-navigation');

if (menuToggle && navigation) {
    menuToggle.addEventListener('click', function () {
        var isOpen = navigation.classList.toggle('is-open');
        menuToggle.classList.toggle('is-open', isOpen);
        menuToggle.setAttribute('aria-expanded', String(isOpen));
        menuToggle.setAttribute('aria-label', isOpen ? 'Close navigation' : 'Open navigation');
    });

    navigation.querySelectorAll('a').forEach(function (link) {
        link.addEventListener('click', function () {
            navigation.classList.remove('is-open');
            menuToggle.classList.remove('is-open');
            menuToggle.setAttribute('aria-expanded', 'false');
            menuToggle.setAttribute('aria-label', 'Open navigation');
        });
    });
}

var revealItems = document.querySelectorAll('.reveal');

if ('IntersectionObserver' in window) {
    var revealObserver = new IntersectionObserver(function (entries, observer) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: .12, rootMargin: '0px 0px -40px' });

    revealItems.forEach(function (item) {
        revealObserver.observe(item);
    });
} else {
    revealItems.forEach(function (item) {
        item.classList.add('is-visible');
    });
}
