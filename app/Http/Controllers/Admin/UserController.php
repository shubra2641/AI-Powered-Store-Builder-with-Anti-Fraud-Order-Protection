<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AddCreditRequest;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use App\Services\DS_BulkDeleteResult;
use App\Services\UserService;
use App\Services\DS_BalanceService;
use App\Services\DS_ImpersonationService;
use App\Traits\DS_TranslationHelper;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use LogicException;

class UserController extends Controller
{
    use DS_TranslationHelper;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected UserService $userService,
        protected DS_BalanceService $balanceService,
        protected DS_ImpersonationService $impersonationService
    ) {}

    /**
     * Display a listing of users.
     */
    public function index(): View
    {
        $users = $this->userService->getAllUsers();
        $roles = $this->userService->getAllRoles();
        $stats = $this->userService->getUserStats();
        return view('admin.users.index', compact('users', 'roles', 'stats'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param StoreUserRequest $request
     * @return RedirectResponse
     */
    public function store(StoreUserRequest $request): RedirectResponse
    {
        $this->userService->createUser($request->validated());

        $this->notifySuccess('admin.user_created_success');
        return redirect()->route('admin.users.index');
    }

    /**
     * Update the specified user in storage.
     *
     * @param UpdateUserRequest $request
     * @param User $user
     * @return RedirectResponse
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $this->userService->updateUser($user, $request->validated());

        $this->notifySuccess('admin.user_updated_success');
        return redirect()->route('admin.users.index');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param User $user
     * @return RedirectResponse
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            $this->userService->deleteUser($user);
            $this->notifySuccess('admin.user_deleted_success');
        } catch (LogicException $e) {
            $this->notifyError($e->getMessage());
        }

        return redirect()->route('admin.users.index');
    }

    /**
     * Bulk delete selected users.
     */
    public function bulkDelete(Request $request): RedirectResponse
    {
        $ids = (array) $request->input('ids', []);

        if (empty($ids)) {
            return redirect()->route('admin.users.index');
        }

        $result = $this->userService->bulkDeleteUsers($ids);

        match ($result) {
            DS_BulkDeleteResult::SUCCESS => $this->notifySuccess('admin.bulk_deleted_success'),
            DS_BulkDeleteResult::PARTIAL => session()->flash('bulk_modal_message', __('admin.bulk_deleted_with_skips_self')),
            DS_BulkDeleteResult::NONE_SELF => session()->flash('bulk_modal_message', __('admin.cannot_delete_self')),
        };

        return redirect()->route('admin.users.index');
    }

    /**
     * Add credit to user balance.
     */
    public function addCredit(AddCreditRequest $request, User $user): RedirectResponse
    {
        $this->balanceService->addCredit($user, (int) $request->amount, $request->description);

        $this->notifySuccess('admin.credit_added_success');
        return redirect()->route('admin.users.index');
    }

    /**
     * Start impersonating a user.
     */
    public function impersonate(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            $this->notifyError('admin.cannot_impersonate_self');
            return redirect()->route('admin.users.index');
        }

        $this->impersonationService->impersonate($user);

        return redirect()->to('/'); 
    }

    /**
     * Stop impersonating and return to admin.
     */
    public function stopImpersonating(): RedirectResponse
    {
        if (!$this->impersonationService->isImpersonating()) {
            return redirect()->to('/');
        }

        $this->impersonationService->stopImpersonating();

        $this->notifySuccess('admin.impersonation_stopped');
        return redirect()->route('admin.users.index');
    }

    /**
     * Verify user email manually without sending email.
     */
    public function verifyEmail(User $user): RedirectResponse
    {
        $this->userService->verifyUserEmail($user);

        $this->notifySuccess('admin.email_verified_success');
        return redirect()->route('admin.users.index');
    }
}
