<?php

class Payment extends App_Controller
{

    public function prepaid($via, $paymentId)
    {
        $this->layout = "payment_layout";
        try {
            /** @var Payment_Backend $backend */
            list($backend, $payment) = $this->getObjects($via, $paymentId);
            $this->viewData['formData'] = $backend->handlePrepaid($payment);
            $this->viewData['backend'] = $backend;
            $this->viewData['payment'] = $payment;
            $this->setTitle("Подождите...");
        } catch (Exception $ex) {
            $this->viewData['error'] = $ex->getMessage();
        }
    }

    public function success($via, $paymentId)
    {
        try {
            /** @var Payment_Backend $backend */
            list($backend, $payment) = $this->getObjects($via, $paymentId);
            $backend->handleSuccess($payment);
            $this->viewData['backend'] = $backend;
            $this->viewData['payment'] = $payment;
        } catch (Exception $ex) {
            $this->viewData['error'] = $ex->getMessage();
        }
    }

    public function failed($via, $paymentId)
    {
        try {
            /** @var Payment_Backend $backend */
            list($backend, $payment) = $this->getObjects($via, $paymentId);
            $backend->handleError($payment);
            $this->viewData['backend'] = $backend;
            $this->viewData['payment'] = $payment;
        } catch (Exception $ex) {
            $this->viewData['error'] = $ex->getMessage();
        }
    }

    /**
     * @param $via
     * @param $paymentId
     * @return Payment_Backend|Payment_Payment
     * @throws Phpr_ApplicationException
     */
    private function getObjects($via, $paymentId)
    {
        $backend = Payment_Helper::findBackendById($via);
        if (!$backend) {
            throw new Phpr_ApplicationException("Неизвестный способ оплаты");
        }
        $payment = Payment_Payment::create()->where('paid_via=?', get_class($backend))->where('internal_payment_id=?', $paymentId)->find();
        if (!$payment) {
            throw new Phpr_ApplicationException("Платеж не найден");
        }
        return array($backend, $payment);
    }

    public function callback($via)
    {
        try {
            $backend = Payment_Helper::findBackendById($via);
            if (!$backend) {
                throw new Phpr_ApplicationException("Неизвестный способ оплаты - $via");
            }
            $backend->handlePaymentSystemRequest();
            Phpr::$response->setHttpStatus(Phpr_Response::httpOk);
        } catch (Exception $ex) {
            Phpr::$response->setHttpStatus(Phpr_Response::httpBadRequest);
            Phpr::$errorLog->logException($ex, true);
        }
        die();
    }
}