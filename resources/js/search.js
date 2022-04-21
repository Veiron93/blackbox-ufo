'use strict'

document.addEventListener("DOMContentLoaded", function () {

    let search = document.querySelector('.header-search'),
        searchInput = search.querySelector('.search-input'),
        searchResultsWrapper = search.querySelector('.header-search-results'),
        sectionCategories = searchResultsWrapper.querySelector('.header-search-results_categories'),
        categoriesList = sectionCategories.querySelector('.header-search-results_categories-list');

    // clear search input
    let btnClear = search.querySelector('.btn-clear'),
        btnClearStatus = false;

    function onClearSearch() {
        searchInput.value = "";
        onStatusBtnClearSearch();
    }

    function onStatusBtnClearSearch() {
        btnClearStatus = btnClearStatus ? false : true;
        btnClear.classList.toggle('active');
    }

    btnClear.addEventListener('click', () => {
        onClearSearch();
        clearResults();
    });


    // clear results
    function clearResults(){
        sectionCategories.classList.remove('active');
        categoriesList.innerHTML = '';
    }


    // redirect search page
    let btnSearch = search.querySelector('.btn-search');
    btnSearch.addEventListener('click', () => window.location = 'search');

    
    // status search result wrapper
    function statusSearchResultWrapper(status = true){
        if(status){
            searchResultsWrapper.classList.add('active');
        }else{
            searchResultsWrapper.classList.remove('active');
        }
    }


    // отправка запроса
    function onSendSearch(){
        axios.get('/search/widget/')
            .then(function (response) {

                let categories = response.data.response.categories,
                    products = response.data.response.products;

                //console.log(response.data.response);

                if(categories) renderCategories(categories);
                //if(products) renderCategories(products);
            })
            .catch(function (error) {
                console.log(error);
            });
    }

    // рендер категорий в список результата поиска
    function renderCategories(categoriesResult){

        console.log(categoriesResult)

        let categories = '',
            words = ['товар', 'товара', 'товаров'];

        categoriesResult.forEach(category => {
            categories += `<a href='/catalog/${category.id}'><span>${category.name}</span> <span>${category.parent_name}</span> <span>12 ${endingWord(words, 14)}</span></a>`;
        })    

        categoriesList.innerHTML = categories;
        sectionCategories.classList.add('active');
        statusSearchResultWrapper();
    }




    // слежение за полем поиска
    let sendFormSearch = null;

    searchInput.addEventListener('input', function () {

        // отправка запроса если есть изменения в поле поиска
        if(this.value.length > 0){
            if (sendFormSearch) clearTimeout(sendFormSearch);
            sendFormSearch = setTimeout(onSendSearch, 1000);
        }else{
            onClearSearch();
            statusSearchResultWrapper(false);
            clearResults();
        }

        // статус кнопки очистить поиск
        if (this.value.length > 0 && !btnClearStatus) {
            onStatusBtnClearSearch();
        }
    });

});