<?php

namespace Tests\Feature\Admin;

use App\Mail\NewsletterBroadcastMail;
use App\Mail\ReservationCancelledMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Category;
use App\Models\Event;
use App\Models\EventType;
use App\Models\EventRegistration;
use App\Models\Product;
use App\Models\Newsletter;
use App\Models\Reservation;
use App\Models\ShopUser;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
use App\Livewire\Admin\Categories as CategoriesTable;
use App\Livewire\Admin\Products as ProductsTable;
use App\Livewire\Admin\Events as EventsTable;
use App\Livewire\Admin\EventTypes as EventTypesTable;
use App\Livewire\Admin\ShopUsers as ShopUsersTable;
use App\Livewire\Admin\StaffUsers as StaffUsersTable;
use App\Livewire\Admin\EventRegistrations as EventRegistrationsTable;
use App\Livewire\Admin\Reservations as ReservationsTable;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_users_are_redirected_from_admin_routes(): void
    {
        $this->get(route('admin.categories.index'))->assertRedirect(route('login'));
        $this->get(route('admin.products.index'))->assertRedirect(route('login'));
        $this->get(route('admin.events.index'))->assertRedirect(route('login'));
        $this->get(route('admin.event-types.index'))->assertRedirect(route('login'));
        $this->get(route('admin.shop-users.index'))->assertRedirect(route('login'));
        $this->get(route('admin.staff.index'))->assertRedirect(route('login'));
        $this->get(route('admin.event-registrations.index'))->assertRedirect(route('login'));
        $this->get(route('admin.reservations.index'))->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_view_category_datatable(): void
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['name' => 'Strategy Games']);

        Livewire::actingAs($user)
            ->test(CategoriesTable::class)
            ->assertStatus(200)
            ->assertSee($category->name);
    }

    public function test_product_datatable_renders_key_fields(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create([
            'name' => 'Premium Dice Set',
            'price' => 45.5,
            'cost_price' => 20,
            'stock' => 15,
            'is_featured' => true,
        ]);

        Livewire::actingAs($user)
            ->test(ProductsTable::class)
            ->assertStatus(200)
            ->assertSee('Premium Dice Set')
            ->assertSee(number_format(45.5, 2))
            ->assertSee(number_format(20, 2))
            ->assertSee((string) $product->stock);
    }

    public function test_admins_can_upload_product_images(): void
    {
        Storage::fake('public');
        $user = User::factory()->create();
        $category = Category::factory()->create();

        Livewire::actingAs($user)
            ->test(ProductsTable::class)
            ->call('openCreateModal')
            ->set('formData.name', 'Collector Edition')
            ->set('formData.price', 99.5)
            ->set('formData.cost_price', 50)
            ->set('formData.stock', 3)
            ->set('formData.category_id', $category->id)
            ->set('imageUpload', UploadedFile::fake()->image('collector.jpg'))
            ->call('saveRecord')
            ->assertHasNoErrors();

        $product = Product::firstWhere('name', 'Collector Edition');

        $this->assertNotNull($product);
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    public function test_event_datatable_formats_dates_and_flags(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->create([
            'name' => 'Launch Party',
            'is_online' => true,
            'is_published' => true,
        ]);

        Livewire::actingAs($user)
            ->test(EventsTable::class)
            ->assertStatus(200)
            ->assertSee('Launch Party')
            ->assertSee($event->start_at->format('Y-m-d'))
            ->assertSee(__('Yes'));
    }

    public function test_event_type_datatable_limits_description(): void
    {
        $user = User::factory()->create();
        $type = EventType::factory()->create([
            'name' => 'League',
            'description' => str_repeat('A', 120),
        ]);

        Livewire::actingAs($user)
            ->test(EventTypesTable::class)
            ->assertStatus(200)
            ->assertSee('League')
            ->assertSee(substr($type->description, 0, 80));
    }

    public function test_shop_users_datatable_lists_customers(): void
    {
        $user = User::factory()->create();
        $shopUser = ShopUser::factory()->create(['name' => 'Customer Zero']);

        Livewire::actingAs($user)
            ->test(ShopUsersTable::class)
            ->assertStatus(200)
            ->assertSee('Customer Zero')
            ->assertSee($shopUser->email);
    }

    public function test_staff_users_datatable_lists_staff(): void
    {
        $user = User::factory()->create();
        $staff = User::factory()->create([
            'name' => 'Support Agent',
            'email' => 'support@example.com',
        ]);

        Livewire::actingAs($user)
            ->test(StaffUsersTable::class)
            ->assertStatus(200)
            ->assertSee('Support Agent')
            ->assertSee($staff->email);
    }

    public function test_event_registrations_datatable_lists_entries(): void
    {
        $user = User::factory()->create();
        $registration = EventRegistration::factory()->create();

        Livewire::actingAs($user)
            ->test(EventRegistrationsTable::class)
            ->assertStatus(200)
            ->assertSee($registration->event->name)
            ->assertSee($registration->shopUser->name);
    }

    public function test_reservations_datatable_lists_entries(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'name' => 'Tabletop Fan',
            'status' => 'pending',
        ]);

        Livewire::actingAs($user)
            ->test(ReservationsTable::class)
            ->assertStatus(200)
            ->assertSee($reservation->name)
            ->assertSee($reservation->product->name)
            ->assertSee($reservation->code);
    }

    public function test_admins_can_confirm_reservations(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'status' => 'pending',
        ]);

        Livewire::actingAs($user)
            ->test(ReservationsTable::class)
            ->call('confirmReservation', $reservation->id)
            ->assertHasNoErrors()
            ->assertSee(__('Collected'))
            ->assertSee($reservation->code);

        $this->assertEquals('confirmed', $reservation->fresh()->status);
    }

    public function test_admins_can_cancel_reservations(): void
    {
        $user = User::factory()->create();
        $reservation = Reservation::factory()->create([
            'status' => 'pending',
        ]);

        config(['mail.default' => 'array']);
        Mail::fake();

        Livewire::actingAs($user)
            ->test(ReservationsTable::class)
            ->call('cancelReservation', $reservation->id)
            ->assertHasNoErrors()
            ->assertSee(__('Cancelled'))
            ->assertSee($reservation->code);

        $this->assertEquals('cancelled', $reservation->fresh()->status);

        Mail::assertSent(ReservationCancelledMail::class, function ($mail) use ($reservation) {
            return $mail->reservation->is($reservation);
        });
    }

    public function test_newsletter_datatable_lists_entries(): void
    {
        $user = User::factory()->create();
        $newsletter = Newsletter::factory()->create(['title' => 'Boletín Lunar']);

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Newsletters::class)
            ->assertStatus(200)
            ->assertSee('Boletín Lunar');
    }

    public function test_admins_can_send_newsletter_now(): void
    {
        $user = User::factory()->create();
        $newsletter = Newsletter::factory()->create(['status' => 'draft']);
        ShopUser::factory()->create([
            'email' => 'subscriber@example.com',
            'newsletter_opt_in' => true,
        ]);

        Mail::fake();

        Livewire::actingAs($user)
            ->test(\App\Livewire\Admin\Newsletters::class)
            ->call('sendNow', $newsletter->id)
            ->assertStatus(200);

        Mail::assertSent(NewsletterBroadcastMail::class);
        $this->assertEquals('sent', $newsletter->fresh()->status);
    }
}
