// калькулятор цены
class Services {
	servicesCatalogBodyNode = null
	servicesCatalogFooterNode = null

	serviceListWrapperNode = null
	categoriesServicesNode = []
	btnsDelService = null

	idActiveCategoryServices = 'phone'

	addUserInformerNode = null

	services = null
	serviceListsNode = null

	// user devices
	userDevicesWrapperNode = null

	userDevicesListWrapperNode = null
	userDevicesAddWrapperNode = null

	selectUserDevices = null
	inputAddUserDevice = null
	selectTypeUserDevices = null

	userDevices = []

	// кнопка добавить устройство
	btnDelDeviceClientDevice = null
	// кнопка удалить устройство
	btnAddDeviceClientDevice = null

	// выбранные устройства
	selectedPhone = null
	selectedTablet = null
	selectedWatch = null

	constructor() {
		this.initNodes()
		this.onActiveCategoryServices()

		this.initServicesCatalog()
		this.changeServiceCatalog()

		this.initUserDevices()
		this.changeUserDevice()
		this.stateUserDevices()

		this.initDeviceServices()
		this.stateListServiceCatalog()
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

		this.addUserInformerNode = this.servicesCatalogBodyNode.querySelector(
			'.add-device-informer'
		)
	}

	initServicesCatalog() {
		this.serviceListWrapperNode = this.servicesCatalogBodyNode.querySelector(
			'.list-services-wrapper'
		)

		this.serviceListsNode =
			this.servicesCatalogBodyNode.querySelectorAll('.list-services')

		this.services = this.servicesCatalogBodyNode.querySelectorAll(
			'.list-services select'
		)

		this.btnsDelService =
			this.serviceListWrapperNode.querySelectorAll('.service_btn-del')

		if (this.btnsDelService) {
			this.btnsDelService.forEach((btn) =>
				btn.addEventListener('click', () => {
					let code = btn.getAttribute('data-code-service')

					let selectService = Array.from(this.services).find(
						(service) => service.getAttribute('name') == code
					)

					if (!selectService) {
						return
					}

					selectService.selectedIndex = 0
					cart.delService(this.getIdActiveDevice(), code)
					this.stateBtnDelService(false, code)
				})
			)
		}
	}

	addService(code) {
		// device
		let device = {
			name: this.selectUserDevices.options[this.selectUserDevices.selectedIndex]
				.text,
			id: this.selectUserDevices.value,
		}

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
		this.stateBtnDelService(true, code)
	}

	resetServicesDevice(idDevice) {
		if (idDevice == this.getIdActiveDevice()) {
			this.services.forEach((service) => {
				this.stateBtnDelService(false, service.getAttribute('name'))
				service.selectedIndex = 0
			})
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

					this.stateBtnDelService(false, service.getAttribute('name'))
				}
			})
		})
	}

	stateBtnDelService(state, code) {
		// service
		let serviceSelect = Array.from(this.services).find(
			(service) => service.getAttribute('name') == code
		)

		if (state) {
			serviceSelect.classList.add('active')
		} else {
			serviceSelect.classList.remove('active')
		}
	}

	stateListServiceCatalog() {
		let state = false

		let localStorageUserDevices = JSON.parse(
			localStorage.getItem('userDevicesProtected')
		)

		if (localStorageUserDevices.length > 0) {
			let devices = localStorageUserDevices.filter(
				(device) => device.type == this.idActiveCategoryServices
			)

			if (devices.length > 0) {
				state = true
			}
		}

		if (state) {
			this.serviceListWrapperNode.classList.remove('hidden')
			this.addUserInformerNode.classList.add('hidden')
		} else {
			this.serviceListWrapperNode.classList.add('hidden')
			this.addUserInformerNode.classList.remove('hidden')
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

		this.selectTypeUserDevices = this.userDevicesAddWrapperNode.querySelector(
			'select[name="type-device"]'
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

	stateUserDevices() {
		let devices = this.selectUserDevices.querySelectorAll('option')

		if (!devices.length) {
			return null
		}

		devices.forEach((device, index) => {
			device.removeAttribute('disabled')
			device.removeAttribute('selected')

			if (device.getAttribute('data-type') != this.idActiveCategoryServices) {
				device.setAttribute('disabled', true)
			}
		})

		let activeDevices = Array.from(devices).filter(
			(device) => !device.getAttribute('disabled')
		)

		if (activeDevices.length) {
			activeDevices[0].setAttribute('selected', true)
		}
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
				option.setAttribute('data-type', device.type)

				devices.push(option)

				if (index == this.userDevices.length - 1) {
					let sorteredDevices = this.sortingUserDevices(devices)

					// let optionSelectDevice = document.createElement('option')
					// optionSelectDevice.disabled = false
					// optionSelectDevice.textContent = 'Добавьте устройство'

					this.selectUserDevices.innerHTML = ''
					this.selectUserDevices.append(...sorteredDevices)
					this.userDevicesListWrapperNode.classList.remove('hidden')
				}
			})
		} else {
			this.userDevicesListWrapperNode.classList.add('hidden')
			this.selectUserDevices.innerHTML = ''
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
			type: this.selectTypeUserDevices.value,
		}

		this.userDevices.push(newDevice)

		localStorage.setItem(
			'userDevicesProtected',
			JSON.stringify(this.userDevices)
		)

		this.inputAddUserDevice.value = ''
		this.renderListUserDevices()
		this.stateUserDevices()
		this.stateListServiceCatalog()
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
		this.stateListServiceCatalog()
		cart.delDevice(id)
	}

	changeUserDevice() {
		this.selectUserDevices.addEventListener('change', () => {
			this.initDeviceServices()
			this.setSelectedDevice()
		})
	}

	initDeviceServices() {
		let cartLocalStorage = cart.getCartLocalStorage()
		let deviceCode = this.selectUserDevices.value

		this.services.forEach((service) => {
			service.selectedIndex = 0
			service.classList.remove('active')
		})

		if (!cartLocalStorage || !deviceCode) {
			return null
		}

		let deviceAddedCart = cartLocalStorage[deviceCode]

		if (!deviceAddedCart) {
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
			selectService.classList.add('active')
		})
	}

	getIdActiveDevice() {
		return this.selectUserDevices.value
	}

	sortingUserDevices(devices) {
		function groupName(type) {
			let option = document.createElement('option')
			option.disabled = true

			let groupName = null

			if (type == 'phone') {
				groupName = 'Смартфоны'
			}

			if (type == 'tablet') {
				groupName = 'Планшеты'
			}

			if (type == 'watch') {
				groupName = 'Смарт-часы'
			}

			option.textContent = '----- ' + groupName.toUpperCase() + ' -----'

			return option
		}

		let phones = []
		let tablets = []
		let watches = []

		phones.push(groupName('phone'))
		tablets.push(groupName('tablet'))
		watches.push(groupName('watch'))

		devices.forEach((device) => {
			if (device.getAttribute('data-type') == 'phone') {
				phones.push(device)
			}

			if (device.getAttribute('data-type') == 'tablet') {
				tablets.push(device)
			}

			if (device.getAttribute('data-type') == 'watch') {
				watches.push(device)
			}
		})

		return [...phones, ...tablets, ...watches]
	}

	setSelectedDevice() {
		let id = this.selectUserDevices.value

		if (this.idActiveCategoryServices == 'phone') {
			this.selectedPhone = id
		}

		if (this.idActiveCategoryServices == 'tablet') {
			this.selectedTablet = id
		}

		if (this.idActiveCategoryServices == 'watch') {
			this.selectedWatch = id
		}
	}

	initSelectedDevice() {
		let devices = this.selectUserDevices.querySelectorAll('option')

		let idCurrentDevice = null

		if (this.idActiveCategoryServices == 'phone') {
			idCurrentDevice = this.selectedPhone
		}

		if (this.idActiveCategoryServices == 'tablet') {
			idCurrentDevice = this.selectedTablet
		}

		if (this.idActiveCategoryServices == 'watch') {
			idCurrentDevice = this.selectedWatch
		}

		let indexCurrentDevice = Array.from(devices).findIndex(
			(device) =>
				device.getAttribute('data-type') == this.idActiveCategoryServices &&
				device.value == idCurrentDevice
		)

		if (indexCurrentDevice == -1) {
			return null
		}

		devices.forEach((divice) => divice.removeAttribute('selected'))
		devices[indexCurrentDevice].selected = true
	}

	initTypeDevices() {
		let types = this.selectTypeUserDevices.querySelectorAll('option')

		if (!types) return null

		types.forEach((type) => {
			type.selected = type.value == this.idActiveCategoryServices ? true : false
		})
	}

	onActiveCategoryServices() {
		this.categoriesServicesNode.forEach((category, index) =>
			category.addEventListener('click', () => {
				let id = category.getAttribute('data-id')

				this.idActiveCategoryServices = id

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

				this.stateUserDevices()
				this.stateListServiceCatalog()

				this.initSelectedDevice()
				this.initDeviceServices()
				this.initTypeDevices()
			})
		)
	}
}
