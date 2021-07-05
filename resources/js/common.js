// fancybox
$(document).ready(function(){
	$('[data-fancybox]').fancybox({
		protect: true,
		buttons : [
			'zoom',
			'thumbs',
			'close'
		]
	});

	// SLIDERS

	// слайдер на главной
	$('.main-slider').not('.slick-initialized').slick({
		infinite: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		autoplay: true,
  		autoplaySpeed: 2000,
		dots: true
	});

	// новые продукты слайер
	$('.new-products-slider').not('.slick-initialized').slick({
		infinite: true,
		slidesToShow: 6,
		slidesToScroll: 6,
		dots: true
	});

	// сллайдер в просмотре продукта
	$('.product-info-slider-for').not('.slick-initialized').slick({
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: true,
		fade: true,
		asNavFor: '.product-info-slider-nav'
	});
	
	$('.product-info-slider-nav').not('.slick-initialized').slick({
		slidesToShow: 5,
		slidesToScroll: 5,
		asNavFor: '.product-info-slider-for',
		arrows: true,
	});

	// КОРЗИНА
	function quantityCart(){

    	function quantityMinus(quantityNum) {
			if (quantityNum.value > 1) {
				quantityNum.value = +quantityNum.value - 1;
			}
		}
			 
		function quantityPlus(quantityNum) {
			quantityNum.value = +quantityNum.value + 1;
		}

		let quantityBtns = document.querySelectorAll('.btn-quantity-cart');
		let quantityNums = document.querySelectorAll('.quantity-num');

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

	quantityCart();


	// ВИД СПИСКА ТОВАРОВ
	function viewProductList(){
		let viewProductListBlock = document.querySelector('.view-product-list');

		if(viewProductListBlock){

			if(!getCookie('viewProductList')){
				setCookie('viewProductList', 'grid');
			}

			let switches = viewProductListBlock.querySelectorAll('.item'),
				typeProductList = getCookie('viewProductList');

			switches.forEach(function(e){

				// if(e.getAttribute('data-type') == typeProductList){
				// 	e.classList.add('active')
				// }

				e.addEventListener('click', function(){
					
					if(this.getAttribute('data-type') == 'grid' || this.getAttribute('data-type') == 'list'){
						typeProductList = this.getAttribute('data-type');
					}else{
						typeProductList = 'grid';
					}

					document.querySelector('.list-products').setAttribute('data-view-list', typeProductList);
					setCookie('viewProductList', typeProductList);

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
		let filterList = document.querySelector('.catalog-filter-list');

		if(filterList){
			let filters = filterList.querySelectorAll('.filter');

			filters.forEach(function(filter){
				filter.querySelector('.filter-head').addEventListener('click', function(){
					filter.classList.toggle('active');
				})

				let filterAttributes = filter.querySelectorAll('.filter-list .list-attribites > *');

				filterAttributes.forEach(function(attribute){

					console.log(attribute);
				})
				
			})
		}
	}

	viewFilterList();
});