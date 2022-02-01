<?php
/*
PHP implementation of BLAKE3

https://github.com/BLAKE3-team/BLAKE3-specs/blob/master/blake3.pdf

https://github.com/BLAKE3-team/BLAKE3

@denobisipsis 2021
@project-principium/starless-sky-network 2022
*/

DEFINE("BLAKE3_XOF_LENGTH", 32);

ini_set('precision', 8);

class BLAKE3
{
    const IV = [
        0x6a09e667, 0xbb67ae85,
        0x3c6ef372, 0xa54ff53a,
        0x510e527f, 0x9b05688c,
        0x1f83d9ab, 0x5be0cd19
    ];

    const BLOCK_SIZE     = 64;
    const HEX_BLOCK_SIZE    = 128;
    const CHUNK_SIZE     = 1024;
    const KEY_SIZE         = 32;
    const HASH_SIZE     = 32;
    const PARENT_SIZE     = 2 * 32;
    const WORD_BITS     = 32;
    const WORD_BYTES     = 4;
    const WORD_MAX         = 2 ** 32 - 1;
    const HEADER_SIZE     = 8;

    # domain flags
    const CHUNK_START         = 1 << 0;
    const CHUNK_END         = 1 << 1;
    const ROOT             = 1 << 3;
    const PARENT             = 1 << 2;
    const KEYED_HASH         = 1 << 4;
    const DERIVE_KEY         = 1 << 5;
    const DERIVE_KEY_MATERIAL     = 1 << 6;

    const PACKING = "V*";

    function __construct($key = "")
    {
        $this->cv    = [];
        $this->state = [];
        $this->key   = "";
        $this->flag            = 0;
        $this->kflag            = 0;

        if ($key) {
            $key  = substr($key, 0, self::BLOCK_SIZE);
            $size = strlen($key);

            if ($size < self::BLOCK_SIZE)
                $key .= str_repeat("\x0", self::BLOCK_SIZE - strlen($key));

            $key  = array_values(unpack(self::PACKING, $key));
            $this->cv      = $key;
            $this->kflag   = self::KEYED_HASH;
        } else    $this->cv      = self::IV;
    }

    function derivekey($context_key = "", $context = "", $xof_length = 32)
    {
        $this->state     = self::IV;

        $size         = strlen($context);
        if ($size < self::BLOCK_SIZE)
            $context .= str_repeat("\0", self::BLOCK_SIZE - $size);

        $context_words = array_values(unpack(self::PACKING, $context));
        $this->chacha($context_words, 0, $size, 43);

        $this->cv = array_slice($this->state, 0, 8);
        $this->kflag      = self::DERIVE_KEY_MATERIAL;

        $derive_key       = $this->hash($context_key, $xof_length);
        $derive_key_words = array_values(unpack(self::PACKING, $derive_key));

        $this->cv       = $derive_key_words;

        return $derive_key;
    }

    function chacha($chunk_words, $counter, $size, $flag, $is_xof = false, $block_over = false)
    {
        $MSG_SCHEDULE = [
            [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15],
            [2, 6, 3, 10, 7, 0, 4, 13, 1, 11, 12, 5, 9, 14, 15, 8],
            [3, 4, 10, 12, 13, 2, 7, 14, 6, 5, 9, 0, 11, 15, 8, 1],
            [10, 7, 12, 9, 14, 3, 13, 15, 4, 0, 11, 2, 5, 8, 1, 6],
            [12, 13, 9, 11, 15, 10, 14, 8, 7, 2, 5, 3, 0, 1, 6, 4],
            [9, 14, 11, 5, 8, 12, 15, 1, 13, 3, 0, 10, 2, 6, 4, 7],
            [11, 15, 5, 0, 1, 9, 8, 6, 14, 10, 2, 12, 3, 4, 7, 13],
        ];

        $v = $this->state;

        $mask   = self::WORD_MAX;

        $shl_i1 = (1 << 16) - 1;
        $shl_i2 = (1 << 24) - 1;
        $shl_h1 = (1 << 20) - 1;
        $shl_h2 = (1 << 25) - 1;

        $f1 = $v[0];
        $f2 = $v[1];
        $f3 = $v[2];
        $f4 = $v[3];
        $g1 = $v[4];
        $g2 = $v[5];
        $g3 = $v[6];
        $g4 = $v[7];
        $h1 = 0x6a09e667;
        $h2 = 0xbb67ae85;
        $h3 = 0x3c6ef372;
        $h4 = 0xa54ff53a;
        $i1 = $counter & $mask;
        $i2 = ($counter >> 32) & $mask;
        $i3 = $size;
        $i4 = $flag;

        for ($r = 0; $r < 7; $r++) {
            $sr = $MSG_SCHEDULE[$r];

            $f1 += $g1 + $chunk_words[$sr[0]];
            $i1 ^= $f1;
            $i1  = ((($i1 >> 16) & $shl_i1)  | ($i1 << 16)) & $mask;
            $h1 += $i1;
            $g1 ^= $h1;
            $g1  = ((($g1 >> 12) & $shl_h1)  | ($g1 << 20)) & $mask;

            $f1 += $g1 + $chunk_words[$sr[1]];
            $i1 ^= $f1;
            $i1  = ((($i1 >> 8)  & $shl_i2)  | ($i1 << 24)) & $mask;
            $h1 += $i1;
            $g1 ^= $h1;
            $g1  = (($g1 >> 7)  & $shl_h2)  | ($g1 << 25);

            $f1 &= $mask;
            $g1 &= $mask;
            $h1 &= $mask;

            $f2 += $g2 + $chunk_words[$sr[2]];
            $i2 ^= $f2;
            $i2  = ((($i2 >> 16) & $shl_i1)  | ($i2 << 16)) & $mask;
            $h2 += $i2;
            $g2 ^= $h2;
            $g2  = ((($g2 >> 12) & $shl_h1)  | ($g2 << 20)) & $mask;

            $f2 += $g2 + $chunk_words[$sr[3]];
            $i2 ^= $f2;
            $i2  = ((($i2 >> 8)  & $shl_i2)  | ($i2 << 24)) & $mask;
            $h2 += $i2;
            $g2 ^= $h2;
            $g2  = (($g2 >> 7)  & $shl_h2)  | ($g2 << 25);

            $f2 &= $mask;
            $g2 &= $mask;
            $h2 &= $mask;

            $f3 += $g3 + $chunk_words[$sr[4]];
            $i3 ^= $f3;
            $i3  = ((($i3 >> 16) & $shl_i1)  | ($i3 << 16)) & $mask;
            $h3 += $i3;
            $g3 ^= $h3;
            $g3  = ((($g3 >> 12) & $shl_h1)  | ($g3 << 20)) & $mask;

            $f3 += $g3 + $chunk_words[$sr[5]];
            $i3 ^= $f3;
            $i3  = ((($i3 >> 8)  & $shl_i2)  | ($i3 << 24)) & $mask;
            $h3 += $i3;
            $g3 ^= $h3;
            $g3  = (($g3 >> 7)  & $shl_h2)  | ($g3 << 25);

            $f3 &= $mask;
            $g3 &= $mask;
            $h3 &= $mask;

            $f4 += $g4 + $chunk_words[$sr[6]];
            $i4 ^= $f4;
            $i4  = ((($i4 >> 16) & $shl_i1)  | ($i4 << 16)) & $mask;
            $h4 += $i4;
            $g4 ^= $h4;
            $g4  = ((($g4 >> 12) & $shl_h1)  | ($g4 << 20)) & $mask;

            $f4 += $g4 + $chunk_words[$sr[7]];
            $i4 ^= $f4;
            $i4  = ((($i4 >> 8)  & $shl_i2)  | ($i4 << 24)) & $mask;
            $h4 += $i4;
            $g4 ^= $h4;
            $g4  = (($g4 >> 7)  & $shl_h2)  | ($g4 << 25);

            $f4 &= $mask;
            $g4 &= $mask;
            $h4 &= $mask;

            $f1 += $g2 + $chunk_words[$sr[8]];
            $i4 ^= $f1;
            $i4  = ((($i4 >> 16) & $shl_i1)  | ($i4 << 16)) & $mask;
            $h3 += $i4;
            $g2 ^= $h3;
            $g2  = ((($g2 >> 12) & $shl_h1)  | ($g2 << 20)) & $mask;

            $f1 += $g2 + $chunk_words[$sr[9]];
            $i4 ^= $f1;
            $i4  = ((($i4 >> 8)  & $shl_i2)  | ($i4 << 24)) & $mask;
            $h3 += $i4;
            $g2 ^= $h3;
            $g2  = (($g2 >> 7)  & $shl_h2)  | ($g2 << 25);

            $f2 += $g3 + $chunk_words[$sr[10]];
            $i1 ^= $f2;
            $i1  = ((($i1 >> 16) & $shl_i1)  | ($i1 << 16)) & $mask;
            $h4 += $i1;
            $g3 ^= $h4;
            $g3  = ((($g3 >> 12) & $shl_h1)  | ($g3 << 20)) & $mask;

            $f2 += $g3 + $chunk_words[$sr[11]];
            $i1 ^= $f2;
            $i1  = ((($i1 >> 8)  & $shl_i2)  | ($i1 << 24)) & $mask;
            $h4 += $i1;
            $g3 ^= $h4;
            $g3  = (($g3 >> 7)  & $shl_h2)  | ($g3 << 25);

            $f3 += $g4 + $chunk_words[$sr[12]];
            $i2 ^= $f3;
            $i2  = ((($i2 >> 16) & $shl_i1)  | ($i2 << 16)) & $mask;
            $h1 += $i2;
            $g4 ^= $h1;
            $g4  = ((($g4 >> 12) & $shl_h1)  | ($g4 << 20)) & $mask;

            $f3 += $g4 + $chunk_words[$sr[13]];
            $i2 ^= $f3;
            $i2  = ((($i2 >> 8)  & $shl_i2)  | ($i2 << 24)) & $mask;
            $h1 += $i2;
            $g4 ^= $h1;
            $g4  = (($g4 >> 7)  & $shl_h2)  | ($g4 << 25);

            $f4 += $g1 + $chunk_words[$sr[14]];
            $i3 ^= $f4;
            $i3  = ((($i3 >> 16) & $shl_i1)  | ($i3 << 16)) & $mask;
            $h2 += $i3;
            $g1 ^= $h2;
            $g1  = ((($g1 >> 12) & $shl_h1)  | ($g1 << 20)) & $mask;

            $f4 += $g1 + $chunk_words[$sr[15]];
            $i3 ^= $f4;
            $i3  = ((($i3 >> 8)  & $shl_i2)  | ($i3 << 24)) & $mask;
            $h2 += $i3;
            $g1 ^= $h2;
            $g1  = (($g1 >> 7)  & $shl_h2)  | ($g1 << 25);
        }

        $v[0] = $f1 ^ $h1;
        $v[1] = $f2 ^ $h2;
        $v[2] = $f3 ^ $h3;
        $v[3] = $f4 ^ $h4;
        $v[4] = $g1 ^ $i1;
        $v[5] = $g2 ^ $i2;
        $v[6] = $g3 ^ $i3;
        $v[7] = $g4 ^ $i4;
        $v[8] = $h1 & $mask;
        $v[9] = $h2 & $mask;
        $v[10] = $h3 & $mask;
        $v[11] = $h4 & $mask;
        $v[12] = $i1 & $mask;
        $v[13] = $i2 & $mask;
        $v[14] = $i3 & $mask;
        $v[15] = $i4 & $mask;

        if ($is_xof) {
            for ($i = 0; $i < 8; $i++)
                $v[$i + 8] ^= $this->cv[$i];
            if (!$block_over)
                $this->cv  = array_slice($v, 0, 8);
        }

        $this->state = $v;
    }

    function setflags($start = 0)
    {
        $this->flag = $this->kflag + $start;
    }

    function nodetree($tree)
    {
        $this->setflags(4);

        while (sizeof($tree) > 1) {
            $chaining = "";
            foreach ($tree as $pair) {
                if (strlen($pair) < 64)
                    $chaining .= $pair;
                else {
                    $this->state     = $this->cv;
                    $chunk_words     = array_values(unpack("V*", $pair));

                    $this->chacha($chunk_words, 0, 64, $this->flag);

                    $chaining .= pack("V*", ...array_slice($this->state, 0, 8));
                }
            }
            $tree = str_split($chaining, 64);
        }

        return $tree;
    }

    function nodebytes($block, $is_root = false)
    {
        $BLOCK_SIZE     = self::BLOCK_SIZE;
        $CHUNK_SIZE     = self::CHUNK_SIZE;
        $hashes     = "";
        $chunks     = str_split($block, $CHUNK_SIZE);
        $size        = $BLOCK_SIZE;

        for ($j = 0; $j < sizeof($chunks) - 1; $j++) {
            $this->state = $this->cv;

            $chunk_words = array_chunk(array_values(unpack("V*", $chunks[$j])), 16);
            $this->chacha($chunk_words[0], $j, $BLOCK_SIZE, $this->kflag + 1, true, !$is_root);
            for ($k = 1; $k < sizeof($chunk_words) - 1; $k++) {
                $this->chacha($chunk_words[$k], $j, $BLOCK_SIZE, $this->kflag, true, !$is_root);
            }
            $this->chacha($chunk_words[$k], $j, $size, $this->kflag + 2, true, !$is_root);

            $hashes .= pack("V*", ...array_slice($this->state, 0, 8));
        }

        $this->state = $this->cv;

        if (strlen($chunks[$j]) > $BLOCK_SIZE) {
            if (strlen($chunks[$j]) < $CHUNK_SIZE) {
                $size = strlen($chunks[$j]) % $BLOCK_SIZE;

                if (!$size)
                    $size = $BLOCK_SIZE;

                $npad          = ceil(strlen($chunks[$j]) / $BLOCK_SIZE) * $BLOCK_SIZE;
                $chunks[$j]  .= str_repeat("\x0", $npad - strlen($chunks[$j]));
            }

            $chunk_words = array_chunk(array_values(unpack("V*", $chunks[$j])), 16);

            $this->chacha($chunk_words[0], $j, $BLOCK_SIZE, $this->kflag + 1, true, !$is_root);

            for ($k = 1; $k < sizeof($chunk_words) - 1; $k++)
                $this->chacha($chunk_words[$k], $j, $BLOCK_SIZE, $this->kflag, true, !$is_root);

            if ($is_root) {
                $this->setflags(10);
                $j = 0;
            } else $this->setflags(2);

            $chunk_words = $chunk_words[$k];
        } else {
            $size = strlen($chunks[$j]);
            $chunk_words = array_values(unpack("V*", $chunks[$j] . str_repeat("\x0", $BLOCK_SIZE - strlen($chunks[$j]))));

            $flag = 3;

            if ($is_root) {
                $flag   |= 8;
                $j      = 0;
            }

            $this->setflags($flag);
        }

        // for XOF output

        $this->last_cv         = $this->cv;
        $this->last_state    = $this->state;

        $this->chacha($chunk_words, $j, $size, $this->flag, true, !$is_root);

        $hashes .= pack("V*", ...array_slice($this->state, 0, 8));

        // last_v for generating the first xof digest

        $this->last_chunk     = $chunk_words;
        $this->last_size     = $size;
        $this->last_v         = $this->state;

        return $hashes;
    }

    function XOF_output($hash, $XOF_digest_length)
    {
        // Output bytes. By default 32

        $cycles     = ceil($XOF_digest_length / self::BLOCK_SIZE);
        $XofHash    = $hash;
        $XofHash       .= pack(self::PACKING, ...array_slice($this->last_v, 8));

        for ($k = 1; $k < $cycles; $k++) {
            $this->cv     = $this->last_cv;
            $this->state    = $this->last_state;
            $this->chacha($this->last_chunk, $k, $this->last_size, $this->flag, true);
            $XofHash       .= pack(self::PACKING, ...$this->state);
        }

        // final xof bytes 

        $last_bytes = self::BLOCK_SIZE - ($XOF_digest_length % self::BLOCK_SIZE);

        if ($last_bytes != self::BLOCK_SIZE)
            $XofHash = substr($XofHash, 0, -$last_bytes);

        return $XofHash;
    }

    function hash($block, $XOF_digest_length = 32)
    {
        if (strlen($block) <= self::CHUNK_SIZE)
            $is_root = true;
        else    $is_root = false;

        $tree = str_split($this->nodebytes($block, $is_root), self::BLOCK_SIZE);
        /*
		This is the reverse tree. It makes a reduction from left to right in pairs
		
		First it computes all the hashes from input data, then make the tree reduction of hashes
		till there is only one pair
		
		If there is an odd number of hashes, it pass the last hash without processing it 
		till there is a parent		
		*/
        if (sizeof($tree) > 1)
            $tree = $this->nodetree($tree);

        if (strlen($tree[0]) > 32) {
            $this->state     = $this->cv;

            $chunk_words     = array_values(unpack("V*", $tree[0]));

            $this->last_cv         = $this->cv;
            $this->last_state     = $this->state;
            $this->last_chunk     = $chunk_words;
            $this->last_size     = 64;

            $flag    = self::CHUNK_START | self::CHUNK_END | self::ROOT;
            $this->setflags(++$flag);

            $this->chacha($chunk_words, 0, 64, $this->flag, 1);

            $this->last_v = $this->state;

            $hash = pack("V*", ...array_slice($this->state, 0, 8));
        } else     $hash = $tree[0];

        return $this->XOF_output($hash, $XOF_digest_length);
    }
}

function hmac_blake3($content, $key, $length = BLAKE3_XOF_LENGTH)
{
    $b2 = new BLAKE3($key);
    return $b2->hash($content, $length);
}

function blake3($content, $length = BLAKE3_XOF_LENGTH)
{
    $b2 = new BLAKE3();
    return bin2hex($b2->hash($content, $length));
}
