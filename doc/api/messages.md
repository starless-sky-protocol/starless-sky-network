# Messages

This topic explains how the API works with messages on the network.

Summary:
* [Sending a message](#sending-a-message)
* [List messages by private key](#list-messages-by-private-key)
* [Read message for receiver](#read-message-for-receiver)
* [Read message for sender](#read-message-for-sender)
* [Edit message after sending it](#edit-message-after-sending-it)
* [Delete message from the receiver](#delete-message-from-the-receiver)
* [Delete message from the sender](#delete-message-from-the-sender)

## Sending a message

To send a message to a public key on the network, use the following endpoint:

```json
POST /messages

{
    "public_key": "<destinatary_public_key>",
    "private_key": "<sender_private_key>",

    "message": {
        "subject": "Important encrypted message",
        "content": "Hello, buddy!"
    }
}
```

where:
- `public_key`: sets the recipient's public key. Sending a message to a random public key is like sending a message to the wind.
- `private_key`: sets the sender's private key. It is used to show the origin of the message (by sending its public key). This field is optional when `ALLOW_NOT_IDENTIFIED_SENDERS` is `false`.
- `message.subject`: It's the subject of the message. Ideally, it should be like the subject of an email: brief and short.
- `message.content`: It's the content of the message. It is always plain text, it is never interpreted by the recipient (or at least it shouldn't be).

## List messages by private key

This method is used to get the inbox messages through the receptor's private key.

> Note: each message on the response trims out the first 30 characters of each message as a preview only. To read each complete message, consider calling a complete message read endpoint.

```json
GET /messages

{
    "private_key": "<receiver_private_key>",
    "pagination_data": {
        "skip": 0,
        "take": 50
    }
}
```

where:
- `private_key`: the private key of the message receiver. The messages that will be returned will be the ones that were sent to the public key of this private key.
- `pagination_data.skip`: skips an number of messages.
- `pagination_data.take`: take an number of messages. Set to `-1` to get all messages from skip.

## Read message for receiver

This method is used for the message receiver content to read the message received by the sender.

```json
GET /messages/receiver/<id>

{
    "private_key": "<receiver_private_key>"
}
```

where:
- `id`: a parameter in the URL that is the ID of the message that will be returned.
- `private_key`: the private key of the message receiver.

## Read message for sender

This method is used by the sender of the message itself to read the message after it has been sent.

```json
GET /messages/sender/<id>

{
    "private_key": "<sender_private_key>",
    "public_key": "<receiver_public_key>"
}
```

where:
- `id`: a parameter in the URL that is the ID of the message that will be returned.
- `private_key`: the private key of the message sender.
- `public_key`: the public key of the message receiver.

## Edit message after sending it

Use this route to edit the message after it has been sent to the receiver. It can only be used by the sender of the message and only if `ALLOW_MESSAGE_EDIT` is enabled on the network.

> Note: using this route the `manifest.is_modified` attribute of the message manifest will be changed to `true` and a new digest hash will be generated for the new message. Also, if the server collects network information, it will be updated on the message manifest too. The message SkyID remains preserved.

```json
PUT /messages/<id>

{
    "private_key": "<sender_private_key>",
    "public_key": "<receiver_public_key>"
}
```

where:
- `id`: a parameter in the URL that is the ID of the message that will be returned.
- `private_key`: the private key of the message sender.
- `public_key`: the public key of the message receiver.
- `message.subject`: The new message subject.
- `message.content`: The new message content.

## Delete message from the receiver

Use this method for the receiver permanently delete an incoming message in their public key.

> Note: this method only works if `ALLOW_MESSAGE_DELETION` is enabled on the server.

```json
DELETE /messages/receiver/<id>

{
    "private_key": "<receiver_private_key>"
}
```

where:
- `id`: a parameter in the URL that is the ID of the message that will be permanently deleted.
- `private_key`: the private key of the message receiver.

## Delete message from the sender

Use this method for the sender to delete the message sent to a public key.

> Note: this method only works if `ALLOW_MESSAGE_DELETION` is enabled on the server.

```json
DELETE /messages/sender/<id>

{
    "private_key": "<sender_private_key>",
    "public_key": "<receiver_public_key>"
}
```

where:
- `id`: a parameter in the URL that is the ID of the message that will be permanently deleted.
- `private_key`: the private key of the message sender.
- `public_key`: the public key of the message receiver.