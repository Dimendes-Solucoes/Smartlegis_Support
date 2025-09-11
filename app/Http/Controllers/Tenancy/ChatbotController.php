<?php

namespace App\Http\Controllers\Tenancy;

use App\Http\Controllers\Controller;
use App\Services\ChatbotService;
use Inertia\Inertia;

class ChatbotController extends Controller
{
    public function __construct(
        protected ChatbotService $service
    ) {}

    public function index()
    {
        return Inertia::render('Tenancy/Chatbots/Index');
    }

    public function new()
    {
        return Inertia::render('Tenancy/Chatbots/ChatWindow');
    }

    public function show(int $id)
    {
        return Inertia::render('Tenancy/Chatbots/ChatWindow');
    }
}
