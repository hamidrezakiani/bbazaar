<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\HomeSlider;
use Illuminate\Auth\Access\HandlesAuthorization;

class HomeSliderPolicy
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
        return $admin->can('view_any_ui::home::slider');
    }

    /**
     * Determine whether the admin can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\HomeSlider  $homeSlider
     * @return bool
     */
    public function view(Admin $admin, HomeSlider $homeSlider): bool
    {
        return $admin->can('view_ui::home::slider');
    }

    /**
     * Determine whether the admin can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_ui::home::slider');
    }

    /**
     * Determine whether the admin can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\HomeSlider  $homeSlider
     * @return bool
     */
    public function update(Admin $admin, HomeSlider $homeSlider): bool
    {
        return $admin->can('update_ui::home::slider');
    }

    /**
     * Determine whether the admin can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\HomeSlider  $homeSlider
     * @return bool
     */
    public function delete(Admin $admin, HomeSlider $homeSlider): bool
    {
        return $admin->can('delete_ui::home::slider');
    }

    /**
     * Determine whether the admin can bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_ui::home::slider');
    }

    /**
     * Determine whether the admin can permanently delete.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\HomeSlider  $homeSlider
     * @return bool
     */
    public function forceDelete(Admin $admin, HomeSlider $homeSlider): bool
    {
        return $admin->can('force_delete_ui::home::slider');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_ui::home::slider');
    }

    /**
     * Determine whether the admin can restore.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\HomeSlider  $homeSlider
     * @return bool
     */
    public function restore(Admin $admin, HomeSlider $homeSlider): bool
    {
        return $admin->can('restore_ui::home::slider');
    }

    /**
     * Determine whether the admin can bulk restore.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_ui::home::slider');
    }

    /**
     * Determine whether the admin can replicate.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\HomeSlider  $homeSlider
     * @return bool
     */
    public function replicate(Admin $admin, HomeSlider $homeSlider): bool
    {
        return $admin->can('replicate_ui::home::slider');
    }

    /**
     * Determine whether the admin can reorder.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_ui::home::slider');
    }

}
