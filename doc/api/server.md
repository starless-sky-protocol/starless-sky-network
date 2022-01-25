# Server

This topic covers routes relating to the server and it's network in general.

## Ping

Use this route to get useful information about the network.

    GET /ping

Example response:

```json
{
	"success": true,
	"messages": [],
	"response": {
		"sls_server_version": "0.12.335",
		"php_version": "8.0.15",
		"operating_system": "WINNT",
		"server_info": {
			"allow_not_identified_senders": false,
			"allow_message_edit": true,
			"allow_message_deletion": true,
			"message_max_size": "2M",
			"sign_message_max_size": "256K",
			"sign_max_expiration": 3600
		}
	}
}
```