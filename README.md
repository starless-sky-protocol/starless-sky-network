![aa](https://i.imgur.com/69IIaFR.png)

<p align=center>Network protocol for sending and receiving secure messages over an assymetric layer.</p>

## What is the Starless Sky Protocol?

The Starless Sky Protocol is a one-to-many messaging connection protocol written in PHP that maintains data integrity and has unique and advanced encryption features. Its construction consists of RESTful APIs that do not depend of a database to function. The relationship of information is made by hashes calculations and asymmetric cryptography, where the holder of the private key has a public key that is used to receive the messages.

The algorithm is customizable by an environment file and centralized on servers known as "networks". Each network may contain its own algorithm for handling information. It is interesting to note that the existence of malicious networks can exist.

## Private Keys vs Public Keys

The idea of private keys and public keys is that one directly depends on the other. A public key is irreversible and cannot have the private key decrypted, but it is possible to calculate the public key from a private key. This indicates that a private key is the master access for that sender/receiver.

> The project is still under development and its algorithm is also being developed. Some security issues may change in the future, along with how it works.

## Documentation

For system documentation, consult the "doc" folder inside this repository.

## Roadmap

- Broadcasting channels (one-to-many communication);
- Groups (many-to-many communication);
- Interface application;
- Closed-generation servers;
- Private Keys authentications;
- Content integrity if network information changes;

## Credits and used third-party technologies

- BLAKE3 hash algorithm by [BLAKE3-Team](https://github.com/BLAKE3-team/BLAKE3)
- Inphinit Framework by [Inphinit](https://github.com/inphinit/inphinit)
- Cryptography research community.
