import {Scopes} from "@/Modules/Auth/auth.constants.js";

export function useScope() {
    return {
        ...Scopes
    }
}
