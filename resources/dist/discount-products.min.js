'use strict'

document.addEventListener('DOMContentLoaded', () => {
	dayjs.locale('ru')
	dayjs.extend(window.dayjs_plugin_duration)

	function getDeclension(number, one, two, five) {
		if (number >= 11 && number <= 19) {
			return five
		}

		const remainder = number % 10

		switch (remainder) {
			case 1:
				return one
			case 2:
			case 3:
			case 4:
				return two
			default:
				return five
		}
	}

	function recalculationDate(endDate) {
		const now = dayjs()
		const end = dayjs(endDate)
		const diff = dayjs.duration(end.diff(now))

		const days = diff.days()
		const hours = diff.hours()
		const minutes = diff.minutes()

		// Формирование строк с учетом окончания
		const daysString = `${days} ${getDeclension(days, 'день', 'дня', 'дней')}`

		const hoursString = `${hours} ${getDeclension(
			hours,
			'час',
			'часа',
			'часов'
		)}`

		const minutesString = `${minutes} ${getDeclension(
			minutes,
			'минута',
			'минуты',
			'минут'
		)}`

		const discountTimeElement = document.querySelector(
			'.discount-products_time'
		)

		let result = ''

		if (days) {
			result += `${daysString} `
		}

		if (hours) {
			result += `${hoursString} `
		}

		if (minutes) {
			result += `${minutesString} `
		}

		discountTimeElement.textContent = result
	}

	function timeRemainingDiscount() {
		const endDate = dayjs().endOf('week')
		recalculationDate(endDate)

		setInterval(() => {
			recalculationDate(endDate)
		}, 1000 * 60)
	}

	timeRemainingDiscount()
})
