![aa](https://i.imgur.com/69IIaFR.png)

<p align=center>Descentralized network protocol providing smart identity over an secure layer.</p>

## What is the Starless Sky Protocol?

Starless Sky is a network protocol for secure identities, providing the use of assymetric identities, public information, end-to-end messaging and smart contracts. The system consists of an identity holder having two keys, the private and the public, where through the private one he controls who he is on the network and the public one he allows to be exposed to receive messages, contracts or provide information about who he is.

It also has an one-to-many messaging connection protocol written that maintains data integrity and has unique and advanced encryption features. Its construction consists of RESTful APIs that do not depend of a database to function. The relationship of information is made by hashes calculations and asymmetric cryptography, where the holder of the private key has a public key that is used to receive the messages.

The algorithm is customizable by an environment file and centralized on servers known as "networks". Users can create their own networks or use existing networks. Each network may contain its own algorithm for handling information. It is interesting to note that the existence of malicious networks can exist.

## Private Keys vs Public Keys

The idea of private keys and public keys is that one directly depends on the other. A public key is irreversible and cannot have the private key decrypted, but it is possible to calculate the public key from a private key. This indicates that a private key is the master access for that sender/receiver.

With a private key it is possible to calculate your public key, but with a public key it is impossible to calculate your private key.

> The project is still under development and its algorithm is also being developed. Some security issues may change in the future, along with how it works.

## Main Features

- Smart contracts and signing modules; 
- Decentralized networks;
- Anonymous and custom identities without revealing the real identity of the keypair holder;
- Based on symmetric and asymmetric encryption, no information is stored in plain text in the network storage.
- Keys and values are never stored directly in the network storage;
- Fast and instant content delivery network - no network authenticity required;
- Easy implementation and use.
- It is naturally impossible to spoof information.

## Security Considerations

There is no perfect system, but there are procedures that make a network secure enough to don't have problems in the future. This system uses the latest encryption technologies and tends to be secure.

Read `doc/considerations.md` to understand the best security practices for a network that can store identities.

## Documentation

For system documentation, consult the `doc` folder inside this repository. You should start learning from `getting-started.md`.

## Roadmap

- Broadcasting channels (one-to-many communication);
- Groups (many-to-many communication);
- Interface application;
- Closed-generation servers;
- ~~Private Keys authentication for networks~~; 💥 _New on update 0.12.204_
- Content integrity if network information changes;
- Store data with Blockchain;

## Donate ❤

If you support a more private and decentralized internet, consider keeping this project alive:

    Bitcoin:       1LeGvA8wXhEBuvrWYt5Yb1jQZ5FsjALYqM
    Litecoin:      LNQ4A7R96vWQ8wzwypJeS3o1wWV2xuAsUv
    Ethereum:      0x8d2ced9d9229149fdbd216284f4bb8d147a50d93
    BNB (BSC):     0x8d2ced9d9229149fdbd216284f4bb8d147a50d93
    USDT (TRX):    TQFprvPVJJTgiiPMZehSnTY5P4RSQCSeNR

## Credits and used third-party technologies

- BLAKE3 hash algorithm by [BLAKE3-Team](https://github.com/BLAKE3-team/BLAKE3)
- Inphinit Framework by [Inphinit](https://github.com/inphinit/inphinit)
- Cryptography research community.
