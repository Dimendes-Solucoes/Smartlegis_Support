<?php

namespace App\Services;

use App\Models\Tenancy\Comission;
use App\Models\Tenancy\ComissionUser;
use App\Models\Tenancy\User;
use Illuminate\Database\Eloquent\Collection;

class CommissionService
{
    public function list(): Collection
    {
        $commissions = Comission::orderBy('comission_name')->get();

        $commissionTypesMap = collect(Comission::LIST_TYPES)->pluck('title', 'id')->all();

        return $commissions->map(function ($commission) use ($commissionTypesMap) {
            $commission->type_description = $commissionTypesMap[$commission->type] ?? $commission->type;
            return $commission;
        });
    }

    public function getCreationFormData(): array
    {
        return [
            'commissionTypes' => Comission::LIST_TYPES
        ];
    }

    public function getCommissionForEdit(int $id): array
    {
        $commission = Comission::findOrFail($id);

        $commissionUsers = $commission->users->map(function ($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'function' => $user->pivot->function
            ];
        })->sortBy('function')->values();

        $counciliors = User::where('status_user', User::USUARIO_VEREADOR)->get();

        $functions = [
            ['id' => ComissionUser::PRESIDENT, 'title' => 'Presidente'],
            ['id' => ComissionUser::VICE, 'title' => 'Vice'],
            ['id' => ComissionUser::REPORTER, 'title' => 'Relator'],
            ['id' => ComissionUser::BOARD, 'title' => 'Membro da Mesa'],
        ];

        return [
            'comission' => $commission,
            'comissionUsers' => $commissionUsers,
            'functions' => $functions,
            'counciliors' => $counciliors,
            'commissionTypes' => Comission::LIST_TYPES
        ];
    }

    public function create(array $data): Comission
    {
        return Comission::create($data);
    }

    public function update(int $id, array $data): Comission
    {
        $commission = Comission::findOrFail($id);
        $commission->update($data);

        return $commission;
    }

    public function updateUsers(int $id, array $data)
    {
        $commission = Comission::findOrFail($id);
        $usersToAttach = [];

        foreach ($data['users'] as $user) {
            $usersToAttach[$user['id']] = [
                'function' => $user['function']
            ];
        }

        $commission->users()->sync($usersToAttach);
    }
}
