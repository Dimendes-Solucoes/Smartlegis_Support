<?php

namespace App\Services;

use App\Models\Tenancy\Author;

class AuthorService
{
    public function store(array $data): Author
    {
        $data['status_sign'] = 0;

        $existingAuthor = Author::where('document_id', $data['document_id'])
            ->where('user_id', $data['user_id'])
            ->first();

        if ($existingAuthor) {
            return $existingAuthor;
        }

        return Author::create($data);
    }

    public function update(int $id, array $data): Author
    {
        $author = Author::findOrFail($id);
        $author->update($data);
        return $author;
    }

    public function destroy(int $id): void
    {
        $author = Author::findOrFail($id);
        $author->delete();
    }
}
