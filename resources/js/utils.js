import { clsx, ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

/**
 *
 * @param  {...ClassValue} inputs
 * @returns {string}
 */
export function cn(...inputs) {
    return twMerge(clsx(inputs));
}
