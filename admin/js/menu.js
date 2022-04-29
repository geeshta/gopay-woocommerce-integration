function openPopup(log) {

    popup = document.getElementById('popup');
    const elem = popup.querySelectorAll('span');
    elem.forEach(e => e.remove());
    span = document.createElement("span");
    span.innerHTML = JSON.stringify(log);
    popup.append(span);

    popup.style.display = 'grid';
}

function closePopup() {
    popup = document.getElementById('popup');
    const elem = popup.querySelectorAll('span');
    elem.forEach(e => e.remove());
    document.getElementById('popup').style.display = 'none';
}