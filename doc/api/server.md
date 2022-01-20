# Server

This topic covers routes relating to the server and it's network in general.

## Ping

Use this route to get useful information about the network.

```json
GET /ping
```

Example response:

```json
{
	"success": true,
	"messages": [],
	"response": {
		"sls_server_version": "0.12.204",
		"php_version": "7.4.19",
		"operating_system": "WINNT",
		"server_info": {
			"COLLECT_SENDER_NET_INFORMATION": true,
			"ALLOW_NOT_IDENTIFIED_SENDERS": true,
			"ALLOW_MESSAGE_DELETION": true,
			"ALLOW_MESSAGE_EDIT": true,
			"MESSAGE_MAX_SIZE": 1048576
		}
	}
}
```