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
	//btnsEvent = null

	// user devices
	userDevicesWrapperNode = null

	userDevicesListWrapperNode = null
	userDevicesAddWrapperNode = null

	userDevicesListNode = null
	inputAddUserDevice = null

	userDevices = []
	btnDelDeviceClientDevice = null
	btnAddDeviceClientDevice = null

	// totalPriceNode = null
	// totalPriceValue = 0

	constructor() {
		this.initNodes()
		this.onActiveCategoryServices()

		this.initServiceCatalog()
		this.changeServiceCatalog()

		this.initUserDevices()
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

		// total price
		//this.totalPriceNode = document.querySelector('.total-price span')
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

		// this.btnsEvent =
		// 	this.servicesCatalogBodyNode.querySelectorAll('.btn-event-service')

		// this.btnsEvent.forEach((btn) =>
		// 	btn.addEventListener('click', () => {
		// 		this.validationServiceCatalog(btn.getAttribute('data-code'))
		// 		this.onEventBtnEventServiceCatalog(btn)
		// 	})
		// )
	}

	// состояние кнопки
	// statusBtnEventServiceCatalog(status, text, btn = null, code = null) {
	// 	let button = btn

	// 	if (!button && code) {
	// 		button = this.getBtnEventsServiceCatalog(code)
	// 	}

	// 	if (!button && !code) {
	// 		return
	// 	}

	// 	button.setAttribute('data-status', status)
	// 	button.textContent = text
	// }

	// onEventBtnEventServiceCatalog(btn) {
	// 	let status = btn.getAttribute('data-status')
	// 	let code = btn.getAttribute('data-code')

	// 	let newStatus = null
	// 	let newText = null

	// 	if (status == 'add') {
	// 		this.addServiceCart(code)
	// 		newStatus = 'del'
	// 		newText = 'Удалить'
	// 	} else if (status == 'del') {
	// 		this.delServiceCart(code)
	// 		newStatus = 'add'
	// 		newText = 'Добавить'
	// 	} else if (status == 'add') {
	// 		newStatus = 'add'
	// 		newText = 'Изменить'
	// 	}

	// 	this.statusBtnEventServiceCatalog(newStatus, newText, btn)
	// }

	validationServiceCatalog() {}

	// getBtnEventsServiceCatalog(code) {
	// 	return Array.from(this.btnsEvent).find(
	// 		(btn) => btn.getAttribute('data-code') == code
	// 	)
	// }

	// stateBntEventServiceCatalog(btnCode, state) {
	// 	let btn = this.getBtnEventsServiceCatalog(btnCode)

	// 	if (!btn) {
	// 		return
	// 	}

	// 	if (state) {
	// 		btn.classList.add('active')
	// 	} else {
	// 		btn.classList.remove('active')
	// 	}
	// }

	// добавить услугу в корзину
	addServiceCart(code) {
		console.log(code)
	}

	// удалить услугу из корзины
	delServiceCart(code) {
		//cart.add(code)
		//console.log(code)
	}

	addService(code) {
		// device
		let device = {
			name: this.userDevicesListNode.options[
				this.userDevicesListNode.selectedIndex
			].text,
			id: this.userDevicesListNode.value,
		}

		// service
		let serviceSelect = Array.from(this.services).find(
			(service) => service.getAttribute('name') == code
		)

		let serviceParent = serviceSelect.parentElement

		let serviceType = serviceParent.querySelector('.service_name').textContent
		serviceType = serviceType.trim()

		let serviceName = serviceSelect.options[serviceSelect.selectedIndex].text
		serviceName = serviceName.split('≈')[0].trim()

		let servicePrice = serviceSelect.value

		let service = {
			type: serviceType,
			name: serviceName,
			price: servicePrice,
		}

		cart.add(device, service)

		//console.log(service)
		//console.log(serviceNode.value)
	}

	changeServiceCatalog() {
		this.services.forEach((service) => {
			service.addEventListener('change', () => {
				let state = service.value ? true : false

				if (state) {
					this.addService(service.getAttribute('name'))
					//this.addServiceCart(service.getAttribute('name'))
				} else {
					this.delServiceCart(service.getAttribute('name'))
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

		this.userDevicesListNode = this.userDevicesListWrapperNode.querySelector(
			'select[name="user-devices"]'
		)

		this.inputAddUserDevice = this.userDevicesAddWrapperNode.querySelector(
			'input[name="add-device"]'
		)

		// btns
		this.btnDelDeviceClientDevice =
			this.userDevicesWrapperNode.querySelector('.btn-del-device')

		this.btnDelDeviceClientDevice.addEventListener('click', () => {
			this.delUserDevice(this.userDevicesListNode.value)
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
					this.userDevicesListNode.innerHTML = ''
					this.userDevicesListNode.append(...devices)
					this.userDevicesListWrapperNode.classList.remove('hidden')
				}
			})

			this.stateListServiceCatalog(true)
		} else {
			this.userDevicesListWrapperNode.classList.add('hidden')
			this.userDevicesListNode.innerHTML = ''
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
	}

	// changeActiveUserDevice(){
	// 	this.
	// }

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
