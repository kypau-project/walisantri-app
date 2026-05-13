<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * List all users with optional filtering
     */
    public function index(Request $request): JsonResponse
    {
        $query = User::with('student');

        // Filter by role
        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        // Search by name or username
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('username', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate($request->per_page ?? 20);

        return response()->json([
            'success' => true,
            'data' => $users->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'username' => $user->username,
                    'role' => $user->role,
                    'student' => $user->student ? [
                        'id' => $user->student->id,
                        'name' => $user->student->name,
                        'nis' => $user->student->nis,
                        'class' => $user->student->class,
                        'room' => $user->student->room,
                    ] : null,
                    'created_at' => $user->created_at,
                ];
            }),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    /**
     * Show a single user detail
     */
    public function show($id): JsonResponse
    {
        $user = User::with('student')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'role' => $user->role,
                'student' => $user->student ? [
                    'id' => $user->student->id,
                    'name' => $user->student->name,
                    'nis' => $user->student->nis,
                    'class' => $user->student->class,
                    'room' => $user->student->room,
                    'gender' => $user->student->gender,
                    'father_phone' => $user->student->father_phone,
                    'mother_phone' => $user->student->mother_phone,
                ] : null,
                'created_at' => $user->created_at,
            ],
        ]);
    }
}
