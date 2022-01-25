## Configuration Variables

It's always important to have all environment variables declared in your configuration file, even the ones that will not be used. The lack of them may cause execution exceptions.

Your configuration file is located at `src/system/application/Config/config.php`.

- `development` (boolean)

   Defines if the application is in debug mode. Use only for when testing the network and not production ready. When enabled, "fatal" level error messages will display detailed error information.

- `crypto.base_hmac_key` (string)

   It's a unique network key that will be used to sign the HMAC codes generated on the platform.

- `crypto.base_symetric_key` (string)

   It's an information that will be keyed to the symmetric key whenever any data is encrypted or decrypted. This information will never be used without any variable key, always accompanied by a non-static member in the encryption.

- `crypto.base_symetric_iv_seed` (string)

   It's the initialization vector of the symetric cryptographic functions. Must be a string with a fixed length of 16 characters.

- `crypto.private_key_server_id` (string)

   It's the initialization vector of the private keys. All private keys will be generated from this vector and they will include an encrypted manifest in their information. Every private key that will access the network must have the network manifest, otherwise it will be prevented from being used.

- `crypto.skyid_instance` (string)

   The instance of a SkyID is a static identifier that will become a SkyID ID. The ideal is a value between 1 to 5 digits maximum. Larger values are concatenated into your ID and have no significance in it's function. For more information, read about skyid in this documentation.

## Network Information

- `information.allow_not_identified_senders` (boolean)

   Defines whether the server will allow sending a message to a public key without specifying a private key. In this case, you can send a message to a public key without identifying its address.

   The sender's public key is sent to the receiver when the sender's private key is used in the message. The sender's private key is never revealed to the receiver.

- `information.allow_message_edit` (boolean)

   Allows the sender to edit their message after sending it to the receiver. Only the sender can edit your message when it was sent using a private key. It is not possible to identify the origin of a message if it does not have a private key in its submission.

- `information.allow_message_deletion` (boolean)

   Allows both parties to delete a sent message. Both receiver and sender can delete the message if they use their private keys. With this option turned off, messages cannot be deleted from the system.

- `information.message_max_size` (string)

   Defines the maximum size of a message with its content and subject. The value is given with the unit suffix, like `512K` for 512 kbytes or `2M` for 2 megabytes. Accepted suffixes are `B`, `K`, `M`, `G` and `T`.

- `information.sign_message_max_size` (string)

   Defines the maximum size of a sign request message. The value is given with the unit suffix, like `512K` for 512 kbytes or `2M` for 2 megabytes. Accepted suffixes are `B`, `K`, `M`, `G` and `T`.

- `information.sign_max_expiration` (int)

   Maximum time in seconds for a sign request to expire.