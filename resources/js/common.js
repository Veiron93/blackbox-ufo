'use strict'

document.addEventListener("DOMContentLoaded", function() { 
	// fancybox

	// $('[data-fancybox]').fancybox({
	// 	protect: true,
	// 	buttons : [
	// 		'zoom',
	// 		'thumbs',
	// 		'close'
	// 	]
	// });

///////////// SLIDERS /////////////

	// главный слайдер
	new Swiper(".main-slider", {
        loop: true,
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
        navigation: {
          nextEl: ".main-slider .button-next",
          prevEl: ".main-slider .button-prev",
        }
    });

	// главный слайдер - мобильная версия
	new Swiper(".main-slider-mobile", {
        loop: true,
		autoplay: {
			delay: 5000,
			disableOnInteraction: false,
		},
		pagination: {
			el: document.querySelector('.main-slider-mobile-pagination'),
		},
    });

	// новые продукты слайер
	new Swiper(".new-products .list-products-slider", {
		
        navigation: {
          nextEl: ".new-products .list-products-slider .button-next",
          prevEl: ".new-products .list-products-slider .button-prev",
        },
		breakpoints: {
			0:{
				slidesPerView: 2,
				spaceBetween: 10,
				centeredSlides: true,
				loop: true,
			},
			768: {
			  slidesPerView: 4,
			  spaceBetween: 20,
			},
			991: {
			  slidesPerView: 5,
			  spaceBetween: 20,
			},
			1200: {
				slidesPerView: 6,
				spaceBetween: 20,
			}
		},
    });

	// слайдер в просмотре продукта

	let swiper = new Swiper(".catalog-product-slider .mySwiper", {
		breakpoints: {
			0:{
				slidesPerView: 3,
				spaceBetween: 5,
			},
			768: {
			  slidesPerView: 3,
			  spaceBetween: 20,
			},
			991: {
			  slidesPerView: 4,
			  spaceBetween: 10,
			}
		},
	});
	
    new Swiper(".catalog-product-slider .mySwiper2", {
        loop: true,
        navigation: {
          nextEl: ".button-next",
          prevEl: ".button-prev",
        },
        thumbs: {
          swiper: swiper,
        },
	});


	// фирменная упаковка в корзине
	// $('.branded-packaging_slider').not('.slick-initialized').slick({
	// 	slidesToShow: 5,
	// 	slidesToScroll: 5,
	// 	arrows: true,
	// });

///////////// КАТАЛОГ МЕНЮ (МОБИЛЬНАЯ ВЕРСИЯ) /////////////
function mobileCatalogMenu(){
	
	function stateCatalogMenu(){
		document.body.classList.toggle('no-scroll');
		catalogMenu.classList.toggle('active');
		closeSubCategories()
	}

	function stateCategoriesLevel_2(){

		activeCategoriesLevel_2 = this.nextElementSibling;

		if (activeCategoriesLevel_2){
			activeCategoriesLevel_2.classList.add('active');

			let categoriesLevel_2 = activeCategoriesLevel_2.querySelectorAll('.category-level-2');

			categoriesLevel_2.forEach(categoryLevel_2 => {
				categoryLevel_2.addEventListener('click', stateCategoriesLevel_3)
			})
		} 
	}

	function stateCategoriesLevel_3(){
		activeCategoriesLevel_3.push(this);

		if (activeCategoriesLevel_3){
			this.classList.toggle('active');
		}
	}

	function closeSubCategories(){
		if(activeCategoriesLevel_2){
			activeCategoriesLevel_2.classList.remove('active');
			activeCategoriesLevel_2 = null;
		}

		if(activeCategoriesLevel_3){

			activeCategoriesLevel_3.forEach(categoryLevel_3 => {
				categoryLevel_3.classList.remove('active');
			})
			
			activeCategoriesLevel_3.length = 0;
		}
	}

	let btnsCatalogMenu = document.querySelectorAll('.btn-catalog-menu-mobile');
	let catalogMenu = document.querySelector('.mobile-menu-catalog');
	let mainCategories = catalogMenu.querySelectorAll('.main-category_wrapper');
	let activeCategoriesLevel_2 = null;
	let activeCategoriesLevel_3 = [];
	let linksBackMainCategories = catalogMenu.querySelectorAll('.link-back');

	btnsCatalogMenu.forEach(btn => {
		btn.addEventListener('click', stateCatalogMenu)
	})

	mainCategories.forEach(mainCategory => {
		mainCategory.addEventListener('click', stateCategoriesLevel_2)
	})

	linksBackMainCategories.forEach(link => {
		link.addEventListener('click', closeSubCategories)
	})
}

mobileCatalogMenu();


///////////// НАВИГАЦИЯ (МОБИЛЬНАЯ ВЕРСИЯ) /////////////
function mobileNavigation(){
	
	function statusNav(){
		document.body.classList.toggle('no-scroll');
		nav.classList.toggle('active');
		btnMobileNav.classList.toggle('active');
	}

	let nav = document.querySelector('header').querySelector('nav');
	let btnMobileNav = document.querySelector('.btn-mobile-nav');
	btnMobileNav.addEventListener('click', statusNav)
}

mobileNavigation();


///////////// КОРЗИНА /////////////

	function quantityCart(){

    	function quantityMinus(quantityNum) {
			if (quantityNum.value > 1) {
				quantityNum.value = +quantityNum.value - 1;
			}
		}
			 
		function quantityPlus(quantityNum) {
			quantityNum.value = +quantityNum.value + 1;
		}

		let cart = document.querySelector('.cart-wrapper');

		if(cart){
			let quantityBtns = cart.querySelectorAll('.btn-quantity-cart'),
				quantityNums = cart.querySelectorAll('.quantity-num');

			quantityBtns.forEach(function(e){

				e.addEventListener('click', function(){
					let quantityNum = e.parentElement.querySelector('.quantity-num');

					if(e.classList.contains('btn-quantity-minus') || quantityNum.value < quantityNum.getAttribute("max")){
						if(e.classList.contains('btn-quantity-minus')){
							quantityMinus(quantityNum);
						}else{
							quantityPlus(quantityNum);
						}
	
						$('#cart-form').sendForm('onUpdateQuantity', {});
					}
				})
			})

			quantityNums.forEach(function(e){
				e.addEventListener('change', function(){
					e.value = e.value >= 1 ? e.value : 1;
					$('#cart-form').sendForm('onUpdateQuantity', {});
				})
			})
		}
    }

	quantityCart();


	function cartDelivery(){

		let cart = document.querySelector('.cart-wrapper');

		if(cart){

			// стоимость доставки
			function setTotalPriceDelivery(deliveryItem){

				let deviveryPriceBlock = cart.querySelector('.devivery-price').querySelector('span'),
					deliveryPrice = deliveryItem.getAttribute('data-price'),
					deliveryCode = deliveryItem.getAttribute('data-code');

				if(deliveryPrice == 0){
					deviveryPriceBlock.textContent = deliveryCode == 'pickup' ? 'Самовывоз':'Бесплатно';
				}else{
					deviveryPriceBlock.textContent = deliveryPrice;
				}
			}

			// общая сумма заказа
			function setTotalPrice(deliveryItem){
				let totalPriceBlock = cart.querySelector('.total-price').querySelector('span'),
					goodsPrice = cart.querySelector('.goods-price').querySelector('span');

				totalPriceBlock.textContent = Number(deliveryItem.getAttribute('data-price')) + Number(goodsPrice.getAttribute('data-summ'));
			}

			function statusSectionAddress(deliveryItem){
				let address = cart.querySelector('.section-address');

				if(deliveryItem.getAttribute('data-code') != 'pickup' && !address.classList.contains('active')){
					address.classList.add('active')
				}else if(deliveryItem.getAttribute('data-code') == 'pickup'){
					address.classList.remove('active')
				}
			}

			let deliveryItems = cart.querySelectorAll('input[name="delivery"]');
			
			deliveryItems.forEach(function(deliveryItem){

				if(deliveryItem.hasAttribute('checked')){
					setTotalPriceDelivery(deliveryItem);
					setTotalPrice(deliveryItem);
					statusSectionAddress(deliveryItem);
				}
		
				// изменение способа доставки
				deliveryItem.addEventListener('change', function(){
					setTotalPriceDelivery(deliveryItem);
					setTotalPrice(deliveryItem);
					statusSectionAddress(deliveryItem);
				})
			})
		}
	}

	cartDelivery()


///////////// ВИД СПИСКА ТОВАРОВ /////////////

	function viewProductList(){
		let viewProductListBlock = document.querySelector('.catalog-products-sorting .view-mode');

		if(viewProductListBlock){

			if(!getCookie('viewModeProductsList')){
				setCookie('viewModeProductsList', 'grid');
			}

			let switches = viewProductListBlock.querySelectorAll('.item'),
				typeProductList = getCookie('viewModeProductsList');

			switches.forEach(function(e){

				e.addEventListener('click', function(){
					
					if(this.getAttribute('data-type') == 'grid' || this.getAttribute('data-type') == 'list'){
						typeProductList = this.getAttribute('data-type');
					}else{
						typeProductList = 'grid';
					}

					document.querySelector('.list-products').setAttribute('data-view-list', typeProductList);
					setCookie('viewModeProductsList', typeProductList);

					switches.forEach(function(item){
						item.classList.remove('active');
					})

					e.classList.add('active')
				})
			})
		}
	}

	viewProductList();


	// КАТАЛОГ - ФИЛЬТРЫ
	function viewFilterList(){
		let filterList = document.querySelector('.catalog-filters-wrapper');

		if(filterList){

			let filters = filterList.querySelectorAll('.filter'),
				btnSubmit = filterList.querySelector('.btn-submit'),
				btnsResetFilters = filterList.querySelectorAll('.btn-reset'), 
				selectFilters = getSelectedFilters();

			filters.forEach(function(filter, key){

				// свернуть фильтр
				filter.querySelector('.filter-head').addEventListener('click', function(){
					filter.classList.toggle('active');
				})

				let filterId = filter.getAttribute("data-filter-id"),
					filterAttributes = filter.querySelectorAll('.filter-list .list-attribites > *'),
					filterBtnClear = filter.querySelector('.filter-clear')

				filterAttributes.forEach(function(attribute){

					attribute.querySelector('input').addEventListener("click", function(){

						let filterAttributeValue = attribute.querySelector('input').value

						

						if(this.checked){

							selectFilters.forEach((f)=>{

								if(f.idFilter == filterId){
									f.selectAttributes.push(filterAttributeValue)
								}

								// else{
								// 	selectFilters.push({
								// 		'idFilter': filterId,
								// 		'selectAttributes': [filterAttributeValue]
								// 	})
								// }
							})






							if(selectFilters.length > 0){

								selectFilters.forEach((f)=>{

									if(f.idFilter == filterId){
										f.selectAttributes.push(filterAttributeValue)
									}else{
										selectFilters.push({
											'idFilter': filterId,
											'selectAttributes': [filterAttributeValue]
										})
									}
								})


							}

							else{
								selectFilters.push({
									'idFilter': filterId,
									'selectAttributes': [filterAttributeValue]
								})
							}

						}else{
							selectFilters.forEach((f, keyFilter)=>{
								f.selectAttributes.forEach(function(a, keyAttribute){
									if(filterAttributeValue == a){
										f.selectAttributes.splice(keyAttribute, 1)

										if(f.selectAttributes.length == 0){
											selectFilters.splice(keyFilter, 1)
										}
									}
								})
							})
						}

						console.log(selectFilters)

						//console.log(selectFilters);

						// статус кнопки сбросить
						//selectFilter.selectAttributes.length > 0 ? filterBtnClear.classList.add("active") : filterBtnClear.classList.remove("active");
					})
				})		

				// сбросить фильтр
				filterBtnClear.addEventListener('click', function(){
					filterAttributes.forEach(e=> e.querySelector('input').checked = false)
					selectFilter.selectAttributes.length=0;
					filterBtnClear.classList.remove("active")
				})
			})

			// получает из url какие фильтры активны
			function getSelectedFilters(){

				// ищет нужный get параметр
				function get(name){
					if(name=(new RegExp('[?&]'+encodeURIComponent(name)+'=([^&]*)')).exec(location.search))
					return decodeURIComponent(name[1]);
				}

				let getActiveFilters = get('f'),
					selectFilters = [];

				if(getActiveFilters){
					activeFiltersArr = getActiveFilters.slice(1,-1).split(';');

					activeFiltersArr.forEach(function(activeFilter){
						let filterId = activeFilter.split('=')[0],
							filterSelectAttributes = activeFilter.split('=')[1].split(',')

						let selectFilter = {
							'idFilter': filterId,
							'selectAttributes': filterSelectAttributes
						};

						selectFilters.push(selectFilter)
					})
				}

				return selectFilters;
			}


			// применить фильтры
			function applyFilters(){
				let urlFilters = '';

				selectFilters.forEach(filter=>{

					if(filter.selectAttributes.length > 0){
						let attributes = filter.selectAttributes.join(',')
						urlFilters += filter.idFilter + "=" + attributes + ";"
					}
				})

				urlFilters = `f=[${urlFilters.slice(0,-1)}]`;

				//console.log(urlFilters)
			}



			// сбросить все фильтры
			function clearFiltres(filters){
				filters.forEach(function(filter){
					let filterAttributes = filter.querySelectorAll('.filter-list .list-attribites > *');

					filterAttributes.forEach(function(attribute){
						attribute.querySelector('input').checked = false;
					})
				})

				selectFilters.forEach(function(filter){
					filter.selectAttributes.length=0;
				})
			}

			// получение активных фильтров из url
			//getSelectedFilters()

			// кнопка применить фильтры
			btnSubmit.addEventListener('click', ()=> applyFilters())

			// кнопка очистить все фильтры
			btnsResetFilters.forEach((btn)=>{
				btn.addEventListener('click', ()=> clearFiltres(filters))
			})


		}
	}

	//viewFilterList();





	function search(){
		let headerSearch = document.querySelector('.header-search');

		if(headerSearch){

		}
	}



///////////// КНОПКА ДОБАВИТЬ В КОРЗИНУ /////////////
	
	function productAddToCart(){
		let btns = document.querySelectorAll('.btn-buy');

		if(btns){
			btns.forEach(btn=>{
				btn.querySelector('a').addEventListener('click', function(){

					let id = this.parentElement.getAttribute('id')
					let products = document.querySelectorAll('#' + id)

					products.forEach(product => product.setAttribute('data-added', true))
				})
			})
		}
	}

	productAddToCart();


///////////// КНОПКА ВВЕРХ /////////////
	{
		function trackScroll(){
			let scrolled = window.pageYOffset,
    			coords = document.documentElement.clientHeight;

			if(scrolled > coords) {
				btns.forEach(btn => btn.classList.add('active'))
			}else{
				btns.forEach(btn => btn.classList.remove('active'))
			}
		}

		function backToTop() {
			if (window.pageYOffset > 0) window.scrollBy(0, - window.pageYOffset);
		}

		window.addEventListener('scroll', trackScroll);

		let btns = document.querySelectorAll('.btn-up-page');
		btns.forEach(btn=> btn.addEventListener('click', backToTop))
	}
});