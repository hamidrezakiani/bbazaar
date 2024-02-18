<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\RatingReview;
use Illuminate\Auth\Access\HandlesAuthorization;

class RatingReviewPolicy
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
        return $admin->can('view_any_rating::review');
    }

    /**
     * Determine whether the admin can view the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\RatingReview  $ratingReview
     * @return bool
     */
    public function view(Admin $admin, RatingReview $ratingReview): bool
    {
        return $admin->can('view_rating::review');
    }

    /**
     * Determine whether the admin can create models.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function create(Admin $admin): bool
    {
        return $admin->can('create_rating::review');
    }

    /**
     * Determine whether the admin can update the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\RatingReview  $ratingReview
     * @return bool
     */
    public function update(Admin $admin, RatingReview $ratingReview): bool
    {
        return $admin->can('update_rating::review');
    }

    /**
     * Determine whether the admin can delete the model.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\RatingReview  $ratingReview
     * @return bool
     */
    public function delete(Admin $admin, RatingReview $ratingReview): bool
    {
        return $admin->can('delete_rating::review');
    }

    /**
     * Determine whether the admin can bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function deleteAny(Admin $admin): bool
    {
        return $admin->can('delete_any_rating::review');
    }

    /**
     * Determine whether the admin can permanently delete.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\RatingReview  $ratingReview
     * @return bool
     */
    public function forceDelete(Admin $admin, RatingReview $ratingReview): bool
    {
        return $admin->can('force_delete_rating::review');
    }

    /**
     * Determine whether the admin can permanently bulk delete.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function forceDeleteAny(Admin $admin): bool
    {
        return $admin->can('force_delete_any_rating::review');
    }

    /**
     * Determine whether the admin can restore.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\RatingReview  $ratingReview
     * @return bool
     */
    public function restore(Admin $admin, RatingReview $ratingReview): bool
    {
        return $admin->can('restore_rating::review');
    }

    /**
     * Determine whether the admin can bulk restore.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function restoreAny(Admin $admin): bool
    {
        return $admin->can('restore_any_rating::review');
    }

    /**
     * Determine whether the admin can replicate.
     *
     * @param  \App\Models\Admin  $admin
     * @param  \App\Models\RatingReview  $ratingReview
     * @return bool
     */
    public function replicate(Admin $admin, RatingReview $ratingReview): bool
    {
        return $admin->can('replicate_rating::review');
    }

    /**
     * Determine whether the admin can reorder.
     *
     * @param  \App\Models\Admin  $admin
     * @return bool
     */
    public function reorder(Admin $admin): bool
    {
        return $admin->can('reorder_rating::review');
    }

}
