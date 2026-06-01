<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AddPartyToUserTerms extends Command
{
    protected $signature   = 'user-terms:add-party {--tenant= : ID de um tenant específico (opcional)}';
    protected $description = 'Adiciona category_party_id em user_terms e popula a partir de users';

    public function handle(): int
    {
        $tenants = $this->option('tenant')
            ? Tenant::where('id', $this->option('tenant'))->get()
            : Tenant::all();

        if ($tenants->isEmpty()) {
            $this->error('Nenhum tenant encontrado.');
            return self::FAILURE;
        }

        foreach ($tenants as $tenant) {
            $schema = $tenant->data['tenancy_db_name'] ?? null;

            if (!$schema) {
                $this->warn("Tenant [{$tenant->id}] sem schema configurado — pulando.");
                continue;
            }

            $this->info("Processando tenant [{$tenant->id}] schema [{$schema}]...");

            DB::statement("SET search_path TO {$schema}");

            $this->addColumnIfMissing($schema);
            $updated = $this->populateParty();

            $this->line("  → {$updated} registro(s) atualizado(s).");
        }

        DB::statement('SET search_path TO public');

        $this->info('Concluído.');
        return self::SUCCESS;
    }

    private function addColumnIfMissing(string $schema): void
    {
        $exists = DB::selectOne("
            SELECT 1
            FROM information_schema.columns
            WHERE table_schema = ?
              AND table_name   = 'user_terms'
              AND column_name  = 'category_party_id'
        ", [$schema]);

        if ($exists) {
            $this->line('  → Coluna category_party_id já existe.');
            return;
        }

        DB::statement('ALTER TABLE user_terms ADD COLUMN category_party_id BIGINT NULL');
        DB::statement('ALTER TABLE user_terms ADD CONSTRAINT fk_user_terms_party FOREIGN KEY (category_party_id) REFERENCES category_parties(id) ON DELETE SET NULL');

        $this->line('  → Coluna category_party_id criada.');
    }

    private function populateParty(): int
    {
        return DB::update('
            UPDATE user_terms ut
            SET    category_party_id = u.category_party_id
            FROM   users u
            WHERE  ut.user_id            = u.id
              AND  u.category_party_id   IS NOT NULL
              AND  ut.category_party_id  IS NULL
        ');
    }
}
