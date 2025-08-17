<x-mail::message>
# Hello {{ $order->user->name }}

Your order has been successfully placed! 🎉

**Order ID:** {{ $order->id }}
**Total Amount:** ${{ $order->total_amount }}

## Items:
@foreach ($order->items as $item)
- {{ $item->menu_item_name }} x {{ $item->quantity }} - ${{ $item->price }}
@endforeach

<x-mail::button :url="route('orders.show', $order->id)">
View Order
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
