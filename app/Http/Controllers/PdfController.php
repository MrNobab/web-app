<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use NumberFormatter;

class PdfController extends Controller
{
    public function downloadInvoice(Invoice $invoice)
    {
        // Security: Ensure the user belongs to the store that owns this invoice
        // (Filament handles this mostly, but good to be safe)
        if (!auth()->user()->stores()->where('stores.id', $invoice->store_id)->exists()) {
             abort(403);
        }

        $invoice->load(['customer', 'items.product', 'store']);

        // Helper for "Amount in Words"
        $formatter = new NumberFormatter("en", NumberFormatter::SPELLOUT);
        $amountInWords = strtoupper($formatter->format($invoice->total));

        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice,
            'amountInWords' => $amountInWords . ' TAKA ONLY',
        ]);

        // 'stream' opens in browser, 'download' forces download
        return $pdf->stream('Invoice-' . $invoice->id . '.pdf');
    }
}