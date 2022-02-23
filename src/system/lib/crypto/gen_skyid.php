<?php

function gen_skyid()
{
    return uniqid() . base_convert(rand(100000, 999999), 10, 32);
}
