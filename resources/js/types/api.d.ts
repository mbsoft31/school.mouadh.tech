// Based on your OpenAPI specification
export interface CourseSummary {
    id: string;
    title: string;
    description: string | null;
    subject: string;
    grade_levels: string[];
    estimated_duration_minutes: number | null;
    lesson_count: number;
    assessment_count: number;
    created_at: string;
    updated_at: string;
}

export interface CoursePackage {
    id: string;
    schema_version: string;
    title: string;
    description: string | null;
    subject: string;
    grade_levels: string[];
    estimated_duration_minutes: number | null;
    standards: string[] | null;
    created_at: string;
    updated_at: string;
    author: string | null;
    status: 'draft' | 'published' | 'archived';
    lessons: Lesson[];
    assessments: Assessment[];
    question_bank: { [key: string]: Question };
}

export interface Lesson {
    id: string;
    title: string;
    description: string | null;
    estimated_duration_minutes: number | null;
    order_index: number;
    content_blocks: ContentBlock[];
}

export interface Assessment {
    id: string;
    title: string;
    description: string | null;
    type: 'formative' | 'summative';
    time_limit_minutes: number;
    max_attempts: number;
    show_feedback_immediately: boolean;
    question_ids: string[];
}

export interface Question {
    type: 'multiple_choice' | 'numeric_input';
    stem: string;
    concept_tags: string[];
    difficulty_level: number;
    points: number;
    choices?: Choice[];
    expected_value?: number;
    tolerance?: number;
    units?: string;
    solution_explanation?: string;
}

export interface Choice {
    text: string;
    is_correct: boolean;
    feedback: string;
}

export type ContentBlock =
    | { type: 'markdown'; content: string; }
    | { type: 'media'; url: string; media_type: 'image' | 'video' | 'audio'; alt_text: string; caption?: string; }
    | { type: 'quiz'; assessment_id: string; };

export interface PaginationMeta {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    from: number | null;
    to: number | null;
    [key: string]: unknown; // This allows for additional properties...
}

export interface PaginatedResponse<T> {
    data: T[];
    meta: PaginationMeta;
}

export interface ErrorResponse {
    message: string;
    errors?: { [key: string]: string[] };
}
