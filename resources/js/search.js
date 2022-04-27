'use strict'

document.addEventListener("DOMContentLoaded", function () {

    let widgetSearch = document.querySelector('.widget-search'),
        searchInput = widgetSearch.querySelector('.search-input'),
        searchResultsWrapper = widgetSearch.querySelector('.widget-search-results');
    
    // категории
    let sectionCategories = searchResultsWrapper.querySelector('.widget-search-results_categories'),
        categoriesList = sectionCategories.querySelector('.widget-search-results_categories-list');

    // товары
    let sectionProducts = searchResultsWrapper.querySelector('.widget-search-results_products'),
        productsList = sectionProducts.querySelector('.widget-search-results_products-list');

    
    //////// ОЧИСТИТЬ ПОИСК ////////
    let btnClear = widgetSearch.querySelector('.btn-clear'),
        btnClearStatus = false;

    function clearSearch() {
        searchInput.value = "";

        if(sectionCategories.classList.contains('active')){
            sectionCategories.classList.remove('active');
            categoriesList.innerHTML = '';
        }

        if(sectionProducts.classList.contains('active')){
            sectionProducts.classList.remove('active');
            productsList.innerHTML = '';
        }

        statusBtnClearSearch();
    }

    function statusBtnClearSearch() {
        btnClearStatus = btnClearStatus ? false : true;
        btnClear.classList.toggle('active');
    }

    // status search result wrapper
    function statusSearchResultWrapper(status = true){
        if(status){
            searchResultsWrapper.classList.add('active');
        }else{
            searchResultsWrapper.classList.remove('active');
        }
    }

    btnClear.addEventListener('click', () => clearSearch());

    
    //////// ОТПРАВКА ЗАПРОСА ПОИСКА ////////
    function onSendSearch(){
        axios.post('/search/widget/', {
            query: searchInput.value
        })
        .then(response => {
            let categories = response.data.response.categories,
                products = response.data.response.products;

            if (categories) renderCategories(categories);
            if (products) renderProducts(products);

            statusSearchResultWrapper();
        })
        .catch(error => console.log(error));
    }


    //////// РЕНДЕР РЕЗУЛЬТАТА ПОИСКА ////////
    // категории
    function renderCategories(categoriesResult){

        let categories = '',
            words = ['товар', 'товара', 'товаров'];

        categoriesResult.forEach(category => {
            if(category.products_count > 0){
                categories += `<div data-href='/catalog/${category.id}'><span>${category.name}</span> <span>${category.parent_name}</span> <span>${category.products_count} ${endingWord(words, 14)}</span></div>`;
            }
        })    

        if(categories.length){
            categoriesList.innerHTML = categories;
            sectionCategories.classList.add('active');
        }

        //clickResultSearch();
    }

    // товары
    function renderProducts(productsResult){

        let products = '';

        productsResult.forEach(product => {
            products += `<div data-href='/catalog/product/${product.id}'><img src="/uploaded/${product.image_path}"><span>${product.name}</span></div>`;
        })    

        if(products.length){
            productsList.innerHTML = products;
            sectionProducts.classList.add('active');
        }

        //clickResultSearch();
    }


    //////// СЛЕЖЕНИЕ ЗА ИЗМЕНЕНИЯМИ ПОЛЯ ПОИСКА ////////
    let sendFormSearch = null;

    searchInput.addEventListener('input', function () {   

        // отправка запроса если есть изменения в поле поиска
        if(this.value.length > 0){
            if (sendFormSearch){
                clearTimeout(sendFormSearch);
            } 
            sendFormSearch = setTimeout(onSendSearch, 1000);
        }else{
            clearSearch();
            statusSearchResultWrapper(false);
        }

        // статус кнопки очистить поиск
        if (this.value.length > 0 && !btnClearStatus) statusBtnClearSearch();
    });


    //////// ПЕРЕХОДЫ ////////
    // на страницу поиска по нажатию кнопки найти
    let btnSearch = widgetSearch.querySelector('.btn-search');
    btnSearch.addEventListener('click', () => window.location = 'search');

    // переход на результат поиска
    function clickResultSearch(){

        let categories = categoriesList.querySelectorAll('div');

        categories.forEach(result => {

            result.addEventListener('click', ()=>{

                let href = result.getAttribute('data-href'),
                    name = result.getAttribute('data-href'),
                    resultData = {name: name, href: href};

                addHistorySearch(resultData);

                //window.location = href;
            })
        }) 
    }


    //////// ИСТОРИЯ ПОИСКА ////////
    // добавить
    function addHistorySearch(value){

        let localStorageName = 'search-history',
            historySearch = localStorage.getItem(localStorageName);

        if(historySearch){
            let add = true;
            
            historySearch = JSON.parse(historySearch);
    
            historySearch.forEach(result =>{
                if (result.href == value.href) add = false
            })

            add && localStorage.setItem(localStorageName, JSON.stringify([...historySearch, value]));
            
        }else{
            localStorage.setItem(localStorageName, JSON.stringify([value]));
        }
    }
});