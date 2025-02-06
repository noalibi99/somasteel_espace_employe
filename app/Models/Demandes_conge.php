<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//pdf dump
use Dompdf\Dompdf;
use Dompdf\Options;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Storage;

class Demandes_conge extends Model 
{
    use HasFactory;
    
    protected $table = 'demandes_conge';
    
    protected $fillable = [
        'demande_id',
        'start_date',
        'end_date',
        'motif',
        'autre',
        'nom_pdf',
        'approuvé_responsable',
        'approuvé_directeur',
        'approuvé_rh',
        'to_responsable_id',
        'to_directeur_id'
    ];
    
    public function demande(){
        return $this->belongsTo(Demande::class);
    }
    
    public function isAcceptedByResp($demandeId){
        return $this->findOrFail($demandeId)->approuvé_responsable;
    }

    public function isAcceptedByDir($demandeId){
        return $this->findOrFail($demandeId)->approuvé_directeur;
    }

    public function isAcceptedByRH($demandeId){
        return $this->findOrFail($demandeId)->approuvé_rh;
    }
    
    protected function generatePDF($pdfData) {
        // Fetch demand data from database based on $id
        // $pdfData = Demande::findOrFail($pdfDataid);

        // Create options
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($options);
        
        // Load HTML content from Blade view
        $html = View::make('pdf/demandeConge', ['pdfData' => $pdfData])->render();
        // $html = Str::replaceArray('@if', ['<?php if'], $html);
        // $html = Str::replaceArray('@endif', ['<?php endif;'], $html);

        // Load HTML to Dompdf
        $dompdf->loadHtml($html);

        // Set paper size and orientation (A5, landscape)
        $dompdf->setPaper('A4', 'portrait');

        // Render PDF (generate PDF content)
        $dompdf->render();

        // Generate PDF file name
        $pdfFileName = 'DCg' . "_" . $pdfData['dcinfo']->id ."_". $pdfData['dcinfo']->nom ."_".
                        $pdfData['dcinfo']->prénom . '.pdf';//id de demndecg et nom et prenom
        // Save PDF to storage, relative path
        $pdfPath = storage_path('app/demandes_conge_pdf/' . $pdfFileName);
        //ENREGISTRAIT LE NOM DANS DB
        $this->where('id','=',$pdfData['dcinfo']->id)->update(['nom_pdf' => $pdfFileName]);
        file_put_contents($pdfPath, $dompdf->output());
        // Provide download link to user
        // return redirect()->route('demandes.index')->with('success', 'Demande acceptée et générée avec succès.');
        }


}