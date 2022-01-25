<?php

function gen_skyid()
{
    return uniqid() . config("crypto.skyid_instance") . base_convert(rand(100000, 999999), 10, 32);
}
