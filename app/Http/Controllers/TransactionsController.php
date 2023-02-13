<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Models\Transactions;
use App\Http\Models\Products;
use App\Http\Models\Users;
use App\Http\Models\OrderProducts;
use App\Http\Models\RiderWallets;
use Illuminate\Support\Facades\Hash;
use App\Http\Utils\TransactionsStatus;
use App\Http\Utils\TransactionsType;
use App\Http\Utils\UsersIsBusy;
use App\Http\Utils\UsersType;
use App\Events\CreateTransactionsNotification;
use App\Events\CustomerTransactionStatusNotification;

class TransactionsController extends Controller
{
     public function index(Request $request)
     {   
         try {
            $result = Transactions::orderBy('created_at', 'desc')
            ->with(['merchant', 'customer', 'products.product', 'rider'])
            ->get();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function create(Request $request)
     {
         try {
             $result;
             $products = $request->input('products');

             // assigned as "CANCELLED" if the latest status is IDLE because it will
             // be overwritten by the most latest transaction to be created.
             $latestTransaction = Transactions::orderBy('id', 'desc')->first();
            if($latestTransaction) {
                if($latestTransaction->status === TransactionsStatus::IDLE) {
                    $result = Transactions::where('id', $latestTransaction->id)
                    ->update([
                       'status' => TransactionsStatus::CANCELLED,
                    ]);
                 }
            }
             // insert transaction
             $create = Transactions::create(array_filter([
                'customerId' => $request->input('customerId'),
                'riderId' => $request->input('riderId'),
                'merchantId' => $request->input('merchantId'),
                'pickUpDestination' => $request->input('pickUpDestination'),
                'dropOffDestination' => $request->input('dropOffDestination'),
                'status' => $request->input('status'),
            ]));

            // loop products then insert and update stocks
            foreach ($products as $product) {

                $productId = $product['productId'];

                // get products stocks
                $productStocks = Products::whereId($productId)
                ->first()->stocks;

                // deduct and update product stocks
                Products::whereId($productId)
                 ->update(['stocks' => ((int)$productStocks - (int)$product['quantity'])]);

                OrderProducts::create(array_filter([
                    'transactionId' => $create->id,
                    'categoryId' => $product["categoryId"],
                    'cost' => $product["cost"],
                    'price' =>  $product["price"],
                    'productId' => $product["productId"],
                    'quantity' => $product["quantity"]
                ]));
            }

            // get the latest entry then return
            $transaction = Transactions::with('customer', 'products')
            ->where('customerId', $request->input('customerId'))
            //Get last record details
            ->orderBy('id', 'desc')->first();
            // ->latest()
            // ->first();

            event(new CreateTransactionsNotification($transaction));

             return response()->json($transaction);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function update(Request $request, $id)
     {
         try {
             $result = Transactions::whereId($id)
                 ->update(array_filter([
                    'customerId' => $request->input('customerId'),
                    'riderId' => $request->input('riderId'),
                    'merchantId' => $request->input('merchantId'),
                    'productId' => $request->input('productId'),
                    'pickUpDestination' => $request->input('pickUpDestination'),
                    'dropOffDestination' => $request->input('dropOffDestination'),
                    'notes' => $request->input('notes'),
                    'serviceFee' => $request->input('serviceFee'),
                    'status' => $request->input('status'),
                 ]));
 
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function delete(Request $request, $id)
     {
         try {
             $result = Transactions::whereId($id)
             ->update([
                'status' => TransactionsStatus::REJECTED,
             ]);
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }
 
     public function getTransaction(Request $request, $id)
     {   
         try {
            
            $result = Transactions::whereId($id)
            ->with(['merchant', 'customer', 'products.product', 'rider'])
            ->first();
             return response()->json($result);
         } catch (Exception $e) {
             return response()->json($e);
         }
     }

     public function updateStatus(Request $request, $id)
     {
        try {
            $status = $request->input('status');
            $update = Transactions::whereId($id)
            ->update(array_filter([
                'status' => $status
            ]));

            $transaction;
            if($update)
            {
                $transaction = Transactions::whereId($id) -> first();
            }
                
            $message = '';
            if($status === TransactionsStatus::CONFIRMED) $message = "You've got the riders for your delivery";
            else if($status === TransactionsStatus::FOR_PICK_UP) $message = "Your items are ready for pick up";
            else if($status === TransactionsStatus::FOR_DROP_OFF) $message = "Your rider is on the way to the drop off location";
            else if($status === TransactionsStatus::DROPPED_OFF_LOCATION) $message = "Your rider just got arrived at the dropped off location";
            else if($status === TransactionsStatus::DELIVERED) $message = "Delivery Success! Thank you for using Beahero App. Have a nice day ahead!";
            else if($status === TransactionsStatus::CANCELLED) $message = "You have cancelled your delivery, But Why?";
            else if($status === TransactionsStatus::REJECTED) $message = "Sorry but you have been rejected. Please choose another rider";
            $data =  new \stdClass();
            $data->message = $message;
            $data->customerId = $transaction -> customerId;
            $data->status = $status;

            event(new CustomerTransactionStatusNotification($data));

             // if transaction status is set to DELIVERED, CANCELLED, REJECTED then set rider 'isBusy' status to false.
             if($status === TransactionsStatus::DELIVERED || $status === TransactionsStatus::CANCELLED || $status === TransactionsStatus::REJECTED) {
                $users = Users::whereId($transaction->riderId)
                ->update([  'isBusy' => UsersIsBusy::FALSE ]);
            }

            return response()->json($update);
        } catch (Exception $e) {
            return response()->json($e);
        }
     }

     public function transactionSuccessfullyDelivered(Request $request, $id)
     {
         try {
             $transactionId = $id;
             $serviceFee = $request->input('serviceFee');
             $riderId = $request->input('riderId');
             $walletDeduction = $request->input('walletDeduction');

             // update service fee and status
             $transaction = Transactions::whereId($transactionId)
                 ->update(array_filter([
                    'serviceFee' => $serviceFee,
                    'status' => TransactionsStatus::DELIVERED
                 ]));

            // update rider busy status to false, 
            // settings this to falls meaning that the rider has finished the the delivery.
            Users::whereId($riderId)
                ->update([
                'isBusy' => UsersIsBusy::FALSE,
            ]);

             // update wallet based on the deductions
             // safety: to make sure that if the transaction is empty do not update wallet
             if($transaction)
             {
                 $riderWallet = RiderWallets::where('riderId', $riderId)
                 ->decrement('amount', $walletDeduction);
             }

             $transactionCustomerId;
             if($transaction)
             {
                 $transactionCustomerId = Transactions::whereId($id) -> first() -> customerId;
             }
 
             // Notification that the delivery is successful
             $data =  new \stdClass();
             $data->message = "Delivery Success! Thank you for using Beahero App. Have a nice day ahead!";
             $data->customerId = $transactionCustomerId;
             $data->status = TransactionsStatus::DELIVERED;
             event(new CustomerTransactionStatusNotification($data));

             return response()->json([
                'trannsaction' => $transaction,
                'wallet' => $riderWallet
             ]);

         } catch (Exception $e) {
             return response()->json($e);
         }
     }
     
}
