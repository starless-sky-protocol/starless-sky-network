<?php

namespace Controller;

class identity_controller
{
    use \svc\identity\generate_random_keypair;
    use \svc\identity\mnemonic_parse;
    use \svc\identity\set_identity_info;
    use \svc\identity\get_identity_info;
    use \svc\identity\delete_identity_info;
}
