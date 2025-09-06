import { CourseSummary } from '@/types/api';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Link } from '@inertiajs/react';
import { BookOpen, Clock, Target, Calendar } from 'lucide-react';
import { formatDuration, formatDate } from '@/lib/utils';

interface CourseCardProps {
    course: CourseSummary;
}

export default function CourseCard({ course }: CourseCardProps) {
    return (
        <Card className="h-full flex flex-col hover:shadow-lg transition-shadow">
            <CardHeader>
                <div className="flex justify-between items-start">
                    <CardTitle className="text-lg">{course.title}</CardTitle>
                    <Badge variant="secondary">{course.subject}</Badge>
                </div>
                {course.description && (
                    <p className="text-sm text-muted-foreground line-clamp-2">
                        {course.description}
                    </p>
                )}
            </CardHeader>

            <CardContent className="flex-1 flex flex-col justify-between">
                <div className="space-y-3">
                    <div className="flex items-center text-sm text-muted-foreground">
                        <Target className="h-4 w-4 mr-2" />
                        Grades: {course.grade_levels.join(', ')}
                    </div>

                    <div className="flex items-center text-sm text-muted-foreground">
                        <Clock className="h-4 w-4 mr-2" />
                        {formatDuration(course.estimated_duration_minutes)}
                    </div>

                    <div className="flex items-center text-sm text-muted-foreground">
                        <BookOpen className="h-4 w-4 mr-2" />
                        {course.lesson_count} lessons, {course.assessment_count} assessments
                    </div>

                    <div className="flex items-center text-sm text-muted-foreground">
                        <Calendar className="h-4 w-4 mr-2" />
                        Updated {formatDate(course.updated_at)}
                    </div>
                </div>

                <div className="pt-4">
                    <Button asChild className="w-full">
                        <Link href={`/courses/${course.id}`}>
                            View Course
                        </Link>
                    </Button>
                </div>
            </CardContent>
        </Card>
    );
}
