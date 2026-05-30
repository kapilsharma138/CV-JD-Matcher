<?php

return [

    'must_have' => [
        // weight 3 — if JD has these and CV doesn't, big penalty
        // think: what does every PHP job ask for?
        'php'        => 3,
        'laravel'    => 3,
        'javascript'    => 3,
        'sql'    => 3,
        'mysql'    => 3,
        'rest api' => 3,
        'restful' => 3,
        // add 5-8 more yourself
    ],

    'important' => [
        // weight 2 — strong signals
        'aws'    => 2,
        'docker' => 2,
        'node.js' => 2,
        'redis' => 2,
        'microservices' => 2,
        'ci/cd' => 2,
        // add more yourself
    ],

    'good_to_have' => [
        // weight 1 — nice to have
        'typescript' => 1,
        'mongodb' => 1,
        'AJAX' => 1,
        'git' => 1,
        // add more yourself
    ],

    'related' => [
        // adjacent terms — used by SuggestionEngine
        // format: 'missing_term' => 'what_you_have_instead'
        'nestjs'     => 'Express.js',
        'terraform'  => 'CloudFormation',
        'graphql'    => null,       // genuine gap
        'kubernetes' => 'Docker',
        'django'     => null,
        'spring'     => null,
        'rails'      => null,
        // add more as you think of them
    ],

];