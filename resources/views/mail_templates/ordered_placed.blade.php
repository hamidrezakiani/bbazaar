@extends('layouts.email_layout')

@section('content')
    <p>Thank you {{ $setting->store_name }}.
        Testing 1
        <a href="{{ env('CLIENT_BASE_URL', config('env.url.CLIENT_BASE_URL')) }}/user/orders">Testing 2</a></p>

    <h3 class="mt-15">Your Order #{{ $order->order }}</h3>
    <p class="mb-20">Place On {{ $order->created }}</p>

    <table class="mb-10">
        <tr>
            <th class="pb-10">Ship To</th>
            <th class="pb-10">Order Method</th>
        </tr>

        <tr>
            <td style="width: 50%;">
                <div style="max-width: 300px;">
                    <h5 style="margin-bottom: 5px">{{ $order->address->name }}</h5>
                    <p>{{ $order->formatted_address }}</p>

                    @if($order->user)
                        <p>Email: {{ $order->user->email }}</p>
                    @endif

                    <p>Phone: {{ $order->address->phone }}</p>
                </div>
            </td>
            <td style="width: 50%;">{{ $order->order_method }}</td>
        </tr>
    </table><!--table-->


    <table style="background: #eee; border: 1px solid #ddd; border-bottom: none" class="mt-20 main-table border-tr">
        <tr>
            <th>title</th>
            <th>delivery_fee</th>
            <th>quantity</th>
            <th>price</th>
            <th>total</th>
        </tr>

        @foreach ($order->ordered_products as $op)
            <tr style="background: #fff">
                <td>
                    <b>{{ $op->product->title }}</b>
                    <span class="mt-5 f-9 block">{{ \App\Models\Helper\MailHelper::generatingAttribute($op) }}</span>
                </td>
                <td>
                    {{ $setting->currency_icon }}
                    {{ \App\Models\Helper\MailHelper::shippingPrice($op->shipping_place, $op->shipping_type) }}
                </td>
                <td>{{ $op->quantity }}</td>

                <td>{{ $setting->currency_icon }}{{ $op->selling }}</td>
                <td>{{ $setting->currency_icon }}{{ $op->selling * $op->quantity }}</td>
            </tr>

        @endforeach
    </table><!--table-->

    <table class="border-tr td-right-align footer-table"
           style="border: 1px solid #ddd; background: #eee; ">
        <tr>
            <td style="width: 630px" >Subtotal</td>
            <td style="width: 70px;">{{ $setting->currency_icon }}{{ $order->calculated_price['subtotal'] }}</td>
        </tr>
        <tr>
            <td>shipping_cost</td>
            <td>{{ $setting->currency_icon }}{{ $order->calculated_price['shipping_price'] }}</td>
        </tr>

        @if ((int) $order->calculated_price['bundle_offer'] > 0)
            <tr>
                <td>bundle_offer</td>
                <td>{{ $setting->currency_icon }}{{ $order->calculated_price['bundle_offer'] }}</td>
            </tr>
        @endif

        @if ((int) $order->calculated_price['voucher_price'] > 0)
            <tr>
                <td>voucher</td>
                <td>{{ $setting->currency_icon }}{{ $order->calculated_price['voucher_price'] }}</td>
            </tr>
        @endif

        @if ((int) $order->calculated_price['tax'] > 0)
            <tr>
                <td>lang.tax</td>
                <td>{{ $setting->currency_icon }}{{ $order->calculated_price['tax'] }}</td>
            </tr>
        @endif

        <tr>
            <td>total</td>
            <td>{{ $setting->currency_icon }}{{ $order->calculated_price['total_price'] }}</td>
        </tr>
    </table>
@stop
