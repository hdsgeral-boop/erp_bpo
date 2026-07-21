<?php

namespace App\Services\AI\Tools;

use App\Services\AI\Contracts\ToolInterface;

class DocumentConsultTool implements ToolInterface
{
    public function getName(): string { return 'document_consult'; }
    public function getDescription(): string { return 'Consult data related to Document'; }
    public function getParameters(): array { return ['type' => 'object', 'properties' => []]; }
    public function execute(array $arguments): mixed { return ['status' => 'not_implemented']; }
}
