# Changelog

## Version **0.12** Build **231**

Development branch: Alpha Release

- The protocol no longer collects network information from the message sender.
- Network Identities: network users can now provide public information about their public keys. The information is provided using their private keys.
- Default `response` object is now `null` instead an empty array when there's no response to return.
- Message routes Add and Edit now returns public key information about sender and receiver.
- Fixed a bug that didn't allow sending an empty private key in the message POST if the network allows anonymous keys.
- Fixed a bug where messages wasn't deleted on DELETE route because their ids could not be found.

## Version **0.12** Build **204**

Development branch: Alpha Release

This is the first working version of Starless Sky Network. Basic testing has been done and better fixes will be made in the future. Situations of small or big changes occurring are expected in certain situations.