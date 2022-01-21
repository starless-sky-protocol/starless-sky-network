## Environment Variables

It's always important to have all environment variables declared in your environment file, even the ones that will not be used. The lack of them may cause execution exceptions.

- `DEBUG_ENABLED` (boolean)

   Defines if the application is in debug mode. Use only for when testing the network and not production ready. When enabled, "fatal" level error messages will display detailed error information.

- `BASE_HMAC_KEY` (string)

   It's a unique network key that will be used to sign the HMAC codes generated on the platform.

- `BASE_SYMETRIC_KEY` (string)

   It's an information that will be concatenated to the symmetric key whenever any data is encrypted or decrypted. This information will never be used without any variable key, always accompanied by a non-static member in the encryption.

- `BASE_SYMETRIC_16BYTES_IV` (string)

   It's the initialization vector of the symetric cryptographic functions. Must be a string with a fixed length of 16 characters.

- `BASE_PRIVATE_KEY_IV` (string)

   It's the initialization vector of the private keys. All private keys will be generated from this vector and they will include an encrypted manifest in their information. Every private key that will access the network must have the network manifest, otherwise it will be prevented from being used.

- `SKYID_INSTANCE` (string)

   The instance of a SkyID is a static identifier that will become a SkyID ID. The ideal is a value between 1 to 5 digits maximum. Larger values are concatenated into your ID and have no significance in it's function. For more information, read about skyid in this documentation.

## Network Information

- `ALLOW_NOT_IDENTIFIED_SENDERS` (boolean)

   Defines whether the server will allow sending a message to a public key without specifying a private key. In this case, you can send a message to a public key without identifying its address.

   The sender's public key is sent to the receiver when the sender's private key is used in the message. The sender's private key is never revealed to the receiver.

- `ALLOW_MESSAGE_EDIT` (boolean)

   Allows the sender to edit their message after sending it to the receiver. Only the sender can edit your message when it was sent using a private key. It is not possible to identify the origin of a message if it does not have a private key in its submission.

- `ALLOW_MESSAGE_DELETION` (boolean)

   Allows both parties to delete a sent message. Both receiver and sender can delete the message if they use their private keys. With this option turned off, messages cannot be deleted from the system.

- `MESSAGE_MAX_SIZE` (int)

   Defines the maximum size of a message with its content and subject. The number is given in bytes.