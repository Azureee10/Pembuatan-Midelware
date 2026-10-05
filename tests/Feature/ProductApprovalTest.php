<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_shows_catalog_summary_and_review_queue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'penjual']);
        $this->createPendingProduct($seller);

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Tinjau 1 produk')
            ->assertSee(route('admin.products.pending'));
    }

    public function test_admin_review_page_separates_seller_and_store_owner_queues(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $seller = User::factory()->create(['role' => 'penjual', 'name' => 'Akun Penjual']);
        $owner = User::factory()->create(['role' => 'pemilik_toko', 'name' => 'Akun Pemilik Satu']);
        $secondOwner = User::factory()->create(['role' => 'pemilik_toko', 'name' => 'Akun Pemilik Dua']);
        $this->createPendingProduct($seller, ['name' => 'Produk Penjual Antrean']);
        $this->createPendingProduct($owner, ['name' => 'Produk Pemilik Satu']);
        $this->createPendingProduct($owner, ['name' => 'Produk Pemilik Satu Lainnya']);
        $this->createPendingProduct($secondOwner, ['name' => 'Produk Pemilik Dua']);

        $response = $this->actingAs($admin)
            ->get(route('admin.products.pending'))
            ->assertOk();

        $response->assertSeeInOrder([
            'id="seller-queue"',
            'Produk Penjual Antrean',
            'id="owner-queue"',
            'data-owner-id="' . $owner->id . '"',
            'Akun Pemilik Satu',
            'Produk Pemilik Satu',
            'Produk Pemilik Satu Lainnya',
            'data-owner-id="' . $secondOwner->id . '"',
            'Akun Pemilik Dua',
            'Produk Pemilik Dua',
        ], false)
            ->assertSee('2 produk menunggu')
            ->assertSee('1 produk menunggu');

        $this->assertSame(2, substr_count($response->getContent(), 'class="admin-owner-account"'));
    }

    public function test_admin_can_approve_all_pending_products_for_one_store_owner(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'pemilik_toko']);
        $otherOwner = User::factory()->create(['role' => 'pemilik_toko']);
        $seller = User::factory()->create(['role' => 'penjual']);
        $firstProduct = $this->createPendingProduct($owner, ['name' => 'Produk Owner Satu']);
        $secondProduct = $this->createPendingProduct($owner, ['name' => 'Produk Owner Dua']);
        $otherOwnersProduct = $this->createPendingProduct($otherOwner, ['name' => 'Produk Owner Lain']);
        $sellersProduct = $this->createPendingProduct($seller, ['name' => 'Produk Penjual']);

        $this->actingAs($admin)
            ->get(route('admin.products.pending'))
            ->assertOk()
            ->assertSee('Setujui Semua');

        $this->patch(route('admin.products.owners.approve-all', $owner))
            ->assertRedirect(route('admin.products.pending'))
            ->assertSessionHas('success', '2 produk milik ' . $owner->name . ' disetujui dan dapat tampil di katalog pembeli.');

        foreach ([$firstProduct, $secondProduct] as $product) {
            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'approval_status' => 'approved',
                'reviewed_by' => $admin->id,
            ]);
        }

        foreach ([$otherOwnersProduct, $sellersProduct] as $product) {
            $this->assertDatabaseHas('products', [
                'id' => $product->id,
                'approval_status' => 'pending',
            ]);
        }
    }

    public function test_seller_product_is_hidden_until_admin_approves_it(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $buyer = User::factory()->create(['role' => 'pembeli']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($seller)->post(route('products.store'), [
            'name' => 'Produk Baru Seller',
            'category' => 'Rumah',
            'description' => 'Produk yang diajukan untuk review',
            'price' => 250000,
            'stock' => 8,
            'is_active' => 1,
        ])->assertRedirect(route('products.index'));

        $product = Product::where('name', 'Produk Baru Seller')->firstOrFail();
        $this->assertSame('pending', $product->approval_status);
        $this->assertSame($seller->id, $product->seller_id);

        $this->actingAs($seller)
            ->get(route('products.index'))
            ->assertSee('Belum Aktif')
            ->assertSee('Menunggu');

        $this->actingAs($buyer)
            ->get(route('buyer.dashboard'))
            ->assertDontSee('Produk Baru Seller');

        $this->actingAs($admin)
            ->get(route('admin.products.pending'))
            ->assertOk()
            ->assertSee('Produk Baru Seller')
            ->assertSee($seller->name);

        $this->patch(route('admin.products.approve', $product))
            ->assertRedirect(route('admin.products.pending'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'approval_status' => 'approved',
            'reviewed_by' => $admin->id,
        ]);

        $this->actingAs($buyer)
            ->get(route('buyer.dashboard'))
            ->assertSee('Produk Baru Seller');
    }

    public function test_store_owner_can_submit_multiple_products_together(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik_toko']);

        $this->actingAs($owner)
            ->get(route('products.create'))
            ->assertOk()
            ->assertSee('Tambah produk sekaligus')
            ->assertSee('add-product-row');

        $this->post(route('products.store'), [
            'products' => [
                [
                    'name' => 'Keripik Pisang',
                    'category' => 'Makanan',
                    'price' => 12000,
                    'stock' => 15,
                    'is_active' => 1,
                ],
                [
                    'name' => 'Teh Melati',
                    'category' => 'Minuman',
                    'price' => 8000,
                    'stock' => 20,
                    'is_active' => 1,
                ],
            ],
        ])->assertRedirect(route('seller.dashboard'))
            ->assertSessionHas('success', '2 produk berhasil dikirim dan menunggu persetujuan admin.');

        $this->assertDatabaseCount('products', 2);
        $this->assertDatabaseHas('products', [
            'name' => 'Keripik Pisang',
            'seller_id' => $owner->id,
            'approval_status' => 'pending',
        ]);
        $this->assertDatabaseHas('products', [
            'name' => 'Teh Melati',
            'seller_id' => $owner->id,
            'approval_status' => 'pending',
        ]);
    }

    public function test_store_owner_can_return_to_batch_form_after_empty_submission(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik_toko']);

        $this->actingAs($owner)
            ->from(route('products.create'))
            ->post(route('products.store'), ['products' => []])
            ->assertRedirect(route('products.create'))
            ->assertSessionHasErrors('products');

        $this->get(route('products.create'))
            ->assertOk()
            ->assertSee('Periksa kembali data produk.')
            ->assertSee('Produk 1');

        $this->assertDatabaseCount('products', 0);
    }

    public function test_store_owner_product_list_redirects_to_dashboard(): void
    {
        $owner = User::factory()->create(['role' => 'pemilik_toko']);

        $this->actingAs($owner)
            ->get(route('products.index'))
            ->assertRedirect(route('seller.dashboard'));
    }

    public function test_admin_can_reject_a_pending_product(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $admin = User::factory()->create(['role' => 'admin']);
        $product = $this->createPendingProduct($seller);

        $this->actingAs($admin)
            ->patch(route('admin.products.reject', $product))
            ->assertRedirect(route('admin.products.pending'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'approval_status' => 'rejected',
            'reviewed_by' => $admin->id,
        ]);

        $this->actingAs(User::factory()->create(['role' => 'pembeli']))
            ->get(route('buyer.dashboard'))
            ->assertDontSee($product->name);
    }

    public function test_seller_edits_require_admin_approval_again(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $product = Product::create([
            'seller_id' => $seller->id,
            'name' => 'Produk Approved',
            'price' => 100000,
            'stock' => 4,
            'is_active' => true,
            'approval_status' => 'approved',
        ]);

        $this->actingAs($seller)
            ->put(route('products.update', $product), [
                'name' => 'Produk Diedit Seller',
                'category' => 'Baru',
                'description' => 'Perubahan menunggu review',
                'price' => 125000,
                'stock' => 4,
                'is_active' => 1,
            ])
            ->assertRedirect(route('products.index'));

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'approval_status' => 'pending',
            'reviewed_by' => null,
            'reviewed_at' => null,
        ]);
    }

    public function test_non_admin_cannot_review_products(): void
    {
        $seller = User::factory()->create(['role' => 'penjual']);
        $product = $this->createPendingProduct($seller);

        $this->actingAs($seller)
            ->get(route('admin.products.pending'))
            ->assertForbidden();

        $this->patch(route('admin.products.approve', $product))
            ->assertForbidden();
    }

    private function createPendingProduct(User $seller, array $attributes = []): Product
    {
        return Product::create(array_merge([
            'seller_id' => $seller->id,
            'name' => 'Produk Menunggu Review',
            'price' => 100000,
            'stock' => 5,
            'is_active' => true,
            'approval_status' => 'pending',
        ], $attributes));
    }
}
