<?php
namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderPlacedMail;
use App\Models\User;

class OrderTest extends TestCase
{
    public function test_order_email_is_sent()
    {
        Mail::fake(); // Email sending fake kar deta hai

        $user = User::factory()->create();

        // Aapka order logic
        $order = [
            'user_id' => $user->id,
            'total_amount' => 100,
        ];

        // Mail send logic
        Mail::to($user->email)->send(new OrderPlacedMail($order));

        // Assertion: check kare ki email send hua
        Mail::assertSent(OrderPlacedMail::class, function ($mail) use ($user) {
            return $mail->hasTo($user->email);
        });
    }
}
