import { Button } from '@/components/ui/button';
import { ChevronLeft, ChevronRight } from 'lucide-react';
import { router } from '@inertiajs/react';
import { PaginationMeta } from '@/types';

interface PaginationControlsProps {
    meta: PaginationMeta;
    baseUrl: string;
    preserveFilters?: Record<string, unknown>;
}

export function PaginationControls({ meta, baseUrl, preserveFilters = {} }: PaginationControlsProps) {
    if (meta.last_page <= 1) return null;

    const handlePageChange = (page: number) => {
        router.get(baseUrl, {
            ...preserveFilters,
            page,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    return (
        <div className="flex items-center justify-between">
            <div className="text-sm text-muted-foreground">
                Showing {meta.from} to {meta.to} of {meta.total} results
            </div>

            <div className="flex items-center space-x-2">
                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => handlePageChange(meta.current_page - 1)}
                    disabled={meta.current_page <= 1}
                >
                    <ChevronLeft className="h-4 w-4 mr-1" />
                    Previous
                </Button>

                <div className="flex items-center space-x-1">
                    {Array.from({ length: Math.min(5, meta.last_page) }, (_, i) => {
                        let page: number;
                        if (meta.last_page <= 5) {
                            page = i + 1;
                        } else if (meta.current_page <= 3) {
                            page = i + 1;
                        } else if (meta.current_page >= meta.last_page - 2) {
                            page = meta.last_page - 4 + i;
                        } else {
                            page = meta.current_page - 2 + i;
                        }

                        return (
                            <Button
                                key={page}
                                variant={page === meta.current_page ? 'default' : 'outline'}
                                size="sm"
                                onClick={() => handlePageChange(page)}
                            >
                                {page}
                            </Button>
                        );
                    })}
                </div>

                <Button
                    variant="outline"
                    size="sm"
                    onClick={() => handlePageChange(meta.current_page + 1)}
                    disabled={meta.current_page >= meta.last_page}
                >
                    Next
                    <ChevronRight className="h-4 w-4 ml-1" />
                </Button>
            </div>
        </div>
    );
}
