# Keys

Use this topic for dedicated routes for keys and key pairs.

## Generate Key Pair

Use this route to generate a cryptographically secure key pair.

```json
GET /identity/generate-keypair
```

Example response:

```json
{
	"success": true,
	"messages": [
		{
			"level": "info",
			"message": "Keypair successfully generated"
		}
	],
	"response": {
		"private_key": "0xSPVxT6pLrTV3LisxAe2t64MYy299LMiMJ3vvxYIRunvS2RZE0j2ODuJDhIBArt+FBYDfOrzQVfQN+Lj+VViFoLuzb9qmpYWxAcndKb5q7Vh\/2uYD+3YzOj4i9eNCxXnnk6e+aweWExxsFI9ZnEfRH8UHa44b55sf6ztNMm6blCvoU6Kx+d85gfPXHgK9S++RhaowlPkXwk8imWSMkEOOC7Yc66dnDtIRn6DmXDVvFXKDwuoSf7Gnu6dsBu0k02N9D+t+IdxIZ+yw2msAjnQWHgglrZ9x90JIC3bAUsYfLdvv3gkSZBWFuU2XzSmB3I0QsrRsKheKha1X78nlgDNExv0Qg==",
		"public_key": "0xSPBuzdz51651vnz1n543l4t4l5h6r12414n5j6118355o6d0z4b71142z3r4u421m79"
	}
}
```

## Set Identity Info on Network

Use this route to define public information about who you are on the network using your private-key. All fields are optional.

> The content must be smaller than `MESSAGE_MAX_SIZE`.

    POST /identity

```json
{
	"private_key": "<your-private-key>",
	"public": {
		"name": "your public name",
		"biography": "your public biography name"
	}
}
```

where:
- `private_key`: the private key of the identity bearer on the network;
- `public.name`: the public name of the identity;
- `public.biography`: the short biography about the identity.

## Get Identity Info on Network

Use this route to get information about a public key present on the network. Note that not all public keys will return information, even if they have messages circulating on the network. Identity on the server is completely optional.

    GET /identity

```json
{
	"public_key": "..."
}
```

where:
- `public_key`: the public key of who you want to see information about.

## Delete Identity Info on Network

Use this route to delete your public identity information on the server.

> Note: this route does not delete messages.

    DELETE /identity

```json
{
	"private_key": "<your-private-key>"
}
```

where:
- `private_key`: the private key of the identity bearer on the network,