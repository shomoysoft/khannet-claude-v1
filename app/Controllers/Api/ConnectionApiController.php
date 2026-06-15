<?php

namespace App\Controllers\Api;

use Framework\Http\Request;
use Framework\Support\Logger;
use App\Models\ConnectionRequest;

class ConnectionApiController extends ApiController
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
            'area'    => 'nullable|max:100',
            'plan'    => 'nullable|max:100',
            'address' => 'nullable|max:255',
            'message' => 'nullable|max:1000',
        ])) {
            response()->error($request->firstError(), 422);
        }

        try {
            $data       = $request->validated();
            $data['ip'] = $request->ip();

            $id = ConnectionRequest::create($data);
            response()->json(['success' => true, 'id' => $id]);
        } catch (\Throwable $e) {
            Logger::exception($e, 'Connection request save failed');
            response()->error('Could not save your request. Please try WhatsApp.', 500);
        }
    }
}
