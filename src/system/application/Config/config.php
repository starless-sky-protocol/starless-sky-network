<?php
return array(
    'development' => true,
    'dns' => 'starlessskynetworkserver.test',
    'crypto' => [
        'base_hmac_key' => "aomaogmeao948",
        'base_hash_salt' => "436461",
        'base_symetric_key' => "baobua94a",
        'base_symetric_iv_seed' => "u949aq9jsa",
        'private_key_server_id' => "12512163",
        'skyid_instance' => 1000
    ],
    'information' => [
        'allow_not_identified_senders' => true,
        'allow_message_edit' => true,
        'allow_message_deletion' => true,
        'message_max_size' => '2M',
        'sign_message_max_size' => '256K',
        'sign_max_expiration' => 3600
    ]
);
