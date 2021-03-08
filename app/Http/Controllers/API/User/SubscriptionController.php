<?php

namespace App\Http\Controllers\API\User;

use App\Http\Requests\CreateSubscriptionRequest;
use App\Http\Requests\DownloadInvoiceRequest;
use App\Http\Resources\UsersResource;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Auth;
use PDF;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\InvalidInvoice;
use Stripe\Exception\ApiErrorException;

class SubscriptionController extends Controller
{
    public function createSubscription(CreateSubscriptionRequest $request)
    {
        $user = auth()->user();
        $plan = Plan::findOrFail($request->get('plan_id'));

        // set stripe secret
        $stripe = Plan::setStripeSecret();

        // Retrieve Payment Method
        $paymentMethod = Plan::retrievePaymentMethod($request->get('payment_method_id'));

        if ($plan->is_trial == config($plan->constant . 'is_trial_code.yes'))
            $subscription = $user->newSubscription('default', $plan->stripe_plan_id)->trialDays($plan->trial_days)->create($paymentMethod);
        else
            $subscription = $user->newSubscription('default', $plan->stripe_plan_id)->create($paymentMethod);

        $subscription->update();

        $user = User::find($user->id); // get the user with latest subscription.

        $totalSubscriptions = $stripe->subscriptions->all(['plan' => $plan->stripe_plan_id, 'limit' => '1', 'include[]' => 'total_count']);
        $plan->total_subscriptions = $totalSubscriptions->total_count;
        $plan->update();

        return new UsersResource($user);
    }

    /**
     * get the subscriber invoice list.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Stripe\Collection|\Symfony\Component\HttpFoundation\BinaryFileResponse
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function getSubscriberInvoiceList(Request $request)
    {
        $data = [];
        $user = auth()->user();
        $invoices = $user->invoices();

        if ($request->filled('is_original'))
            return $invoices;

        if (!$invoices->isEmpty()) {
            foreach ($invoices as $invoice) {

                $planId = $planName = '';
                if ($invoice->lines->data[0]->plan) {
                    $planId = $invoice->lines->data[0]->plan->id;
                    $planName = $invoice->lines->data[0]->plan->nickname;
                }

                $data[] = [
                    'user_id' => (string)$user->id,
                    'email' => (string)$user->email,
                    'stripe_customer_id' => (string)$invoice->customer,
                    'invoice_id' => (string)$invoice->id,
                    'invoice_number' => $invoice->number,
                    'stripe_plan_id' => (string)$planId,
                    'plan_name' => (string)$planName,
                    'amount_due' => (string)Cashier::formatAmount($invoice->amount_due, $invoice->currency),
                    'amount_paid' => (string)Cashier::formatAmount($invoice->amount_paid, $invoice->currency),
                    'amount_remaining' => (string)Cashier::formatAmount($invoice->amount_remaining, $invoice->currency),
                    'currency' => (string)$invoice->currency,
                    'created' => (string)$invoice->created,
                    'status' => (string)$invoice->status,
                    'hosted_invoice_url' => $invoice->hosted_invoice_url,
                    'invoice_pdf' => $invoice->invoice_pdf,
                ];
            }
        }

        return User::customPaginationWithSearchAndSort($request, $data);
    }

    /**
     * List the payment method of the logged in users.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function listPaymentMethods(Request $request)
    {
        $user = auth()->user();

        $paymentMethods = $user->paymentMethods();
        $defaultPaymentMethod = $user->defaultPaymentMethod();

        $data = [];
        if (!$paymentMethods->isEmpty()) {
            foreach ($paymentMethods as $pm) {

                $isDefault = '0';
                if ($defaultPaymentMethod) {
                    if ($defaultPaymentMethod->id == $pm->id) {
                        $isDefault = '1';
                    }
                }

                $data[] = [
                    'id' => $pm->id,
                    'card' => 'XXXX XXXX XXXX ' . $pm->card->last4,
                    'card_last4' => $pm->card->last4,
                    'exp_month' => (string)$pm->card->exp_month,
                    'exp_year' => (string)$pm->card->exp_year,
                    'brand' => $pm->card->brand,
                    'country' => $pm->card->country,
                    'is_default' => $isDefault,
                    'is_default_text' => $isDefault == '1' ? 'Yes' : 'No',
                ];
            }
        }

        return User::customPaginationWithSearchAndSort($request, $data);
    }


    /**
     * Download invoice pdf.
     *
     * @param DownloadInvoiceRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function invoiceDownload(DownloadInvoiceRequest $request)
    {
        $user = auth()->user();

        try {
            $invoice = $user->findInvoice($request->get('invoice_id'));
            $stripeInvoice = $invoice->asStripeInvoice();

            if ($request->filled('is_original'))
                return $stripeInvoice;

            $pdf = PDF::loadView('download_invoice', compact('invoice'), compact('request', 'stripeInvoice'));

            $pdf->output();
            //$pdf->setFooter('{PAGENO}');
//            $dom_pdf = $pdf->getDomPDF();
//            $canvas = $dom_pdf->get_canvas();
//            //$x = $pdf->get_width() - 15 - 50;
//            $canvas->page_text(450, 820, $stripeInvoice->number . " - Page {PAGE_NUM}", null, 10, array(0, 0, 0)); // page number position bottom-center

            //$pdf->save('/home/hardik/invoice.pdf');
            return $pdf->stream('Invoice-' . $stripeInvoice->number . '.pdf');
            //return $pdf->stream('Invoice.pdf');

        } catch (ApiErrorException $exception) {
            return User::getError($exception->getMessage());
        } catch (InvalidInvoice $exception) {
            return User::getError("The invoice `{$request->get('invoice_id')}` does not belong to this user `{$user->id}`.");
        }
    }

    /**
     * logged in user's plans listing API.
     *
     * @param Request $request
     * @return \Illuminate\Pagination\LengthAwarePaginator|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function plansListing(Request $request)
    {
        $user = auth()->user(); // logged in user

        $data = [];
        if (!$user->subscriptions->isEmpty()) {
            foreach ($user->subscriptions as $subscription) {
                $data[] = Plan::subscriptionResponseCommon($subscription);
            }
        }

        return User::customPaginationWithSearchAndSort($request, $data);
    }

}
