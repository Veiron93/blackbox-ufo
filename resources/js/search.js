document.addEventListener("DOMContentLoaded", function () {

    let localStorageName = 'search-history';

    let widgetSearch = document.querySelector('.widget-search'),
        searchInput = widgetSearch.querySelector('.search-input'),
        btnsSearch = widgetSearch.querySelectorAll('.btn-search'),
        searchResultsWrapper = widgetSearch.querySelector('.widget-search-results');

    // категории
    let sectionCategories = searchResultsWrapper.querySelector('.widget-search-results_categories'),
        categoriesList = sectionCategories.querySelector('.widget-search-results_categories-list');

    // товары
    let sectionProducts = searchResultsWrapper.querySelector('.widget-search-results_products'),
        productsList = sectionProducts.querySelector('.widget-search-results_products-list');

    // история
    let sectionHistory = searchResultsWrapper.querySelector('.widget-search-results_history'),
        historyList = sectionHistory.querySelector('.widget-search-results_history-list');

    // все результаты
    let sectionAllResults = searchResultsWrapper.querySelector('.widget-search-results_all'),
        countCategoriesAllResults = sectionAllResults.querySelector('.all-results_categories'),
        countProductsAllResults = sectionAllResults.querySelector('.all-results_products');


    // ВАЛИДАЦИЯ ЗАПРОСА (УДАЛЕНИЕ СКИПТОВ И Т.Д.)
    function queryValid() {
        let result = searchInput.value;
        result = result.replaceAll(/<[^>]+>/gi, "");
        return result;
    }


    // ОЧИСТИТЬ ПОИСК
    let btnClear = widgetSearch.querySelector('.btn-clear'),
        btnClearStatus = false;


    function clearBtnAllResults() {

        sectionAllResults.classList.remove('active');

        countCategoriesAllResults.classList.remove('active');
        countProductsAllResults.classList.remove('active');

        countCategoriesAllResults.querySelector('span').textContent = ''
        countProductsAllResults.querySelector('span').textContent = ''
    }

    function clearSearch() {
        searchInput.value = "";

        if (sectionCategories.classList.contains('active')) {
            sectionCategories.classList.remove('active');
            categoriesList.innerHTML = '';
        }

        if (sectionProducts.classList.contains('active')) {
            sectionProducts.classList.remove('active');
            productsList.innerHTML = '';
        }

        clearBtnAllResults();

        renderHistory();
        statusBtnClearSearch();
    }

    function statusBtnClearSearch() {
        btnClearStatus = btnClearStatus ? false : true;
        btnClear.classList.toggle('active');
    }

    btnClear.addEventListener('click', () => clearSearch());


    // status search result wrapper
    function statusSearchResultWrapper() {

        renderHistory();

        searchInput.addEventListener('click', () => {
            if (!searchResultsWrapper.classList.contains('active')) {
                searchResultsWrapper.classList.add('active');
            }
        })

        window.addEventListener('click', element => {
            if (searchResultsWrapper.classList.contains('active') && element.target.className != 'widget-search' && !element.target.closest('.widget-search')) {
                searchResultsWrapper.classList.remove('active');
            }
        });
    }

    statusSearchResultWrapper();

    // ОТПРАВКА ЗАПРОСА ПОИСКА
    function onSendSearch() {
        axios.post('/search/widget/', {
            query: queryValid()
        }).then(response => {
            renderResults(response.data.response);
        })
    }


    // РЕНДЕР РЕЗУЛЬТАТОВ ПОИСКА

    function renderHistory() {

        let history = getHistorySearch(),
            historyListTemp = '',
            i = 0;

        if (history && history.length) {

            history.forEach(e => {

                historyListTemp += `<div><a href="${e.href}">${e.name}</a></div>`;

                i++;

                if (i == history.length && historyListTemp.length) {
                    historyList.innerHTML = historyListTemp;
                    sectionHistory.classList.add('active');
                }
            })
        }
    }

    function renderResults(searchResult) {


        // категории
        function renderCategories(categoriesResult) {

            let categories = '',
                words = ['товар', 'товара', 'товаров'],
                i = 0;

            categoriesResult.forEach(category => {
                if (category.products_count > 0) {
                    categories += `<div data-href='/catalog/${category.id}'><span data-level="${category.level}">${category.name}</span> <span data-level="${category.parent_level}">${category.parent_name}</span> <span>${category.products_count} ${endingWord(words, category.products_count)}</span></div>`;
                }

                i++;

                if (i == categoriesResult.length && categories.length) {
                    categoriesList.innerHTML = categories;
                    sectionCategories.classList.add('active');
                }
            })
        }

        // товары
        function renderProducts(productsResult) {

            let products = '',
                i = 0;

            productsResult.forEach(product => {
                products += `<div data-href='/catalog/product/${product.id}'><img src="${product.image}"><span>${product.name}</span></div>`;

                i++;

                if (i == productsResult.length && products.length) {
                    productsList.innerHTML = products;
                    sectionProducts.classList.add('active');
                }
            })
        }

        function allResults(searchResult) {

            clearBtnAllResults();

            if (!searchResult) {
                return
            }

            let countCategories = searchResult.count_categories,
                countProducts = searchResult.count_products

            if (countCategories) {
                countCategoriesAllResults.querySelector('span').textContent = countCategories;
                countCategoriesAllResults.classList.add('active');
            }

            if (countProducts) {
                countProductsAllResults.querySelector('span').textContent = countProducts;
                countProductsAllResults.classList.add('active');
            }

            if (countCategories || countProducts) {
                sectionAllResults.classList.add('active');
            }
        }


        if (searchResult && searchResult.categories.length) {
            renderCategories(searchResult.categories)
        } else {
            categoriesList.innerHTML = '';
            sectionCategories.classList.remove('active');
        }

        if (searchResult && searchResult.products.length) {
            renderProducts(searchResult.products)
        } else {
            productsList.innerHTML = '';
            sectionProducts.classList.remove('active');
        }

        allResults(searchResult);

        // скрыть секцию История
        sectionHistory.classList.remove('active');

        clickResultSearch();
    }


    // СЛЕЖЕНИЕ ЗА ИЗМЕНЕНИЯМИ ПОЛЯ ПОИСКА
    let sendFormSearch = null;

    // ввод
    searchInput.addEventListener('input', function () {

        // отправка запроса если есть изменения в поле поиска
        if (this.value.length > 0) {
            if (sendFormSearch) {
                clearTimeout(sendFormSearch);
            }
            sendFormSearch = setTimeout(onSendSearch, 1000);
        } else {
            clearSearch();
        }

        // статус кнопки очистить поиск
        if (this.value.length > 0 && !btnClearStatus) statusBtnClearSearch();
    });


    //ПЕРЕХОДЫ

    // нажатие Enter
    searchInput.addEventListener("keypress", function (event) {
        if (event.key === "Enter") {

            let href = '/search/?q=' + queryValid().replace(' ', '+'),
                name = queryValid(),
                resultData = { name: name, href: href };

            addHistorySearch(resultData);

            event.preventDefault();

            btnsSearch.forEach(btn => btn.click())
        }
    });

    // на страницу поиска по нажатию кнопки найти
    btnsSearch.forEach(btn => btn.addEventListener('click', () => window.location = '/search?q=' + queryValid()))

    // переход на результат поиска
    function clickResultSearch() {

        let categories = categoriesList.querySelectorAll('div'),
            products = productsList.querySelectorAll('div'),
            searchResults = [...categories, ...products];

        searchResults.forEach(result => {
            result.addEventListener('click', () => {

                let href = result.getAttribute('data-href'),
                    name = result.querySelector('span').textContent,
                    resultData = { name: name, href: href };

                addHistorySearch(resultData);
            })
        })
    }


    // ИСТОРИЯ ПОИСКА

    // получение истории
    // @return array
    function getHistorySearch() {
        let historySearch = JSON.parse(localStorage.getItem(localStorageName));
        return historySearch;
    }


    // добавить
    function addHistorySearch(value) {

        let historySearch = getHistorySearch();

        if (historySearch && historySearch.length) {

            let add = true;

            historySearch.forEach(result => {
                if (result.href == value.href) add = false
            })

            // удалить старое значение если история переполнилась
            if (historySearch.length == 3) historySearch.splice(0, 1);

            // добавить значение в историю
            add && localStorage.setItem(localStorageName, JSON.stringify([...historySearch, value]));

            // переадресовать на страницу результата
            window.location = value.href;

        } else {
            localStorage.setItem(localStorageName, JSON.stringify([value]));
            window.location = value.href;
        }
    }

    // очистка истории поиска
    function clearHistorySearch() {
        let btnClear = sectionHistory.querySelector('.widget-search-results_history-btn-clear');

        btnClear.addEventListener('click', () => {
            if (getHistorySearch()) {
                sectionHistory.classList.remove('active');
                localStorage.removeItem(localStorageName)
            };
        });
    }

    clearHistorySearch();


    // МОБИЛЬНЫЙ ПОИСК
    function statusMobileSearch() {
        let btnsStatusSearch = document.querySelectorAll('.btn-state-search');

        if (btnsStatusSearch) {
            btnsStatusSearch.forEach(btn => {
                btn.addEventListener('click', () => document.body.classList.toggle('search-active'));
            })
        }
    }

    statusMobileSearch();
});