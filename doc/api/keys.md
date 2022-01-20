# Keys

Use this topic for dedicated routes for keys and key pairs.

## Generate Key Pair

Use this route to generate a cryptographically secure key pair.

```json
GET /generate-keypair
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