'use strict'

document.addEventListener("DOMContentLoaded", function () {

    // КОЛИЧЕСТВО
    function quantityCart() {

        function quantityMinus(quantityNum) {
            if (quantityNum.value > 1) {
                quantityNum.value = +quantityNum.value - 1;
            }
        }

        function quantityPlus(quantityNum) {
            quantityNum.value = +quantityNum.value + 1;
        }

        let cart = document.querySelector('.cart-wrapper');

        if (cart) {
            let quantityBtns = cart.querySelectorAll('.btn-quantity-cart'),
                quantityNums = cart.querySelectorAll('.quantity-num');

            quantityBtns.forEach(function (e) {

                e.addEventListener('click', function () {
                    let quantityNum = e.parentElement.querySelector('.quantity-num');

                    if (e.classList.contains('btn-quantity-minus') || quantityNum.value < quantityNum.getAttribute("max")) {
                        if (e.classList.contains('btn-quantity-minus')) {
                            quantityMinus(quantityNum);
                        } else {
                            quantityPlus(quantityNum);
                        }

                        $('#cart-form').sendForm('onUpdateQuantity', {});
                    }
                })
            })

            quantityNums.forEach(function (e) {
                e.addEventListener('change', function () {
                    e.value = e.value >= 1 ? e.value : 1;
                    $('#cart-form').sendForm('onUpdateQuantity', {});
                })
            })
        }
    }

    quantityCart();


    // ДОСТАВКА
    function cartDelivery() {

        let cart = document.querySelector('.cart-wrapper');

        if (cart) {

            // стоимость доставки
            function setTotalPriceDelivery(deliveryItem) {

                let deviveryPriceBlock = cart.querySelector('.devivery-price').querySelector('span'),
                    deliveryPrice = deliveryItem.getAttribute('data-price'),
                    deliveryCode = deliveryItem.getAttribute('data-code');

                if (deliveryPrice == 0) {

                    let text = null;

                    switch (deliveryCode) {
                        case 'pickup':
                            text = 'Самовывоз';
                            break;
                        case 'tk':
                            text = 'Бесплатно до ТК';
                            break;
                        default:
                            text = 'Бесплатно';
                    }

                    deviveryPriceBlock.textContent = text;
                } else {
                    deviveryPriceBlock.textContent = deliveryPrice + ' ₽';
                }
            }

            // общая сумма заказа
            function setTotalPrice(deliveryItem) {
                let totalPriceBlock = cart.querySelector('.total-price').querySelector('span'),
                    goodsPrice = cart.querySelector('.goods-price').querySelector('span');

                totalPriceBlock.textContent = Number(deliveryItem.getAttribute('data-price')) + Number(goodsPrice.getAttribute('data-summ'));
            }

            function statusSectionAddress(deliveryItem) {
                let address = cart.querySelector('.section-address');

                if (deliveryItem.getAttribute('data-code') != 'pickup' && !address.classList.contains('active')) {
                    address.classList.add('active')
                } else if (deliveryItem.getAttribute('data-code') == 'pickup') {
                    address.classList.remove('active')
                }
            }

            let deliveryItems = cart.querySelectorAll('input[name="delivery"]');

            deliveryItems.forEach(function (deliveryItem) {

                if (deliveryItem.hasAttribute('checked')) {
                    setTotalPriceDelivery(deliveryItem);
                    setTotalPrice(deliveryItem);
                    statusSectionAddress(deliveryItem);
                }

                // изменение способа доставки
                deliveryItem.addEventListener('change', function () {
                    setTotalPriceDelivery(deliveryItem);
                    setTotalPrice(deliveryItem);
                    statusSectionAddress(deliveryItem);
                })
            })
        }
    }

    cartDelivery();

});