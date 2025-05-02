
<button
    id="sidebarToggle"
    class="fixed top-4 left-4 z-50 lg:hidden bg-white border border-gray-200 rounded-md shadow p-2"
    onclick="toggleSidebar()"
>
    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
    </svg>
</button>

<div id="sidebar" class="fixed top-0 left-0 z-40 h-full w-64 bg-white shadow-lg -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out">
    <div class="flex flex-col h-full p-4">
        <div class="flex justify-center py-6 border-b border-gray-100">
            <div class="flex items-center">
                <img src="{{ asset('images/logosomasteel.png') }}" alt="SomaSteel Logo" class="h-10 w-auto">
            </div>
        </div>

        <nav class="flex flex-col gap-1 mt-8">
            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('home') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                Profile
            </a>

            <a
                href="{{ route('demandes.index') }}"
                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('demandes-conge') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                    <line x1="16" y1="2" x2="16" y2="6"></line>
                    <line x1="8" y1="2" x2="8" y2="6"></line>
                    <line x1="3" y1="10" x2="21" y2="10"></line>
                </svg>
                Demandes Congé
            </a>

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('declaration-absences') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                    <path d="M14 2v6h6"></path>
                    <line x1="16" y1="13" x2="8" y2="13"></line>
                    <line x1="16" y1="17" x2="8" y2="17"></line>
                    <line x1="10" y1="9" x2="8" y2="9"></line>
                </svg>
                Déclaration des absences
            </a>

            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors {{ request()->routeIs('employees') ? 'bg-somasteel-orange/10 text-somasteel-orange' : 'text-secondary hover:bg-somasteel-orange/10 hover:text-somasteel-orange' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                    <circle cx="9" cy="7" r="4"></circle>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                </svg>
                Annuaire des Employés
            </a>
        </nav>

        <div class="mt-auto pt-6 border-t border-gray-100">
            <a
                href="{{ route('home') }}"
                class="flex items-center gap-3 rounded-md px-3 py-2 text-sm transition-colors text-somasteel-red hover:bg-red-50"
                onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                    <polyline points="16 17 21 12 16 7"></polyline>
                    <line x1="21" y1="12" x2="9" y2="12"></line>
                </svg>
                Logout
            </a>

            <form id="logout-form" action="{{ route('home') }}" method="POST" class="hidden">
                @csrf
            </form>
        </div>
    </div>
</div>

<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 z-30 lg:hidden hidden" onclick="toggleSidebar()"></div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');

        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }
</script>
