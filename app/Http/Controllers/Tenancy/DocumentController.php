<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\DocumentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DocumentController extends Controller
{
    public function __construct(protected DocumentService $service)
    {
    }

    public function index(Request $request)
    {
        return Inertia::render('Tenancy/Documents/Index', [
            'documents' => $this->service->getAllDocuments($request),
            'filters' => $request->only(['search']), 
        ]);
    }

}