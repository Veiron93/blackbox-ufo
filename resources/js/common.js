document.addEventListener("DOMContentLoaded", function() { 

	// fancybox

	$('[data-fancybox]').fancybox({
		protect: true,
		buttons : [
			'zoom',
			'thumbs',
			'close'
		]
	});

///////////// SLIDERS /////////////

	// слайдер на главной
	new Swiper(".main-slider", {
        loop: true,
        navigation: {
          nextEl: ".main-slider .button-next",
          prevEl: ".main-slider .button-prev",
        }
    });

	// новые продукты слайер
	new Swiper(".new-products .list-products-slider", {
        spaceBetween: 20,
        slidesPerView: 6,
        navigation: {
          nextEl: ".new-products .list-products-slider .button-next",
          prevEl: ".new-products .list-products-slider .button-prev",
        }
    });

	// слайдер в просмотре продукта
	let swiper = new Swiper(".catalog-product-slider .mySwiper", {
        spaceBetween: 10,
        slidesPerView: 6,
        freeMode: true,
        watchSlidesProgress: true,
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

		let cart = document.querySelector('.cart');

		if(cart){
			let quantityBtns = cart.querySelectorAll('.btn-quantity-cart'),
				quantityNums = cart.querySelectorAll('.quantity-num');

			quantityBtns.forEach(function(e){

				e.addEventListener('click', function(){
					let quantityNum = e.parentElement.querySelector('.quantity-num');

					if(e.classList.contains('btn-quantity-minus')){
						quantityMinus(quantityNum);
					}else{
						quantityPlus(quantityNum);
					}

					$('#cart-form').sendForm('onUpdateQuantity', {});
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

		let cart = document.querySelector('.cart');

		if(cart){

			let deliveryItems = cart.querySelectorAll('input[name="delivery"]'),
				address = cart.querySelector('.section-address'),
				deviveryPrice = cart.querySelector('.devivery-price').querySelector('span'),
				goodsPrice = cart.querySelector('.goods-price').querySelector('span'),
				totalPrice = cart.querySelector('.total-price').querySelector('span');

			function setTotalPriceDelivery(e){
				if(e.getAttribute('data-price') == 0 && e.getAttribute('data-code') == 'pickup'){
					deviveryPrice.textContent = "Самовывоз"
				}else if(e.getAttribute('data-price') == 0 && e.getAttribute('data-code') != 'pickup'){
					deviveryPrice.textContent = "Бесплатно"
				}else{
					deviveryPrice.textContent = e.getAttribute('data-price')
				}
			}
			
			deliveryItems.forEach(function(e){

				if(e.checked) setTotalPriceDelivery(e);
				
				// изменение способа доставки
				e.addEventListener('change', function(){
					setTotalPriceDelivery(e)
					address.classList.toggle('active')
					totalPrice.textContent = Number(e.getAttribute('data-price')) + Number(goodsPrice.getAttribute('data-summ'));
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
});