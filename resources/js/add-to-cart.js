'use strict'

document.addEventListener('DOMContentLoaded', () => {
	const config = {
		headers: {
			'UFO-AJAX-HANDLER': 'ev{onAddToCart}',
			'UFO-REQUEST': 1,
		},
	}

	const btns = document.querySelectorAll('.btn-add-to-cart')
	const miniCartInfoWrapper = document.querySelector('.mini-cart_info')

	function onAddToCart(data) {
		axios.post('/shop/', { ...data }, config).then((response) => {
			updateMiniCart(response.data.response['mini-cart-info'])
			setAddetToCart(data.id)
		})
	}

	function updateMiniCart(data) {
		if (data) {
			miniCartInfoWrapper.innerHTML = data
		}
	}

	function setAddetToCart(id) {
		const products = document.querySelectorAll(
			'.product-card[data-id="' + id + '"]'
		)

		if (products) {
			products.forEach((product) => product.setAttribute('data-added', '1'))
		}
	}

	function initEventsBtnsAddToCart() {
		if (btns) {
			btns.forEach((btn) => {
				btn.addEventListener('click', () => {
					const data = {
						id: btn.getAttribute('data-id'),
						type: btn.getAttribute('data-type'),
						quantity: btn.getAttribute('data-quantity'),
						id_sku: btn.getAttribute('data-id-sku'),
					}

					onAddToCart(data)
				})
			})
		}
	}

	initEventsBtnsAddToCart()
})

//const url = location.pathname
//const page = url.split('/')[2] == 'product' ? 'product' : 'catalog'

//let products = []

// if (page === 'product') {
// 	let product = document.querySelector('.catalog-product')
// 	products.push(product)
// } else {
// 	products = document.querySelectorAll('.product-card')
// }
