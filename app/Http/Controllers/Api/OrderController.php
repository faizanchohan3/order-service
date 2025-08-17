<?php

namespace App\Http\Controllers\Api;
use App\Mail\OrderPlacedMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\StoreOrderRequest;
use Illuminate\Support\Facades\Http;
use App\Models\Order;
use App\Events\OrderStatusUpdated;
use Illuminate\Support\Facades\Mail;
use App\Jobs\PublishOrderPlacedEvent;use Illuminate\Support\Facades\Log;
class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    public function myOrders(Request $request)
    {

        $userResponse = Http::withtoken($request->bearerToken())->get(env('USER_SERVICE_URL') . '/api/user');
        if ($userResponse->failed()) {
            return response()->json(['message', $userResponse->failed()], 0);

        }
        $user = $userResponse->json();
        $order = Order::where('user_id', $user['id'])->with('items')->latest()->get();
        return response()->json($order);
    }

    public function index()
    {
        //
    }
    public function getOrdersForRestaurant($restaurantId)
    {
        $orders = Order::where('restaurant_id', $restaurantId)
            ->with('items')
            ->latest()
            ->get();

        return response()->json($orders);
    }
    public function updateStatus(Request $request, $orderId) // <-- Yahan $order ko $orderId se badal dein
    {
        // Pehle, order ko manually dhoondein
        $order = Order::find($orderId);
        if (!$order) {
            return response()->json(['message' => 'Order not found.'], 404);
        }

        // Step 1: Request se naya status validate karein
        $validated = $request->validate([
            'status' => 'required|string|in:accepted,preparing,delivered,cancelled',
        ]);

        // --- Step 2: Authorization Check ---
        $userResponse = Http::withToken($request->bearerToken())->get(env('USER_SERVICE_URL') . '/api/user');
        if ($userResponse->failed()) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }
        $ownerId = $userResponse->json('id');

        $restaurantResponse = Http::get(env('RESTAURANT_SERVICE_URL') . '/api/restaurants/' . $order->restaurant_id);
        if ($restaurantResponse->failed()) {
            return response()->json(['message' => 'Could not verify restaurant ownership.'], 500);
        }
        $restaurantOwnerId = $restaurantResponse->json('user_id');

        if ($ownerId !== $restaurantOwnerId) {
            return response()->json(['message' => 'This action is forbidden.'], 403);
        }
        // --- Authorization Mukammal Hua ---

        // Step 3: Order ka status update karein
        $order->status = $validated['status'];
        $order->save();

        // -- Debugging ke liye Log add karein --
        Log::info("Broadcasting OrderStatusUpdated for Order ID: " . $order->id);

        event(new OrderStatusUpdated($order)    );


        // Step 5: Updated order wapis bhej dein
        return response()->json($order);
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreOrderRequest $request)
    {
        // -- Step 1: Authenticated User Get Karein (UserService se) --
        $userResponse = Http::withToken($request->bearerToken())->get(env('USER_SERVICE_URL') . '/api/user');
        if ($userResponse->failed()) {
            return response()->json(['message' => 'Failed to fetch user'], 401);
        }
        $user = $userResponse->json();

        $validatedData = $request->validated();

        try {
            // DB Transaction shuru karein taake agar koi error aye to sab kuch rollback ho jaye
            $order = DB::transaction(function () use ($validatedData, $user) {
                $totalAmount = 0;
                $orderItemsData = [];
                $socket=$user['socket_token'];
                $email=$user['email'];

                // -- Step 2: Har Item ki Price RestaurantService se Confirm Karein --
                foreach ($validatedData['items'] as $item) {
                    $url = env('RESTAURANT_SERVICE_URL') . '/api/restaurants/' . $validatedData['restaurant_id'] . '/menu-items/' . $item['menu_item_id'];
                    $menuItemResponse = Http::get($url);

                    if ($menuItemResponse->failed()) {
                        throw new \Exception('Menu item with ID ' . $item['menu_item_id'] . ' not found.');
                    }

                    $menuItem = $menuItemResponse->json();
                    $totalAmount += $menuItem['price'] * $item['quantity'];

                    $orderItemsData[] = [
                        'menu_item_id' => $item['menu_item_id'],
                        'quantity' => $item['quantity'],
                        'price' => $menuItem['price'],
                    ];
                }

                // -- Step 3: Order ko Database mein Create Karein --
                $order = Order::create([
                    'user_id' => $user['id'],
                    'restaurant_id' => $validatedData['restaurant_id'],
                    'total_amount' => $totalAmount,
                    'status' => 'pending',
                    'user_socket_token' => $socket,
                ]);
                Mail::to($email)->queue(new OrderPlacedMail($order));
                // -- Step 4: Order Items ko Database mein Create Karein --
                $order->items()->createMany($orderItemsData);

                return $order;
            });


            PublishOrderPlacedEvent::dispatch($order->load('items'));

            // -- Step 6: Naya Order wapis Bhejein --
            return response()->json($order->load('items'), 201);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create order.', 'error' => $e->getMessage()], 500);
        }
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
