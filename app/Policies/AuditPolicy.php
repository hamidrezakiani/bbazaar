<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Audit;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuditPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the admin can view any models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function viewAny(Admin $admin): bool
    {
        return $admin->can('view_any_audit::log');
    }

    /**
     * Determine whether the admin can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Audit  $audit
     * @return bool
     */
    public function view(Admin $admin, Audit $audit): bool
    {
        return $admin->can('view_audit::log');
    }

    /**
     * Determine whether the admin can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_audit::log');
    }

    /**
     * Determine whether the admin can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Audit  $audit
     * @return bool
     */
    public function update(Admin $admin, Audit $audit): bool
    {
        return $admin->can('update_audit::log');
    }

    /**
     * Determine whether the admin can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Audit  $audit
     * @return bool
     */
    public function delete(Admin $admin, Audit $audit): bool
    {
        return $admin->can('delete_audit::log');
    }

    /**
     * Determine whether the admin can bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_audit::log');
    }

    /**
     * Determine whether the admin can permanently delete.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Audit  $audit
     * @return bool
     */
    public function forceDelete(Admin $admin, Audit $audit): bool
    {
        return $admin->can('force_delete_audit::log');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_audit::log');
    }

    /**
     * Determine whether the admin can restore.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Audit  $audit
     * @return bool
     */
    public function restore(Admin $admin, Audit $audit): bool
    {
        return $admin->can('restore_audit::log');
    }

    /**
     * Determine whether the admin can bulk restore.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_audit::log');
    }

    /**
     * Determine whether the admin can replicate.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\Audit  $audit
     * @return bool
     */
    public function replicate(Admin $admin, Audit $audit): bool
    {
        return $admin->can('replicate_audit::log');
    }

    /**
     * Determine whether the admin can reorder.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_audit::log');
    }

}
