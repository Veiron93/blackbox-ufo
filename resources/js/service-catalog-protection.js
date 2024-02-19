// калькулятор цены
class Services {
	servicesCatalogWrapperNode = null

	categoriesServicesNode = []
	idActiveCategoryServices = null
	indexActiveCategoryServices = 0

	services = null

	serviceListsNode = null

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
		//this.initServiceCatalog()
		this.onActiveCategoryServices()
		this.changeServices()
		this.initUserDevices()
	}

	initNodes() {
		this.servicesCatalogWrapperNode = document.querySelector(
			'.service-catalog_body'
		)

		this.categoriesServicesNode =
			this.servicesCatalogWrapperNode.querySelectorAll('.categories .item')

		// services
		this.services = this.servicesCatalogWrapperNode.querySelectorAll(
			'.list-services select'
		)

		this.serviceListsNode =
			this.servicesCatalogWrapperNode.querySelectorAll('.list-services')

		// user devices

		// total price
		//this.totalPriceNode = document.querySelector('.total-price span')
	}

	initEvents() {}

	// initServiceCatalog() {

	// }

	initUserDevices() {
		this.userDevicesWrapperNode =
			this.servicesCatalogWrapperNode.querySelector('.user-devices_body')

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

		this.initListUserDevices()
	}

	// инициализация списка
	initListUserDevices() {
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
		} else {
			this.userDevicesListWrapperNode.classList.add('hidden')
			this.userDevicesListNode.innerHTML = ''
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
		this.initListUserDevices()
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

		this.initListUserDevices()
	}

	changeServices() {
		this.services.forEach((service) => {
			//service.addEventListener('change', () => this.recalculationTotalPrice())
		})
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

	getCategoriesServices() {}

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
