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

                $id_sku = post('id_sku');

                /** @var Catalog_Sku $sku */
                $sku = Catalog_Sku::create()->find($id_sku);

                if (!$sku) {
                    throw new Phpr_ApplicationException("Артикул не найден. Попробуйте обновить страницу и попробовать снова.");
                }

                if ($this->checkLeftover($sku, $quantity)) {
                    $cart->addSku($sku, $quantity);
                }
            }

            $this->renderMultiple([
                "#mini-cart" => "@_mini_cart",
                "#mini-cart-mobile" => "@_mini_cart_mobile",
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

                    if($newQuantity <= $leftoverProduct){ 
                        $item->setQuantity((int)$quantity[$item->getId()]);
                    }elseif($newQuantity > $leftoverProduct){
                        $item->setQuantity($leftoverProduct);
                    }else if($newQuantity < 1){
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

            foreach($cartItems as $item){
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

    protected function onRecalculationCart(){
        try{  
                
            if(App_Controller::$statusUpdateCart){
                throw new Phpr_ApplicationException("Упс... Обновите корзину, у некоторых товаров изменилась цена или количество в наличии меньше чем Вы выбрали");
            }else{
                $this->onPlaceOrder();
            }

        }catch (\Exception $ex) {
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


            // if($_POST['delivery']){
            //     $delivery = Phpr::$config->get('DELIVERY')[$_POST['delivery']];
            // }else{
            //     $delivery = Phpr::$config->get('DELIVERY')[0];
            // }

            $this->validation->add("name", "Имя")->required("Укажите имя");
            $this->validation->add("phone", "Телефон")->required("Укажите свой номер телефон");
            $this->validation->add("comment", "Комментарий")->fn("trim");

            if($delivery['code'] != 'pickup'){
                $this->validation->add("customer-address", "Адрес")->required("Укажите адрес");

				$freeDelivery = $this->menuItems->getTextBlock('free_delivery', 'Бесплатная доставка', Admin_TextBlock::TYPE_PLAIN)->content; 

                if($freeDelivery && $delivery['code'] === 'ys' && $freeDelivery <= $cart->getTotalPrice()){
                    $delivery['price'] = 'Бесплатно';
                }

                $deliveryDescription = "Район: " . $delivery['name'] . PHP_EOL . "Стоимость доставки: " . $delivery['price'];
            }else{
                $deliveryDescription = "Самовывоз";
            }

            $this->validation->add("customer-email", "Эл. почта")->email(false, "Укажите корректный адрес эл. почты");

            if (!$this->validation->validate($_POST)) {
                $this->validation->throwException();
            }

            $values = $this->validation->fieldValues;

            $order = Shop_Order::create();
            $order->customer_name = $values['name'];
            $order->customer_phone = $values['phone'];
            $order->customer_email = $values['customer-email'];

            if($delivery['code'] != 'pickup'){
                $order->customer_address = $deliveryDescription . PHP_EOL . "Адрес: " . $values['customer-address'];
            }else{
                $order->customer_address = $deliveryDescription;
            }
            
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
            //$this->setSalesProducts($cart->getItems());
            $cart->delete();
            Phpr::$response->redirect("/shop/success");

        } catch (\Exception $ex) {
            $this->ajaxError($ex);
        }
    }

    // счётчик проданных товаров
    // protected function setSalesProducts($products)
    // {
    //     $productsId = [];

    //     foreach ($products as $product) {
    //         array_push($productsId, $product->productId);
    //     }

    //     Db_DbHelper::objectArray("UPDATE catalog_products SET sales = sales + 1 WHERE id IN (" . implode(',', $productsId) . ")");
	// }

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

    public static function getSavedCustomerField($field) {
        $values = Phpr::$request->cookie(self::COOKIE_DATA);
        
        if (!$values) return null;

        $values = unserialize($values);

        if (isset($values[$field])) return $values[$field];

        if ($field == 'save_order_info' && count($values)) return true;
    
        return null;
    }
}