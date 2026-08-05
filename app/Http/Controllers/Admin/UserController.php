<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::withCount('reservations');

        if ($request->filled('search')) {
            $search = '%' . addslashes($request->search) . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone', 'like', $search);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('verified') && $request->verified !== '') {
            if ($request->verified === '1') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        if ($request->get('trashed') === '1') {
            $query->onlyTrashed();
        }

        $users = $query->latest()->paginate(10)->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();

        // Only a full admin may create or grant the admin role; owners are
        // scoped and must never be able to escalate privileges.
        if (($data['role'] ?? 'guest') === 'admin' && ! auth()->user()->isAdmin()) {
            abort(403, __('admin.user.admin_only'));
        }

        User::create($data);

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user.created'))
            ->orJson();
    }

    public function show(User $user): View
    {
        $user->loadCount(['hotels', 'reservations', 'reviews']);
        $user->load([
            'recentReservations.hotel',
            'recentReviews.hotel',
        ]);

        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $data = $request->validated();

        if (empty($data['password'])) {
            unset($data['password']);
        }

        // Only a full admin may manage admin accounts or grant the admin role;
        // owners are scoped and must never escalate privileges.
        $newRole = $data['role'] ?? $user->role;
        if (! auth()->user()->isAdmin() && ($newRole === 'admin' || $user->isAdmin())) {
            abort(403, __('admin.user.admin_only'));
        }

        $user->update($data);

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user.updated'));
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->authorize('delete', $user);

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user.deleted'))
            ->orJson();
    }

    public function restore($id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $this->authorize('restore', $user);

        $user->restore();

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user.restored'))
            ->orJson();
    }

    public function forceDelete($id): RedirectResponse
    {
        $user = User::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $user);

        $user->forceDelete();

        return redirect()->route('admin.users.index')
            ->with('success', __('admin.user.permanently_deleted'))
            ->orJson();
    }
}
