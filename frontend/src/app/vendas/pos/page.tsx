'use client';

import { useState, useEffect } from 'react';
import api from '@/lib/api';
import { 
  Plus, 
  Minus, 
  ShoppingCart, 
  Trash2, 
  User, 
  CreditCard, 
  Banknote,
  Search,
  CheckCircle,
  AlertTriangle
} from 'lucide-react';

interface Product {
  id: number;
  name: string;
  price: number;
  tax_percent?: number;
  stock_qty: number;
  category?: { name: string };
}

interface CartItem extends Product {
  qty: number;
  discount: number;
}

export default function POSPage() {
  const [sessionActive, setSessionActive] = useState<boolean | null>(null);
  const [registers, setRegisters] = useState<any[]>([]);
  const [selectedRegisterId, setSelectedRegisterId] = useState('');
  const [openingBalance, setOpeningBalance] = useState('5000');
  
  // Active session data
  const [products, setProducts] = useState<Product[]>([]);
  const [categories, setCategories] = useState<any[]>([]);
  const [selectedCategory, setSelectedCategory] = useState<string>('');
  const [customers, setCustomers] = useState<any[]>([]);
  const [selectedCustomerId, setSelectedCustomerId] = useState<string>('CF'); // CF = Consumidor Final
  
  // Cart
  const [cart, setCart] = useState<CartItem[]>([]);
  const [searchQuery, setSearchQuery] = useState('');
  
  // Payment
  const [paymentMethod, setPaymentMethod] = useState<'CASH' | 'CARD'>('CASH');
  const [cashReceived, setCashReceived] = useState<string>('');
  
  const [loading, setLoading] = useState(true);
  const [submitting, setSubmitting] = useState(false);
  const [error, setError] = useState('');

  const checkSession = async () => {
    setLoading(true);
    try {
      const response = await api.get('/vendas/pos/session');
      if (response.data.session_active) {
        setSessionActive(true);
        setProducts(response.data.products || []);
        setCategories(response.data.categories || []);
        setCustomers(response.data.customers || []);
      } else {
        setSessionActive(false);
        setRegisters(response.data.registers || []);
        if (response.data.registers?.length > 0) {
          setSelectedRegisterId(response.data.registers[0].id.toString());
        }
      }
    } catch (err: any) {
      setError('Erro ao carregar o estado do POS.');
      console.error(err);
    } finally {
      setLoading(false);
    }
  };

  useEffect(() => {
    checkSession();
  }, []);

  const handleOpenSession = async (e: React.FormEvent) => {
    e.preventDefault();
    setSubmitting(true);
    setError('');
    try {
      const res = await api.post('/vendas/pos/open', {
        pos_register_id: Number(selectedRegisterId),
        opening_balance: Number(openingBalance)
      });
      if (res.data.success) {
        checkSession();
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Falha ao abrir turno de caixa.');
    } finally {
      setSubmitting(false);
    }
  };

  const addToCart = (product: Product) => {
    setCart((prevCart) => {
      const existing = prevCart.find((item) => item.id === product.id);
      if (existing) {
        return prevCart.map((item) =>
          item.id === product.id ? { ...item, qty: item.qty + 1 } : item
        );
      }
      return [...prevCart, { ...product, qty: 1, discount: 0 }];
    });
  };

  const updateQty = (id: number, delta: number) => {
    setCart((prevCart) =>
      prevCart
        .map((item) => {
          if (item.id === id) {
            const newQty = item.qty + delta;
            return newQty > 0 ? { ...item, qty: newQty } : null;
          }
          return item;
        })
        .filter((item): item is CartItem => item !== null)
    );
  };

  const removeFromCart = (id: number) => {
    setCart((prevCart) => prevCart.filter((item) => item.id !== id));
  };

  const getSubtotal = () => {
    return cart.reduce((acc, item) => acc + item.price * item.qty, 0);
  };

  const getTax = () => {
    return cart.reduce((acc, item) => {
      const itemSub = item.price * item.qty;
      const rate = item.tax_percent || 14; // Default VAT Angola is 14%
      return acc + itemSub * (rate / 100);
    }, 0);
  };

  const getTotal = () => {
    return getSubtotal() + getTax();
  };

  const handleCloseSession = async () => {
    if (!confirm('Deseja encerrar o seu turno de caixa agora?')) return;
    setSubmitting(true);
    try {
      const res = await api.post('/vendas/pos/close', {
        closing_balance: getTotal() // Simplificação
      });
      alert(res.data.message);
      checkSession();
    } catch (err: any) {
      alert(err.response?.data?.message || 'Erro ao fechar sessão.');
    } finally {
      setSubmitting(false);
    }
  };

  const handleCheckout = async () => {
    if (cart.length === 0) return;
    setSubmitting(true);
    setError('');

    const payload = {
      customer_id: selectedCustomerId,
      doc_type: 'FR', // Fatura-Recibo
      items: cart.map((item) => ({
        id: item.id,
        qty: item.qty,
        price: item.price,
        tax_percent: item.tax_percent || 14,
        discount: item.discount
      })),
      payments: [
        {
          method: paymentMethod,
          amount: getTotal()
        }
      ]
    };

    try {
      const res = await api.post('/vendas/pos/store', payload);
      if (res.data.success) {
        alert('Venda Concluída com Sucesso! Fatura emitida.');
        setCart([]);
        setCashReceived('');
      } else {
        setError(res.data.message || 'Erro ao concluir venda.');
      }
    } catch (err: any) {
      setError(err.response?.data?.message || 'Erro de comunicação com o servidor.');
    } finally {
      setSubmitting(false);
    }
  };

  if (loading) {
    return (
      <div className="flex items-center justify-center py-20">
        <div className="animate-spin h-8 w-8 border-4 border-blue-500 border-t-transparent rounded-full" />
      </div>
    );
  }

  // Se caixa estiver fechada, renderizar ecrã de Abertura de Turno
  if (sessionActive === false) {
    return (
      <div className="max-w-md mx-auto my-12">
        <div className="enterprise-card p-8 bg-white border border-slate-200 shadow-2xl">
          <div className="text-center mb-6">
            <div className="mx-auto h-12 w-12 rounded-xl bg-blue-50 border border-blue-150 flex items-center justify-center text-blue-600 mb-3">
              <ShoppingCart className="h-6 w-6" />
            </div>
            <h2 className="text-2xl font-extrabold text-slate-900 tracking-tight">Abertura de Caixa</h2>
            <p className="text-xs text-slate-400 font-semibold mt-1">Abra o seu turno para começar a vender no terminal de POS</p>
          </div>

          <form onSubmit={handleOpenSession} className="space-y-4">
            <div>
              <label className="block text-xs font-bold text-slate-500 uppercase mb-2">Selecione o Terminal</label>
              <select
                value={selectedRegisterId}
                onChange={(e) => setSelectedRegisterId(e.target.value)}
                className="enterprise-input"
              >
                {registers.map((reg) => (
                  <option key={reg.id} value={reg.id}>
                    {reg.name} ({reg.code})
                  </option>
                ))}
                {registers.length === 0 && (
                  <option>Nenhum terminal registado</option>
                )}
              </select>
            </div>

            <div>
              <label className="block text-xs font-bold text-slate-500 uppercase mb-2">Fundo de Maneio Inicial (Kz)</label>
              <input
                type="number"
                value={openingBalance}
                onChange={(e) => setOpeningBalance(e.target.value)}
                className="enterprise-input"
                placeholder="Introduza o valor em caixa"
              />
            </div>

            {error && (
              <div className="p-3 bg-red-50 border border-red-200 text-red-700 text-xs rounded-lg font-medium">
                {error}
              </div>
            )}

            <button
              type="submit"
              disabled={submitting}
              className="w-full enterprise-btn enterprise-btn-primary py-3"
            >
              Confirmar Abertura
            </button>
          </form>
        </div>
      </div>
    );
  }

  const filteredProducts = products.filter(p => {
    const matchesSearch = p.name.toLowerCase().includes(searchQuery.toLowerCase());
    const matchesCategory = selectedCategory ? p.category?.name === selectedCategory : true;
    return matchesSearch && matchesCategory;
  });

  return (
    <div className="flex flex-col xl:flex-row gap-6 h-[calc(100vh-8rem)]">
      {/* Catalogo de Artigos (Left Grid) */}
      <div className="flex-1 flex flex-col gap-4 overflow-hidden h-full">
        {/* Search & Category toolbar */}
        <div className="enterprise-card p-4 bg-white flex flex-col md:flex-row gap-4 items-center justify-between shadow-sm">
          <div className="relative flex-1 w-full">
            <Search className="absolute left-3 top-3 h-4 w-4 text-slate-400" />
            <input
              type="text"
              placeholder="Pesquisar por nome ou código..."
              value={searchQuery}
              onChange={(e) => setSearchQuery(e.target.value)}
              className="enterprise-input pl-9"
            />
          </div>
          
          <div className="flex gap-2 overflow-x-auto w-full md:w-auto pb-1 md:pb-0">
            <button
              onClick={() => setSelectedCategory('')}
              className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all border ${!selectedCategory ? 'bg-blue-600 text-white border-blue-500 shadow' : 'bg-slate-50 text-slate-600 border-slate-200'}`}
            >
              Todos
            </button>
            {categories.map((cat: any) => (
              <button
                key={cat.id}
                onClick={() => setSelectedCategory(cat.name)}
                className={`px-3 py-1.5 rounded-lg text-xs font-bold transition-all border shrink-0 ${selectedCategory === cat.name ? 'bg-blue-600 text-white border-blue-500 shadow' : 'bg-slate-50 text-slate-600 border-slate-200'}`}
              >
                {cat.name}
              </button>
            ))}
          </div>
        </div>

        {/* Products Grid */}
        <div className="flex-1 overflow-y-auto pr-1">
          <div className="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
            {filteredProducts.map((p) => (
              <div 
                key={p.id}
                onClick={() => addToCart(p)}
                className="enterprise-card p-4 bg-white hover:border-blue-300 hover:ring-2 hover:ring-blue-100 transition-all cursor-pointer flex flex-col justify-between h-36"
              >
                <div>
                  <span className="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-0.5 rounded border border-slate-200">
                    {p.category?.name || 'Geral'}
                  </span>
                  <h4 className="font-extrabold text-slate-800 text-sm mt-2 line-clamp-2 leading-tight">{p.name}</h4>
                </div>
                <div className="flex justify-between items-baseline mt-2">
                  <span className="text-xs text-slate-400 font-bold">Stock: {p.stock_qty}</span>
                  <span className="text-sm font-extrabold text-blue-600">
                    {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(p.price)}
                  </span>
                </div>
              </div>
            ))}
            {filteredProducts.length === 0 && (
              <div className="col-span-full enterprise-card p-12 text-center text-slate-400 bg-white">
                Nenhum artigo disponível no catálogo.
              </div>
            )}
          </div>
        </div>
      </div>

      {/* Cart & Checkout Terminal (Right Panel) */}
      <div className="w-full xl:w-96 enterprise-card bg-white border border-slate-200 shadow-xl flex flex-col justify-between h-full overflow-hidden">
        {/* Header */}
        <div className="p-4 border-b border-slate-200/80 flex items-center justify-between bg-slate-50">
          <div className="flex items-center gap-2">
            <ShoppingCart className="h-5 w-5 text-slate-600" />
            <h3 className="font-extrabold text-slate-800 text-base">Carrinho de Compras</h3>
          </div>
          <button 
            onClick={handleCloseSession}
            className="text-xs font-bold text-red-500 hover:text-red-700 bg-red-50 border border-red-150 px-2 py-1 rounded"
          >
            Fechar Caixa
          </button>
        </div>

        {/* Cart Item List */}
        <div className="flex-1 overflow-y-auto p-4 space-y-4">
          {cart.map((item) => (
            <div key={item.id} className="flex justify-between items-center border-b border-slate-100 pb-3">
              <div className="flex-1 min-w-0 pr-3">
                <h4 className="font-extrabold text-slate-800 text-xs truncate leading-snug">{item.name}</h4>
                <span className="text-[10px] text-slate-400 font-bold">
                  {new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(item.price)} cada
                </span>
              </div>
              
              <div className="flex items-center gap-2">
                <button 
                  onClick={() => updateQty(item.id, -1)}
                  className="p-1 hover:bg-slate-100 border border-slate-250 rounded text-slate-600"
                >
                  <Minus className="h-3 w-3" />
                </button>
                <span className="text-xs font-bold text-slate-800 w-6 text-center">{item.qty}</span>
                <button 
                  onClick={() => updateQty(item.id, 1)}
                  className="p-1 hover:bg-slate-100 border border-slate-250 rounded text-slate-600"
                >
                  <Plus className="h-3 w-3" />
                </button>
                <button 
                  onClick={() => removeFromCart(item.id)}
                  className="p-1.5 hover:bg-red-50 rounded text-red-500 ml-2"
                >
                  <Trash2 className="h-4 w-4" />
                </button>
              </div>
            </div>
          ))}
          {cart.length === 0 && (
            <div className="flex flex-col items-center justify-center py-20 text-slate-400 gap-2">
              <ShoppingCart className="h-10 w-10 text-slate-300" />
              <span className="text-xs font-medium">Terminal vazio. Adicione artigos.</span>
            </div>
          )}
        </div>

        {/* Customer & Totals Summary */}
        <div className="p-4 border-t border-slate-200 bg-slate-50 space-y-4">
          {/* Customer select */}
          <div>
            <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Cliente</label>
            <select
              value={selectedCustomerId}
              onChange={(e) => setSelectedCustomerId(e.target.value)}
              className="enterprise-input py-1.5 text-xs"
            >
              <option value="CF">Consumidor Final (CF)</option>
              {customers.map((c) => (
                <option key={c.id} value={c.id}>{c.name}</option>
              ))}
            </select>
          </div>

          {/* Payment Method */}
          <div>
            <label className="block text-[10px] font-bold text-slate-400 uppercase mb-1.5">Forma de Pagamento</label>
            <div className="grid grid-cols-2 gap-2">
              <button
                onClick={() => setPaymentMethod('CASH')}
                className={`py-2 rounded-lg text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer ${paymentMethod === 'CASH' ? 'bg-blue-600 text-white border-blue-500 shadow' : 'bg-white text-slate-600 border-slate-250'}`}
              >
                <Banknote className="h-4 w-4" />
                <span>Numerário</span>
              </button>
              <button
                onClick={() => setPaymentMethod('CARD')}
                className={`py-2 rounded-lg text-xs font-bold border flex items-center justify-center gap-1.5 transition-all cursor-pointer ${paymentMethod === 'CARD' ? 'bg-blue-600 text-white border-blue-500 shadow' : 'bg-white text-slate-600 border-slate-250'}`}
              >
                <CreditCard className="h-4 w-4" />
                <span>Multicaixa</span>
              </button>
            </div>
          </div>

          {/* Pricing calculations */}
          <div className="space-y-1.5 text-xs border-t border-slate-200/80 pt-3">
            <div className="flex justify-between font-semibold text-slate-500">
              <span>Subtotal:</span>
              <span>{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(getSubtotal())}</span>
            </div>
            <div className="flex justify-between font-semibold text-slate-500">
              <span>IVA (14%):</span>
              <span>{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(getTax())}</span>
            </div>
            <div className="flex justify-between text-base font-extrabold text-slate-900 border-t border-dashed border-slate-200 pt-2">
              <span>Total a Pagar:</span>
              <span>{new Intl.NumberFormat('pt-AO', { style: 'currency', currency: 'AOA' }).format(getTotal())}</span>
            </div>
          </div>

          {error && (
            <div className="p-2 bg-red-50 border border-red-200 text-red-700 text-[10px] rounded font-semibold text-center">
              {error}
            </div>
          )}

          {/* Complete sale button */}
          <button
            onClick={handleCheckout}
            disabled={cart.length === 0 || submitting}
            className="w-full enterprise-btn enterprise-btn-primary py-3 flex items-center justify-center gap-2 disabled:opacity-50"
          >
            <CheckCircle className="h-5 w-5" />
            <span>Confirmar Pagamento</span>
          </button>
        </div>
      </div>
    </div>
  );
}
