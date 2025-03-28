<?php

namespace App\Policies;

use App\Models\ReportTemplate;
use App\Models\User;

class ReportTemplatePolicy
{
    public function view(User $user, ReportTemplate $template): bool
    {
        return $template->user_id === $user->id || $template->is_shared;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, ReportTemplate $template): bool
    {
        return $template->user_id === $user->id;
    }

    public function delete(User $user, ReportTemplate $template): bool
    {
        return $template->user_id === $user->id;
    }
}
