<?php

namespace KhanNet\Controllers\Api;

use App\Http\Request;
use KhanNet\Models\Quote;

class QuoteApiController extends ApiController
{
    public function submit(Request $request): void
    {
        if (!$request->isPost()) {
            response()->error('Method not allowed', 405);
        }

        if ($request->filled('botcheck')) {
            response()->success();
        }

        if (!$request->validate([
            'name'    => 'required|max:100',
            'mobile'  => 'required|max:20',
            'service' => 'nullable|max:100',
            'budget'  => 'nullable|max:50',
            'details' => 'nullable|max:2000',
        ])) {
            response()->error($request->firstError(), 422);
        }

        try {
            $data       = $request->validated();
            $data['ip'] = $request->ip();

            $id = Quote::create($data);
            response()->json(['success' => true, 'id' => $id]);
        } catch (\Throwable) {
            response()->error('Could not save your request. Please try WhatsApp.', 500);
        }
    }
}
