// корзина
class Cart {
	cartNode = null
	listDevices = null

	cart = null

	totalPriceNode = null
	totalPriceValue = 0

	constructor() {
		this.initNodes()
		this.renderCart()
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

	getCartLocalStorage() {
		return JSON.parse(localStorage.getItem('cartProtection'))
	}

	setCartLocalStorage(cart) {
		localStorage.setItem('cartProtection', JSON.stringify(cart))
	}

	stateBtnService() {}

	getDeviceCart() {}

	add(device, service) {
		console.log(device)
		console.log(service)

		let cart = this.getCartLocalStorage()

		if (cart && Object.keys(cart).length) {
			let index = this.isServiceToCart(device.id, service.code)

			if (index != -1) {
				cart[device.id].services[index] = service
			} else {
				cart[device.id].services.push(service)
			}
		} else {
			cart = {
				[device.id]: {
					deviceName: device.name,
					services: [service],
				},
			}
		}

		//this.cart = cart

		//console.log(cart)

		this.setCartLocalStorage(cart)
		this.renderCart()

		//console.log(cart)
	}

	del(idDevice) {
		let cart = this.getCartLocalStorage()

		if (!cart) {
			return null
		}

		delete cart[idDevice]

		this.setCartLocalStorage(cart)
		this.renderCart()
	}

	isServiceToCart(deviceId, serviceCode) {
		function getCode(serviceCode) {
			return serviceCode.split('~')[0]
		}

		let cart = this.getCartLocalStorage()

		if (!cart && !Object.keys(cart).length) {
			return null
		}

		let services = cart[deviceId].services

		let index = Array.from(services).findIndex(
			(service) => getCode(service.code) == getCode(serviceCode)
		)

		return index
	}

	renderCart() {
		let cart = this.getCartLocalStorage()

		let list = ''

		for (let device in cart) {
			list += this.getDevice(cart[device])
		}

		this.listDevices.innerHTML = list
	}

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

		return device.trim()
	}

	// recalculationTotalPrice() {
	// 	let total = 0

	// 	this.services.forEach((service) => {
	// 		if (service.value) {
	// 			total += Number(service.value)
	// 		}
	// 	})

	// 	this.totalPriceValue = total
	// 	this.totalPriceNode.textContent = total
	// }

	test() {
		console.log(777)
	}
}
