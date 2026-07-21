<?php

namespace App\Services\AI\Tools;

use App\Services\AI\Contracts\ToolInterface;

class SupplierConsultTool implements ToolInterface
{
    public function getName(): string { return 'supplier_consult'; }
    public function getDescription(): string { return 'Consult data related to Supplier'; }
    public function getParameters(): array { return ['type' => 'object', 'properties' => []]; }
    public function execute(array $arguments): mixed { return ['status' => 'not_implemented']; }
}
