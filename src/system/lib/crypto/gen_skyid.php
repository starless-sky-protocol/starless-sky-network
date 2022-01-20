<?php

function gen_skyid()
{
    return SLS_ID_PREFIX . uniqid() . SKYID_INSTANCE . base_convert(rand(100000, 999999), 10, 32);
}
