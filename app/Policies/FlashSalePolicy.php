<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\FlashSale;
use Illuminate\Auth\Access\HandlesAuthorization;

class FlashSalePolicy
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
        return $admin->can('view_any_flash::sale');
    }

    /**
     * Determine whether the admin can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\FlashSale  $flashSale
     * @return bool
     */
    public function view(Admin $admin, FlashSale $flashSale): bool
    {
        return $admin->can('view_flash::sale');
    }

    /**
     * Determine whether the admin can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_flash::sale');
    }

    /**
     * Determine whether the admin can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\FlashSale  $flashSale
     * @return bool
     */
    public function update(Admin $admin, FlashSale $flashSale): bool
    {
        return $admin->can('update_flash::sale');
    }

    /**
     * Determine whether the admin can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\FlashSale  $flashSale
     * @return bool
     */
    public function delete(Admin $admin, FlashSale $flashSale): bool
    {
        return $admin->can('delete_flash::sale');
    }

    /**
     * Determine whether the admin can bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_flash::sale');
    }

    /**
     * Determine whether the admin can permanently delete.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\FlashSale  $flashSale
     * @return bool
     */
    public function forceDelete(Admin $admin, FlashSale $flashSale): bool
    {
        return $admin->can('force_delete_flash::sale');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_flash::sale');
    }

    /**
     * Determine whether the admin can restore.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\FlashSale  $flashSale
     * @return bool
     */
    public function restore(Admin $admin, FlashSale $flashSale): bool
    {
        return $admin->can('restore_flash::sale');
    }

    /**
     * Determine whether the admin can bulk restore.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_flash::sale');
    }

    /**
     * Determine whether the admin can replicate.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\FlashSale  $flashSale
     * @return bool
     */
    public function replicate(Admin $admin, FlashSale $flashSale): bool
    {
        return $admin->can('replicate_flash::sale');
    }

    /**
     * Determine whether the admin can reorder.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_flash::sale');
    }

}
