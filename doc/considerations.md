## Security Considerations

Starless Sky Networks encrypts messages in it's storage, however it can be decrypted using public keys. To avoid catastrophes in network intrusions, none of the key pairs are stored on the server without some encryption.

Public keys is stored in the message signature, like the public key of the sender and the receptor. A message is stored with encryption as explained below:

```php
# Message path:
hash(public_key) / hash(message_sky_id)

# Message contents:
message_data = encrypt(raw_message, server_salt + receptor_public_key)
```

Messages are stored within directories described by public key hashes, making it ineffective to look up a message using only its public key. The SkyID of messages is also hashed in the database, making it impossible to search for a SkyID directly in the files.

An modified network can always be **malicious**. Trust the network you are using, because its source code can be changed and don't keep the anonymity of receivers or senders.