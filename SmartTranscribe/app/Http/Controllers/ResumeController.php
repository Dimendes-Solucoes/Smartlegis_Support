<?php

namespace App\Http\Controllers;

use App\Http\Requests\Resumes\ResumeCreateRequest;
use App\Services\ResumeService;

class ResumeController extends Controller
{
    public function __construct(
        protected ResumeService $resumeService
    ) {}

    public function list()
    {
        $resumes = $this->resumeService->list();

        return response()->json([
            'resumes' => $resumes
        ]);
    }

    public function create(ResumeCreateRequest $request)
    {
        $resume = $this->resumeService->create($request->all());

        return response()->json([
            'resume' => $resume
        ]);
    }

    public function delete(int $id)
    {
        $this->resumeService->delete($id);

        return response()->noContent();
    }
}
