<?php

namespace App\Providers;

use App\Models\Demande;
use App\Models\PurchaseRequest;
use App\Models\Rfq;
use App\Models\Supplier;
use App\Policies\DemandePolicy;
use App\Policies\PurchaseRequestPolicy;
use App\Policies\RFQPolicy;
use App\Policies\SupplierPolicy;
use App\Models\Offer;
use App\Policies\OfferPolicy;
use App\Models\PurchaseOrder;
use App\Policies\PurchaseOrderPolicy;
use App\Models\Delivery;
use App\Policies\DeliveryPolicy;
use App\Models\Invoice;
use App\Policies\InvoicePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Demande::class => DemandePolicy::class,
        PurchaseRequest::class => PurchaseRequestPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Rfq::class => RFQPolicy::class, // Ajouter cette ligne
        Offer::class => OfferPolicy::class,
        PurchaseOrder::class => PurchaseOrderPolicy::class,
        Delivery::class => DeliveryPolicy::class,
        Invoice::class => InvoicePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Définir la gate pour le dashboard achat explicitement
        Gate::define('view-purchase-dashboard', [RFQPolicy::class, 'viewPurchaseDashboard']);
    }
}
