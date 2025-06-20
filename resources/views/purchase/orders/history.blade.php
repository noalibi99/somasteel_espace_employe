@extends('layouts.app')

@section('title', "Historique des Commandes d'Achat")

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row justify-between sm:items-center gap-4">
        <h1 class="text-2xl font-bold text-gray-900">Historique des Commandes d'Achat</h1>
        {{-- Peut-être un bouton pour retourner à la liste principale des BDC si différent --}}
        {{-- No need for a button here as we are already on the history page. DELETE INDEX--}}
        {{-- <a href="{{ route('purchase.orders.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-arrow-left mr-2"></i> Retour à la liste des BDC
        {{-- <a href="{{ route('purchase.orders.index') }}"
           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
            <i class="fas fa-clipboard-list mr-2"></i> Vue Standard BDC
        </a> --}}
    </div>

    @include('layouts.partials.flash_messages')

    {{-- Section des Filtres --}}
    <div class="bg-white shadow sm:rounded-lg p-4 md:p-6">
        <form method="GET" action="{{ route('purchase.orders.history') }}" class="space-y-4 md:space-y-0 md:grid md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6 md:gap-4 items-end">
            <div>
                <label for="search_term" class="block text-xs font-medium text-gray-700">Recherche</label>
                <input type="text" name="search_term" id="search_term" value="{{ $request->input('search_term') }}" placeholder="N° BDC, Fournisseur, Demandeur..."
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
            </div>
            <div>
                <label for="status" class="block text-xs font-medium text-gray-700">Statut</label>
                <select name="status" id="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
                    <option value="">Tous les statuts</option>
                    @foreach($statuses as $statusCode => $statusLabel)
                        <option value="{{ $statusCode }}" {{ $request->input('status') == $statusCode ? 'selected' : '' }}>{{ $statusLabel }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="supplier_id" class="block text-xs font-medium text-gray-700">Fournisseur</label>
                <select name="supplier_id" id="supplier_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
                    <option value="">Tous les fournisseurs</option>
                    @foreach($suppliers as $id => $name)
                        <option value="{{ $id }}" {{ $request->input('supplier_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            @if(!Auth::user()->isOuvrier() || Auth::user()->isAdmin() || Auth::user()->isDirector()) {{-- Masquer pour l'ouvrier simple car il ne voit que les siennes --}}
            <div>
                <label for="requester_id" class="block text-xs font-medium text-gray-700">Demandeur Initial</label>
                <select name="requester_id" id="requester_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
                    <option value="">Tous les demandeurs</option>
                    @foreach($requesters as $id => $name)
                        <option value="{{ $id }}" {{ $request->input('requester_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            @endif
            <div>
                <label for="date_from" class="block text-xs font-medium text-gray-700">Date Commande (De)</label>
                <input type="date" name="date_from" id="date_from" value="{{ $request->input('date_from') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
            </div>
            <div>
                <label for="date_to" class="block text-xs font-medium text-gray-700">Date Commande (À)</label>
                <input type="date" name="date_to" id="date_to" value="{{ $request->input('date_to') }}"
                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm p-2 focus:ring-somasteel-orange focus:border-somasteel-orange text-sm">
            </div>
            <div class="xl:col-span-2 flex items-end space-x-2">
                <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-somasteel-orange hover:bg-somasteel-orange/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-somasteel-orange">
                    <i class="fas fa-filter mr-2"></i> Filtrer
                </button>
                <a href="{{ route('purchase.orders.history') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50">
                    <i class="fas fa-times mr-2"></i> Effacer
                </a>
            </div>
        </form>
    </div>

    <div class="bg-white shadow overflow-hidden sm:rounded-lg">
        <div class="border-t border-gray-200">
            @if($purchaseOrders->isEmpty())
                <p class="px-6 py-10 text-center text-sm text-gray-500">Aucun bon de commande ne correspond à vos critères de recherche.</p>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200" id="purchaseOrdersHistoryTable">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">N° BDC</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Cmd.</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fournisseur</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Demandeur Initial</th>
                                <th scope="col" class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Montant Total</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut BDC</th>
                                <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Créateur BDC</th>
                                <th scope="col" class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Détails</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($purchaseOrders as $po)
                                <tr class="hover:bg-gray-50 transition-colors duration-150">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-somasteel-orange hover:text-somasteel-orange/80">
                                        <a href="{{ route('purchase.orders.show', $po) }}">{{ $po->po_number }}</a>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $po->order_date ? $po->order_date->format('d/m/Y') : 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-700">{{ $po->supplier->company_name ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $po->rfq->purchaseRequest->user->nom ?? 'N/A' }} {{ $po->rfq->purchaseRequest->user->prénom ?? '' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800 font-medium text-right">{{ number_format($po->total_po_price, 2, ',', ' ') }} {{-- devise --}}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full {{ $po->status_color == 'secondary' ? 'bg-gray-100 text-gray-800' : 'bg-'.$po->status_color.'-100 text-'.$po->status_color.'-800' }} {{ $po->status_color == 'teal' ? 'bg-teal-100 text-teal-800' : '' }} {{ $po->status_color == 'purple' ? 'bg-purple-100 text-purple-800' : '' }}">
                                            {{ $po->status_label }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $po->user->nom ?? 'N/A' }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-center">
                                        <a href="{{ route('purchase.orders.show', $po) }}" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-somasteel-orange hover:bg-somasteel-orange/10" title="Voir Détails du BDC">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('purchase.requests.show', $po->rfq->purchaseRequest) }}" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-blue-600 hover:bg-blue-100" title="Voir Demande d'Achat Originale">
                                            <i class="fas fa-file-alt"></i>
                                        </a>
                                         <a href="{{ route('purchase.rfqs.show', $po->rfq) }}" class="inline-flex items-center p-1.5 border border-transparent text-xs font-medium rounded-md text-purple-600 hover:bg-purple-100" title="Voir RFQ Associé">
                                            <i class="fas fa-file-signature"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @if ($purchaseOrders->hasPages())
                <div class="p-4 bg-white border-t border-gray-200 rounded-b-lg">
                    {{ $purchaseOrders->links('pagination::tailwind') }}
                </div>
                @endif
            @endif
        </div>
    </div>
</div>
@endsection
