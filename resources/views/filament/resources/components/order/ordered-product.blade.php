@php
    $totalAmount = 0;
    foreach ($getRecord()->ordered_p as $orderDetail) {
        $totalAmount += $orderDetail['selling'] * $orderDetail['quantity'];
        $orderNumber = $orderDetail->order;
    }
@endphp
<div class="mb-2 flex justify-end">
    <x-filament::button x-on:click="printPageArea('printPaper')" icon="tabler-printer">Print Invoice</x-filament::button>
</div>
<div class="flex justify-center">
    <div class="bg-white shadow-xl dark:bg-gray-950 p-8 rounded-xl w-[640px] h-[960px] printSection border" id="printPaper">
        <!-- Colored Header with Logo -->
        <div class="flex">
            <div class="text-white py-3 flex items-center justify-start dark:bg-gray-900"
                 style="height: 80px; width: 85%; background-color: #f6f7f8;">
                <div class="text-left" style="padding-left:1rem;">
                    <img src="{{ asset('bazar-logo.png') }}" alt="logo" style="width: 120px; height: auto">
                </div>
            </div>

            <!-- Ribbon Container -->
            <div class="text-white flex flex-col justify-end p-2"
                 style="background: #dc2626; width: 30%; height: 120px; margin-left: -15%;">
                <div class="text-center align-bottom">
                    <h1 class="text-2xl font-bold">Order Details</h1>
                </div>
            </div>
        </div>

        <!-- Company Details -->
        <div class="flex justify-between">
            <div class="text-xs text-gray-600 dark:text-gray-200 space-y-1">
                <h2 class="text-xl font-bold text-gray-800 dark:text-white">
                    {{ \App\Models\SiteSetting::first()?->site_name }}</h2>
                <p>{{ \App\Models\Setting::first()?->address_1 }}</p>
                <p>Email: {{ \App\Models\Setting::first()?->email }}</p>
                <p>Phone: {{ \App\Models\Setting::first()?->phone }}</p>
            </div>

            <table class="mt-6" style="width: 40%; height: 100px">
                <thead style="border-bottom:solid 2px #b8c3cc">
                <tr class="p-1">
                    <th class="text-sm text text-gray-500 text-left dark:text-white">Order</th>
                    <th class="text-sm text-gray-500 text-left">:</th>
                    <th class="text-sm font-semibold text-gray-800 dark:text-white text-right">
                        {{ $getRecord()->order }}</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="3" class="py-1"></td>
                </tr>
                </tbody>
                <tbody class="text-xs text-gray-500 dark:text-white">
                <tr class="p-1">
                    <td class="text-left">Payment Method</td>
                    <td class="text-left">:</td>
                    @php
                        $method = match ($getRecord()->order_method) {
                            1 => 'Razorpay',
                            2 => 'Cash on delivery',
                            3 => 'Stripe',
                            4 => 'Paypal',
                            5 => 'Flutterwave',
                            6 => 'Iyzico Payment',
                            7 => 'Bank Transfer'
                        };
                    @endphp
                    <td class="text-right">{{ $method }}</td>
                </tr>
                <tr class="p-1">
                    <td class="text-left">Payment Status</td>
                    <td class="text-left">:</td>
                    @php
                        $payment = match ($getRecord()->payment_done) {
                            1 => 'Paid',
                            0 => 'Unpaid',
                        };
                    @endphp
                    <td class="text-right">{{ $payment }}</td>
                </tr>
                <tr class="p-1">
                    <td class="text-left">Date</td>
                    <td class="text-left">:</td>
                    <td class="text-right">{{ $getRecord()->created_at }}</td>
                </tr>
                <tr class="p-1">
                    <td class="text-left">Total Amount</td>
                    <td class="text-left">:</td>
                    <td class="text-right">
                        {{ number_format($totalAmount) . ' ' . App\Models\Setting::first()?->currency_icon }}</td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="text-xs text-gray-600 dark:text-gray-200 mb-4">
            <h3 class="text-base font-semibold text-gray-600 dark:text-gray-200 tracking-tight mb-2">INVOICE TO</h3>
            <p class="text-lg font-semibold" style="color: #dc2626">{{ $getRecord()?->address->name }}</p>
            <p>
                @php
                    $address = \App\Models\UserAddress::find($getRecord()?->address->id);
                @endphp
                {{ $address->address_1 .
                    ', ' .
                    $address->address_2 .
                    ', ' .
                    $address->city .
                    '-' .
                    $address->zip .
                    ',' .
                    \Squire\Models\Region::where('code', $address->state)->first()?->name .
                    ',' .
                    \Squire\Models\Country::where('code', $address->country)->first()?->name }}
            </p>
            <p>Email: {{ $address?->email ?? 'N/A' }}</p>
            <p>Phone: {{ $address?->phone ?? 'N/A' }}</p>
        </div>

        <!-- Line Items Table -->
        <div class="mb-8 mt-4">
            <table class="w-full border-collapse text-sm">
                <tr class="text-xs"
                    style="background: #fee2e2; border-top: 1px solid #b91c1c; border-bottom:2px solid #b91c1c; color: #7f1d1d;">
                    <th class="text-left p-2 w-1/10" style="width: 100% !important;">Title</th>
                    <th class="text-left p-2 w-7/12">Delivery Fee</th>
                    <th class="text-left p-2 w-1/6">Qty</th>
                    <th class="text-left p-2 w-1/6">Price(؋)</th>
                    <th class="text-left p-2 w-1/6">Total(؋)</th>
                </tr>
                <tbody class="text-xs">
                @foreach ($getRecord()->ordered_p as $product)
                    <tr class="border-b border-gray-300 dark:border-gray-600">
                        <td class="p-2">
                            {{ substr($product->product->title, 0, 50) }}
                            <b>
                                {{ \App\Models\Helper\MailHelper::generatingAttribute($product) }}
                            </b>
                        </td>
                        <td class="p-2">
                            {{ App\Models\Setting::first()?->currency_icon  }}
                            {{ \App\Models\Helper\MailHelper::shippingPrice($product->shipping_place, $product->shipping_type) }}
                        </td>
                        <td class="p-2">
                            {{ $product['quantity'] }}
                        </td>
                        <td class="p-2">
                            {{ App\Models\Setting::first()?->currency_icon  }}
                            {{ $product['selling'] }}
                        </td>
                        <td class="p-2">
                            {{ App\Models\Setting::first()?->currency_icon  }}
                            {{ number_format($product['selling'] * $product['quantity']) }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <!-- Footer Notes -->
        <div class="text-gray-600 dark:text-gray-300 text-xs pt-6">
            <h4 class="font-semibold text-gray-700 dark:text-gray-100 mb-2" style="color: #dc2626">Terms &amp;
                Conditions:</h4>
            <div class="flex mt-2 justify-between py-2 border-t-2" style="border-color: #b8c3cc;">
                <div class="w-1/2">
                    <p>Payment is due within thirty (30) days from the date of invoice. Any discrepancies should be
                        reported within fourteen (14) days of receipt.</p>
                </div>
                <div class="w-1/2 text-right">
                    <p>Thank you for your business!</p>
                </div>
            </div>
        </div>
    </div>
</div>
