@extends('layouts.app')

@section('title', "Commande d'achat")

@section('content')
<!-- Alpine.js CDN pour l'interactivité -->
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<div class="px-4 py-6" x-data="bonDeCommande()">
    <!-- Barre de recherche et titre -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6 gap-4">
        <h1 class="text-2xl font-bold text-gray-800">Commande d'achat</h1>
        <div class="flex items-center gap-2">
            <input type="text" placeholder="Rechercher" class="border rounded-md px-3 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-somasteel-orange/50" />
            <button class="ml-2 p-2 bg-somasteel-orange text-white rounded-md"><i class="fa fa-search"></i></button>
        </div>
    </div>

    <!-- Tableau principal -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6 overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 font-semibold">ID</th>
                    <th class="px-4 py-2 font-semibold">ID RFQ</th>
                    <th class="px-4 py-2 font-semibold">Fournisseur</th>
                    <th class="px-4 py-2 font-semibold">Cicle</th>
                    <th class="px-4 py-2 font-semibold">Article</th>
                    <th class="px-4 py-2 font-semibold">Status</th>
                    <th class="px-4 py-2 font-semibold">Total</th>
                    <th class="px-4 py-2 font-semibold">Action</th>
                </tr>
            </thead>
            <tbody>
                <template x-for="(commande, idx) in commandes" :key="commande.id">
                    <tr class="border-b cursor-pointer hover:bg-orange-50" :class="selectedCommande === idx ? 'bg-orange-100' : ''" @click="selectedCommande = idx; selectedFournisseur = ''">
                        <td class="px-4 py-2" x-text="commande.id"></td>
                        <td class="px-4 py-2" x-text="commande.rfq"></td>
                        <td class="px-4 py-2" x-text="commande.fournisseur"></td>
                        <td class="px-4 py-2" x-text="commande.cicle"></td>
                        <td class="px-4 py-2" x-text="commande.article"></td>
                        <td class="px-4 py-2">
                            <span :class="commande.status === 'Validé' ? 'bg-green-100 text-green-800' : (commande.status === 'En cours' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') + ' px-2 py-1 rounded-full text-xs'" x-text="commande.status"></span>
                        </td>
                        <td class="px-4 py-2" x-text="commande.total"></td>
                        <td class="px-4 py-2 text-center">
                            <button class="text-gray-500 hover:text-somasteel-orange"><i class="fa fa-ellipsis-h"></i></button>
                        </td>
                    </tr>
                </template>
            </tbody>
        </table>
    </div>

    <!-- Menu déroulant Fournisseur + Articles commandés -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="mb-2 flex flex-col md:flex-row md:items-center gap-2">
            <label class="font-semibold text-gray-700 mr-2">Fournisseur :</label>
            <select class="border rounded-md px-3 py-2 w-64" x-model="selectedFournisseur">
                <option value="">Tous les fournisseurs</option>
                <template x-for="f in fournisseursUnique" :key="f">
                    <option :value="f" x-text="f"></option>
                </template>
            </select>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 font-semibold">Article</th>
                        <th class="px-4 py-2 font-semibold">Fournisseur</th>
                        <th class="px-4 py-2 font-semibold">Qté</th>
                        <th class="px-4 py-2 font-semibold">Prix</th>
                        <th class="px-4 py-2 font-semibold">Status</th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="article in filteredArticles" :key="article.nom">
                        <tr class="border-b">
                            <td class="px-4 py-2" x-text="article.nom"></td>
                            <td class="px-4 py-2" x-text="article.fournisseur"></td>
                            <td class="px-4 py-2" x-text="article.qte"></td>
                            <td class="px-4 py-2" x-text="article.prix"></td>
                            <td class="px-4 py-2">
                                <span :class="article.status === 'Received' ? 'bg-green-100 text-green-800' : (article.status === 'In Progress' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') + ' px-2 py-1 rounded-full text-xs'" x-text="article.status"></span>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Actions -->
    <div class="flex flex-wrap gap-2 justify-end">
        <button class="bg-gray-200 text-gray-700 px-4 py-2 rounded hover:bg-gray-300"><i class="fa fa-info-circle mr-1"></i> Détails</button>
        <button class="bg-green-200 text-green-800 px-4 py-2 rounded hover:bg-green-300"><i class="fa fa-check mr-1"></i> Réceptionner</button>
        <button class="bg-blue-200 text-blue-800 px-4 py-2 rounded hover:bg-blue-300"><i class="fa fa-print mr-1"></i> Imprimer</button>
        <button class="bg-orange-200 text-orange-800 px-4 py-2 rounded hover:bg-orange-300"><i class="fa fa-file-export mr-1"></i> Exporter</button>
    </div>
</div>

<script>
function bonDeCommande() {
    return {
        selectedCommande: 0,
        selectedFournisseur: '',
        get fournisseursUnique() {
            const articles = this.commandes[this.selectedCommande].articles;
            return [...new Set(articles.map(a => a.fournisseur))];
        },
        get filteredArticles() {
            const articles = this.commandes[this.selectedCommande].articles;
            if (!this.selectedFournisseur) return articles;
            return articles.filter(a => a.fournisseur === this.selectedFournisseur);
        },
        commandes: [
            {
                id: 'PO-001',
                rfq: 'RFQ039',
                fournisseur: 'Fournisseur1',
                cicle: 10,
                article: '6 Juin 2024',
                status: 'En cours',
                total: '120000DH',
                articles: [
                    { nom: 'Article 1', fournisseur: 'Fournisseur1', qte: '1 pcs', prix: '4778 DHS', status: 'Received' },
                    { nom: 'Article 2', fournisseur: 'Fournisseur2', qte: '4 pcs', prix: '7446 DHS', status: 'Received' },
                    { nom: 'Article 3', fournisseur: 'Fournisseur2', qte: '10 pcs', prix: '4900 DHS', status: 'In Progress' },
                ]
            },
            {
                id: 'PO-002',
                rfq: 'RFQ035',
                fournisseur: 'Fournisseur2',
                cicle: 9,
                article: '6 Juin 2024',
                status: 'En cours',
                total: '85000DH',
                articles: [
                    { nom: 'Article 4', fournisseur: 'Fournisseur2', qte: '2 pcs', prix: '3000 DHS', status: 'Received' },
                    { nom: 'Article 5', fournisseur: 'Fournisseur2', qte: '3 pcs', prix: '2000 DHS', status: 'In Progress' },
                ]
            },
            {
                id: 'PO-003',
                rfq: 'RFQ060',
                fournisseur: 'Fournisseur2',
                cicle: 5,
                article: '9 Juin 2024',
                status: 'Validé',
                total: '49000DH',
                articles: [
                    { nom: 'Article 6', fournisseur: 'Fournisseur2', qte: '5 pcs', prix: '10000 DHS', status: 'Received' },
                ]
            },
        ]
    }
}
</script>
@endsection 