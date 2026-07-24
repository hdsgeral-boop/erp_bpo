<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class WebsiteController extends Controller
{
    /**
     * Exibe a página principal do website público / Landing Page de Captação
     */
    public function index()
    {
        $companiesCount = Company::count();
        return view('website.index', compact('companiesCount'));
    }

    /**
     * Exibe a página Sobre Nós (Carta de Apresentação Consulvolt Soluções)
     */
    public function about()
    {
        return view('website.about');
    }

    /**
     * Exibe a página de Serviços & Módulos ERP
     */
    public function services()
    {
        return view('website.services');
    }

    /**
     * Exibe a página dedicada de Contactos
     */
    public function contactView()
    {
        return view('website.contact');
    }

    /**
     * Exibe a página de Termos, Privacidade & Certificação AGT
     */
    public function terms()
    {
        return view('website.terms');
    }

    /**
     * Processa o formulário de contacto / pedido de demonstração
     */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'nullable|string|max:50',
            'company_name' => 'nullable|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        return back()->with('success', 'Obrigado pelo seu contacto! A equipa da Consulvolt Soluções entrará em contacto em breve.');
    }
}
