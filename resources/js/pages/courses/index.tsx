import { useState } from 'react';
import { Head, router } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import CourseCard from '@/components/course/CourseCard';
import { CourseSummary, PaginatedResponse, PageProps, BreadcrumbItem } from '@/types';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { PaginationControls } from '@/components/ui/pagination-controls';
import { Plus, Search } from 'lucide-react';
import { create, index } from '@/routes/courses';

interface CourseIndexProps extends PageProps {
    courses: PaginatedResponse<CourseSummary>;
    filters: {
        search?: string;
        subject?: string;
        grade_level?: string;
    };
}

export default function CourseIndex({ courses, filters }: CourseIndexProps) {
    console.log(courses);
    const [search, setSearch] = useState(filters.search || '');
    const [subject, setSubject] = useState(filters.subject || 'any');
    const [gradeLevel, setGradeLevel] = useState(filters.grade_level || 'any');

    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Courses', href: index().url },
    ];

    const handleFilterChange = () => {
        router.get(index().url, {
            search: search || undefined,
            subject: subject == 'any' ? undefined : subject,
            grade_level: gradeLevel == 'any' ? undefined : gradeLevel,
            page: 1,
        }, {
            preserveState: true,
            replace: true,
        });
    };

    const clearFilters = () => {
        setSearch('');
        setSubject('any');
        setGradeLevel('any');
        router.get(index().url, {}, { replace: true });
    };

    const currentFilters = {
        search: search || undefined,
        subject: subject === 'any' ? undefined : subject,
        grade_level: gradeLevel === 'any' ? undefined : gradeLevel,
    };

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title="Courses" />

            <div className="space-y-6 p-6">
                {/* Header */}
                <div className="flex justify-between items-center">
                    <div>
                        <h1 className="text-3xl font-bold tracking-tight">Courses</h1>
                        <p className="text-muted-foreground">
                            Browse and manage your course library
                        </p>
                    </div>
                    <Button onClick={() => router.get(create().url)}>
                        <Plus className="h-4 w-4 mr-2" />
                        Create Course
                    </Button>
                </div>

                {/* Filters */}
                <div className="flex flex-col sm:flex-row gap-4">
                    <div className="flex-1">
                        <Input
                            placeholder="Search courses..."
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            onKeyDown={(e) => e.key === 'Enter' && handleFilterChange()}
                            className="w-full"
                        />
                    </div>

                    <Select value={subject} onValueChange={setSubject}>
                        <SelectTrigger className="w-full sm:w-48">
                            <SelectValue placeholder="All Subjects" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="any">All Subjects</SelectItem>
                            <SelectItem value="Mathematics">Mathematics</SelectItem>
                            <SelectItem value="Science">Science</SelectItem>
                            <SelectItem value="English Language Arts">English Language Arts</SelectItem>
                            <SelectItem value="Social Studies">Social Studies</SelectItem>
                            <SelectItem value="Computer Science">Computer Science</SelectItem>
                        </SelectContent>
                    </Select>

                    <Select value={gradeLevel} onValueChange={setGradeLevel}>
                        <SelectTrigger className="w-full sm:w-48">
                            <SelectValue placeholder="All Grades" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="any">All Grades</SelectItem>
                            {Array.from({ length: 12 }, (_, i) => (
                                <SelectItem key={i + 1} value={String(i + 1)}>
                                    Grade {i + 1}
                                </SelectItem>
                            ))}
                        </SelectContent>
                    </Select>

                    <div className="flex gap-2">
                        <Button onClick={handleFilterChange}>
                            <Search className="h-4 w-4 mr-2" />
                            Search
                        </Button>
                        <Button variant="outline" onClick={clearFilters}>
                            Clear
                        </Button>
                    </div>
                </div>

                {/* Results */}
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    {courses.data.map((course) => (
                        <CourseCard key={course.id} course={course} />
                    ))}
                </div>

                {/* Loading State */}
                {!courses && (
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        {Array.from({ length: 8 }).map((_, i) => (
                            <div key={i} className="animate-pulse">
                                <div className="bg-gray-200 rounded-lg h-48"></div>
                            </div>
                        ))}
                    </div>
                )}

                {/* Empty State */}
                {courses.data.length === 0 && (
                    <div className="text-center py-12">
                        <h3 className="mt-2 text-sm font-semibold text-gray-900">No courses found</h3>
                        <p className="mt-1 text-sm text-gray-500">
                            Try adjusting your search criteria or create a new course.
                        </p>
                        <div className="mt-6">
                            <Button onClick={() => router.get(create().url)}>
                                <Plus className="h-4 w-4 mr-2" />
                                Create Course
                            </Button>
                        </div>
                    </div>
                )}

                {/* Pagination */}
                <PaginationControls
                    meta={courses.meta}
                    baseUrl={index().url}
                    preserveFilters={currentFilters}
                />
            </div>
        </AppLayout>
    );
}
