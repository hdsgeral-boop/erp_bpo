<?php

namespace App\Services\AI\Tools;

use App\Services\AI\Contracts\ToolInterface;

class EmployeeConsultTool implements ToolInterface
{
    public function getName(): string { return 'employee_consult'; }
    public function getDescription(): string { return 'Consult data related to Employee'; }
    public function getParameters(): array { return ['type' => 'object', 'properties' => []]; }
    public function execute(array $arguments): mixed { return ['status' => 'not_implemented']; }
}
