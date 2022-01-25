# Messages

Messaging on the Starless Sky platform works end-to-end. As all content circulating on the network, messages are also encrypted and only the receiver or sender of the message can access them. The symmetric key shared between them is always a hmac hash of the recipient's public key.

Starless Sky currently supports sending one-to-one messages, editing messages (if allowed) and deleting the message (if allowed). Only the sender of a message can edit the sent message, and only if the server allows them to be modified. Messages should never be interpreted. They are sent in plain text. Interpreting messages on client can cause the remote code execution problem.

