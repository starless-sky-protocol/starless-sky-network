## Security Considerations

Starless Sky Networks encrypts contents in it's storage, such as identity info or contracts, however it can be decrypted using public keys. To avoid catastrophes in network intrusions, none of the key pairs are stored on the server without encryption.

Private keys are never stored on the network in any way. Public keys and SkyIDs are stored in hashed forms.

Plain public keys is stored in the message signature, like the public key of the sender and the receptor. Every content is stored with symetric encryption as explained below:

```php
# Information path:
hash(public_key) / hash(sky_id)

# Contents:
data = encrypt(plain_text, hmac(hash(public_key), server_key))
```

Content are stored within directories described by public key hashes, making it ineffective to look up a message using only its public key. The SkyID of content is also hashed in the database, making it impossible to search for a SkyID directly in the files.

> An modified network can always be **malicious**. Trust the network you are using, because its source code can be changed and don't keep the anonymity of receivers or senders.

It is always ideal that the machine where the network is running has all its operating system modules updated and has strong authentication (like SSH key).

According to the project's license, The MIT License, Starless Sky or Project Principium does not provides any warranty about the content circulating on the network.