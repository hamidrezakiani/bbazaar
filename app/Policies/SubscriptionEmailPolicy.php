<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\SubscriptionEmail;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubscriptionEmailPolicy
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
        return $admin->can('view_any_subscriber');
    }

    /**
     * Determine whether the admin can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\SubscriptionEmail  $subscriptionEmail
     * @return bool
     */
    public function view(Admin $admin, SubscriptionEmail $subscriptionEmail): bool
    {
        return $admin->can('view_subscriber');
    }

    /**
     * Determine whether the admin can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_subscriber');
    }

    /**
     * Determine whether the admin can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\SubscriptionEmail  $subscriptionEmail
     * @return bool
     */
    public function update(Admin $admin, SubscriptionEmail $subscriptionEmail): bool
    {
        return $admin->can('update_subscriber');
    }

    /**
     * Determine whether the admin can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\SubscriptionEmail  $subscriptionEmail
     * @return bool
     */
    public function delete(Admin $admin, SubscriptionEmail $subscriptionEmail): bool
    {
        return $admin->can('delete_subscriber');
    }

    /**
     * Determine whether the admin can bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_subscriber');
    }

    /**
     * Determine whether the admin can permanently delete.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\SubscriptionEmail  $subscriptionEmail
     * @return bool
     */
    public function forceDelete(Admin $admin, SubscriptionEmail $subscriptionEmail): bool
    {
        return $admin->can('force_delete_subscriber');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_subscriber');
    }

    /**
     * Determine whether the admin can restore.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\SubscriptionEmail  $subscriptionEmail
     * @return bool
     */
    public function restore(Admin $admin, SubscriptionEmail $subscriptionEmail): bool
    {
        return $admin->can('restore_subscriber');
    }

    /**
     * Determine whether the admin can bulk restore.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_subscriber');
    }

    /**
     * Determine whether the admin can replicate.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\SubscriptionEmail  $subscriptionEmail
     * @return bool
     */
    public function replicate(Admin $admin, SubscriptionEmail $subscriptionEmail): bool
    {
        return $admin->can('replicate_subscriber');
    }

    /**
     * Determine whether the admin can reorder.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_subscriber');
    }

}
