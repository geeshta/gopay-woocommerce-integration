function openPopup(log) {
	const popup = document.getElementById( 'woocommerce-gopay-menu-popup' );
	popup.querySelectorAll( 'span' ).forEach( e => e.remove() );
	const span     = document.createElement( 'span' );
	span.innerHTML = JSON.stringify( log );
	popup.append( span );

	popup.style.display = 'grid';
}

function closePopup() {
	const popup = document.getElementById( 'woocommerce-gopay-menu-popup' );
	const elem  = popup.querySelectorAll( 'span' );
	elem.forEach( e => e.remove() );

	popup.style.display = 'none';
}
