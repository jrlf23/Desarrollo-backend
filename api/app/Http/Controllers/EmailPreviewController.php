<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use View;

class EmailPreviewController extends Controller
{
    public function __invoke()
    {
        $ValidateData=request()->validate
        ([
            'customer'=>['required','string'],
            "email"=>["required","email"],
            'payment_method'=>["required","in:1,2,3 "],
            'products'=>['required','array'],
            'products.*.name'=>['required','string', 'max:50'],
            'products.*.price'=>['required','numeric', 'gt:0'],
            'products.*.quantity'=>['required','gte:1', 'integer'],
        ]);


        $products=$ValidateData['products'];

        $productsData=[];
        $total=0;
        foreach ($products as $product)
        {
            $subtotal=$product['price']*$product['quantity'];
            $total+=$subtotal;

            $productsData[]=
            [
                'name'=>$product['name'],
                'quantity'=>$product['quantity'],
                'price'=>number_format($product['price'], 2),
                'subtotal'=> number_format($subtotal,2),
            ];
        }

        $data=
        [
            'customer'=>$ValidateData['customer'],
            'create_at'=>now()->format('Y-m-d H:i'),
            'email'=>$ValidateData['email'],
            'order_number'=>'RB'.now()->format('Y').now()->format('m').'-'.rand(1,100),
            'payment_method'=>match($ValidateData['payment_method'])
            {
                1=> 'Transferencia bancaria',
                2=> 'Contraentrega',
                3=> 'Tarjeta de credito',
            },

            'order_status'=>match($ValidateData['payment_method'])
            {
                1=> 'Pendiente de revision',
                2=> 'En proceso',
                3=> 'En proceso',
            },

            'products'=>$productsData,
            'total'=>number_format($total,2),
        ];


        return view('EmailPreview',$data);
    }
}
