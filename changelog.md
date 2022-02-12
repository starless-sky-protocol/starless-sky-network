# Changelog

## Version **0.16** Alpha

Development branch: `ALPHA`

- Add mnemonics to key pair generation, and reduced private key to it's BLAKE3 notation.
- Multicast is now supported. Send using `public_keys` instead of `public_key` in the `send-message` method.
- PHP Error messages (when development) will be included on error response too.
- Removed `config.php`.
- `composer.json` fixes.

## Version **0.15** Build **600**

Development branch: Alpha Release

- Updated project `composer.json` data to match project information.
- Introduced contracts: issuance of messages that allow the authentication of information on the network.
- As `inphinit/framework` updated to version `0.5.16`, there was an bug fixes that prevented the system from running properly in PHP 8.1.
- Now sending messages creates two copies of it, but maintaining its integrity and SkyID. One is stored for the sender and the other for the receiver. In this way, the sender is able to obtain a list of messages sent by him. Changing a message's data implies changing the messages in both stores.
- Routes that needed two ends (sender/receiver) were unified. More details on how to use them in the API documentation.
- Added `dns` option to the configuration file.
- Bug fixes in the generation of private keys.

Known bugs:

- The testing tool does not work in this version. A working version is already being made.

## Version **0.13** Build **335**

Development branch: Alpha Release

- Environment file is now configuration file. More details in the documentation `configuration.md`.
- Symmetric Encrypting functions now generate a 16-byte fixed random IV.
- Optimized the generation of hashes by reducing its base from 34 to 16. Private-keys remains at Base 64.
- The hashes and address prefixes will always be "0x".
- Message sizes and other variables can now be written in non-bytes notation in `.env`.
- Updated example environment file `.env.example`.
- Fixed a bug where the Blake3 lib script was not compatible with PHP 8 and above.
- Fixed a bug where the system did not check the message size in the edit route.

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