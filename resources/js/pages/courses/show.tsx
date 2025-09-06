import { Head, Link } from '@inertiajs/react';
import AppLayout from '@/layouts/app-layout';
import { CoursePackage, PageProps, BreadcrumbItem } from '@/types';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs';
import { BookOpen, Clock, Target, Calendar, Edit, Play, FileText } from 'lucide-react';
import { formatDuration, formatDate } from '@/lib/utils';
import { show } from '@/routes/courses';

interface CourseShowProps extends PageProps {
    course: CoursePackage;
}

export default function CourseShow({ course }: CourseShowProps) {
    const breadcrumbs: BreadcrumbItem[] = [
        { title: 'Dashboard', href: '/dashboard' },
        { title: 'Courses', href: '/courses' },
        { title: course.title, href: show(course).url },
    ];

    return (
        <AppLayout breadcrumbs={breadcrumbs}>
            <Head title={course.title} />

            <div className="space-y-6 p-6">
                {/* Course Header */}
                <div className="flex justify-between items-start">
                    <div className="space-y-4">
                        <div className="flex items-center gap-3">
                            <h1 className="text-3xl font-bold tracking-tight">{course.title}</h1>
                            <Badge variant={course.status === 'published' ? 'default' : 'secondary'}>
                                {course.status}
                            </Badge>
                        </div>

                        {course.description && (
                            <p className="text-lg text-muted-foreground max-w-3xl">
                                {course.description}
                            </p>
                        )}

                        <div className="flex flex-wrap gap-4 text-sm text-muted-foreground">
                            <div className="flex items-center gap-1">
                                <Target className="h-4 w-4" />
                                <span>{course.subject} • Grades {course.grade_levels.join(', ')}</span>
                            </div>
                            <div className="flex items-center gap-1">
                                <Clock className="h-4 w-4" />
                                <span>{formatDuration(course.estimated_duration_minutes)}</span>
                            </div>
                            <div className="flex items-center gap-1">
                                <BookOpen className="h-4 w-4" />
                                <span>{course.lessons.length} lessons • {course.assessments.length} assessments</span>
                            </div>
                            <div className="flex items-center gap-1">
                                <Calendar className="h-4 w-4" />
                                <span>Updated {formatDate(course.updated_at)}</span>
                            </div>
                        </div>
                    </div>

                    <div className="flex gap-2">
                        <Button variant="outline" asChild>
                            <Link href={`/courses/${course.id}/edit`}>
                                <Edit className="h-4 w-4 mr-2" />
                                Edit Course
                            </Link>
                        </Button>
                        <Button asChild>
                            <Link href={`/courses/${course.id}/preview`}>
                                <Play className="h-4 w-4 mr-2" />
                                Preview Course
                            </Link>
                        </Button>
                    </div>
                </div>

                {/* Course Content */}
                <Tabs defaultValue="overview" className="space-y-6">
                    <TabsList>
                        <TabsTrigger value="overview">Overview</TabsTrigger>
                        <TabsTrigger value="lessons">Lessons ({course.lessons.length})</TabsTrigger>
                        <TabsTrigger value="assessments">Assessments ({course.assessments.length})</TabsTrigger>
                        <TabsTrigger value="questions">Question Bank ({Object.keys(course.question_bank).length})</TabsTrigger>
                    </TabsList>

                    <TabsContent value="overview" className="space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Course Details</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <div>
                                        <span className="text-sm font-medium">Author:</span>
                                        <p className="text-muted-foreground">{course.author || 'Not specified'}</p>
                                    </div>
                                    <div>
                                        <span className="text-sm font-medium">Schema Version:</span>
                                        <p className="text-muted-foreground">{course.schema_version}</p>
                                    </div>
                                    {course.standards && course.standards.length > 0 && (
                                        <div>
                                            <span className="text-sm font-medium">Standards:</span>
                                            <div className="flex flex-wrap gap-1 mt-1">
                                                {course.standards.map((standard) => (
                                                    <Badge key={standard} variant="outline" className="text-xs">
                                                        {standard}
                                                    </Badge>
                                                ))}
                                            </div>
                                        </div>
                                    )}
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Progress Overview</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-3">
                                    <div className="flex justify-between">
                                        <span className="text-sm">Lessons:</span>
                                        <span className="font-medium">{course.lessons.length}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm">Assessments:</span>
                                        <span className="font-medium">{course.assessments.length}</span>
                                    </div>
                                    <div className="flex justify-between">
                                        <span className="text-sm">Questions:</span>
                                        <span className="font-medium">{Object.keys(course.question_bank).length}</span>
                                    </div>
                                </CardContent>
                            </Card>

                            <Card>
                                <CardHeader>
                                    <CardTitle className="text-lg">Quick Actions</CardTitle>
                                </CardHeader>
                                <CardContent className="space-y-2">
                                    <Button variant="outline" size="sm" className="w-full justify-start" asChild>
                                        <Link href={`/courses/${course.id}/lessons/create`}>
                                            <BookOpen className="h-4 w-4 mr-2" />
                                            Add Lesson
                                        </Link>
                                    </Button>
                                    <Button variant="outline" size="sm" className="w-full justify-start" asChild>
                                        <Link href={`/courses/${course.id}/assessments/create`}>
                                            <FileText className="h-4 w-4 mr-2" />
                                            Add Assessment
                                        </Link>
                                    </Button>
                                </CardContent>
                            </Card>
                        </div>
                    </TabsContent>

                    <TabsContent value="lessons" className="space-y-4">
                        {course.lessons.length === 0 ? (
                            <div className="text-center py-12">
                                <h3 className="text-lg font-semibold text-gray-900">No lessons yet</h3>
                                <p className="text-gray-500 mt-2">Start building your course by adding the first lesson.</p>
                                <Button className="mt-4" asChild>
                                    <Link href={`/courses/${course.id}/lessons/create`}>
                                        <BookOpen className="h-4 w-4 mr-2" />
                                        Create First Lesson
                                    </Link>
                                </Button>
                            </div>
                        ) : (
                            <div className="grid gap-4">
                                {course.lessons
                                    .sort((a, b) => a.order_index - b.order_index)
                                    .map((lesson, index) => (
                                        <Card key={lesson.id}>
                                            <CardContent className="p-4">
                                                <div className="flex items-center justify-between">
                                                    <div className="flex items-center space-x-4">
                                                        <div className="w-8 h-8 rounded-full bg-primary/10 text-primary flex items-center justify-center text-sm font-medium">
                                                            {index + 1}
                                                        </div>
                                                        <div>
                                                            <h3 className="font-semibold">{lesson.title}</h3>
                                                            {lesson.description && (
                                                                <p className="text-sm text-muted-foreground">{lesson.description}</p>
                                                            )}
                                                            <div className="flex items-center gap-4 mt-1 text-xs text-muted-foreground">
                                                                <span>{formatDuration(lesson.estimated_duration_minutes)}</span>
                                                                <span>{lesson.content_blocks.length} content blocks</span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <Button variant="outline" size="sm" asChild>
                                                        <Link href={`/courses/${course.id}/lessons/${lesson.id}/edit`}>
                                                            <Edit className="h-4 w-4" />
                                                        </Link>
                                                    </Button>
                                                </div>
                                            </CardContent>
                                        </Card>
                                    ))}
                            </div>
                        )}
                    </TabsContent>

                    <TabsContent value="assessments" className="space-y-4">
                        {course.assessments.length === 0 ? (
                            <div className="text-center py-12">
                                <h3 className="text-lg font-semibold text-gray-900">No assessments yet</h3>
                                <p className="text-gray-500 mt-2">Add assessments to test student understanding.</p>
                                <Button className="mt-4" asChild>
                                    <Link href={`/courses/${course.id}/assessments/create`}>
                                        <FileText className="h-4 w-4 mr-2" />
                                        Create First Assessment
                                    </Link>
                                </Button>
                            </div>
                        ) : (
                            <div className="grid gap-4">
                                {course.assessments.map((assessment) => (
                                    <Card key={assessment.id}>
                                        <CardContent className="p-4">
                                            <div className="flex items-center justify-between">
                                                <div>
                                                    <div className="flex items-center gap-2">
                                                        <h3 className="font-semibold">{assessment.title}</h3>
                                                        <Badge variant={assessment.type === 'summative' ? 'default' : 'secondary'}>
                                                            {assessment.type}
                                                        </Badge>
                                                    </div>
                                                    {assessment.description && (
                                                        <p className="text-sm text-muted-foreground mt-1">{assessment.description}</p>
                                                    )}
                                                    <div className="flex items-center gap-4 mt-2 text-xs text-muted-foreground">
                                                        <span>{assessment.question_ids.length} questions</span>
                                                        {assessment.time_limit_minutes > 0 && (
                                                            <span>Time limit: {assessment.time_limit_minutes} min</span>
                                                        )}
                                                        {assessment.max_attempts !== -1 && (
                                                            <span>Max attempts: {assessment.max_attempts}</span>
                                                        )}
                                                    </div>
                                                </div>
                                                <Button variant="outline" size="sm" asChild>
                                                    <Link href={`/courses/${course.id}/assessments/${assessment.id}/edit`}>
                                                        <Edit className="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        )}
                    </TabsContent>

                    <TabsContent value="questions" className="space-y-4">
                        {Object.keys(course.question_bank).length === 0 ? (
                            <div className="text-center py-12">
                                <h3 className="text-lg font-semibold text-gray-900">No questions yet</h3>
                                <p className="text-gray-500 mt-2">Build a question bank to use in your assessments.</p>
                                <Button className="mt-4" asChild>
                                    <Link href={`/courses/${course.id}/questions/create`}>
                                        Create First Question
                                    </Link>
                                </Button>
                            </div>
                        ) : (
                            <div className="grid gap-4">
                                {Object.entries(course.question_bank).map(([questionId, question]) => (
                                    <Card key={questionId}>
                                        <CardContent className="p-4">
                                            <div className="flex items-start justify-between">
                                                <div className="flex-1">
                                                    <div className="flex items-center gap-2 mb-2">
                                                        <Badge variant={question.type === 'multiple_choice' ? 'default' : 'secondary'}>
                                                            {question.type.replace('_', ' ')}
                                                        </Badge>
                                                        <Badge variant="outline">
                                                            {question.points} pts
                                                        </Badge>
                                                        <Badge variant="outline">
                                                            Level {question.difficulty_level}
                                                        </Badge>
                                                    </div>
                                                    <p className="text-sm font-medium mb-2">{question.stem}</p>
                                                    <div className="flex flex-wrap gap-1">
                                                        {question.concept_tags.map((tag) => (
                                                            <Badge key={tag} variant="outline" className="text-xs">
                                                                {tag}
                                                            </Badge>
                                                        ))}
                                                    </div>
                                                </div>
                                                <Button variant="outline" size="sm" asChild>
                                                    <Link href={`/courses/${course.id}/questions/${questionId}/edit`}>
                                                        <Edit className="h-4 w-4" />
                                                    </Link>
                                                </Button>
                                            </div>
                                        </CardContent>
                                    </Card>
                                ))}
                            </div>
                        )}
                    </TabsContent>
                </Tabs>
            </div>
        </AppLayout>
    );
}
