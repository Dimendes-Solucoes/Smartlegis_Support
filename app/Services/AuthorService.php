<?php

namespace App\Services;

use App\Models\Tenancy\Author;

class AuthorService
{
    public function destroy(int $id): void
    {
        $user = Author::findOrFail($id);
        $user->delete();
    }
}
