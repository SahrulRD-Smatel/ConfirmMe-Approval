<?php

namespace Database\Seeders;

use App\Models\Budget;
use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentYear = date('Y');
        $departments = [
            [
                'name' => 'Budget Operasional IT',
                'code' => 'BUD-IT-' . $currentYear,
                'department' => 'IT',
                'total_amount' => 500000000,
                'used_amount' => 150000000,
                'committed_budget' => 75000000,
            ],
            [
                'name' => 'Budget Operasional HR',
                'code' => 'BUD-HR-' . $currentYear,
                'department' => 'Human Resources',
                'total_amount' => 300000000,
                'used_amount' => 80000000,
                'committed_budget' => 50000000,
            ],
            [
                'name' => 'Budget Sales & Marketing',
                'code' => 'BUD-SLS-' . $currentYear,
                'department' => 'Sales',
                'total_amount' => 400000000,
                'used_amount' => 320000000,
                'committed_budget' => 60000000,
            ],
            [
                'name' => 'Budget Operasional Finance',
                'code' => 'BUD-FIN-' . $currentYear,
                'department' => 'Finance',
                'total_amount' => 200000000,
                'used_amount' => 45000000,
                'committed_budget' => 25000000,
            ],
            [
                'name' => 'Budget Operations',
                'code' => 'BUD-OPS-' . $currentYear,
                'department' => 'Operations',
                'total_amount' => 250000000,
                'used_amount' => 100000000,
                'committed_budget' => 30000000,
            ],
        ];

        foreach ($departments as $dept) {
            Budget::updateOrCreate(
                ['code' => $dept['code']],
                [
                    'name' => $dept['name'],
                    'department' => $dept['department'],
                    'fiscal_year' => $currentYear,
                    'total_amount' => $dept['total_amount'],
                    'used_amount' => $dept['used_amount'],
                    'committed_budget' => $dept['committed_budget'],
                    'reserved_amount' => 0,
                    'is_active' => true,
                    'created_by' => 1,
                ]
            );
        }

        $this->command->info('Budget data seeded successfully for year ' . $currentYear);
    }
}
