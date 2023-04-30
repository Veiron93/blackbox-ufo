"use strict";

document.addEventListener("DOMContentLoaded", function () {
	// ФОТО ТОВАРА
	let params = null;

	if(window.innerWidth <= 768){
		params = {
			pagination: {
				el: ".swiper-pagination",
				type: "fraction",
			},
		}
	}else{
		let sliderThumbs = new Swiper(".catalog-product .product-gallery_thumbs", {
			direction: 'vertical',
			slidesPerView: 6,
			spaceBetween: 10,
		});

		params = {
			thumbs: {
				swiper: sliderThumbs,
			},
		}
	}

	new Swiper(".catalog-product .product-gallery_big", {
		loop: true,
		//effect: "fade",
		// navigation: {
		// 	nextEl: ".button-next",
		// 	prevEl: ".button-prev",
		// },
		...params
	});

	// ВЫБОР АРТИКУЛА
	function selectSku() {
		let productPage = document.querySelector(".catalog-product");

		if(!productPage){
			return false;
		}

		const skus = productPage.querySelectorAll("input[name='product-sku']");
		const btnsAddToCart = productPage.querySelectorAll(".btn-add-cart");
		const sectionsActualPriceProduct = productPage.querySelectorAll(".product-price .product-price_actual");

		const sectionsProductCode = productPage.querySelectorAll(".product-code");
		const sectionsProductAmount = productPage.querySelectorAll(".product-amount");

		function setDataIdSku(sku) {
			btnsAddToCart.forEach(btn => btn.setAttribute("data-id-sku", sku.value));
		}

		function price(sku) {
			let price = sku.getAttribute("data-price");
			sectionsActualPriceProduct.forEach(priceProduct => priceProduct.textContent = price);
		}

		function productsOtherInfo(sku) {
			let leftover = sku.getAttribute("data-leftover");
			let skuId = sku.value;

			sectionsProductCode.forEach(productCode => productCode.querySelector(".product-code_sku").textContent = skuId);
			sectionsProductAmount.forEach(productAmount => productAmount.querySelector("span").textContent = leftover);
		}
		
		if (skus) {
			skus.forEach((sku) =>
				sku.addEventListener("change", (ev) => {
					let sku = ev.target;

					setDataIdSku(sku);
					price(sku);
					productsOtherInfo(sku);
				})
			);
		}
	}

	selectSku();
});
