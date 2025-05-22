{{-- resources/views/partials/sidebar.blade.php --}}

<button
    id="sidebarToggle"
    class="fixed top-4 left-4 z-50 lg:hidden bg-white border border-gray-200 rounded-md shadow p-2 focus:outline-none focus:ring-2 focus:ring-somasteel-orange"
    onclick="toggleSidebar()"
    aria-label="Basculer la barre latérale"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<div id="sidebar" class="fixed top-0 left-0 z-40 h-full w-64 bg-white shadow-lg -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out print:hidden">
    <div class="flex flex-col h-full p-4">
        <div class="flex justify-center py-6 border-b border-gray-100">
            <div class="flex items-center">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/logosomasteel.png') }}" alt="SomaSteel Logo" class="h-10 w-auto">
                </a>
            </div>
        </div>

        <nav class="flex-grow flex flex-col gap-1 mt-2 overflow-y-auto">
            @guest
                <a
                    href="{{ route('login') }}"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange transition-colors"
                >
                    <i class="fas fa-sign-in-alt fa-fw"></i>
                    Se connecter
                </a>
            @endguest

            @auth
                {{-- SECTION PROFIL & DEMANDES PERSONNELLES --}}
                <span class="px-1 py-2 text-xs text-gray-400 font-semibold uppercase tracking-wider">
                    Mon Espace
                </span>
                <a
                    href="{{ route('home') }}"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('home') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fa-fw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                    Profil
                </a>

                <a
                    href="{{ route('demandes.index') }}"
                    class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('demandes.index') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fa-fw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    Demandes Congé
                </a>

                {{-- SECTION GESTION DU PERSONNEL (RH / RESPONSABLE) --}}
                @if(Auth::user()->isRH() || Auth::user()->isResponsable() || Auth::user()->isAdmin())
                    <i class="border-b border-gray-200 w-full h-px my-3"></i>
                    <span class="px-1 py-2 text-xs text-gray-400 font-semibold uppercase tracking-wider">
                        Gestion Personnel
                    </span>
                    @if(Auth::user()->isRH() || Auth::user()->isResponsable())
                        <a
                            href="{{ route('absenceDec.index') }}"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('absenceDec.index') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fa-fw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                                <path d="M14 2v6h6"></path>
                                <line x1="16" y1="13" x2="8" y2="13"></line>
                                <line x1="16" y1="17" x2="8" y2="17"></line>
                                <line x1="10" y1="9" x2="8" y2="9"></line>
                            </svg>
                            Déclaration Absences
                        </a>
                    @endif
                    @if(Auth::user()->isRH() || Auth::user()->isAdmin())
                        <a
                            href="{{ route('annuaire.index') }}"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs(['annuaire.index', 'annuaire.depart', 'annuaire.employee']) ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fa-fw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                                <circle cx="9" cy="7" r="4"></circle>
                                <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                                <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                            </svg>
                            Annuaire Employés
                        </a>
                    @endif
                @endif


                {{-- SECTION GESTION D'ACHAT --}}
                {{-- Condition générale pour afficher la section Achat --}}
                @if(Auth::user()->isPurchase() || Auth::user()->isDirector() || Auth::user()->isMagasinier() || Auth::user()->isComptable() || Auth::user()->isAdmin() || Auth::check()) {{-- Auth::check() pour que tout utilisateur connecté voit au moins "Mes Demandes" --}}
                    <i class="border-b border-gray-200 w-full h-px my-3"></i>
                    <span class="px-1 py-2 text-xs text-gray-400 font-semibold uppercase tracking-wider">
                        Gestion d'Achat
                    </span>

                    {{-- Demandes d'Achat (accessible à tous les authentifiés pour créer/voir les siennes, rôles spécifiques pour tout voir) --}}
                    <a
                        href="{{ route('purchase.requests.index') }}"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors
                               {{ request()->routeIs(['purchase.requests.index', 'purchase.requests.create', 'purchase.requests.show', 'purchase.requests.allpurchase', 'purchase.requests.pending']) ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                    >
                        <i class="fas fa-file-alt fa-fw"></i>
                        Demandes d'Achat
                    </a>

                    {{-- Dashboard Achat & RFQ (Service Achat, Directeur, Admin) --}}
                    @if(Gate::allows('view-purchase-dashboard') || Gate::allows('viewAny', App\Models\RFQ::class))
                        @can('view-purchase-dashboard') {{-- Gate spécifique pour le dashboard --}}
                        <a
                            href="{{ route('purchase.rfq.dashboard') }}"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('purchase.rfq.dashboard') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                        >
                            <i class="fas fa-columns fa-fw"></i> {{-- Icône changée pour dashboard --}}
                            Dashboard Achat (RFQ)
                        </a>
                        @endcan
                        @can('viewAny', App\Models\RFQ::class) {{-- Policy pour la liste des RFQs --}}
                        <a
                            href="{{ route('purchase.rfqs.index') }}"
                            class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs(['purchase.rfqs.index', 'purchase.rfqs.show', 'purchase.rfq.create', 'purchase.rfqs.edit', 'rfqs.offers.create', 'rfqs.offers.edit']) ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                        >
                            <i class="fas fa-file-signature fa-fw"></i>
                            Demandes de Prix (RFQ)
                        </a>
                        @endcan
                    @endif

                    {{-- Bons de Commande & Historique (Service Achat, Directeur, Compta, Admin, Ouvrier pour ses propres POs) --}}
                    @can('viewAny', App\Models\PurchaseOrder::class)
                    <a
                        href="{{ route('purchase.orders.index') }}"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs(['purchase.orders.index', 'purchase.orders.show', 'purchase.orders.create', 'purchase.orders.edit']) && !request()->routeIs('purchase.orders.history') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                    >
                        <i class="fas fa-clipboard-list fa-fw"></i>
                        Bons de Commande
                    </a>
                    {{-- L'ouvrier peut aussi voir l'historique de SES commandes --}}
                    @if(Gate::allows('viewAny', App\Models\PurchaseOrder::class))
                    <a
                        href="{{ route('purchase.orders.history') }}"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('purchase.orders.history') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                    >
                        <i class="fas fa-history fa-fw"></i>
                        Historique Commandes
                    </a>
                    @endif
                    @endcan


                    {{-- Réceptions (Magasin, Achat, Admin) --}}
                    @canany(['viewDeliveryDashboard', 'viewAny'], App\Models\Delivery::class)
                    <a
                        href="{{ route('purchase.deliveries.dashboard') }}"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs(['purchase.deliveries.dashboard', 'purchase.deliveries.index', 'purchase.deliveries.create', 'purchase.deliveries.show']) ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                    >
                        <i class="fas fa-truck-loading fa-fw"></i>
                        Réceptions (Magasin)
                    </a>
                    @endcanany

                    {{-- Facturation (Compta, Achat, Admin) --}}
                    {{-- Facturation (Compta, Achat, Admin) --}}
                    @canany(['viewAccountingDashboard', 'viewAny'], App\Models\Invoice::class)
                     <a
                        href="{{ route('purchase.invoices.dashboard') }}"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs(['purchase.invoices.dashboard', 'purchase.invoices.index', 'purchase.invoices.create', 'purchase.invoices.show', 'purchase.invoices.edit', 'purchase.invoices.recordPaymentForm']) && !request()->routeIs('purchase.payments.history') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                    >
                        <i class="fas fa-file-invoice-dollar fa-fw"></i>
                        Facturation Fournisseurs
                    </a>
                    {{-- NOUVEAU LIEN POUR L'HISTORIQUE DES PAIEMENTS --}}
                    <a
                        href="{{ route('purchase.payments.history') }}" {{-- Nouvelle route à créer --}}
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('purchase.payments.history') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                    >
                        <i class="fas fa-money-check-alt fa-fw"></i>
                        Historique Paiements
                    </a>
                    @endcanany

                    {{-- Fournisseurs (Service Achat, RH, Admin) --}}
                    @can('viewAny', App\Models\Supplier::class)
                    <a
                        href="{{ route('purchase.suppliers.index') }}"
                        class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs(['purchase.suppliers.index', 'purchase.suppliers.create', 'purchase.suppliers.show', 'purchase.suppliers.edit']) ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
                    >
                        <i class="fas fa-users fa-fw"></i>
                        Fournisseurs
                    </a>
                    @endcan

                @endif {{-- Fin condition générale pour la section Achat --}}
            @endauth
        </nav>

        @auth
        <div class="mt-auto pt-6 border-t border-gray-100">
            <a
                href="#"
                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors text-red-600 hover:bg-red-100 hover:text-red-700"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fa-fw" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Déconnexion
            </a>

            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
        @endauth
    </div>
</div>

<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        // const toggleButton = document.getElementById('sidebarToggle'); // Pas utilisé ici

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Optionnel: fermer la sidebar si on clique sur un lien sur mobile
    document.querySelectorAll('#sidebar nav a').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 1024) { // lg breakpoint
                 const sidebar = document.getElementById('sidebar');
                 const overlay = document.getElementById('sidebarOverlay');
                 if (!sidebar.classList.contains('-translate-x-full')) {
                    sidebar.classList.add('-translate-x-full');
                    overlay.classList.add('hidden');
                 }
            }
        });
    });
</script>
