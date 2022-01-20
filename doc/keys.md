## Private Keys

A private key is the master key that controls an identity on the network. It can be used to send messages, communicate or generate it's public key.

A private key should never be exposed or sent to anyone, as it is used to gain control over that identity on the server.

Private keys are generated from random cryptographic data. Depending on the server implementation it may have different sizes, but the default is 2048 bits.

Private keys are never stored on the server.

## Public Keys

Public keys are public addresses that are exposed on the network, without exposing it's private key. It is used to receive messages and identify their sender.

These keys are generated through the private keys, but irreversibly. A private key will always produce the same public key.

The public key hashes are stored on the server, avoiding directly exposing the public key in the server's storage.
