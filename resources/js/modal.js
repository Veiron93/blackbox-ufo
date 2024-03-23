document.addEventListener('DOMContentLoaded', function () {
	const btnsModal = document.querySelectorAll('[data-btn-modal]')

	if (!btnsModal) {
		return false
	}

	const modals = document.querySelectorAll('.modal')

	if (modals) {
		modals.forEach((modal) => {
			modal.addEventListener('click', (e) => {
				if (
					e.target.classList.contains('modal') ||
					e.target.classList.contains('btn-close')
				) {
					stateModal(modal.id, false)
				}
			})
		})
	}

	btnsModal.forEach((btn) =>
		btn.addEventListener('click', () =>
			stateModal(btn.getAttribute('data-btn-modal'), true)
		)
	)

	function stateModal(idModal, state) {
		let modal = document.getElementById(idModal)

		if (state) {
			modal.classList.add('active')
			document.body.classList.add('scroll-y-disabled')
		} else {
			modal.classList.remove('active')
			document.body.classList.remove('scroll-y-disabled')
		}
	}
})
