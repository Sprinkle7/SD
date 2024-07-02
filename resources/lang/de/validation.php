<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */

    'accepted' => 'German The :attribute must be accepted.',
    'active_url' => 'German The :attribute is not a valid URL.',
    'after' => 'German The :attribute must be a date after :date.',
    'after_or_equal' => 'German The :attribute must be a date after or equal to :date.',
    'alpha' => 'German The :attribute must only contain letters.',
    'alpha_dash' => 'German The :attribute must only contain letters, numbers, dashes and underscores.',
    'alpha_num' => 'German The :attribute must only contain letters and numbers.',
    'array' => 'German The :attribute must be an array.',
    'before' => 'German The :attribute must be a date before :date.',
    'before_or_equal' => 'German The :attribute must be a date before or equal to :date.',
    'between' => [
        'numeric' => 'German The :attribute must be between :min and :max.',
        'file' => 'German The :attribute must be between :min and :max kilobytes.',
        'string' => 'German The :attribute must be between :min and :max characters.',
        'array' => 'German The :attribute must have between :min and :max items.',
    ],
    'boolean' => 'German The :attribute field must be true or false.',
    'confirmed' => 'German The :attribute confirmation does not match.',
    'current_password' => 'German The password is incorrect.',
    'date' => 'German The :attribute is not a valid date.',
    'date_equals' => 'German The :attribute must be a date equal to :date.',
    'date_format' => 'German The :attribute does not match the format :format.',
    'different' => 'German The :attribute and :other must be different.',
    'digits' => 'German The :attribute must be :digits digits.',
    'digits_between' => 'German The :attribute must be between :min and :max digits.',
    'dimensions' => 'German The :attribute has invalid image dimensions.',
    'distinct' => 'German The :attribute field has a duplicate value.',
    'email' => 'German The :attribute must be a valid email address.',
    'ends_with' => 'German The :attribute must end with one of the following: :values.',
    'exists' => 'German The selected :attribute is invalid.',
    'file' => 'German The :attribute must be a file.',
    'filled' => 'German The :attribute field must have a value.',
    'gt' => [
        'numeric' => 'German The :attribute must be greater than :value.',
        'file' => 'German The :attribute must be greater than :value kilobytes.',
        'string' => 'German The :attribute must be greater than :value characters.',
        'array' => 'German The :attribute must have more than :value items.',
    ],
    'gte' => [
        'numeric' => 'German The :attribute must be greater than or equal :value.',
        'file' => 'German The :attribute must be greater than or equal :value kilobytes.',
        'string' => 'German The :attribute must be greater than or equal :value characters.',
        'array' => 'German The :attribute must have :value items or more.',
    ],
    'image' => 'German The :attribute must be an image.',
    'in' => 'German The selected :attribute is invalid.',
    'in_array' => 'German The :attribute field does not exist in :other.',
    'integer' => 'German The :attribute must be an integer.',
    'ip' => 'German The :attribute must be a valid IP address.',
    'ipv4' => 'German The :attribute must be a valid IPv4 address.',
    'ipv6' => 'German The :attribute must be a valid IPv6 address.',
    'json' => 'German The :attribute must be a valid JSON string.',
    'lt' => [
        'numeric' => 'German The :attribute must be less than :value.',
        'file' => 'German The :attribute must be less than :value kilobytes.',
        'string' => 'German The :attribute must be less than :value characters.',
        'array' => 'German The :attribute must have less than :value items.',
    ],
    'lte' => [
        'numeric' => 'German The :attribute must be less than or equal :value.',
        'file' => 'German The :attribute must be less than or equal :value kilobytes.',
        'string' => 'German The :attribute must be less than or equal :value characters.',
        'array' => 'German The :attribute must not have more than :value items.',
    ],
    'max' => [
        'numeric' => 'German The :attribute must not be greater than :max.',
        'file' => 'German The :attribute must not be greater than :max kilobytes.',
        'string' => 'German The :attribute must not be greater than :max characters.',
        'array' => 'German The :attribute must not have more than :max items.',
    ],
    'mimes' => 'German The :attribute must be a file of type: :values.',
    'mimetypes' => 'German The :attribute must be a file of type: :values.',
    'min' => [
        'numeric' => 'German The :attribute must be at least :min.',
        'file' => 'German The :attribute must be at least :min kilobytes.',
        'string' => 'German The :attribute must be at least :min characters.',
        'array' => 'German The :attribute must have at least :min items.',
    ],
    'multiple_of' => 'German The :attribute must be a multiple of :value.',
    'not_in' => 'German The selected :attribute is invalid.',
    'not_regex' => 'German The :attribute format is invalid.',
    'numeric' => 'German The :attribute must be a number.',
    'password' => 'German The password is incorrect.',
    'present' => 'German The :attribute field must be present.',
    'regex' => 'German The :attribute format is invalid.',
    'required' => 'German The :attribute field is required.',
    'required_if' => 'German The :attribute field is required when :other is :value.',
    'required_unless' => 'German The :attribute field is required unless :other is in :values.',
    'required_with' => 'German The :attribute field is required when :values is present.',
    'required_with_all' => 'German The :attribute field is required when :values are present.',
    'required_without' => 'German The :attribute field is required when :values is not present.',
    'required_without_all' => 'German The :attribute field is required when none of :values are present.',
    'prohibited' => 'German The :attribute field is prohibited.',
    'prohibited_if' => 'German The :attribute field is prohibited when :other is :value.',
    'prohibited_unless' => 'German The :attribute field is prohibited unless :other is in :values.',
    'same' => 'German The :attribute and :other must match.',
    'size' => [
        'numeric' => 'German The :attribute must be :size.',
        'file' => 'German The :attribute must be :size kilobytes.',
        'string' => 'German The :attribute must be :size characters.',
        'array' => 'German The :attribute must contain :size items.',
    ],
    'starts_with' => 'German The :attribute must start with one of the following: :values.',
    'string' => 'German The :attribute must be a string.',
    'timezone' => 'German The :attribute must be a valid timezone.',
    'unique' => 'German The :attribute has already been taken.',
    'uploaded' => 'German The :attribute failed to upload.',
    'url' => 'German The :attribute must be a valid URL.',
    'uuid' => 'German The :attribute must be a valid UUID.',
    'check-language' => 'German Selected language is not available',
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
