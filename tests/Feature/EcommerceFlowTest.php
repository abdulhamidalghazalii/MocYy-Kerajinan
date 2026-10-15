<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Order;
use App\Models\PaymentMethod;
use App\Models\Product;
use App\Models\ShippingMethod;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EcommerceFlowTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('private');
        Storage::fake('public');
    }

    public function test_storefront_home_and_catalog_render_successfully(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
        $response->assertSee('MocYy');
        $response->assertSee('Katalog Buket');

        $catalogResponse = $this->get(route('products.index'));
        $catalogResponse->assertStatus(200);
    }

    public function test_customer_can_view_product_detail(): void
    {
        $product = Product::first();
        $this->assertNotNull($product, 'Product must exist from seeder');

        $response = $this->get(route('products.show', $product->slug));
        $response->assertStatus(200);
        $response->assertSee($product->name);
        $response->assertSee('Kartu Ucapan Gratis');
    }

    public function test_cart_operations(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);

        $product = Product::where('stock', '>', 5)->first();

        // Add to cart
        $addResponse = $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 2,
            'greeting_card_text' => 'Selamat Wisuda!',
        ]);
        $addResponse->assertRedirect(route('cart.index'));

        // View Cart
        $viewResponse = $this->get(route('cart.index'));
        $viewResponse->assertStatus(200);
        $viewResponse->assertSee($product->name);
    }

    public function test_customer_cannot_access_admin_portal(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(403);

        $verifyResponse = $this->get(route('admin.payments.verification'));
        $verifyResponse->assertStatus(403);
    }

    public function test_admin_can_access_dashboard_and_verification_center(): void
    {
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $response = $this->get(route('admin.dashboard'));
        $response->assertStatus(200);

        $verifyResponse = $this->get(route('admin.payments.verification'));
        $verifyResponse->assertStatus(200);
    }

    public function test_checkout_and_payment_proof_upload_flow(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);

        $product = Product::where('stock', '>', 5)->first();
        $shipping = ShippingMethod::first();
        $payment = PaymentMethod::where('is_active', true)->first();

        // Put item in cart
        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
            'greeting_card_text' => 'Happy Birthday!',
        ]);

        // Place Order via Checkout with full required attributes
        $checkoutResponse = $this->post(route('checkout.store'), [
            'recipient_name' => 'Budi Santoso',
            'recipient_phone' => '081298765432',
            'full_address' => 'Jl. Tebet Raya No. 15, RT 02/05',
            'city' => 'Jakarta Selatan',
            'postal_code' => '12810',
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
            'greeting_card_note' => 'Selamat Ulang Tahun!',
            'customer_note' => 'Tolong bungkus rapi dengan pita emas',
        ]);

        $order = Order::where('user_id', $customer->id)->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('unpaid', $order->payment_status);

        $checkoutResponse->assertRedirect(route('customer.orders.payment', $order->id));

        // Customer views payment page
        $paymentPageResponse = $this->get(route('customer.orders.payment', $order->id));
        $paymentPageResponse->assertStatus(200);
        $paymentPageResponse->assertSee('MocYy, Kerajinan');

        // Upload Payment Proof
        $file = UploadedFile::fake()->create('transfer_proof.jpg', 150, 'image/jpeg');
        $uploadResponse = $this->post(route('customer.orders.upload_proof', $order->id), [
            'proof_file' => $file,
            'sender_account_name' => 'Budi Santoso',
            'sender_account_number' => '081298765432',
            'amount_paid' => $order->grand_total,
            'notes' => 'Transfer via DANA Merchant',
        ]);

        $uploadResponse->assertRedirect(route('customer.orders.show', $order->id));

        $order->refresh();
        $this->assertEquals('pending_review', $order->payment_status);
        $this->assertNotNull($order->latestPayment);
        $this->assertNotEmpty($order->latestPayment->proofs);

        // Admin Confirms Payment
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        $confirmResponse = $this->post(route('admin.payments.confirm', $order->id), [
            'notes' => 'Dana sudah masuk ke mutasi DANA merchant',
        ]);

        $confirmResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->order_status);

        // Idempotency: Second confirmation attempt must be rejected
        $secondConfirm = $this->post(route('admin.payments.confirm', $order->id));
        $secondConfirm->assertSessionHas('error');
    }

    public function test_admin_can_reject_payment_with_mandatory_reason(): void
    {
        $customer = User::where('role', 'customer')->first();
        $this->actingAs($customer);

        $product = Product::where('stock', '>', 5)->first();
        $shipping = ShippingMethod::first();
        $payment = PaymentMethod::where('is_active', true)->first();

        $this->post(route('cart.store'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $this->post(route('checkout.store'), [
            'recipient_name' => 'Siti Nurhaliza',
            'recipient_phone' => '081211112222',
            'full_address' => 'Jl. Kemang Raya No. 10',
            'city' => 'Jakarta Selatan',
            'shipping_method_id' => $shipping->id,
            'payment_method_id' => $payment->id,
        ]);

        $order = Order::where('user_id', $customer->id)->latest()->first();

        // Customer uploads invalid proof
        $file = UploadedFile::fake()->create('unreadable_receipt.jpg', 50, 'image/jpeg');
        $this->post(route('customer.orders.upload_proof', $order->id), [
            'proof_file' => $file,
            'sender_account_name' => 'Siti',
        ]);

        // Admin Rejects Payment
        $admin = User::where('role', 'admin')->first();
        $this->actingAs($admin);

        // Rejection without reason fails validation
        $invalidReject = $this->post(route('admin.payments.reject', $order->id), [
            'rejection_reason' => '',
        ]);
        $invalidReject->assertSessionHasErrors('rejection_reason');

        // Rejection with reason succeeds
        $validReject = $this->post(route('admin.payments.reject', $order->id), [
            'rejection_reason' => 'Foto bukti buram dan nominal tidak terbaca. Mohon unggah ulang screenshot resmi dari aplikasi DANA.',
        ]);
        $validReject->assertRedirect();

        $order->refresh();
        $this->assertEquals('rejected', $order->payment_status);
        $this->assertEquals('pending_payment', $order->order_status);
        $this->assertEquals('Foto bukti buram dan nominal tidak terbaca. Mohon unggah ulang screenshot resmi dari aplikasi DANA.', $order->latestPayment->rejection_reason);
    }
}
