<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ─── Health Check ────────────────────────────────────────
Route::get('/health', fn() => response()->json(['status' => 'ok', 'service' => 'ERP Consulvolt API']));

// ─── Rota raiz: redireciona para o frontend Next.js ──────
Route::get('/', fn() => response()->json(['message' => 'Use o frontend em http://localhost:3000']));
