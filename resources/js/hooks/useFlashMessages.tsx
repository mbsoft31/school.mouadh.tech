// resources/js/hooks/useFlashMessages.ts
import { usePage } from '@inertiajs/react';
import { toast } from 'sonner';
import { useEffect } from 'react';

export function useFlashMessages() {
    const { flash } = usePage<{
        flash: {
            success?: string;
            error?: string;
            info?: string;
        }
    }>().props;

    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
        if (flash.error) {
            toast.error(flash.error);
        }
        if (flash.info) {
            toast.info(flash.info);
        }
    }, [flash]);
}
