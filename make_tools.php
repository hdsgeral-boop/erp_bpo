<?php
$tools = ['PurchaseConsultTool', 'CustomerConsultTool', 'SupplierConsultTool', 'TreasuryConsultTool', 'AccountingConsultTool', 'PayrollConsultTool', 'EmployeeConsultTool', 'AssetConsultTool', 'DashboardConsultTool', 'ReportConsultTool', 'DocumentConsultTool', 'SettingsConsultTool', 'CompanyConsultTool'];

foreach($tools as $tool) {
    $name = strtolower(str_replace('ConsultTool', '_consult', $tool));
    $desc = "Consult data related to " . str_replace('ConsultTool', '', $tool);
    
    $content = "<?php\n\nnamespace App\Services\AI\Tools;\n\nuse App\Services\AI\Contracts\ToolInterface;\n\nclass {$tool} implements ToolInterface\n{\n    public function getName(): string { return '{$name}'; }\n    public function getDescription(): string { return '{$desc}'; }\n    public function getParameters(): array { return ['type' => 'object', 'properties' => []]; }\n    public function execute(array \$arguments): mixed { return ['status' => 'not_implemented']; }\n}\n";
    
    file_put_contents(__DIR__ . '/app/Services/AI/Tools/' . $tool . '.php', $content);
}
echo "Stubs created.\n";
