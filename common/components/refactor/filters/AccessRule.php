<?php
/**
 * User: keven
 * Date: 2016/7/8
 * Time: 11:09
 */

namespace common\components\refactor\filters;


class AccessRule extends \yii\filters\AccessRule
{
    protected function matchRole($user)
    {
        if (empty($this->roles)) {
            return true;
        }
        foreach ($this->roles as $role) {

            if ($role === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($role === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
            } elseif ($this->isUserRoleValid($user, $role)) {
                return true;
            }else if($role===0)
                return true;
        }
        return false;
    }

    private function isUserRoleValid($user, $role)
    {

        if (!is_int($role) || $user->getIsGuest()) {
            return false;
        }
        $userRole = $user->identity->user_role;
        if (is_null($userRole) || empty($userRole)) {
            return false;
        }
        if ($role & $userRole) {
            return true;
        }
        return false;
    }
}