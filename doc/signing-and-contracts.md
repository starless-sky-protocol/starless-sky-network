# Signing and Smart Contracts

Smart contracts/signing is a way to authenticate a message without having to expose your private key outside the network. Basically, one end sends an authentication request to the other end. It can refuse or sign the authentication, sending the response to the sending end.

```
                                                                 ┌────────┐
                                                         ┌─────► │  Sign  ├────────┐
                                                         │       └────────┘        │
         ┌──────────────────┐   ┌───────────────┐        │                         │
 Alice───┤Please, sign this.├───┤ 61ef113ab3e0c ├─► Bob ─┤                         │
         └──────────────────┘   └───────▲───────┘        │                         │
                                        │                │       ┌────────┐        │
                                        │                └─────► │ Refuse │        │
                                        │                        └────────┘        │
                                        │                                          │
                                        └──────────────────────────────────────────┘
```

Read more about how it works in the API documentation.