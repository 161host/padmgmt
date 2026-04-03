<?php
return [
    'client_id' => getenv('MGMT_CLIENT_ID'),
    'client_secret' => getenv('MGMT_CLIENT_SECRET'),
    'redirect_uri' => getenv('MGMT_REDIRECT_URI'),
    'oidc_url' => getenv('MGMT_OIDC_URL'),
    'api_url' => getenv('API_URL'),
    'api_key' => getenv('MGMT_API_KEY'),
    'flow_id' => getenv('FLOW_ID'),
    'product_name' => getenv('PRODUCT_NAME'),
    'invite_url' => getenv('INVITE_URL')
];