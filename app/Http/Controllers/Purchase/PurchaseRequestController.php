<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\PurchaseRequest;
use App\Models\Line;
use App\Models\Article;
use App\Notifications\PurchaseRequestSubmitted;
use App\Notifications\PurchaseRequestApproved;
use App\Notifications\PurchaseRequestRejected;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Models\User; 

class PurchaseRequestController extends Controller
{
    public function index()
    {
        $requests = PurchaseRequest::with(['user', 'validator'])
            ->where('user_id', Auth::id())
            ->orWhereHas('validator', function($q) {
                $q->where('id', Auth::id());
            })
            ->latest()
            ->paginate(10);

            $currentUser = auth()->user();
        return view('purchase.requests.index', compact('requests','currentUser'));
        
    }

    public function AllRequests()
    {
        $Allrequests = PurchaseRequest::with(['user', 'validator'])
            ->latest()
            ->paginate(10);

            $currentUser = auth()->user();
        return view('purchase.requests.allpurchase', compact('Allrequests','currentUser'));
        
    }

    public function create()
    {
        // $approvedArticles = Article::where('status', 'draft')->get();
        // return view('purchase.requests.create', compact('approvedArticles'));

        $approvedArticles = Article::where('status', 'approved')->get();
        return view('purchase.requests.create', compact('approvedArticles'));
    }

    public function store(Request $request)
    {

        $validated = $request->validate([
            'description' => 'required|string|min:10',
            'items' => 'required|array|min:1',
            'items.*.type' => 'required|in:existing,new',
            'items.*.article_id' => 'required_if:items.*.type,existing|nullable|exists:articles,id',
            'items.*.reference' => 'required_if:items.*.type,new',
            'items.*.designation' => 'required_if:items.*.type,new',
            'items.*.new_description' => 'required_if:items.*.type,new',
            'items.*.sn' => 'nullable|string',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $purchaseRequest = PurchaseRequest::create([
            'user_id' => Auth::id(),
            'description' => $validated['description'],
            'status' => 'pending',
        ]);

        foreach ($validated['items'] as $item) {
            if ($item['type'] === 'existing') {
                // Pour les articles existants
                Line::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'article_id' => $item['article_id'],
                    'quantity' => $item['quantity']
                ]);
            } else {
                // Pour les nouveaux articles
                $article = Article::create([
                    'reference' => $item['reference'],
                    'designation' => $item['designation'],
                    'description' => $item['new_description'],
                    'sn' => $item['sn'] ?? null,
                    'status' => 'draft',
                ]);

                Line::create([
                    'purchase_request_id' => $purchaseRequest->id,
                    'article_id' => $article->id,
                    'quantity' => $item['quantity']
                ]);
            }
        }

    // Notifier les directeurs
$directors = User::where('type', 'directeur')->get();

if ($directors->isEmpty()) {
    Log::warning('Aucun directeur trouvé pour notification');
} else {
    Log::info('Nombre de directeurs à notifier: ' . $directors->count());
    
    try {
        Notification::send($directors, new PurchaseRequestSubmitted($purchaseRequest));
        Log::info('Notifications envoyées avec succès');
    } catch (\Exception $e) {
        Log::error('Erreur envoi notifications: ' . $e->getMessage());
    }
}

    return redirect()->route('purchase.requests.show', $purchaseRequest)
        ->with('success', 'Demande d\'achat soumise avec succès');
    }

    public function show(PurchaseRequest $request)
    {
        $this->authorize('view', $request);
        
        return view('purchase.requests.show', compact('request'));
    }

    public function approve(PurchaseRequest $request)
    {
        $this->authorize('approve', $request);

        $request->update([
            'status' => 'approved',
            'validator_id' => Auth::id(),
            'validated_at' => now(),
        ]);

        // Notifier le demandeur
        $request->user->notify(new PurchaseRequestApproved($request));

        return redirect()->route('purchase.requests.show', $request)
            ->with('success', 'Demande approuvée avec succès');
    }

    public function reject(PurchaseRequest $request)
    {
        $this->authorize('approve', $request);

        $request->update([
            'status' => 'rejected',
            'validator_id' => Auth::id(),
        ]);

        // Notifier le demandeur
        $request->user->notify(new PurchaseRequestRejected($request));

        return redirect()->route('purchase.requests.show', $request)
            ->with('success', 'Demande rejetée avec succès');
    }
    
    public function pendingApproval()
    {
        $this->authorize('viewAny', PurchaseRequest::class);
        
        $pendingRequests = PurchaseRequest::with(['user', 'lines'])
            ->where('status', 'pending')
            ->latest()
            ->paginate(10);

        return view('purchase.requests.pending', compact('pendingRequests'));
    }
}