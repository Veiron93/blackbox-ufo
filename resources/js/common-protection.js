document.addEventListener('DOMContentLoaded', function () {
	window.cart = new Cart()
	window.services = new Services()

	new Swiper('.brands_slider', {
		loop: true,
		//effect: 'fade',
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},

		breakpoints: {
			0: {
				slidesPerView: 2.2,
				spaceBetween: 10,
			},
			768: {
				slidesPerView: 4,
			},
			991: {
				slidesPerView: 5,
				spaceBetween: 20,
			},
		},
		// navigation: {
		// 	nextEl: '.main-slider .button-next',
		// 	prevEl: '.main-slider .button-prev',
		// },
	})
})
