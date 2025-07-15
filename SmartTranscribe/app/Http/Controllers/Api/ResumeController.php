<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Resumes\ResumeCreateRequest;
use App\Services\ResumeService;

class ResumeController extends Controller
{
    public function __construct(
        protected ResumeService $service
    ) {}

    /** @group ClientResumes */
    public function list()
    {
        return response()->json([
            'resumes' => $this->service->list()
        ]);
    }

    /** @group ClientResumes */
    public function create(ResumeCreateRequest $request)
    {
        $resume = $this->service->create($request->all());

        return response()->json([
            'resume' => $resume
        ]);
    }

    /** @group ClientResumes */
    public function delete(int $id)
    {
        $this->service->delete($id);

        return response()->noContent();
    }
}
