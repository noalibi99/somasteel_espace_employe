<?php

use App\Http\Controllers\ProfilController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

use App\Http\Middleware\IsRHmd;
use App\Http\Middleware\IsResponsable;
use App\Http\Middleware\IsRhOrResp;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DemandesController;
use App\Http\Controllers\DemandesCongeController;
use App\Http\Controllers\AnnuaireController;
use App\Http\Controllers\AbsenceController;
use App\Http\Controllers\ShiftController;


use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Purchase\PurchaseRequestController;
use App\Http\Controllers\Purchase\SupplierController;
use App\Http\Controllers\Purchase\RFQController;
use App\Http\Controllers\Purchase\OfferController;
use App\Http\Controllers\Purchase\PurchaseOrderController;
use App\Http\Controllers\Purchase\DeliveryController;
use App\Http\Controllers\Purchase\InvoiceController;






//I don't need registration
// Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
// Route::Put('/register', [RegisterController::class, 'update']);

//somasteel Blog

// Route::get('/SomaProduit', [BlogeController::class, 'produitIndex'])->name('bloge.produit');


Route::get('/test-email', function() {
    try {
        Mail::raw('Test email', function($message) {
            $message->to(env('TEST_EMAIL', 'fallback@example.com'))
            ->subject('Test SMTP');
        });
        return "Email envoyé avec succès!";
    } catch (\Exception $e) {
        return "Erreur: " . $e->getMessage();
    }
});


Route::middleware('auth')->group(function () {

    Route::prefix('purchase')->group(function () {
        // Routes manuelles
        Route::get('requests', [PurchaseRequestController::class, 'index'])->name('purchase.requests.index');
        Route::get('requests/All', [PurchaseRequestController::class, 'AllRequests'])->name('purchase.requests.allpurchase');

        Route::get('requests/create', [PurchaseRequestController::class, 'create'])->name('purchase.requests.create');
        Route::get('requests/pending', [PurchaseRequestController::class, 'pendingApproval'])
            ->name('purchase.requests.pending')
            ->middleware('can:viewAny,App\Models\PurchaseRequest');
        Route::post('requests', [PurchaseRequestController::class, 'store'])->name('purchase.requests.store');
        Route::get('requests/{request}', [PurchaseRequestController::class, 'show'])->name('purchase.requests.show');

        // Routes d'approbation
        Route::post('requests/{request}/approve', [PurchaseRequestController::class, 'approve'])
            ->name('purchase.requests.approve');
        Route::post('requests/{request}/reject', [PurchaseRequestController::class, 'reject'])
            ->name('purchase.requests.reject');

        // Routes suppliers
        Route::get('suppliers', [SupplierController::class, 'index'])->name('purchase.suppliers.index');
        Route::get('suppliers/create', [SupplierController::class, 'create'])->name('purchase.suppliers.create');
        Route::post('suppliers', [SupplierController::class, 'store'])->name('purchase.suppliers.store');
        Route::get('suppliers/{supplier}', [SupplierController::class, 'show'])->name('purchase.suppliers.show');
        Route::get('suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('purchase.suppliers.edit');
        Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('purchase.suppliers.update');
        Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('purchase.suppliers.destroy');

        // Route::get('requests/pending', [PurchaseRequestController::class, 'pendingApproval'])
        //     ->name('purchase.requests.pending')
        //     ->middleware('can:viewAny,App\Models\PurchaseRequest');

        // Route::get('requests/pending', [PurchaseRequestController::class, 'pending'])->name('purchase.requests.pending');


        Route::get('rfq/dashboard', [RFQController::class, 'dashboard'])->name('rfq.dashboard');
        Route::get('rfq', [RFQController::class, 'index'])->name('purchase.rfqs.index');
        Route::get('rfq/all', [RFQController::class, 'dashboard'])->name('purchase.rfq.dashboard');
        // Route pour afficher le formulaire de création de RFQ à partir d'une PurchaseRequest
        Route::get('purchase-requests/{purchaseRequest}/rfq/create', [RFQController::class, 'create'])->name('purchase.rfq.create');
        // Route pour stocker le RFQ créé
        Route::post('purchase-requests/{purchaseRequest}/rfq', [RFQController::class, 'store'])->name('purchase.rfq.store');
        // Route::post('rfq', [RFQController::class, 'store'])->name('purchase.rfq.store');

        // Resource routes pour RFQ (index, show, edit, update, destroy)
        // Route::resource('rfqs', RFQController::class)->except(['create', 'store']);

        // Route::resource('rfqs', RFQController::class)
        //     ->except(['create', 'store'])
        //     ->names('purchase.rfqs');

        // Route::get('rfqs/{rfq}/edit', [RFQController::class, 'edit'])
        //     ->name('purchase.rfqs.edit');

        // Index: Lister tous les RFQs
        Route::get('rfqs', [RFQController::class, 'index']) // Changé de 'rfq' à 'rfqs' pour la convention de liste
            ->name('purchase.rfqs.index');

        // Show: Afficher un RFQ spécifique
        Route::get('rfqs/{rfq}', [RFQController::class, 'show'])
            ->name('purchase.rfqs.show');

        // Edit: Afficher le formulaire de modification d'un RFQ
        Route::get('rfqs/{rfq}/edit', [RFQController::class, 'edit'])
            ->name('purchase.rfqs.edit');

        // Update: Mettre à jour un RFQ spécifique
        Route::put('rfqs/{rfq}', [RFQController::class, 'update'])
            ->name('purchase.rfqs.update');
        // Optionnel: Si vous utilisez PATCH pour les mises à jour partielles
        // Route::patch('rfqs/{rfq}', [RFQController::class, 'update']);

        // Destroy: Supprimer un RFQ spécifique
        Route::delete('rfqs/{rfq}', [RFQController::class, 'destroy'])
            ->name('purchase.rfqs.destroy');

        Route::post('rfqs/{rfq}/select-offer', [RFQController::class, 'selectOffer'])->name('rfq.selectOffer');
        Route::post('rfqs/{rfq}/deselect-offer', [RFQController::class, 'deselectOffer'])->name('rfq.deselectOffer');
        // Route::resource('rfqs', RFQController::class);

        Route::resource('rfqs.offers', OfferController::class)->except(['index', 'show']);

        // Routes pour PurchaseOrderController
        // La création est initiée à partir d'un RFQ
        Route::get('rfqs/{rfq}/purchase-order/create', [PurchaseOrderController::class, 'create'])->name('purchase.orders.create');
        Route::post('rfqs/{rfq}/purchase-order', [PurchaseOrderController::class, 'store'])->name('purchase.orders.store');

        // Routes pour les actions spécifiques sur un PO
        Route::get('orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'downloadPDF'])->name('purchase.orders.pdf');
        Route::post('orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'sendToSupplier'])->name('purchase.orders.send');

        // Resource routes standard pour les POs (index, show, edit, update, destroy)
        // Le 'create' et 'store' sont gérés par les routes spécifiques ci-dessus.
        Route::get('orders', [PurchaseOrderController::class, 'index'])->name('purchase.orders.index');
        Route::get('orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase.orders.show');
        // Route::resource('orders', PurchaseOrderController::class)
        //     ->except(['create', 'store'])
        //     ->names('purchase.orders');

        Route::get('orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])
            ->name('purchase.orders.show');

        // Edit: Afficher le formulaire de modification d'un bon de commande
        Route::get('orders/{purchaseOrder}/edit', [PurchaseOrderController::class, 'edit'])
            ->name('purchase.orders.edit');

        // Update: Mettre à jour un bon de commande spécifique
        Route::put('orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])
            ->name('purchase.orders.update');
        // Si vous utilisez PATCH pour les mises à jour partielles, vous pouvez aussi ajouter :
        // Route::patch('orders/{purchaseOrder}', [PurchaseOrderController::class, 'update']);

        // Destroy: Supprimer (ou annuler) un bon de commande spécifique
        Route::delete('orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])
            ->name('purchase.orders.destroy');

        // Actions spécifiques
        Route::get('orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'downloadPDF'])
            ->name('purchase.orders.pdf');

        Route::post('orders/{purchaseOrder}/send', [PurchaseOrderController::class, 'sendToSupplier'])
            ->name('purchase.orders.send');

        // Routes pour DeliveryController
        Route::get('deliveries/dashboard', [DeliveryController::class, 'dashboard'])
            ->name('purchase.deliveries.dashboard'); // Dashboard Magasin

        // Création d'une livraison pour un PO spécifique
        Route::get('orders/{purchaseOrder}/delivery/create', [DeliveryController::class, 'create'])
            ->name('purchase.deliveries.create');
        Route::post('orders/{purchaseOrder}/delivery', [DeliveryController::class, 'store'])
            ->name('purchase.deliveries.store');

        // Resource routes standard pour les livraisons
        Route::resource('deliveries', DeliveryController::class)
            ->except(['create', 'store']) // Gérés par les routes spécifiques ci-dessus
            ->names('purchase.deliveries'); // purchase.deliveries.index, .show, etc.


        // Routes pour InvoiceController
        Route::get('invoices/dashboard', [InvoiceController::class, 'dashboard'])
            ->name('purchase.invoices.dashboard'); // Dashboard Compta

        // Création d'une facture pour un PO spécifique
        Route::get('orders/{purchaseOrder}/invoice/create', [InvoiceController::class, 'create'])
            ->name('purchase.invoices.create');
        Route::post('orders/{purchaseOrder}/invoice', [InvoiceController::class, 'store'])
            ->name('purchase.invoices.store');

        // Actions spécifiques sur une facture
        Route::post('invoices/{invoice}/validate-action', [InvoiceController::class, 'validateInvoiceAction'])
            ->name('purchase.invoices.validateAction');
        Route::get('invoices/{invoice}/record-payment', [InvoiceController::class, 'recordPaymentForm'])
            ->name('purchase.invoices.recordPaymentForm');
        Route::post('invoices/{invoice}/record-payment', [InvoiceController::class, 'storePayment'])
            ->name('purchase.invoices.storePayment');


        // Resource routes standard pour les factures
        Route::resource('invoices', InvoiceController::class)
            ->except(['create', 'store']) // Gérés par les routes spécifiques ci-dessus
            ->names('purchase.invoices'); // purchase.invoices.index, .show, .edit, .update, .destroy

        Route::get('orders-history', [PurchaseOrderController::class, 'history'])
            ->name('purchase.orders.history');

        Route::get('payments-history', [InvoiceController::class, 'paymentsHistory'])
            ->name('purchase.payments.history');
    });

    Route::get('/bon-de-commande', function () {
        return view('commandes.show');
    })->name('bondecommande');



    Route::put('/home/updateEmail', [HomeController::class, 'updateEmail'])->name('home.update');
    Route::get('/home', [HomeController::class, 'index'])->name('home');
    Route::put('/home/update-picture', [HomeController::class, 'updatePicture'])->name('profile.updatePicture');
    Route::put('/home/updatePassword', [HomeController::class, 'updatePassword'])->name('home.updatePassword');
    Route::get('/home/profiles_imgs/{filename}', [HomeController::class ,'getProfileImage'])->name('profile.image');
    Route::delete('/home/delete-picture', [HomeController::class,'deleteProfilePicture'])->name('home.delete.picture');
    // dd('test');

    Route::get('/demandes', [DemandesController::class, 'index'])->name('demandes.index');
    Route::post('/demandes', [DemandesCongeController::class, 'store'])->name('demandesconge.store');
    Route::put('/demandes/{demande_id}/decide', [DemandesCongeController::class, 'update'])->name('demandeconge.update');
    Route::get('/demandes/download/{dc_id}', [DemandesCongeController::class, 'downloadConge'])->name('demandeConge.downloadConge');




    Route::middleware(IsRhOrResp::class)->group(function () {
        //Absence
        Route::get('/AbsDeclaration', [AbsenceController::class, 'index'])->name('absenceDec.index');
        Route::post('/AbsDeclaration/store', [AbsenceController::class, 'store'])->name('absenceDec.store');
        Route::post('/update-shift', [AbsenceController::class, 'updateShift'])->name('updateShift');
        Route::post('/attendance/declare', [AbsenceController::class, 'declareAttendance'])->name('attendance.declare');

        Route::put('/manage-teams/{id}', [AbsenceController::class, 'updateEquipe'])->name('teams.update');
        Route::post('/create-team', [AbsenceController::class, 'createEquipe']);
        Route::delete('/delete-team/{id}', [AbsenceController::class, 'deleteEquipe']);

        Route::get('/download-planning', [AbsenceController::class, 'downloadPlanning'])->name('download-planning');

        Route::get('/export', [AbsenceController::class, 'export'])->name('export.shifts');


    });
    //Annuaire routes
    Route::middleware(IsRHmd::class)->group(function () {
        Route::get('/Annuaire', [AnnuaireController::class, 'index'])->name('annuaire.index');//done
        Route::get('/Annuaire/{projet}/{depart}', [AnnuaireController::class, 'showDepartment'])->name('annuaire.depart');//done
        Route::get('/Annuaire/{projet}/{depart}/{employee_nom}/{employee_id}', [AnnuaireController::class,'showEmployee'])->name('annuaire.employee');//done
        Route::post('/Annuaire/create-department', [AnnuaireController::class,'storeService'])->name('annuaire.depart.store');
        Route::delete('/Annuaire/delete-department', [AnnuaireController::class,'deleteService'])->name('annuaire.depart.delete');

        // Route::get('/Annuaire/{depart}/{employee_nom}_{employee_id}/edit', [AnnuaireController::class, 'editEmp'])->name('annuaire.editEmployee');
        Route::put('/Annuaire/update/{projet}/{employee_id}', [AnnuaireController::class, 'updateEmp'])->name('annuaire.employee.update');
        Route::put('/Annuaire/updatePass/{employee_id}', [AnnuaireController::class, 'changePassword'])->name('annuaire.employee.changePassword');
        Route::delete('/Annuaire/delete/{employee_id}', [AnnuaireController::class, 'destroyEmp'])->name('annuaire.employee.destroy');
        Route::post('/Annuaire/{projet}/{depart}/register', [AnnuaireController::class, 'storeEmployee'])->name('annuaire.employee.register');
        Route::put('/setResponsable/{id}/{depart}/{projet}', [AnnuaireController::class, 'updateResponsible'])->name('annuaire.employee.setResponsable');


        Route::get('/shifts', [ShiftController::class, 'index'])->name('shifts.index');
        Route::get('/shifts/{shift}', [ShiftController::class, 'show']);
        Route::post('/shifts', [ShiftController::class, 'store']);
        Route::put('/shifts/{id}', [ShiftController::class, 'update']);
        Route::delete('/shifts/{id}', [ShiftController::class, 'destroy']);

        //password
        //Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
        //Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
        //Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
        //Route::post('/password/reset', [ResetPasswordController::class, 'reset']);
    });
});

Route::get('/download-app', [HomeController::class, 'getApp'])->name('download.app');

Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/home');
    } else {
        return view('auth.login');
    }
});

// Auth::routes();
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');


Route::get('/updateSolde', [ProfilController::class, 'updateSolde']);

// Fallback route for undefined URLs
Route::fallback(function () {
    return response()->view('not-found', [], 404);
});