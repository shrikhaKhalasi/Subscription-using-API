<?php

namespace App\Traits;

use Stripe;
use Exception;
use Stripe\Exception\ExceptionInterface;

trait Stripes
{
    public function scopeCreateStripeCustomer($query, $data, $fromCashier = false)
    {
        $email = $data['email'];

        if ($fromCashier)
            $tokenID = $data['token_id'];
        else {
            $token = $this->createStripeToken($data);

            if (isset($token->id))
                $tokenID = $token->id;
            else
                return $token;
        }

        return Stripe\Customer::create([
            "description" => $data['name'],
            "source" => $tokenID,
            "email" => $email,
        ]);

    }

    /**
     *  Get Stripe Customer Details by it's id
     *
     * @param $query - Calling object
     * @param $stripeSecretKey - Stripe secret key
     * @param $id - Stripe customer id
     * @return array - Return stripe object
     * @throws Stripe\Exception\ApiErrorException
     */
    public function scopeGetByIdStripeCustomer($query, $stripeSecretKey, $id)
    {
        Stripe\Stripe::setApiKey($stripeSecretKey);
        $customer = Stripe\Customer::retrieve($id);
        return $this->getSingleCustomerStripeData($customer);
    }

    public function scopeGetAllStripeCustomer($query, $stripeSecretKey, $patientId)
    {
        Stripe\Stripe::setApiKey($stripeSecretKey);
        $customerData = Stripe\Customer::all(["email" => "customer" . $patientId . "@gmail.com"]);

        $response = [];

        foreach ($customerData as $customer)
            $response[] = $this->getSingleCustomerStripeData($customer);

        return $response;
    }

    /**
     *  Common Stripe customer stripe data
     *
     * @param $customer - Customer object
     * @return array - Return array of customer details
     */
    public function getSingleCustomerStripeData($customer)
    {
        return [
            'customer_id' => $customer->id,
            'card_id' => $customer->sources->data[0]->id,
            'last4' => $customer->sources->data[0]->last4,
            'exp_month' => (string)$customer->sources->data[0]->exp_month,
            'exp_year' => (string)$customer->sources->data[0]->exp_year,
        ];
    }

    public function scopeCreateStripeToken($query, $data)
    {
        try {
            return Stripe\Token::create([
                "card" => [
                    "number" => $data['card_number'],
                    "exp_month" => $data['exp_month'],
                    "exp_year" => $data['exp_year'],
                    "cvc" => $data['cvc'],
                ]
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /***
     *  Create stripe payment
     *
     * @param $query - Calling object
     * @param $stripeSecretKey - Stripe Secret key
     * @param $amount - Amount
     * @param $customer - Customer
     * @param string $description - Description
     * @param string $receiptEmail - Email
     * @return Stripe\Charge - Return Stripe object
     */
    public function scopeCreateStripeCharge($query, $stripeSecretKey, $amount, $customer, $description = '', $receiptEmail = '')
    {
        try {
            Stripe\Stripe::setApiKey($stripeSecretKey);
            return Stripe\Charge::create([
                "amount" => (int)($amount * 100),
                "currency" => "usd",
                "customer" => $customer,
                "description" => $description,
                "receipt_email" => $receiptEmail,
            ]);
        } catch (ExceptionInterface $e) {
            return $e->getJsonBody();
        }


    }

    public function scopeGetByIdStripeCharge($query, $stripeSecretKey, $id)
    {
        Stripe\Stripe::setApiKey($stripeSecretKey);
        return Stripe\Charge::retrieve($id);
    }

    /**
     *  Check Customer is Stripe Customer OR Not
     *
     * @param $query - Calling objct
     * @param $stripeSecretKey - Stripe secret key
     * @param $stripeCustomerId - Stripe Customer Id
     * @return bool - Return Stripe success object or False
     */
    public function scopeCheckCustomer($query, $stripeSecretKey, $stripeCustomerId)
    {
        try {
            $this->getByIdStripeCustomer($stripeSecretKey, $stripeCustomerId);
        } catch (ExceptionInterface $e) {
            return false;
        }
    }

    /**
     * Refund stripe payment
     *
     * @param $query - Calling object
     * @param $stripeSecretKey - Stripe Secret Key
     * @param $stripeChargeId - Stripe Charge ID
     * @param $amount - Amount
     * @return Stripe\Refund - Return Stripe Refund Object or Stripe Error Messages
     */
    public function scopeRefundStripePayment($query, $stripeSecretKey, $stripeChargeId, $amount)
    {
        try {
            Stripe\Stripe::setApiKey($stripeSecretKey);
            return Stripe\Refund::create([
                'charge' => $stripeChargeId,//required
                'amount' => (int)($amount * 100),//optional
                //'reason' => 'duplicate',// 'duplicate', 'fraudulent', 'requested_by_customer'
                //'metadata' => ["payment_id" => "101"],
            ]);
        } catch (ExceptionInterface $e) {
            return $e->getJsonBody();
        }
    }

    /**
     * create stripe payment method
     *
     * @param $query
     * @param $data - array of card values.
     * @return Stripe\PaymentMethod
     * @throws Stripe\Exception\ApiErrorException
     */
    public function scopeCreatePaymentMethod($query, $data, $card = 'card')
    {
        // $card accept ('au_becs_debit', 'card', 'fpx', 'ideal', 'sepa_debit')
        return \Stripe\PaymentMethod::create([
            'type' => $card,
            'card' => [
                "number" => $data['card_number'],
                "exp_month" => $data['exp_month'],
                "exp_year" => $data['exp_year'],
                "cvc" => $data['cvc'],
            ],
        ]);
    }

    /**
     * Retrieve stripe Ppyment method
     *
     * @param $query
     * @param $id - Payment method id
     * @return Stripe\PaymentMethod
     * @throws Stripe\Exception\ApiErrorException
     */
    public function scopeRetrievePaymentMethod($query, $id)
    {
        return \Stripe\PaymentMethod::retrieve(
            $id,
            []
        );
    }

}
