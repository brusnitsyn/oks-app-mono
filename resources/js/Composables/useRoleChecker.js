import {usePage} from "@inertiajs/vue3";

export const roles = Object.freeze({
    ADMIN: 'admin',
    DOCTOR: 'doctor',
    OPERATOR: 'operator'
})

export function useCheckRole(roleSlug) {
    const userObj = usePage().props.auth.user

    console.log(userObj.role)
    return userObj.role === roleSlug;
}
