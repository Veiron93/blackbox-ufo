'use strict'

document.addEventListener("DOMContentLoaded", function () {

    // ФОТО ТОВАРА
    let swiper = new Swiper(".catalog-product-slider .mySwiper", {
        breakpoints: {
            0: {
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


    // ВЫБОР АРТИКУЛА
    function selectSku() {

        function dataExtra(sku) {
            const btnAddToCart = productPage.querySelector(".add-to-cart-btn");

            let id = sku.value,
                extra = JSON.parse(btnAddToCart.getAttribute('data-extra'));

            extra.id_sku = id;
            btnAddToCart.setAttribute('data-extra', JSON.stringify(extra));
        }

        function price(sku) {
            let price = sku.getAttribute('data-price');

            const priceProduct = productPage.querySelector(".catalog-product_buy-price .actual");

            priceProduct.textContent = price;
        }

        function productsOtherInfo(sku) {
            const blockProductsOtherInfo = productPage.querySelector(".product-other-info");
            const productAmount = blockProductsOtherInfo.querySelector(".product-amount");
            const productCode = blockProductsOtherInfo.querySelector(".product-code_sku");

            let leftover = sku.getAttribute('data-leftover');
            let skuId = sku.value;

            productAmount.querySelector("span").textContent = leftover;
            productCode.textContent = skuId;
        }


        let productPage = document.querySelector('.catalog-product');

        if (productPage) {
            let skus = productPage.querySelectorAll("input[name='product-sku']");

            if (skus) {
                skus.forEach(sku => sku.addEventListener("change", ev => {
                    let sku = ev.target;

                    dataExtra(sku);
                    price(sku);
                    productsOtherInfo(sku);
                }))
            }
        }
    }

    selectSku();


    // РАЗВЕРНУТЬ ПОЛНОЕ ОПИСАНИЕ ТОВАРА
    function productDescription() {

        let descriptionSection = document.querySelector('.catalog-product_description-hidden');

        if (!descriptionSection) return;

        let btn = descriptionSection.querySelector('.btn-more');

        btn.addEventListener('click', function () {
            descriptionSection.classList.toggle('active');
            btn.textContent = descriptionSection.classList.contains('active') ? 'Свернуть' : 'Показать всё';
        })
    }

    productDescription();
});