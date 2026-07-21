<?php

namespace App\Services\AI\Tools;

use App\Services\AI\Contracts\ToolInterface;

class DashboardConsultTool implements ToolInterface
{
    public function getName(): string { return 'dashboard_consult'; }
    public function getDescription(): string { return 'Consult data related to Dashboard'; }
    public function getParameters(): array { return ['type' => 'object', 'properties' => []]; }
    public function execute(array $arguments): mixed { return ['status' => 'not_implemented']; }
}
