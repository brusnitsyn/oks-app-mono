<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreReportTemplateRequest;
use App\Models\ReportTemplate;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportTemplateController extends Controller
{
    use AuthorizesRequests;

    public function index(): JsonResponse
    {
        $templates = ReportTemplate::query()
            ->where('user_id', auth()->id())
            ->orWhere('is_shared', true)
            ->get();

        return response()->json($templates);
    }

    public function getOptions(Request $request)
    {
        $request->validate([
            'query' => 'required|string'
        ]);

        try {
            // Безопасное выполнение запроса для получения опций
            if (stripos($request->input('query'), 'SELECT') === 0) {
                $results = DB::select($request->input('query'));
                return response()->json($results);
            }

            // Если переданы значения через запятую
            if (str_contains($request->input('query'), ',')) {
                return response()->json(
                    array_map('trim', explode(',', $request->input('query')))
                );
            }

            return response()->json([$request->input('query')]);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Invalid query',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function store(StoreReportTemplateRequest $request): \Illuminate\Http\RedirectResponse
    {
        $template = ReportTemplate::create([
            ...$request->validated(),
            'user_id' => auth()->id(),
        ]);

        return redirect()->route('reports.show', $template);
    }

    public function show(ReportTemplate $template): JsonResponse
    {
        $this->authorize('view', $template);
        return response()->json($template);
    }

    public function destroy(ReportTemplate $report_template)
    {
        $this->authorize('delete', $report_template);
        $report_template->delete();
    }
}
