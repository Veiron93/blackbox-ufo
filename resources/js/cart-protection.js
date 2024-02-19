// корзина
class Cart {
	cartNode = null
	listDevices = null

	totalPriceNode = null
	totalPriceValue = 0

	constructor() {
		this.initNodes()
	}

	initNodes() {
		this.cartNode = document.querySelector('.cart')

		this.listDevices = this.cartNode.querySelector(
			'.cart_services-list-devices'
		)

		this.totalPriceNode = this.cartNode.querySelector(
			'.cart_services-total-price'
		)
	}

	initEvents() {}

	getCartLocalStorage() {
		localStorage.getItem('cartProtection')
	}

	setCartLocalStorage() {
		localStorage.setItem('cartProtection', 1)
	}

	stateBtnService() {}

	addCartDevice() {}

	delCartDevice() {}

	getDevice(data) {
		let services = []

		data.services.forEach((service) => {
			services.push(`
                <div class="service">
                    <div class="service-name">
                        <span>${service.type}</span>
                        <span>${service.name}</span>
                    </div>
                    <div class="service-price">≈ ${service.price} руб.</div>
                </div>
            `)
		})

		let device = `
        <div class="device">
            <div class="device-header">
                <div class="device-name">${data.deviceName}</div>
                <div class="device-btn-del"></div>
            </div>

            <div class="device-services">${services.join('')}</div>
        </div>
        `

		return device
	}

	test() {
		console.log(777)
	}
}
