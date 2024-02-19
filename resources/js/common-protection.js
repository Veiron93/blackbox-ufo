document.addEventListener('DOMContentLoaded', function () {
	window.services = new Services()
	window.cart = new Cart()

	// console.log(
	// 	cart.getDevice({
	// 		deviceName: 'iphone',
	// 		services: [
	// 			{ type: 'стекло', name: 'глянец', price: 1230 },
	// 			{ type: 'плёнка', name: 'матовая', price: 500 },
	// 		],
	// 	})
	// )
})
