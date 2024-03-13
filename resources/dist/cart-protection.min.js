// корзина
class Cart {
	cartNode = null
	listDevices = null

	totalPriceNode = null
	totalPriceValue = 0

	// form
	form = null
	btnSend = null
	errorSectionNode = null
	errorText = null

	phone = null
	name = null
	date = null

	constructor() {
		this.initNodes()
		this.renderCart()
		this.recalculationTotalPrice()
		this.initUser()
	}

	initNodes() {
		this.cartNode = document.querySelector('.cart')

		this.listDevices = this.cartNode.querySelector(
			'.cart_services-list-devices'
		)

		this.totalPriceNode = this.cartNode.querySelector(
			'.cart_services-total-price span'
		)

		this.form = this.cartNode.querySelector('form')
		this.phone = this.form.querySelector('input[name="phone"]')
		this.name = this.form.querySelector('input[name="name"]')
		this.date = this.form.querySelector('input[name="date"]')

		this.date.addEventListener('change', () => {
			if (this.date.value) {
				this.date.classList.add('active')
			} else {
				this.date.classList.remove('active')
			}
		})

		this.btnSend = document.getElementById('send-order-protection')

		if (this.btnSend) {
			this.btnSend.addEventListener('click', () => this.send())
		}

		this.errorSectionNode = this.form.querySelector('.error-section')
	}

	getCartLocalStorage() {
		return JSON.parse(localStorage.getItem('cartProtection'))
	}

	setCartLocalStorage(cart) {
		localStorage.setItem('cartProtection', JSON.stringify(cart))
	}

	add(device, service) {
		let cart = this.getCartLocalStorage()

		if (cart && Object.keys(cart).length) {
			let index = this.isServiceToCart(device.id, service.code)

			if (index == -1 || index == null) {
				if (this.isDeviceToCart(device.id)) {
					// устройсвто уже есть в корзине
					cart[device.id].services.push(service)
				} else {
					// устройства нет в корзине
					cart[device.id] = {
						deviceName: device.name,
						services: [service],
					}
				}
			} else {
				cart[device.id].services[index] = service
			}
		} else {
			cart = {
				[device.id]: {
					deviceName: device.name,
					services: [service],
				},
			}
		}

		this.setCartLocalStorage(cart)
		this.renderCart()
		this.recalculationTotalPrice()
	}

	delService(idDevice, codeService) {
		let cart = this.getCartLocalStorage()

		if (!cart && !Object.keys(cart).length) {
			return null
		}

		let index = this.isServiceToCart(idDevice, codeService)

		if (index == -1) {
			return null
		}

		cart[idDevice].services.splice(index, 1)

		if (!cart[idDevice].services.length) {
			delete cart[idDevice]
		}

		this.setCartLocalStorage(cart)
		this.renderCart()
		this.recalculationTotalPrice()
	}

	isServiceToCart(deviceId, serviceCode) {
		function getCode(serviceCode) {
			return serviceCode.split('~')[0]
		}

		let cart = this.getCartLocalStorage()

		if (!cart || !Object.keys(cart).length || !cart[deviceId]) {
			return null
		}

		let services = cart[deviceId].services

		let index = Array.from(services).findIndex(
			(service) => getCode(service.code) == getCode(serviceCode)
		)

		return index
	}

	isDeviceToCart(idDevice) {
		let cart = this.getCartLocalStorage()

		if (!cart || !Object.keys(cart).length || !cart[idDevice]) {
			return false
		} else {
			return true
		}
	}

	renderCart() {
		let cart = this.getCartLocalStorage()

		let list = ''

		for (let device in cart) {
			list += this.getDevice(cart[device], device)
		}

		this.listDevices.innerHTML = list
	}

	getDevice(data, idDevice) {
		let services = []

		data.services.forEach((service) => {
			let codeService = service.code.split('~')[0]

			services.push(`
                <div class="service" data-code="${codeService}">
                    <div class="service-name">
                        <span>${service.type}</span>
                        <span>${service.name}</span>
                    </div>
                    <div class="service-price">${service.price} руб.</div>
                </div>
            `)
		})

		let device = `
        <div class="device">
            <div class="device-header">
                <div class="device-name">${data.deviceName}</div>
                <div class="device-btn-del" onclick="cart.delDevice('${idDevice}');services.resetServicesDevice('${idDevice}')"></div>
            </div>

            <div class="device-services">${services.join('')}</div>
        </div>
        `

		return device.trim()
	}

	delDevice(idDevice) {
		let cart = this.getCartLocalStorage()

		if (!cart) {
			return null
		}

		delete cart[idDevice]

		this.setCartLocalStorage(cart)
		this.renderCart()
		this.recalculationTotalPrice()
	}

	recalculationTotalPrice() {
		let cart = this.getCartLocalStorage()

		if (!cart) {
			return null
		}

		let total = 0

		for (let device in cart) {
			cart[device].services.forEach((service) => (total += +service.price))
		}

		this.totalPriceValue = total
		this.totalPriceNode.textContent = total
	}

	validationForm() {
		this.errorText = null

		let cart = this.getCartLocalStorage()

		if (!cart || !Object.keys(cart).length) {
			this.errorText = 'Добавьте услуги'
			return
		}

		this.phone.value = this.phone.value.trim()

		if (this.phone.value.length < 6) {
			this.errorText = 'Введите номер телефона'
			return
		}

		this.name.value = this.name.value.trim()

		if (this.name.value.length < 3) {
			this.errorText = 'Введите Имя'
			return
		}

		if (!this.date.value) {
			this.errorText = 'Выберите дату записи'
			return
		}
	}

	setUserDataLocalStorage() {}

	// отправка формы
	send() {
		this.validationForm()

		if (this.errorText) {
			this.errorSectionNode.textContent = this.errorText
			return null
		}

		this.errorSectionNode.textContent = ''

		let cart = this.getCartLocalStorage()

		this.saveUserData()

		fetch('/protection/', {
			method: 'POST',
			headers: {
				'UFO-AJAX-HANDLER': 'ev{onOrderProtection}',
				'UFO-REQUEST': 1,
			},
			body: JSON.stringify({
				data: {
					phone: this.phone.value,
					name: this.name.value,
					date: this.date.value,
				},
				cart: cart,
			}),
		})
			.then((response) => response.json())
			.then((response) => {
				this.cartNode.classList.add('success')
				this.date.value = ''

				localStorage.removeItem('cartProtection')
				this.renderCart()
				this.recalculationTotalPrice()
				services.initDeviceServices()

				setTimeout(() => {
					this.cartNode.classList.remove('success')
				}, 7000)
			})
			.catch((error) => {
				//console.log(error)
			})
	}

	saveUserData() {
		let data = {
			phone: this.phone.value,
			name: this.name.value,
		}

		localStorage.setItem('user-data-bb-protection', JSON.stringify(data))
	}

	initUser() {
		let data = JSON.parse(localStorage.getItem('user-data-bb-protection'))

		if (!data) {
			return null
		}

		if (data.phone) {
			this.phone.value = data.phone
		}

		if (data.name) {
			this.name.value = data.name
		}
	}
}
