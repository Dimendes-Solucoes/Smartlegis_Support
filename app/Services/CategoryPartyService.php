<?php

namespace App\Services;

use App\Libraries\ImageUploader;
use App\Models\Tenancy\CategoryParty;
use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CategoryPartyService
{
    public function list()
    {
        return CategoryParty::orderBy('name_party')->get();
    }

    public function findOrFail(int $id): CategoryParty
    {
        return CategoryParty::findOrFail($id);
    }

    public function create(array $data): CategoryParty
    {
        try {
            DB::beginTransaction();

            $data = $this->prepareData($data);
            $party = CategoryParty::create($data);

            DB::commit();

            return $party;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao criar partido: " . $e->getMessage());
        }
    }

    public function update(int $id, array $data): CategoryParty
    {
        try {
            DB::beginTransaction();

            $data = $this->prepareData($data);

            $party = CategoryParty::findOrFail($id);
            $party->update($data);

            DB::commit();

            return $party;
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao atualizar partido: " . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        try {
            DB::beginTransaction();

            $party = CategoryParty::findOrFail($id);
            $party->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception("Erro ao remover partido: " . $e->getMessage());
        }
    }

    private function prepareData(array $data): array
    {
        if (isset($data['logo']) && !empty($data['logo'])) {
            $data['logo'] = ImageUploader::handleImageUpload($data['logo'], 'category-parties');
        } else {
            unset($data['logo']);
        }

        return Arr::except($data, []);
    }
}
