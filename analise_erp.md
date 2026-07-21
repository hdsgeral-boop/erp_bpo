# Análise Comparativa: Sistema Antigo vs erp_consulvolt

> **Antigo:** `c:\xampp\htdocs\ERP\antigo` — ERP_CONSULT v2.2 (JavaScript puro + IndexedDB no browser)
> **Atual:** `c:\xampp\htdocs\ERP\erp_consulvolt` — Laravel (PHP MVC + MySQL)

---

## 📐 Diferenças de Arquitetura

| Aspecto | Sistema Antigo (antigo) | Sistema Atual (erp_consulvolt) |
|---|---|---|
| **Tecnologia** | HTML + JavaScript puro, IndexedDB (offline) | Laravel 11 MVC + Blade, MySQL (servidor) |
| **Dados** | Armazenados no browser do utilizador | Base de dados MySQL centralizada |
| **Autenticação** | Login local (IndexedDB) | Laravel Auth com sessão + Sanctum API |
| **PDF** | html2pdf.js / pdfMake (client-side) | Barryvdh/DomPDF (server-side) |
| **Serviços** | Monolítico (um só ficheiro `app.js` + ui_*.js) | Camadas: Controllers → Services → Repositories |
| **Multi-empresa** | ✅ Sim (múltiplas empresas no mesmo browser) | ✅ Sim (tabela `companies`, `session('company_id')`) |
| **API Externa** | ❌ Não | ✅ Sim (API Sanctum para PowerBI, Mobile, RH) |

---

## 🟢 Módulos Presentes em Ambos os Sistemas

### ✅ Vendas e Faturação
O antigo tinha POS e faturação em JavaScript. O erp_consulvolt tem:
- `CommercialDocumentController` — Criar FT, FR, OR, PP, NC, ND, GT, GR com séries documentais configuráveis
- `SalesPOSController` — Frente de caixa com abertura/fecho de sessão
- `SaleController` — CRUD de vendas + anulação com reversão de stock
- Séries documentais configuráveis por tipo e empresa (`DocumentSeriesController`)
- Anulação com motivo obrigatório e reversão de stock automática ✅

### ✅ Compras
O antigo tinha um módulo de compras extenso. O erp_consulvolt tem fluxo completo:
- `PurchaseRequestController` — Pedidos internos (com aprovação/rejeição)
- `PurchaseOrderController` — Notas de encomenda (com aprovação)
- `PurchaseDeliveryController` — Receções de mercadoria
- `PurchaseInvoiceController` — Faturas de fornecedor
- Fluxo: **Pedido → Encomenda → Receção → Fatura** ✅ (mais completo que o antigo que era sem aprovação)

### ✅ RH e Salários
O antigo processava salários em JavaScript. O erp_consulvolt tem:
- `PayrollController` com `PayrollEngine` (motor de cálculo separado)
- Cálculo de **IRT** e **INSS** (empregado + empresa)
- Processamento em **modo simulação → confirmação** (wizard em 2 passos)
- **Estorno de processamento** com inversão de diários contábeis ✅
- Exportação AGT (ficheiro Excel formatado para a AGT)
- Recibos de vencimento em PDF (`Barryvdh/DomPDF`)
- Gestão de ausências, horas extra, benefícios, assiduidade ✅
- API REST para integração com sistemas externos (`/api/v1/hr/payroll`)

### ✅ Activos Imobilizados
O antigo tinha gestão básica. O erp_consulvolt tem arquitetura sofisticada:
- `AssetController` + `AssetService` + `AssetRepositoryInterface` (Repository Pattern)
- `AssetDepreciationService` (cálculo de amortizações)
- Filtros por categoria, departamento, funcionário, estado
- Upload de anexos por ativo (contratos, faturas de compra)
- Integração com Terceiros para vendor de compra

### ✅ Tesouraria
O antigo tinha tesouraria em JavaScript. O erp_consulvolt tem:
- `ReceiptController` — Recibos de clientes (RC) e Pagamentos a fornecedores (PG)
- `TreasuryService` — Liquidação parcial de documentos com atualização de `amount_paid`
- `CurrentAccountController` — Extrato completo por terceiro (débitos/créditos/saldo corrente)
- `ReconciliationController` — Reconciliação bancária com extrato importado
- `BankStatementController` — Importação de extratos bancários

### ✅ Contabilidade
O antigo tinha lançamentos manuais. O erp_consulvolt tem:
- `AccountingController` — Dashboard com balancete, trial balance por ano
- `JournalController` — Lançamentos contábeis (diários)
- `ChartOfAccountController` — Plano de contas configurável
- `AccountingMapController` — Mapeamento de rubricas salariais a contas
- Balancetes com agregação hierárquica de contas-mãe ✅
- Lançamentos automáticos na confirmação do processamento salarial ✅

### ✅ Logística / Inventário
- `InventorySessionController` — Sessões de inventário físico com contagem e revisão
- `WarehouseController` — Gestão de armazéns
- `StockMovementController` — Histórico de movimentos
- `WarehousePOSController` — POS de armazém (balcão e picking)
- `WaybillController` — Guias de saída
- `WarehouseReceiptController` — Validação de entradas

### ✅ SGD (Sistema de Gestão Documental)
- `DocumentController` — Gestão documental interna

### ✅ AI Agent
- `AiAgentController` + `AIService` — Agente de IA integrado (com gestão de conversas, histórico, providers configuráveis)
- `AiAdminController` — Painel de administração da plataforma IA
- Persistência de conversas na base de dados ✅ (o antigo era sessão de browser)

### ✅ Business Intelligence
- `BiController` com endpoint de dataset
- Integração com tabelas pivot (antigo usava PivotTable.js, novo usa dados do servidor)

---

## 🔴 Erros Identificados no erp_consulvolt

### Bug 1: `company_id` Hardcoded — Risco de Multi-empresa
**Ficheiros afetados:** `SaleController.php` (L138, L168), `PayrollController.php` (L123, L138, L165, L181), `ReconciliationController.php` (L29), `InventoryMovementController` (estimado).

```php
// ❌ Hardcoded em múltiplos sítios
'company_id' => 1,

// ✅ Deveria ser
'company_id' => session('company_id'),
```
Se o sistema funcionar com mais do que uma empresa, registos de empresa 2 vão ser associados à empresa 1.

---

### Bug 2: Race Condition na Numeração de Documentos
**Ficheiro:** `SaleController.php` (L128-129)

```php
$count = Sale::where('doc_type', $docType)->count();
$docNumber = $docType . ' ' . date('Y/m') . '/' . ($count + 1);
```
Se dois utilizadores criarem uma fatura em simultâneo, ambos podem obter o mesmo `$count` e gerar o **mesmo número de fatura** — violação da sequencialidade fiscal exigida pela AGT. O `CommercialDocumentController` usa `DocumentSeries` com locking, mas o `SaleController` (usado no POS) não.

---

### Bug 3: Dois Fluxos de Vendas Separados e Inconsistentes
O sistema tem **dois controllers de vendas paralelos** com lógica diferente:
- `SaleController` → usado para POS (tabela `sales`, `sale_items`)
- `CommercialDocumentController` + `SaleService` → usado para faturação (mesmas tabelas, lógica diferente)

O `SaleService` gera hash SAFT e usa séries. O `SaleController` não. Isto significa que faturas emitidas pelo POS **não têm hash** e **não usam séries documentais**, criando inconsistência nos dados para o SAF-T.

---

### Bug 4: `CurrentAccountController` Usa `session('company_id')` Sem Fallback
```php
$query = ThirdParty::where('company_id', session('company_id')); // retorna null se sem sessão
```
Se a sessão não tiver `company_id`, a query retorna `WHERE company_id = NULL` que em MySQL retorna 0 registos silenciosamente.

---

### Bug 5: Estorno Salarial por Pattern de String (Frágil)
**Ficheiro:** `PayrollController.php` (L213-215)

```php
Receipt::where('notes', 'like', 'Vencimento ' . $run->reference . '% (V' . $run->version . ')')
    ->where('status', 'PENDING')
    ->update(['status' => 'CANCELLED']);
```
O estorno depende de encontrar recibos pelo campo `notes` com um padrão de texto específico. Se o nome do funcionário contiver caracteres especiais, ou se a nota for editada manualmente, o estorno **não cancela os recibos de tesouraria correctamente**.

---

### Bug 6: `transactions->sortBy()` Espera Objeto com `->date->timestamp`
**Ficheiro:** `CurrentAccountController.php` (L157-159)

```php
$transactions = $transactions->sortBy(function($t) {
    return $t->date->timestamp; // ❌ $t->date é string, não Carbon
})->values();
```
`$t->date` vem de campos de eloquent que não são automaticamente cast para Carbon (não há `$casts` definido). `->timestamp` numa string PHP lança `Error: Call to a member function timestamp() on string`.

---

## 🟡 Funcionalidades do Sistema Antigo NÃO portadas para o erp_consulvolt

| Funcionalidade | Antigo | erp_consulvolt |
|---|---|---|
| **Modo Offline** | ✅ (IndexedDB, funciona sem internet) | ❌ Requer servidor |
| **Multi-empresa no mesmo login** | ✅ (switch de empresa no header) | ⚠️ Parcial (sessão por empresa) |
| **Favoritos / Atalhos** | ✅ (menu personalizável por utilizador) | ❌ Menu fixo |
| **Backup manual da BD** (export IndexedDB) | ✅ | ❌ Ausente (dependente do servidor) |
| **Migração de Dados** via formulário | ✅ (import de CSV/Excel) | ⚠️ `DataImportController` existe mas limitado |
| **Relatórios de RH avançados** | ✅ (mapas AGT, quadro pessoal, INSS) | ⚠️ Só exportação Excel |
| **Dashboard BI Pivot interativo** | ✅ (PivotTable.js interativo no browser) | ⚠️ Endpoint existe, sem UI pivot completa |
| **Imposto de Selo (1%)** | ✅ (rotina dedicada) | ⚠️ `AccountingRoutineController` tem `processStampDuty` mas limitado |
| **Encomendas de Clientes** | ✅ | ❌ Ausente (só encomendas a fornecedores) |
| **Config. de Terminais POS** | ✅ | ❌ Ausente |
| **Config. de Impressão POS** | ✅ | ❌ Ausente |
| **Coordenadas Bancárias de Colaboradores** | ✅ | ❌ Campo `bank_account` no modelo mas sem UI |
| **Relatório e Contas** (Balanço, DRE formal) | ✅ (gerado em PDF) | ❌ Só trial balance (balancete) |

---

## 🟢 O que o erp_consulvolt tem que o Antigo NÃO tinha

| Funcionalidade Nova | Porquê é Melhor |
|---|---|
| **Dados no servidor (MySQL)** | Segurança real, backup centralizado, acesso multi-device |
| **RBAC (Roles & Permissions)** | Permissões granulares por módulo (`sales.view`, `hr.view`, etc.) |
| **Séries Documentais configuráveis** | Conformidade legal (AGT) com numeração controlada |
| **Fluxo de Aprovação em Compras** | Pedido → Encomenda → Receção → Fatura (com aprovação) |
| **Reconciliação Bancária real** | Importação de extrato + matching com movimentos ERP |
| **API REST com Tokens Sanctum** | Integração com PowerBI, mobile, sistemas externos |
| **AI Agent persistente** | Conversas guardadas na BD, múltiplos providers |
| **Repository Pattern** (Assets, Sales) | Código testável, separação de concerns |
| **Processamento Salarial em 2 fases** | Simulação → Confirmação (evita erros irrecuperáveis) |
| **Estorno Salarial** | Reverter processamento e criar lançamento contábil inverso |
| **Amortizações de Ativos** com `AssetDepreciationService` | Cálculo automático de depreciação |
| **OCR** (pasta `Services/OCR`) | Reconhecimento de documentos (em desenvolvimento) |
| **Anexos** em Ativos, Funcionários, Terceiros | Gestão documental ligada a entidades |
| **Throttle de Login** | Proteção contra bruteforce |
| **Reset de Password** por email | Segurança em produção |

---

## ⚡ Prioridade de Correção no erp_consulvolt

### Crítico
1. 🔴 `company_id => 1` hardcoded — substituir por `session('company_id')` em todos os controllers
2. 🔴 Race condition na numeração do POS (`SaleController`) — usar `DocumentSeries` com `lockForUpdate()`
3. 🔴 `$t->date->timestamp` em `CurrentAccountController` — fazer cast para Carbon

### Alta Prioridade
4. 🟡 Unificar `SaleController` e `CommercialDocumentController` num único fluxo com `SaleService`
5. 🟡 Estorno salarial por `payroll_run_id` em vez de pattern de string

### Médio Prazo
6. 🟡 Completar UI de Relatório e Contas (Balanço/DRE formal)
7. 🟡 Completar Encomendas de Clientes
8. 🟡 Configuração de terminais e impressão POS
