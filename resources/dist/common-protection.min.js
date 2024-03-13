document.addEventListener('DOMContentLoaded', function () {
	window.cart = new Cart()
	window.services = new Services()

	new Swiper('.brands_slider', {
		loop: true,

		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},

		breakpoints: {
			0: {
				slidesPerView: 1.5,
				spaceBetween: 10,
			},
			991: {
				slidesPerView: 5,
				spaceBetween: 20,
			},
		},
	})
})
