<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;

class MaterialPolicy
{
    /**
     * Guru hanya bisa kelola materi di kelas yang dia ampu.
     */
    private function ownsClass(User $user, Material $material): bool
    {
        return $material->meeting->schoolClass->teacher->user_id === $user->id;
    }

    public function view(User $user, Material $material): bool
    {
        if ($user->isAdmin()) return true;

        if ($user->isGuru()) return $this->ownsClass($user, $material);

        // Siswa hanya bisa lihat yang published dan sudah terdaftar di kelasnya
        if ($user->isSiswa()) {
            $classId = $material->meeting->school_class_id;
            $enrolled = $user->student->enrollments()
                ->where('school_class_id', $classId)
                ->exists();

            return $enrolled && $material->status === 'published';
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isGuru() || $user->isAdmin();
    }

    public function update(User $user, Material $material): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isGuru() && $this->ownsClass($user, $material);
    }

    public function delete(User $user, Material $material): bool
    {
        if ($user->isAdmin()) return true;
        return $user->isGuru() && $this->ownsClass($user, $material);
    }
}
