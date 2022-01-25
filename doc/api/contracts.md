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

## Decide Sign Request

Use this function for the contract receiver to make a decision about the contract. Decisions include viewing, signing or rejecting.

    POST /sign/<term>

```json
{
	"id": "<contract-id>",
	"private_key": "<signer-private-key>"
}
```

where:
- `term`: is the action where the receiver will act in this contract. Terms can be one of:
  - `view`: this action does not make any decisions in the contract, it just visualizes it.
  - `sign`: The receiver signs the contract and authenticate it.
  - `refuse`: The receiver refuses the contract and rejects it.
- `private_key`: the private key of the contract receiver/signer;
- `id`: The SkyID of the issuer's contract.

> Note: after signing or refusing a contract it can no longer be changed.

## View Contract status (by issuer)

Gets information about the generated contract for one signer.

    GET /sign

```json
{
	"private_key": "<issuer-private-key>",
	"public_key": "<signer-public-key>",
	"id": "<contract-id>"
}
```

where:
- `private_key`: the private key of the contract issuer;
- `public_key`: the public key of the target signer;
- `id`: The SkyID of the issued contract.