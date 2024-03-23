'use strict'

document.addEventListener('DOMContentLoaded', function () {
	// SLIDERS
	// главный слайдер
	new Swiper('.main-slider-wrapper', {
		loop: true,
		effect: 'fade',
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
	})

	// главный слайдер - мобильная версия
	new Swiper('.main-slider-mobile', {
		loop: true,
		effect: 'fade',
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
	})

	// КНОПКА ВВЕРХ
	// {
	// 	function trackScroll() {
	// 		let scrolled = window.pageYOffset,
	// 			coords = document.documentElement.clientHeight

	// 		if (scrolled > coords) {
	// 			btns.forEach((btn) => btn.classList.add('active'))
	// 		} else {
	// 			btns.forEach((btn) => btn.classList.remove('active'))
	// 		}
	// 	}

	// 	function backToTop() {
	// 		if (window.pageYOffset > 0) window.scrollBy(0, -window.pageYOffset)
	// 	}

	// 	window.addEventListener('scroll', trackScroll)

	// 	let btns = document.querySelectorAll('.btn-up-page')
	// 	btns.forEach((btn) => btn.addEventListener('click', backToTop))
	// }

	// товары
	function productsSliders() {
		let defaultSliderSettings = {
			autoplay: {
				delay: 2500,
				disableOnInteraction: false,
			},

			navigation: {
				nextEl: null,
				prevEl: null,
			},

			loop: true,
		}

		function initSliders(sliders, settings) {
			sliders.forEach((slider) => {
				let sliderNavArrows = slider.querySelector('.navigation')

				settings.navigation.prevEl = sliderNavArrows.querySelector('.btn-prev')
				settings.navigation.nextEl = sliderNavArrows.querySelector('.btn-next')

				new Swiper(slider, settings)
			})
		}

		let productSlidersTypeGrid = document.querySelectorAll(
				'.block-product-slider__grid'
			),
			productSlidersTypeList = document.querySelectorAll(
				'.block-product-slider__list'
			)

		// type grid
		if (productSlidersTypeGrid) {
			let settings = defaultSliderSettings

			settings.breakpoints = {
				0: {
					slidesPerView: 2.2,
					spaceBetween: 10,
				},
				768: {
					slidesPerView: 4,
					spaceBetween: 20,
				},
				991: {
					slidesPerView: 5,
					spaceBetween: 20,
				},
				1200: {
					slidesPerView: 6,
					spaceBetween: 20,
				},
			}

			initSliders(productSlidersTypeGrid, settings)
		}

		// type list
		if (productSlidersTypeList) {
			let settings = defaultSliderSettings

			settings.breakpoints = {
				0: {
					slidesPerView: 1,
					spaceBetween: 10,
				},
				768: {
					slidesPerView: 2,
					spaceBetween: 20,
				},
				991: {
					slidesPerView: 3,
					spaceBetween: 20,
				},
				1200: {
					slidesPerView: 3,
					spaceBetween: 20,
				},
			}

			initSliders(productSlidersTypeList, settings)
		}
	}

	productsSliders()

	// фирменная упаковка в корзине
	// $('.branded-packaging_slider').not('.slick-initialized').slick({
	// 	slidesToShow: 5,
	// 	slidesToScroll: 5,
	// 	arrows: true,
	// });

	// КАТАЛОГ МЕНЮ (МОБИЛЬНАЯ ВЕРСИЯ)
	function mobileCatalogMenu() {
		let catalogMenu = document.querySelector('.mobile-menu-catalog')

		if (!catalogMenu) {
			return false
		}

		function stateCatalogMenu() {
			document.body.classList.toggle('no-scroll')
			catalogMenu.classList.toggle('active')
			closeSubCategories()
		}

		function stateCategoriesLevel_2() {
			activeCategoriesLevel_2 = this.nextElementSibling

			if (activeCategoriesLevel_2) {
				activeCategoriesLevel_2.classList.add('active')

				let categoriesLevel_2 =
					activeCategoriesLevel_2.querySelectorAll('.category-level-2')

				categoriesLevel_2.forEach((categoryLevel_2) => {
					categoryLevel_2.addEventListener('click', stateCategoriesLevel_3)
				})
			}
		}

		function stateCategoriesLevel_3() {
			activeCategoriesLevel_3.push(this)

			if (activeCategoriesLevel_3) {
				this.classList.toggle('active')
			}
		}

		function closeSubCategories() {
			if (activeCategoriesLevel_2) {
				activeCategoriesLevel_2.classList.remove('active')
				activeCategoriesLevel_2 = null
			}

			if (activeCategoriesLevel_3) {
				activeCategoriesLevel_3.forEach((categoryLevel_3) => {
					categoryLevel_3.classList.remove('active')
				})

				activeCategoriesLevel_3.length = 0
			}
		}

		let btnsCatalogMenu = document.querySelectorAll('.btn-catalog-menu-mobile')

		let mainCategories = catalogMenu.querySelectorAll('.main-category_wrapper')

		let activeCategoriesLevel_2 = null
		let activeCategoriesLevel_3 = []
		let linksBackMainCategories = catalogMenu.querySelectorAll('.link-back')

		btnsCatalogMenu.forEach((btn) => {
			btn.addEventListener('click', stateCatalogMenu)
		})

		mainCategories.forEach((mainCategory) => {
			mainCategory.addEventListener('click', stateCategoriesLevel_2)
		})

		linksBackMainCategories.forEach((link) => {
			link.addEventListener('click', closeSubCategories)
		})
	}

	mobileCatalogMenu()

	// НАВИГАЦИЯ (МОБИЛЬНАЯ ВЕРСИЯ)
	function mobileNavigation() {
		const menu = document.querySelector('.mobile-menu')
		const btnMobileMenu = document.querySelector('.btn-mobile-menu')

		if (!btnMobileMenu || !menu) {
			return false
		}

		function stateMenu() {
			document.body.classList.toggle('no-scroll')
			menu.classList.toggle('active')
			btnMobileMenu.classList.toggle('active')
		}

		btnMobileMenu.addEventListener('click', stateMenu)
	}

	mobileNavigation()

	// ВИД СПИСКА ТОВАРОВ
	function viewProductList() {
		let viewProductListBlock = document.querySelector(
			'.catalog-products-sorting .view-mode'
		)

		if (viewProductListBlock) {
			if (!getCookie('viewModeProductsList')) {
				setCookie('viewModeProductsList', 'grid')
			}

			let switches = viewProductListBlock.querySelectorAll('.item'),
				typeProductList = getCookie('viewModeProductsList')

			switches.forEach(function (e) {
				e.addEventListener('click', function () {
					if (
						this.getAttribute('data-type') == 'grid' ||
						this.getAttribute('data-type') == 'list'
					) {
						typeProductList = this.getAttribute('data-type')
					} else {
						typeProductList = 'grid'
					}

					document
						.querySelector('.wrapper-products--default')
						.setAttribute('data-view-list', typeProductList)
					setCookie('viewModeProductsList', typeProductList)

					switches.forEach(function (item) {
						item.classList.remove('active')
					})

					e.classList.add('active')
				})
			})
		}
	}

	viewProductList()

	// ДОБАВИТЬ В КОРЗИНУ
	function productAddToCart() {
		const url = location.pathname
		const page = url.split('/')[2] == 'product' ? 'product' : 'catalog'

		let btns = document.querySelectorAll('.btn-add-cart')
		let products = []

		if (page == 'product') {
			let product = document.querySelector('.catalog-product')
			products.push(product)
		} else {
			products = document.querySelectorAll('.product-card')
		}

		let config = {
			headers: {
				'UFO-AJAX-HANDLER': 'ev{onAddToCart}',
				'UFO-REQUEST': 1,
			},
		}

		let miniCartWrapper = document.getElementById('mini-cart'),
			miniCartMobileWrapper = document.getElementById('mini-cart-mobile')

		if (btns && products) {
			btns.forEach((btn) => {
				btn.addEventListener('click', function () {
					let id = this.getAttribute('data-id'),
						type = this.getAttribute('data-type'),
						quantity = this.getAttribute('data-quantity'),
						id_sku = this.getAttribute('data-id-sku')

					axios
						.post(
							'/shop/',
							{
								id: id,
								type: type,
								quantity: quantity,
								id_sku: id_sku,
							},
							config
						)
						.then((response) => {
							let updatedMiniCart = response.data.response['#mini-cart']
							let updatedMiniCartMobile =
								response.data.response['#mini-cart-mobile']

							if (updatedMiniCart && updatedMiniCartMobile) {
								if (miniCartWrapper) {
									miniCartWrapper.innerHTML = updatedMiniCart
								}

								if (miniCartMobileWrapper) {
									miniCartMobileWrapper.innerHTML = updatedMiniCartMobile
								}

								products.forEach((product) => {
									if (product.getAttribute('data-id') == id) {
										product.classList.add('added')
									}
								})
							}
						})
				})
			})
		}
	}

	productAddToCart()

	// БЛОК БЕСКОНЕЧНЫЙ СПИСОК ТОВАРОВ
	function onInfinityProductsList() {
		let infinityProductsList = document.querySelector('.infinity-products-list')

		if (!infinityProductsList) {
			return
		}

		let btnMore = infinityProductsList.querySelector('.btn-more-products')
		let wrapperProductsList =
			infinityProductsList.querySelector('.wrapper-products')

		let config = {
			headers: {
				'UFO-AJAX-HANDLER': 'ev{onGetRandomProducts}',
				'UFO-REQUEST': 1,
			},
		}

		let products = wrapperProductsList.querySelectorAll('.product-card')
		let ids = []

		if (products) {
			products.forEach((product) => ids.push(product.getAttribute('data-id')))
		}

		btnMore.addEventListener('click', () => {
			axios
				.post(
					'/',
					{
						ids: ids,
					},
					config
				)
				.then((response) => {
					if (!response.data.response) {
						return
					}

					let newProductsIds = response.data.response.newProductsIds,
						newProducts = response.data.response.newProducts

					wrapperProductsList.insertAdjacentHTML('beforeend', newProducts)
					newProductsIds.forEach((id) => ids.push(id))

					productAddToCart()
				})
		})
	}

	onInfinityProductsList()

	function stateTopSectionHeader() {
		const btn = document.querySelector('.btn-state-top-section')

		if (!btn) {
			return false
		}

		const topSection = document.querySelector('.header-section-top')

		btn.addEventListener('click', () => {
			topSection.classList.toggle('active')
			btn.classList.toggle('active')
			btn.setAttribute(
				'title',
				btn.classList.contains('active')
					? 'Скрыть'
					: 'Показать больше информации'
			)
		})
	}

	stateTopSectionHeader()
})
