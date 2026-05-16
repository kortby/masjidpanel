/**
 * Category-specific meta field definitions for the post creation/edit forms.
 * Each category gets contextual fields, placeholder text, and suggested tags
 * to help users create well-structured, useful posts.
 */
export interface MetaFieldOption {
    label: string;
    value: string;
}

export interface MetaField {
    key: string;
    label: string;
    type: 'text' | 'select' | 'number';
    placeholder?: string;
    options?: MetaFieldOption[];
    required?: boolean;
    prefix?: string;
}

export interface CategoryConfig {
    fields: MetaField[];
    descriptionPlaceholder: string;
    suggestedTags: string[];
}

const categoryConfigs: Record<string, CategoryConfig> = {
    'Jobs & Hiring': {
        fields: [
            {
                key: 'job_type',
                label: 'Job Type',
                type: 'select',
                options: [
                    { label: 'Full-Time', value: 'Full-Time' },
                    { label: 'Part-Time', value: 'Part-Time' },
                    { label: 'Contract', value: 'Contract' },
                    { label: 'Freelance', value: 'Freelance' },
                    { label: 'Internship', value: 'Internship' },
                ],
            },
            { key: 'salary', label: 'Salary / Pay', type: 'text', placeholder: 'e.g. $50k/year, $25/hr' },
            { key: 'company', label: 'Company Name', type: 'text', placeholder: 'e.g. ABC Corp' },
        ],
        descriptionPlaceholder: 'Describe the job role, responsibilities, requirements, and how to apply...',
        suggestedTags: ['Remote', 'Urgent', 'No Experience', 'Halal'],
    },
    'Housing & Roommates': {
        fields: [
            {
                key: 'listing_type',
                label: 'Listing Type',
                type: 'select',
                options: [
                    { label: 'Room for Rent', value: 'Room for Rent' },
                    { label: 'Apartment for Rent', value: 'Apartment for Rent' },
                    { label: 'House for Rent', value: 'House for Rent' },
                    { label: 'Looking for Roommate', value: 'Looking for Roommate' },
                    { label: 'Sublease', value: 'Sublease' },
                ],
            },
            { key: 'price', label: 'Monthly Rent', type: 'text', placeholder: 'e.g. $800/month', prefix: '$' },
            { key: 'bedrooms', label: 'Bedrooms', type: 'text', placeholder: 'e.g. 2 BR' },
            {
                key: 'furnished',
                label: 'Furnished?',
                type: 'select',
                options: [
                    { label: 'Yes', value: 'Yes' },
                    { label: 'No', value: 'No' },
                    { label: 'Partially', value: 'Partially' },
                ],
            },
        ],
        descriptionPlaceholder: 'Describe the living space, amenities, nearby transit, move-in date, and any rules or preferences...',
        suggestedTags: ['Furnished', 'Near Transit', 'Muslim Friendly', 'Utilities Included'],
    },
    'Marriage & Matrimony': {
        fields: [
            {
                key: 'looking_for',
                label: 'Looking For',
                type: 'select',
                options: [
                    { label: 'Husband', value: 'Husband' },
                    { label: 'Wife', value: 'Wife' },
                ],
            },
            { key: 'age', label: 'Age', type: 'text', placeholder: 'e.g. 28' },
            { key: 'ethnicity', label: 'Ethnicity/Background', type: 'text', placeholder: 'e.g. South Asian, Arab, African' },
            {
                key: 'marital_status',
                label: 'Marital Status',
                type: 'select',
                options: [
                    { label: 'Never Married', value: 'Never Married' },
                    { label: 'Divorced', value: 'Divorced' },
                    { label: 'Widowed', value: 'Widowed' },
                ],
            },
        ],
        descriptionPlaceholder: 'Share about yourself, your values, what you are looking for in a spouse, your education, and your expectations...',
        suggestedTags: ['Serious Only', 'Family Oriented', 'Practicing'],
    },
    'Buy, Sell & Give Away': {
        fields: [
            {
                key: 'condition',
                label: 'Condition',
                type: 'select',
                options: [
                    { label: 'New', value: 'New' },
                    { label: 'Like New', value: 'Like New' },
                    { label: 'Good', value: 'Good' },
                    { label: 'Fair', value: 'Fair' },
                    { label: 'For Parts', value: 'For Parts' },
                ],
            },
            { key: 'price', label: 'Price', type: 'text', placeholder: 'e.g. $150 or Free', prefix: '$' },
            {
                key: 'negotiable',
                label: 'Negotiable?',
                type: 'select',
                options: [
                    { label: 'Yes', value: 'Yes' },
                    { label: 'No', value: 'No' },
                    { label: 'Free', value: 'Free' },
                ],
            },
        ],
        descriptionPlaceholder: 'Describe what you are selling/giving away, brand, model, condition details, and pickup/delivery options...',
        suggestedTags: ['Free', 'New', 'Used', 'Discount', 'OBO'],
    },
    'Rideshare & Carpool': {
        fields: [
            {
                key: 'ride_type',
                label: 'Type',
                type: 'select',
                options: [
                    { label: 'Offering a Ride', value: 'Offering a Ride' },
                    { label: 'Looking for a Ride', value: 'Looking for a Ride' },
                    { label: 'Daily Carpool', value: 'Daily Carpool' },
                ],
            },
            { key: 'from_location', label: 'From', type: 'text', placeholder: 'e.g. Dallas, TX' },
            { key: 'to_location', label: 'To', type: 'text', placeholder: 'e.g. Houston, TX' },
            { key: 'travel_date', label: 'Date', type: 'text', placeholder: 'e.g. May 20, 2026 or Daily' },
        ],
        descriptionPlaceholder: 'Describe pickup/dropoff details, time, cost-sharing, and any preferences (no smoking, etc.)...',
        suggestedTags: ['Daily', 'One-Way', 'Weekend', 'Airport'],
    },
    'Education & Tutors': {
        fields: [
            {
                key: 'type',
                label: 'Type',
                type: 'select',
                options: [
                    { label: 'Offering Tutoring', value: 'Offering Tutoring' },
                    { label: 'Looking for Tutor', value: 'Looking for Tutor' },
                    { label: 'Study Group', value: 'Study Group' },
                    { label: 'Course/Workshop', value: 'Course/Workshop' },
                ],
            },
            { key: 'subject', label: 'Subject', type: 'text', placeholder: 'e.g. Math, Quran, Arabic' },
            { key: 'price', label: 'Rate', type: 'text', placeholder: 'e.g. $30/hr or Free' },
        ],
        descriptionPlaceholder: 'Describe the subject, your qualifications, availability, whether online or in-person, and experience level...',
        suggestedTags: ['Online', 'In-Person', 'Free', 'Quran', 'Arabic'],
    },
    'Local Services': {
        fields: [
            {
                key: 'service_type',
                label: 'Service Type',
                type: 'select',
                options: [
                    { label: 'Offering Service', value: 'Offering Service' },
                    { label: 'Looking for Service', value: 'Looking for Service' },
                ],
            },
            { key: 'specialty', label: 'Specialty', type: 'text', placeholder: 'e.g. Plumbing, Catering, Tailoring' },
            { key: 'price', label: 'Starting Price', type: 'text', placeholder: 'e.g. $50/hr or Contact for Quote' },
        ],
        descriptionPlaceholder: 'Describe the service, your experience, service area, availability, and how to reach you...',
        suggestedTags: ['Licensed', 'Halal', 'Mobile', 'Discount'],
    },
    'Community Events': {
        fields: [
            { key: 'event_date', label: 'Event Date', type: 'text', placeholder: 'e.g. June 15, 2026' },
            { key: 'event_time', label: 'Time', type: 'text', placeholder: 'e.g. 7:00 PM - 10:00 PM' },
            { key: 'venue', label: 'Venue / Location', type: 'text', placeholder: 'e.g. Islamic Center, Main Hall' },
            { key: 'price', label: 'Entry Fee', type: 'text', placeholder: 'e.g. Free or $10' },
        ],
        descriptionPlaceholder: 'Describe the event, speakers/activities, who should attend, and any registration details...',
        suggestedTags: ['Free', 'Family', 'Sisters Only', 'Brothers Only', 'Fundraiser'],
    },
    'Sports & Activities': {
        fields: [
            {
                key: 'activity_type',
                label: 'Type',
                type: 'select',
                options: [
                    { label: 'Looking for Players', value: 'Looking for Players' },
                    { label: 'Offering Coaching', value: 'Offering Coaching' },
                    { label: 'Team/Group', value: 'Team/Group' },
                    { label: 'Equipment for Sale', value: 'Equipment for Sale' },
                ],
            },
            { key: 'sport', label: 'Sport / Activity', type: 'text', placeholder: 'e.g. Soccer, Basketball, Hiking' },
            { key: 'schedule', label: 'Schedule', type: 'text', placeholder: 'e.g. Every Saturday 10 AM' },
        ],
        descriptionPlaceholder: 'Describe the activity, skill level, location, and how to join...',
        suggestedTags: ['Weekly', 'Beginner', 'Competitive', 'Free'],
    },
    'Professional Networking': {
        fields: [
            {
                key: 'networking_type',
                label: 'Type',
                type: 'select',
                options: [
                    { label: 'Looking for Partner', value: 'Looking for Partner' },
                    { label: 'Mentorship', value: 'Mentorship' },
                    { label: 'Collaboration', value: 'Collaboration' },
                    { label: 'Business Opportunity', value: 'Business Opportunity' },
                ],
            },
            { key: 'industry', label: 'Industry', type: 'text', placeholder: 'e.g. Tech, Healthcare, Real Estate' },
        ],
        descriptionPlaceholder: 'Describe what you are looking for, your background, and how others can connect with you...',
        suggestedTags: ['Startup', 'Muslim Owned', 'Halal', 'Volunteer'],
    },
    'Websites & Apps': {
        fields: [
            {
                key: 'project_type',
                label: 'Type',
                type: 'select',
                options: [
                    { label: 'Offering Development', value: 'Offering Development' },
                    { label: 'Looking for Developer', value: 'Looking for Developer' },
                    { label: 'Showcase / Launch', value: 'Showcase / Launch' },
                ],
            },
            { key: 'tech_stack', label: 'Technology', type: 'text', placeholder: 'e.g. React, Laravel, WordPress' },
            { key: 'price', label: 'Budget / Rate', type: 'text', placeholder: 'e.g. $500 fixed or $50/hr' },
        ],
        descriptionPlaceholder: 'Describe the project, requirements, timeline, and your experience or what you need...',
        suggestedTags: ['Remote', 'Freelance', 'Muslim Startup'],
    },
};

export function useCategoryFields(categoryName: string): CategoryConfig {
    return categoryConfigs[categoryName] || {
        fields: [],
        descriptionPlaceholder: 'Describe what you are offering or looking for...',
        suggestedTags: [],
    };
}
