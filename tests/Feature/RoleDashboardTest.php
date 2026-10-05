<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_only_browse_active_in_stock_products(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $buyer = User::factory()->create(['role' => 'pembeli']);
        $availableProduct = $this->createProduct($seller, ['name' => 'Produk Tersedia']);
        $this->createProduct($seller, ['name' => 'Produk Nonaktif', 'is_active' => false]);
        $this->createProduct($seller, ['name' => 'Produk Habis', 'stock' => 0]);

        $this->actingAs($buyer)
            ->get(route('dashboard'))
            ->assertRedirect(route('buyer.dashboard'));

        $this->get(route('buyer.dashboard'))
            ->assertOk()
            ->assertSee($availableProduct->name)
            ->assertDontSee('Produk Nonaktif')
            ->assertDontSee('Produk Habis')
            ->assertDontSee('Tambah Produk');
    }

    public function test_buyer_can_browse_sellers_and_stores_separately(): void
    {
        $buyer = User::factory()->create(['role' => 'pembeli']);
        $seller = User::factory()->create(['role' => 'penjual', 'name' => 'Akun Penjual Direktori']);
        $owner = User::factory()->create(['role' => 'pemilik_toko', 'name' => 'Toko Pemilik Direktori']);
        $sellerProduct = $this->createProduct($seller, ['name' => 'Produk Penjual Direktori']);
        $ownerProduct = $this->createProduct($owner, ['name' => 'Produk Toko Direktori']);

        $this->actingAs($buyer)
            ->get(route('buyer.sellers'))
            ->assertOk()
            ->assertSee('Penjual Pilihan')
            ->assertSee('Akun Penjual Direktori')
            ->assertDontSee('Toko Pemilik Direktori')
            ->assertSee(route('buyer.stores'));

        $this->get(route('buyer.stores'))
            ->assertOk()
            ->assertSee('Toko Pilihan')
            ->assertSee('Toko Pemilik Direktori')
            ->assertDontSee('Akun Penjual Direktori')
            ->assertSee(route('buyer.sellers'));

        $this->get(route('buyer.dashboard', ['seller' => $owner->id]))
            ->assertOk()
            ->assertSee('Produk dari Toko Pemilik Direktori')
            ->assertSee($ownerProduct->name)
            ->assertDontSee($sellerProduct->name);
    }

    public function test_buyer_cannot_open_seller_management_routes(): void
    {
        $buyer = User::factory()->create(['role' => 'pembeli']);

        $this->actingAs($buyer)
            ->get(route('seller.dashboard'))
            ->assertForbidden();

        $this->get(route('products.index'))->assertForbidden();
    }

    public function test_seller_sees_only_own_products_and_records_a_sale(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $otherSeller = User::factory()->create(['role' => 'penjual']);
        $ownProduct = $this->createProduct($seller, ['name' => 'Produk Toko Saya', 'price' => 500000]);
        $otherProduct = $this->createProduct($otherSeller, ['name' => 'Produk Toko Lain']);

        $this->actingAs($seller)
            ->get(route('dashboard'))
            ->assertRedirect(route('seller.dashboard'));

        $this->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee($ownProduct->name)
            ->assertSee(route('products.edit', $ownProduct))
            ->assertSee(route('products.destroy', $ownProduct))
            ->assertDontSee($otherProduct->name)
            ->assertDontSee(route('products.edit', $otherProduct));

        $this->post(route('seller.sales.store'), [
            'product_id' => $ownProduct->id,
            'quantity' => 3,
        ])->assertRedirect(route('seller.dashboard'));

        $this->assertDatabaseHas('products', [
            'id' => $ownProduct->id,
            'stock' => $ownProduct->stock - 3,
        ]);
        $this->assertDatabaseHas('sales', [
            'seller_id' => $seller->id,
            'product_id' => $ownProduct->id,
            'quantity' => 3,
            'total_amount' => 1500000,
        ]);

        $this->get(route('seller.dashboard'))
            ->assertSee('Rp 1.500.000');
    }

    public function test_store_owner_can_access_store_dashboard_and_own_products(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik_toko']);
        $ownProduct = $this->createProduct($owner, ['name' => 'Produk Pemilik']);

        $this->actingAs($owner)
            ->get(route('dashboard'))
            ->assertRedirect(route('seller.dashboard'));

        $this->get(route('seller.dashboard'))
            ->assertOk()
            ->assertSee('Ruang Pemilik Toko')
            ->assertDontSee('Ruang Penjual')
            ->assertSee($ownProduct->name);

        $this->get(route('products.index'))
            ->assertRedirect(route('seller.dashboard'));
    }

    public function test_seller_cannot_edit_another_sellers_product(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $otherSeller = User::factory()->create(['role' => 'penjual']);
        $otherProduct = $this->createProduct($otherSeller);

        $this->actingAs($seller)
            ->get(route('products.edit', $otherProduct))
            ->assertForbidden();
    }

    public function test_seller_cannot_record_a_sale_above_available_stock(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $product = $this->createProduct($seller, ['stock' => 2]);

        $this->actingAs($seller)
            ->from(route('seller.dashboard'))
            ->post(route('seller.sales.store'), [
                'product_id' => $product->id,
                'quantity' => 3,
            ])
            ->assertRedirect(route('seller.dashboard'))
            ->assertSessionHasErrors('quantity');

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'stock' => 2,
        ]);
        $this->assertDatabaseCount('sales', 0);
    }

    private function createProduct(User $seller, array $attributes = []): Product
    {
        return Product::create(array_merge([
            'seller_id' => $seller->id,
            'name' => 'Contoh Produk',
            'category' => 'Umum',
            'description' => 'Produk untuk pengujian',
            'price' => 100000,
            'stock' => 10,
            'is_active' => true,
            'approval_status' => 'approved',
        ], $attributes));
    }
}
