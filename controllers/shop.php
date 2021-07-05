<?php

class Shop extends App_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->globalHandlers[] = 'onAddToCart';
        $this->globalHandlers[] = 'onUpdateQuantity';
        $this->globalHandlers[] = 'onDeleteItem';
        $this->globalHandlers[] = 'onPlaceOrder';
    }

    public function index()
    {

    }

    public function cart_print()
    {
        $this->layout = 'cart_print';
        $this->viewData['cart'] = Shop_Cart::getCart();
        $this->setTitle("Корзина");
    }

    public function cart()
    {
        $this->viewData['cart'] = Shop_Cart::getCart();
        $this->setTitle("Корзина");
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
            $id = post('id');
            $quantity = post('quantity', 1);
            $type = post('type');
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
                /** @var Catalog_Sku $sku */
                $sku = Catalog_Sku::create()->find($id);
                if (!$sku) {
                    throw new Phpr_ApplicationException("Артикул не найден. Попробуйте обновить страницу и попробовать снова.");
                }
                if ($this->checkLeftover($sku, $quantity)) {
                    $cart->addSku($sku, $quantity);
                }
            }
            $this->renderMultiple([
                "#mini-cart" => "@_mini_cart"
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
                    $newQuantity = (int)$quantity[$item->getId()];
                    if ($newQuantity < 1) {
                        $cart->deleteItem($item);
                    } else {
                        $item->setQuantity((int)$quantity[$item->getId()]);
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

    protected function onPlaceOrder()
    {
        try {
            $cart = Shop_Cart::getCart();
            if ($cart->getItemsCount() == 0) {
                throw new Phpr_ApplicationException("Ваша корзина пуста. Оформление заказа невозможно.");
            }
            $this->validation->add("customer-name", "ФИО")->required("Заполните поле «ФИО»");
            $this->validation->add("customer-phone", "Телефон")->required("Укажите телефон");
            $this->validation->add("customer-email", "Эл. почта")->email(false, "Укажите корректный адрес эл. почты");
            $this->validation->add("customer-address", "Адрес")->required("Укажите адрес");
            $this->validation->add("comment", "Комментарий")->fn("trim");
            if (!$this->validation->validate($_POST)) {
                $this->validation->throwException();
            }
            $order = Shop_Order::create();
            $values = $this->validation->fieldValues;
            $order->customer_name = $values['customer-name'];
            $order->customer_phone = $values['customer-phone'];
            $order->customer_email = $values['customer-email'];
            $order->customer_address = $values['customer-address'];
            $order->comment = $values['comment'];
            $order->cart_id = $cart->cart_id;
            $order->save();
            foreach ($cart->getItems() as $item) {
                $orderItem = Shop_OrderItem::createFromCartItem($item);
                $orderItem->order_id = $order->id;
                $orderItem->save();
            }
            $order->recalculate();
            $order->markAsCompiled();
            $cart->delete();
            Phpr::$response->redirect("/shop/success");
        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    public function success()
    {

    }

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

}