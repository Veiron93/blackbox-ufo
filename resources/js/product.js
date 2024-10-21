'use strict'

document.addEventListener('DOMContentLoaded', () => {
	// ФОТО ТОВАРА
	new Swiper('.product_gallery-slider', {
		loop: true,
		slidesPerView: 1,
		effect: 'fade',
		fadeEffect: {
			crossFade: true,
		},
		pagination: {
			el: '.product_gallery-pagination',
			type: 'fraction',
		},
	})

	// ВЫБОР АРТИКУЛА
	function selectSku() {
		let productPage = document.querySelector('.product')

		if (!productPage) {
			return false
		}

		const skus = productPage.querySelectorAll("input[name='product-sku']")
		const btnsAddToCart = productPage.querySelectorAll('.btn-add-cart')
		const sectionsActualPriceProduct = productPage.querySelectorAll(
			'.product-price .product-price_actual'
		)

		const sectionsProductCode = productPage.querySelectorAll('.product-code')
		const sectionsProductAmount =
			productPage.querySelectorAll('.product-amount')

		function setDataIdSku(sku) {
			btnsAddToCart.forEach((btn) => btn.setAttribute('data-id-sku', sku.value))
		}

		function price(sku) {
			let price = sku.getAttribute('data-price')
			sectionsActualPriceProduct.forEach(
				(priceProduct) => (priceProduct.textContent = price)
			)
		}

		function productsOtherInfo(sku) {
			let leftover = sku.getAttribute('data-leftover')
			let skuId = sku.value

			sectionsProductCode.forEach(
				(productCode) =>
					(productCode.querySelector('.product-code_sku').textContent = skuId)
			)
			sectionsProductAmount.forEach(
				(productAmount) =>
					(productAmount.querySelector('span').textContent = leftover)
			)
		}

		if (skus) {
			skus.forEach((sku) =>
				sku.addEventListener('change', (ev) => {
					let sku = ev.target

					setDataIdSku(sku)
					price(sku)
					productsOtherInfo(sku)
				})
			)
		}
	}

	selectSku()

	function protectiveGlassInstallation() {
		const modal = document.querySelector('#modal-protective-glass-installation')

		if (!modal) {
			return
		}

		const productName = modal.querySelector('.product-name span')
		const priceProduct = modal.querySelector('.price-product')
		const priceService = modal.querySelector('.price-service')
		const priceTotal = modal.querySelector('.price-total')

		Fancybox.bind('.btn_protective-glass-installation', {
			src: '.test',
			zoom: false,
			type: 'inline',
			scrolling: 'no',
			dragToClose: false,

			on: {
				init: () => {
					if (document.querySelector('.skus-list')) {
						productName.textContent = document.querySelector(
							'.skus-list input:checked + label'
						).textContent
					}

					priceProduct.textContent = document.querySelector(
						'.product-price_actual'
					).textContent

					priceTotal.textContent =
						Number(priceProduct.textContent) + Number(priceService.textContent)
				},
			},
		})
	}

	protectiveGlassInstallation()

	function onGlassInstallation() {
		const modal = document.querySelector('#modal-protective-glass-installation')

		if (!modal) {
			return
		}

		const formWrapper = modal.querySelector(
			'.protective-glass-installation-wrapper'
		)
		const success = modal.querySelector('.success')

		const glassName = modal.querySelector('.product-name')
		const glassLink = location.href

		const priceProduct = modal.querySelector('.price-product')
		const priceService = modal.querySelector('.price-service')
		const priceTotal = modal.querySelector('.price-total')

		const phone = modal.querySelector(
			'input[name="protective-glass-installation-phone"]'
		)
		const date = modal.querySelector(
			'input[name="protective-glass-installation-date"]'
		)
		const time = modal.querySelector(
			'input[name="protective-glass-installation-time"]'
		)
		const btn = modal.querySelector('.protective-glass-installation-btn')

		btn.addEventListener('click', () => {
			if (!phone.value || !date.value || !time.value) {
				if (!phone.value) {
					phone.classList.add('error')
				}

				if (!date.value) {
					date.classList.add('error')
				}

				if (!time.value) {
					time.classList.add('error')
				}

				return
			}

			fetch('/', {
				method: 'POST',
				headers: {
					'UFO-AJAX-HANDLER': 'ev{onOrderGlassInstallation}',
					'UFO-REQUEST': 1,
				},
				body: JSON.stringify({
					phone: phone.value,
					date: date.value,
					time: time.value,
					glassName: glassName.textContent,
					glassLink: glassLink,
					priceProduct: priceProduct.textContent.trim(),
					priceService: priceService.textContent.trim(),
					priceTotal: priceTotal.textContent.trim(),
				}),
			})
				.then((response) => response.json())
				.then((response) => {
					phone.value = ''
					date.value = ''
					time.value = ''

					formWrapper.classList.add('hidden')
					success.classList.add('active')
				})
				.catch((error) => {
					//console.log(error)
				})
		})
	}

	onGlassInstallation()
})
