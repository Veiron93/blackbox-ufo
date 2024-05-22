document.addEventListener('DOMContentLoaded', function () {
	window.cart = new Cart()
	window.services = new Services()

	new Swiper('.portfolio-slider-wrapper', {
		loop: true,

		autoplay: {
			delay: 10000,
			disableOnInteraction: false,
		},

		breakpoints: {
			0: {
				slidesPerView: 1.2,
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
		},
	})

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
