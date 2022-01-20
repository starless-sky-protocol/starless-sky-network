# Starless Sky API reference

The Starless Sky API allows the client to communicate with the network running Starless Sky. All requests must be made in JSON format, and responses will always follow this format:

```json
{
	"success": true,
	"messages": [
		{
			"level": "info",
			"message": "Message inserted at public key"
		}
	],
	"response": {
		"message_length": 576,
		"id": "0xSID61e8b3f17e7002450r528",
		"message_blake3_digest": "..."
	}
}
```

Responses will have three root fields: 

- `success`: gets whether the request was successfully executed on the server;
- `messages`: returns a array of messages that occurred during execution. Response messages have two fields: `level` and `message`. Message levels can be:
   - `info`: informative message;
   - `warn`: some important warning, but that did not prevent the execution;
   - `error`: an error on the client-side that prevented the execution;
   - `fatal`: an error on the server-side that prevented the execution.
- `response`: the response body.