# Smart Contracts and Signing information

Use these functions to issue, verify and validate smart contracts on the Starless Sky network.

## Issue Sign Request

Use this function to issue a contract request with your identity to a public identity.

    POST /sign

```json
{
	"private_key": "<issuer-private-key>",
	"public_key": "<signer-public-key>",
    "message": "Authorize login at foobar.com...",
    "expires": 3600
}
```

where:
- `private_key`: the private key of the contract issuer;
- `public_key`: the public key of the target signer;
- `message`: the contract content message. It should be smaller than `information.sign_message_max_size`.
- `expires`: the time in seconds for the contract to expire if the recipient does not sign the message. If the value is lesser than `information.sign_max_expiration`, the server's default sign maximum expiration time will be used.