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

function searchTable() {
	const to_be_filtered = document.getElementById('log_table_filter').value;
	const rows = document.getElementById('log_table_body').rows;

	Object.values(rows).forEach( (row) => {
		const tds = row.getElementsByTagName('td');
		row.style.display = 'none';
		Object.values(tds).forEach( (td) => {
			const re = new RegExp(to_be_filtered, 'gi');
			if (td.innerHTML.match(re)) {
				row.style.display = '';
			}
		} );
	} );
}
