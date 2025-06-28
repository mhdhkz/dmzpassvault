<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PasswordAuditLog;

class AuditLogController extends Controller
{
  public function store(Request $request)
  {
    $data = $request->validate([
      'identity_id' => 'required|string|exists:identities,id',
      'event_type' => 'required|in:created,updated,rotated,requested,accessed',
      'triggered_by' => 'required|in:user,system',
      'user_id' => 'nullable|exists:users,id',
      'actor_ip_addr' => 'nullable|ip',
      'note' => 'nullable|string',
    ]);

    PasswordAuditLog::create([
      'identity_id' => $data['identity_id'],
      'event_type' => $data['event_type'],
      'event_time' => now(),
      'user_id' => $data['user_id'] ?? null,
      'triggered_by' => $data['triggered_by'],
      'actor_ip_addr' => $data['actor_ip_addr'] ?? null,
      'note' => $data['note'] ?? null,
    ]);

    return response()->json(['message' => 'Log recorded.'], 201);
  }
}
