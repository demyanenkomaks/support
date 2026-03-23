<?php

declare(strict_types=1);

return [
    'validate' => [
        'errors' => [
            'email' => [
                'format' => 'Invalid email format.',
                'max_length' => 'Email must not be longer than :max characters.',
                'local_max_length' => 'Local part of email (before @) must not be longer than :max characters.',
                'domain_max_length' => 'Domain part of email (after @) must not be longer than :max characters.',
                'invalid_tld' => 'The domain zone :tld is not valid.',
            ],
            'phone' => [
                'start' => 'Phone number must start with +7.',
                'length' => 'Phone number must contain exactly :length digits.',
                'format' => 'Invalid phone number format. Use format +71234567890.',
            ],
            'phone_international' => [
                'start' => 'International phone number must start with +.',
                'min' => 'International phone number must contain at least :min digits.',
                'max' => 'International phone number must contain at most :max digits.',
                'format' => 'Invalid international phone number format.',
            ],
            'name' => [
                'max_length' => 'Field must not exceed :max characters.',
                'invalid_characters' => 'Only letters, hyphen, space and apostrophe are allowed.',
                'empty' => 'Field cannot contain only spaces, hyphens or apostrophes.',
            ],
            'quantity' => [
                'forbidden_characters' => 'Field must not contain characters e, E, +, -.',
                'not_integer' => 'Field must be an integer.',
                'min' => 'Value must be at least :min.',
                'max' => 'Value must be no more than :max.',
            ],
            'date' => [
                'format' => 'Invalid date format. Use format :format.',
                'future' => 'Date must be in the future.',
                'past' => 'Date must be in the past.',
            ],
            'time' => [
                'format' => 'Invalid time format. Use format :format.',
                'future' => 'Time must be in the future.',
                'past' => 'Time must be in the past.',
            ],
            'datetime' => [
                'format' => 'Invalid date and time format. Use format :format.',
                'future' => 'Date and time must be in the future.',
                'past' => 'Date and time must be in the past.',
            ],
            'image' => [
                'extension' => 'File must have one of the following extensions: :extensions.',
                'mime' => 'Invalid image file type.',
                'size' => 'Image size must not exceed :max MB.',
            ],
            'video' => [
                'extension' => 'File must have one of the following extensions: :extensions.',
                'mime' => 'Invalid video file type.',
                'size' => 'Video size must not exceed :max MB.',
            ],
            'document' => [
                'extension' => 'File must have one of the following extensions: :extensions.',
                'mime' => 'Invalid document type.',
                'size' => 'Document size must not exceed :max MB.',
            ],
        ],
    ],
];
