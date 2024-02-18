<div class="flex items-center justify-between mt-4 text-center">
    <div>
        <h1>Pending</h1>
        <div class="text-3xl font-semibold" style="color: #f59e0b">{{ $data['pending'] }}</div>
    </div>

    <div>
        <h1>Delivered</h1>
        <div class="text-3xl font-semibold" style="color: #16a34a">{{ $data['delivered'] }}</div>
    </div>
    <div>
        <h1>Cancelled</h1>
        <div class="text-3xl font-semibold" style="color: #dc2626">{{ $data['cancelled'] }}</div>
    </div>
</div>
