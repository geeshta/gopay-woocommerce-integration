function openPopup(log) {

    popup = document.getElementById('woocommerce-gopay-menu-popup');
    const elem = popup.querySelectorAll('span');
    elem.forEach(e => e.remove());
    span = document.createElement('span');
    span.innerHTML = JSON.stringify(log);
    popup.append(span);

    popup.style.display = 'grid';
}

function closePopup() {
    popup = document.getElementById('woocommerce-gopay-menu-popup');
    const elem = popup.querySelectorAll('span');
    elem.forEach(e => e.remove());

    popup.style.display = 'none';
}