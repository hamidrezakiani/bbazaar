<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\TaxRules;
use Illuminate\Auth\Access\HandlesAuthorization;

class TaxRulesPolicy
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
        return $admin->can('view_any_tax::rules');
    }

    /**
     * Determine whether the admin can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\TaxRules  $taxRules
     * @return bool
     */
    public function view(Admin $admin, TaxRules $taxRules): bool
    {
        return $admin->can('view_tax::rules');
    }

    /**
     * Determine whether the admin can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_tax::rules');
    }

    /**
     * Determine whether the admin can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\TaxRules  $taxRules
     * @return bool
     */
    public function update(Admin $admin, TaxRules $taxRules): bool
    {
        return $admin->can('update_tax::rules');
    }

    /**
     * Determine whether the admin can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\TaxRules  $taxRules
     * @return bool
     */
    public function delete(Admin $admin, TaxRules $taxRules): bool
    {
        return $admin->can('delete_tax::rules');
    }

    /**
     * Determine whether the admin can bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_tax::rules');
    }

    /**
     * Determine whether the admin can permanently delete.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\TaxRules  $taxRules
     * @return bool
     */
    public function forceDelete(Admin $admin, TaxRules $taxRules): bool
    {
        return $admin->can('force_delete_tax::rules');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_tax::rules');
    }

    /**
     * Determine whether the admin can restore.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\TaxRules  $taxRules
     * @return bool
     */
    public function restore(Admin $admin, TaxRules $taxRules): bool
    {
        return $admin->can('restore_tax::rules');
    }

    /**
     * Determine whether the admin can bulk restore.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_tax::rules');
    }

    /**
     * Determine whether the admin can replicate.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\TaxRules  $taxRules
     * @return bool
     */
    public function replicate(Admin $admin, TaxRules $taxRules): bool
    {
        return $admin->can('replicate_tax::rules');
    }

    /**
     * Determine whether the admin can reorder.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_tax::rules');
    }

}
