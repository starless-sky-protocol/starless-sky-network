<?php

namespace Controller;

class messages_controller
{
    use \svc\message\add;
    use \svc\message\edit;
    use \svc\message\browse;
    use \svc\message\read_from_sender;
    use \svc\message\read_from_receiver;
    use \svc\message\delete_from_sender;
    use \svc\message\delete_from_receiver;
}
