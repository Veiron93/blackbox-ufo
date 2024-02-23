// калькулятор цены
class Services {
	servicesCatalogBodyNode = null
	servicesCatalogFooterNode = null

	serviceListWrapperNode = null
	categoriesServicesNode = []
	idActiveCategoryServices = null
	indexActiveCategoryServices = 0

	newUserWrapperNode = null

	services = null
	serviceListsNode = null

	// user devices
	userDevicesWrapperNode = null

	userDevicesListWrapperNode = null
	userDevicesAddWrapperNode = null

	selectUserDevices = null
	inputAddUserDevice = null

	userDevices = []
	btnDelDeviceClientDevice = null
	btnAddDeviceClientDevice = null

	constructor() {
		this.initNodes()
		this.onActiveCategoryServices()

		this.initServiceCatalog()
		this.changeServiceCatalog()

		this.initUserDevices()
		this.changeUserDevice()

		this.initDeviceServices()
	}

	initNodes() {
		this.servicesCatalogBodyNode = document.querySelector(
			'.service-catalog_body'
		)

		this.servicesCatalogFooterNode = document.querySelector(
			'.service-catalog_footer'
		)

		this.categoriesServicesNode =
			this.servicesCatalogBodyNode.querySelectorAll('.categories .item')

		this.newUserWrapperNode =
			this.servicesCatalogBodyNode.querySelector('.new-user')
	}

	initServiceCatalog() {
		this.serviceListWrapperNode = this.servicesCatalogBodyNode.querySelector(
			'.list-services-wrapper'
		)

		this.serviceListsNode =
			this.servicesCatalogBodyNode.querySelectorAll('.list-services')

		this.services = this.servicesCatalogBodyNode.querySelectorAll(
			'.list-services select'
		)
	}

	addService(code) {
		// device
		let device = {
			name: this.selectUserDevices.options[this.selectUserDevices.selectedIndex]
				.text,
			id: this.selectUserDevices.value,
		}

		// service
		let serviceSelect = Array.from(this.services).find(
			(service) => service.getAttribute('name') == code
		)

		let serviceParent = serviceSelect.parentElement

		let serviceType = serviceParent.querySelector('.service_name').textContent
		serviceType = serviceType.trim()

		let serviceValue = serviceSelect.options[serviceSelect.selectedIndex].text

		let serviceName = serviceValue.split('≈')[0].trim()
		let serviceCode = serviceSelect.value
		let servicePrice = serviceValue.split('≈')[1].trim().split(' ')[0]

		let service = {
			type: serviceType,
			name: serviceName,
			code: serviceCode,
			price: servicePrice,
		}

		cart.add(device, service)
	}

	resetServicesDevice(idDevice) {
		if (idDevice == this.getIdActiveDevice()) {
			this.services.forEach((service) => (service.selectedIndex = 0))
		}
	}

	changeServiceCatalog() {
		this.services.forEach((service) => {
			service.addEventListener('change', () => {
				let state = service.value ? true : false

				if (state) {
					this.addService(service.getAttribute('name'))
				} else {
					// del
					cart.delService(
						this.getIdActiveDevice(),
						service.getAttribute('name')
					)
				}
			})
		})
	}

	stateListServiceCatalog(state) {
		if (state) {
			this.serviceListWrapperNode.classList.remove('hidden')
			this.newUserWrapperNode.classList.add('hidden')
		} else {
			this.serviceListWrapperNode.classList.add('hidden')
			this.newUserWrapperNode.classList.remove('hidden')
		}
	}

	initUserDevices() {
		this.userDevicesWrapperNode =
			this.servicesCatalogFooterNode.querySelector('.user-devices_body')

		this.userDevicesListWrapperNode =
			this.userDevicesWrapperNode.querySelector('.user-devices_list')

		this.userDevicesAddWrapperNode =
			this.userDevicesWrapperNode.querySelector('.user-devices_add')

		this.selectUserDevices = this.userDevicesListWrapperNode.querySelector(
			'select[name="user-devices"]'
		)

		this.inputAddUserDevice = this.userDevicesAddWrapperNode.querySelector(
			'input[name="add-device"]'
		)

		// btns
		this.btnDelDeviceClientDevice =
			this.userDevicesWrapperNode.querySelector('.btn-del-device')

		this.btnDelDeviceClientDevice.addEventListener('click', () => {
			this.delUserDevice(this.selectUserDevices.value)
		})

		this.btnAddDeviceClientDevice =
			this.userDevicesWrapperNode.querySelector('.btn-add-device')

		this.btnAddDeviceClientDevice.addEventListener('click', () =>
			this.addUserDevice(this.inputAddUserDevice.value)
		)

		this.renderListUserDevices()
	}

	// инициализация списка
	renderListUserDevices() {
		let localStorageUserDevices = JSON.parse(
			localStorage.getItem('userDevicesProtected')
		)

		if (localStorageUserDevices) {
			this.userDevices = localStorageUserDevices
		}

		if (this.userDevices.length) {
			let devices = []

			this.userDevices.forEach((device, index) => {
				let option = document.createElement('option')
				option.textContent = device.name
				option.value = device.id

				devices.push(option)

				if (index == this.userDevices.length - 1) {
					this.selectUserDevices.innerHTML = ''
					this.selectUserDevices.append(...devices)
					this.userDevicesListWrapperNode.classList.remove('hidden')
				}
			})

			this.stateListServiceCatalog(true)
		} else {
			this.userDevicesListWrapperNode.classList.add('hidden')
			this.selectUserDevices.innerHTML = ''
			this.stateListServiceCatalog(false)
		}
	}

	addUserDevice(value) {
		let deviceName = value.trim()

		if (!deviceName) {
			return
		}

		let newDevice = {
			id: Math.random().toString(16).slice(2),
			name: value,
		}

		this.userDevices.push(newDevice)

		localStorage.setItem(
			'userDevicesProtected',
			JSON.stringify(this.userDevices)
		)

		this.inputAddUserDevice.value = ''
		this.renderListUserDevices()
	}

	delUserDevice(id) {
		let index = this.userDevices.findIndex((device) => device.id == id)

		if (index != -1) {
			this.userDevices.splice(index, 1)
		}

		localStorage.setItem(
			'userDevicesProtected',
			JSON.stringify(this.userDevices)
		)

		this.renderListUserDevices()
		this.initDeviceServices()
		cart.delDevice(id)
	}

	changeUserDevice() {
		this.selectUserDevices.addEventListener('change', () => {
			this.initDeviceServices()
		})
	}

	initDeviceServices() {
		let cartLocalStorage = cart.getCartLocalStorage()
		let deviceCode = this.selectUserDevices.value

		this.services.forEach((service) => (service.selectedIndex = 0))

		if (!cartLocalStorage || !deviceCode) {
			return null
		}

		let deviceAddedCart = cartLocalStorage[deviceCode]

		if (!deviceAddedCart) {
			this.services.forEach((service) => (service.selectedIndex = 0))
			return null
		}

		deviceAddedCart.services.forEach((service) => {
			let selectService = Array.from(this.services).find(
				(select) => select.getAttribute('name') == service.code.split('~')[0]
			)

			if (!selectService) {
				return false
			}

			let options = selectService.querySelectorAll('option')

			let optionIndex = Array.from(options).findIndex(
				(option) => option.value == service.code
			)

			if (!optionIndex) {
				return null
			}

			selectService.selectedIndex = optionIndex
		})
	}

	getIdActiveDevice() {
		return this.selectUserDevices.value
	}

	onActiveCategoryServices() {
		this.categoriesServicesNode.forEach((category, index) =>
			category.addEventListener('click', () => {
				let id = category.getAttribute('data-id')

				// категория
				this.categoriesServicesNode.forEach((category) => {
					if (category.getAttribute('data-id') != id) {
						category.classList.remove('active')
					} else {
						category.classList.add('active')
					}
				})

				// вкладка
				this.serviceListsNode.forEach((list) => {
					if (list.getAttribute('data-id') != id) {
						list.classList.remove('active')
					} else {
						list.classList.add('active')
					}
				})
			})
		)
	}
}
