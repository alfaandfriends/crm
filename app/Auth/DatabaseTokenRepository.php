<?php

namespace App\Auth;

use Illuminate\Auth\Passwords\DatabaseTokenRepository as DatabaseTokenRepositoryBase;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Illuminate\Support\Carbon;

class DatabaseTokenRepository extends DatabaseTokenRepositoryBase
{
    public function create(CanResetPasswordContract $user)
    {
        $email = $user->getEmailForPasswordReset();
        $this->deleteExisting($user);
        $token = $this->createNewToken();
        $this->getTable()->insert($this->getPayload($email, $token));
        return $token;
    }

    protected function deleteExisting(CanResetPasswordContract $user)
    {
        return $this->getTable()
            ->where("user_email", $user->getEmailForPasswordReset())
            ->delete();
    }

    protected function getPayload($email, $token): array
    {
        return [
            "user_email" => $email,
            "token" => $this->hasher->make($token),
            "created_at" => new Carbon(),
        ];
    }

    public function exists(CanResetPasswordContract $user, $token)
    {
        $record = (array) $this->getTable()
            ->where("user_email", $user->getEmailForPasswordReset())
            ->first();
        return $record &&
               ! $this->tokenExpired($record["created_at"]) &&
                 $this->hasher->check($token, $record["token"]);
    }

    public function recentlyCreatedToken(CanResetPasswordContract $user)
    {
        $record = (array) $this->getTable()
            ->where("user_email", $user->getEmailForPasswordReset())
            ->first();

        return $record && $this->tokenRecentlyCreated($record['created_at']);
    }
}
