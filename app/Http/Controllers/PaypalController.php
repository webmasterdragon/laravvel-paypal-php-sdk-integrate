<?php

namespace App\Http\Controllers;

require base_path().'/vendor/PayPalPHPSDK/sample/bootstrap.php';

use PayPal\Api\Amount;
use PayPal\Api\Details;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use ResultPrinter;
use Illuminate\Http\Request;

class PaypalController extends Controller
{
    public function index()
    {
        $payer = new Payer();
        $payer->setPaymentMethod("paypal");

// ### Itemized information
// (Optional) Lets you specify item wise
// information
        $item1 = new Item();
        $item1->setName('Ground Coffee 40 oz')
            ->setCurrency('USD')
            ->setQuantity(1)
            ->setPrice(7.5);
        $item2 = new Item();
        $item2->setName('Granola bars')
            ->setCurrency('USD')
            ->setQuantity(5)
            ->setPrice(2);

        $itemList = new ItemList();
        $itemList->setItems(array($item1, $item2));

// ### Additional payment details
// Use this optional field to set additional
// payment information such as tax, shipping
// charges etc.
        $details = new Details();
        $details->setShipping(1.2)
            ->setTax(1.3)
            ->setSubtotal(17.50);

// ### Amount
// Lets you specify a payment amount.
// You can also specify additional details
// such as shipping, tax.
        $amount = new Amount();
        $amount->setCurrency("USD")
            ->setTotal(20)
            ->setDetails($details);

// ### Transaction
// A transaction defines the contract of a
// payment - what is the payment for and who
// is fulfilling it.
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($itemList)
            ->setDescription("Payment description")
            ->setInvoiceNumber(uniqid());

// ### Redirect urls
// Set the urls that the buyer must be redirected to after
// payment approval/ cancellation.
        $baseUrl = getBaseUrl();
        $redirectUrls = new RedirectUrls();
        $redirectUrls->setReturnUrl("$baseUrl/paypal/success")
            ->setCancelUrl("$baseUrl/paypal/cancel");

// ### Payment
// A Payment Resource; create one using
// the above types and intent set to 'order'
        $payment = new Payment();
        $payment->setIntent("order")
            ->setPayer($payer)
            ->setRedirectUrls($redirectUrls)
            ->setTransactions(array($transaction));


// For Sample Purposes Only.
        $request = clone $payment;

// ### Create Payment
// Create a payment by calling the 'create' method
// passing it a valid apiContext.
// (See bootstrap.php for more on `ApiContext`)
// The return object contains the state and the
// url to which the buyer must be redirected to
// for payment approval


        $clientId = 'AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS';
        $clientSecret = 'EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL';
        $apiContext = getApiContext($clientId, $clientSecret);

        try {
            $payment->create($apiContext);
        } catch (Exception $ex) {
            // NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
            ResultPrinter::printError("Created Payment Order Using PayPal. Please visit the URL to Approve.", "Payment", null, $request, $ex);
            exit(1);
        }

// ### Get redirect url
// The API response provides the url that you must redirect
// the buyer to. Retrieve the url from the $payment->getApprovalLink()
// method
        $approvalUrl = $payment->getApprovalLink();

// NOTE: PLEASE DO NOT USE RESULTPRINTER CLASS IN YOUR ORIGINAL CODE. FOR SAMPLE ONLY
        ResultPrinter::printResult("Created Payment Order Using PayPal. Please visit the URL to Approve.", "Payment", "<a href='$approvalUrl' >$approvalUrl</a>", $request, $payment);

//        return $payment;
        return redirect($approvalUrl);
    }

    public function success(){
        print_r('Thank you');
    }
    public function cancel(){
        print_r('Woop');
    }
}
