<?php

class Shop extends App_Controller
{
    const COOKIE_DATA = 'blackbox_catalog_user_cookie';

    public function __construct()
    {
        parent::__construct();

        $this->globalHandlers[] = 'onAddToCart';
        $this->globalHandlers[] = 'onUpdateQuantity';
        $this->globalHandlers[] = 'onDeleteItem';
        $this->globalHandlers[] = 'onRecalculationCart';
        //$this->globalHandlers[] = 'onPlaceOrder';
        $this->globalHandlers[] = 'onClearCart';
        $this->globalHandlers[] = 'onDeleteProductCart';
    }

    public function index() {}

    public function cart()
    {
        $this->viewData['cart'] = Shop_Cart::getCart();
    }

    public function checkout()
    {
        $this->viewData['cart'] = $cart = Shop_Cart::getCart();
        if ($cart->getItemsCount() < 1) {
            Phpr::$response->redirect(u("shop_cart", []));
        }
        $this->setTitle("Оформление заказа");
    }

    protected function onAddToCart()
    {
        try {
            $inputJSON = file_get_contents('php://input');
            $data = json_decode($inputJSON, TRUE);

            $id = $data['id'];
            $quantity = $data['quantity'];
            $type = $data['type'];

            //traceLog($data);


            // $id = post('id');
            // $quantity = post('quantity', 1);
            // $type = post('type');

            if (!$id || !$type) {
                throw new Phpr_ApplicationException("Illegal request");
            }

            $cart = Shop_Cart::getCart();

            if ($type == "product") {

                /** @var Catalog_Product $product */
                $product = Catalog_Product::create()->where('hidden is null')->find($id);

                if (!$product) {
                    throw new Phpr_ApplicationException("Продукт не найден. Попробуйте обновить страницу и попробовать снова.");
                }

                if ($this->checkLeftover($product, $quantity)) {
                    $cart->addProduct($product, $quantity);
                }
            } else if ($type == "sku") {

                //$id_sku = post('id_sku');
                $id_sku = $data['id_sku'];

                /** @var Catalog_Sku $sku */
                $sku = Catalog_Sku::create()->find($id_sku);

                if (!$sku) {
                    throw new Phpr_ApplicationException("Артикул не найден. Попробуйте обновить страницу и попробовать снова.");
                }

                if ($this->checkLeftover($sku, $quantity)) {

                    if (!$sku->price) {
                        $product = Catalog_Product::create()->where('hidden is null')->find($id);

                        $sku->price = $product->price;
                    }

                    $cart->addSku($sku, $quantity);
                }
            }

            $this->renderMultiple([
                "mini-cart-info" => "@_mini_cart_info",
            ]);
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    private function checkLeftover($item, $requested)
    {
        if (Phpr::$config->get('SHOP_ENABLE_LEFTOVERS')) {
            if (Admin_ModuleSettings::get_module_parameter('shop', 'allow_add_more_than_left')) {
                return true;
            }
            if ($item->leftover < $requested) {
                throw new Phpr_ApplicationException("В наличии нет столько продуктов.");
            }
        }
        return true;
    }

    protected function onUpdateQuantity()
    {
        try {
            $cart = Shop_Cart::getCart();
            $cartItems = $cart->getItems();
            $quantity = post('quantity', []);

            if (!is_array($quantity)) {
                throw new Phpr_ApplicationException("Illegal request");
            }

            foreach ($cartItems as $item) {
                if (array_key_exists($item->getId(), $quantity)) {

                    $leftoverProduct = Db_DbHelper::scalar("SELECT leftover FROM catalog_products
                        WHERE id = ?", [$item->productId]);

                    $newQuantity = (int)$quantity[$item->getId()];

                    if ($newQuantity <= $leftoverProduct) {
                        $item->setQuantity((int)$quantity[$item->getId()]);
                    } elseif ($newQuantity > $leftoverProduct) {
                        $item->setQuantity($leftoverProduct);
                    } else if ($newQuantity < 1) {
                        $cart->deleteItem($item);
                    }
                }
            }

            $cart->notifyCartUpdated();

            Phpr::$response->redirect(u('shop_cart', []));
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    protected function onDeleteItem()
    {
        try {
            $cart = Shop_Cart::getCart();
            $id = post('id');

            if (!$id) {
                throw new Phpr_ApplicationException("Illegal request");
            }

            $item = $cart->getItem($id);

            if ($item) {
                $cart->deleteItem($item);
            }

            Phpr::$response->redirect(u('shop_cart', []));
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    protected function onClearCart()
    {
        try {
            $cart = Shop_Cart::getCart();

            if ($cart->getItemsCount() == 0) {
                throw new Phpr_ApplicationException("Illegal request");
            }

            $cartItems = $cart->getItems();

            foreach ($cartItems as $item) {
                $cart->deleteItem($item);
            }

            Phpr::$response->redirect(u('shop_cart', []));
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    protected function onDeleteProductCart()
    {
        try {
            $cart = Shop_Cart::getCart();
            $id = post('id');

            if (!$id) {
                throw new Phpr_ApplicationException("Illegal request");
            }

            $item = $cart->getItem($id);

            if ($item) {
                $cart->deleteItem($item);
            }

            Phpr::$response->redirect($_POST['page']);
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    protected function onRecalculationCart()
    {
        try {
            if (App_Controller::$statusUpdateCart) {
                throw new Phpr_ApplicationException("Упс... Обновите корзину, у некоторых товаров изменилась цена или количество в наличии меньше чем Вы выбрали");
            } else {
                $this->onPlaceOrder();
            }
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    protected function onPlaceOrder()
    {
        try {
            $cart = Shop_Cart::getCart();

            if ($cart->getItemsCount() == 0) {
                throw new Phpr_ApplicationException("Ваша корзина пуста. Оформление заказа невозможно.");
            }

            $delivery = Phpr::$config->get('DELIVERY')[$_POST['delivery'] ? $_POST['delivery'] : 0];

            $this->validation->add("name", "Имя")->required("Укажите своё имя");
            $this->validation->add("phone", "Телефон")->required("Укажите свой номер телефон");
            $this->validation->add("comment", "Комментарий")->fn("trim");

            if ($delivery['code'] != 'pickup') {
                $this->validation->add("customer-address", "Адрес")->required("Укажите адрес");

                $freeDelivery = $this->menuItems->getTextBlock('free_delivery', 'Бесплатная доставка', Admin_TextBlock::TYPE_PLAIN)->content;

                if ($freeDelivery && $delivery['code'] === 'ys' && $freeDelivery <= $cart->getTotalPrice()) {
                    $delivery['price'] = 'Бесплатно';
                }

                $deliveryDescription = "Район: " . $delivery['name'] . PHP_EOL . "Стоимость доставки: " . $delivery['price'];
            } else {
                $deliveryDescription = "Самовывоз";
            }

            if (!$this->validation->validate($_POST)) {
                $this->validation->throwException();
            }

            $values = $this->validation->fieldValues;

            $order = Shop_Order::create();
            $order->customer_name = $values['name'];
            $order->customer_phone = $values['phone'];
            $order->customer_email = "no-replay@bb65.ru";
            $order->comment = $values['comment'];
            $order->customer_address = $deliveryDescription;

            if ($delivery['code'] != 'pickup') {
                $order->customer_address .= PHP_EOL . "Адрес: " . $values['customer-address'];
            }

            $order->cart_id = $cart->cart_id;
            $order->save();

            $cart_items = $cart->getItems();

            foreach ($cart_items as $item) {
                $orderItem = Shop_OrderItem::createFromCartItem($item);
                $orderItem->order_id = $order->id;
                $orderItem->save();
            }

            $order->recalculate();
            $order->markAsCompiled();
            $cart->delete();

            $this->setSalesProducts($cart_items);
            $this->setLeftoverProducts($cart_items);

            Phpr::$response->redirect("/shop/success");
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    // количество продаж товара
    protected function setSalesProducts($products_cart)
    {
        foreach ($products_cart as $product) {
            Db_DbHelper::query("UPDATE catalog_products SET sales = IF(sales, sales + $product->quantity , $product->quantity) WHERE id = $product->productId");
        }
    }

    // остаток товара
    protected function setLeftoverProducts($products_cart)
    {
        $skus = [];
        $products = [];

        foreach ($products_cart as $product) {
            if ($product->skuId) {
                array_push($skus, $product);
            } else {
                array_push($products, $product);
            }
        }

        foreach ($skus as $sku) {
            Db_DbHelper::query("UPDATE catalog_skus SET leftover = leftover - $sku->quantity WHERE id = $sku->skuId");
        }

        foreach ($products as $product) {
            Db_DbHelper::query("UPDATE catalog_products SET leftover = leftover - $product->quantity WHERE id = $product->productId");
        }
    }

    public function success() {}

    public function payment($orderId, $hash)
    {
        if (!Phpr::$config->get('PAYMENT_PAGE_SUPPORT', false)) {
            Phpr::$response->redirect('/');
        }
        $order = Shop_Order::create()->find($orderId);
        if (!$order) {
            Phpr::$response->redirect('/');
        }
        if (md5($orderId . $order->customer_email) != $hash) {
            Phpr::$response->redirect('/');
        }
        $this->viewData['order'] = $order;
        $this->viewData['backends'] = array_filter(Payment_Helper::listBackends(), function ($e) {
            return !in_array(get_class($e), ['Payment_CashBackend', 'Payment_CardBackend']);
        });
        if (post('pay')) {
            try {
                $paymentBackend = Payment_Helper::findBackendById(post('payment_backend'));
                if (!$paymentBackend) {
                    throw new Phpr_ApplicationException("Выбран недопустимый способ оплаты");
                }
                $payment = Payment_Payment::create();
                $payment->payable_object = $order->className;
                $payment->payable_id = $order->id;
                $payment->status = Payment_Payment::statusPrepare;
                $payment->paid_via = get_class($paymentBackend);
                $payment->amount = $order->amount;
                $payment->comment = $order->comment;
                $payment->payable_comment = sprintf("Оплата заказа №%d", $order->id);
                $payment->save();
                $url = Phpr::$router->url('payment_prepaid', [$paymentBackend->getId(), $payment->internal_payment_id]);
                Phpr::$response->redirect($url);
            } catch (\Exception $ex) {
                $this->ajaxError($ex);
            }
        }
    }

    public static function getSavedCustomerField($field)
    {
        $values = Phpr::$request->cookie(self::COOKIE_DATA);

        if (!$values) return null;

        $values = unserialize($values);

        if (isset($values[$field])) return $values[$field];

        if ($field == 'save_order_info' && count($values)) return true;

        return null;
    }
}
