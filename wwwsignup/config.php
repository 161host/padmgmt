<?php
return [
    'product_name' => getenv('PRODUCT_NAME'),

    'smtp_host' => getenv('SMTP_HOST'),
    'smtp_user' => getenv('SMTP_USER'),
    'smtp_pass' => getenv('SMTP_PASS'),
    'smtp_port' => getenv('SMTP_PORT'),
    'smtp_secure' => getenv('SMTP_SECURE'),

    'from_email' => getenv('FROM_EMAIL'),
    'from_name' => getenv('FROM_NAME'),

    'base_url' => getenv('BASE_URL'),
    'admin_secret' => getenv('ADMIN_SECRET'),

    'telegram_bot_token' => getenv('TELEGRAM_BOT_TOKEN'),
    'telegram_chat_id' => getenv('TELEGRAM_CHAT_ID'),

    'data_file' => getenv('DATA_FILE'),
    'reject_file' => getenv('REJECT_FILE'),

    'api_key' => getenv('ADMIN_API_KEY'),
    'flow_id' => getenv('FLOW_ID'),
    'group_flow_id' => getenv('GROUP_FLOW_ID'),

    'flow_name' => getenv('FLOW_NAME'),
    'group_flow_name' => getenv('GROUP_FLOW_NAME'),

    'invite_url' => getenv('INVITE_URL'),
    'api_url' => getenv('API_URL'),
    'mgmt_url' => getenv('MGMT_URL'),
    'legal' => getenv('LEGAL_URL')
];