import { usePage } from '@inertiajs/vue3';
import {Scopes} from "@/Modules/Auth/auth.constants.js";
export function useCheckScope() {
    const { props } = usePage()
    const hasScope = (scope) => {
        return props.auth.user && props.auth.user.scopes.includes(scope)
    }

    const scopes = Scopes

    return {
        hasScope,
        scopes
    }
}
